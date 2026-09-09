<?php

namespace App\Services;

class TaskTrackerImport
{
    const MAX_ROWS = 2000;
    const MAX_XML_BYTES = 25000000;

    private $requiredHeaders = [
        'bug_cr',
        'module',
        'screen_feature',
        'description',
        'type',
        'priority',
        'reported_by',
        'date_reported',
        'status',
        'notes_dev_action',
    ];

    public function read($path, $extension)
    {
        $extension = strtolower($extension);

        if ($extension === 'xlsx') {
            $rows = $this->readXlsx($path);
        } elseif ($extension === 'csv') {
            $rows = $this->readCsv($path);
        } else {
            throw new \RuntimeException('Use an .xlsx or .csv tracker file.');
        }

        return $this->mapRows($rows);
    }

    private function readXlsx($path)
    {
        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException('Excel import requires the PHP Zip extension.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('The Excel file could not be opened.');
        }

        try {
            $sharedStrings = $this->readSharedStrings($zip);
            $worksheetNames = [];

            for ($index = 0; $index < $zip->numFiles; $index++) {
                $name = $zip->getNameIndex($index);
                if (preg_match('#^xl/worksheets/sheet[0-9]+\.xml$#i', $name)) {
                    $worksheetNames[] = $name;
                }
            }

            natsort($worksheetNames);
            foreach ($worksheetNames as $worksheetName) {
                $rows = $this->readWorksheet($zip, $worksheetName, $sharedStrings);
                if ($this->findHeaderRow($rows) !== null) {
                    return $rows;
                }
            }
        } finally {
            $zip->close();
        }

        throw new \RuntimeException('No worksheet with the required tracker headers was found.');
    }

    private function readSharedStrings(\ZipArchive $zip)
    {
        if ($zip->locateName('xl/sharedStrings.xml') === false) {
            return [];
        }

        $xml = $this->readZipEntry($zip, 'xl/sharedStrings.xml');
        $document = $this->loadXml($xml, 'shared strings');
        $namespace = $document->getNamespaces(true);
        $mainNamespace = isset($namespace['']) ? $namespace[''] : null;

        if ($mainNamespace) {
            $document->registerXPathNamespace('x', $mainNamespace);
            $items = $document->xpath('//x:si');
        } else {
            $items = $document->xpath('//si');
        }

        $strings = [];
        foreach ($items ?: [] as $item) {
            $strings[] = $this->nodeText($item);
        }

        return $strings;
    }

    private function readWorksheet(\ZipArchive $zip, $name, array $sharedStrings)
    {
        $xml = $this->readZipEntry($zip, $name);
        $document = $this->loadXml($xml, 'worksheet');
        $namespace = $document->getNamespaces(true);
        $mainNamespace = isset($namespace['']) ? $namespace[''] : null;

        if ($mainNamespace) {
            $document->registerXPathNamespace('x', $mainNamespace);
            $rowNodes = $document->xpath('//x:sheetData/x:row');
        } else {
            $rowNodes = $document->xpath('//sheetData/row');
        }

        $rows = [];
        foreach ($rowNodes ?: [] as $rowNode) {
            $row = [];
            $cells = $mainNamespace ? $rowNode->children($mainNamespace)->c : $rowNode->c;

            foreach ($cells as $cell) {
                $attributes = $cell->attributes();
                $reference = (string) $attributes['r'];
                $type = (string) $attributes['t'];
                $column = $this->columnIndex($reference);
                $children = $mainNamespace ? $cell->children($mainNamespace) : $cell;
                $value = (string) $children->v;

                if ($type === 's') {
                    $value = isset($sharedStrings[(int) $value]) ? $sharedStrings[(int) $value] : '';
                } elseif ($type === 'inlineStr') {
                    $value = $this->nodeText($cell);
                } elseif ($type === 'b') {
                    $value = $value === '1' ? 'Yes' : 'No';
                }

                $row[$column] = trim((string) $value);
            }

            if ($row) {
                ksort($row);
                $rows[] = $row;

                if (count($rows) > self::MAX_ROWS + 20) {
                    throw new \RuntimeException('The tracker exceeds the 2,000-row import limit.');
                }
            }
        }

        return $rows;
    }

    private function readCsv($path)
    {
        $handle = fopen($path, 'rb');
        if (!$handle) {
            throw new \RuntimeException('The CSV file could not be opened.');
        }

        try {
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                return [];
            }

            $delimiter = $this->detectDelimiter($firstLine);
            rewind($handle);
            $rows = [];

            while (($values = fgetcsv($handle, 0, $delimiter)) !== false) {
                $row = [];
                foreach ($values as $column => $value) {
                    $row[$column] = trim((string) $value);
                }
                $rows[] = $row;

                if (count($rows) > self::MAX_ROWS + 20) {
                    throw new \RuntimeException('The tracker exceeds the 2,000-row import limit.');
                }
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }

    private function mapRows(array $rows)
    {
        $headerIndex = $this->findHeaderRow($rows);
        if ($headerIndex === null) {
            throw new \RuntimeException('The tracker headers do not match the required format.');
        }

        $columns = [];
        foreach ($rows[$headerIndex] as $column => $heading) {
            $key = $this->headerKey($heading);
            if ($key) {
                $columns[$key] = $column;
            }
        }

        $missing = array_values(array_diff($this->requiredHeaders, array_keys($columns)));
        if ($missing) {
            throw new \RuntimeException('Missing tracker columns: '.implode(', ', $this->displayHeaders($missing)).'.');
        }

        $records = [];
        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $record = [];
            foreach ($this->requiredHeaders as $key) {
                $record[$key] = isset($row[$columns[$key]]) ? trim((string) $row[$columns[$key]]) : '';
            }

            if (implode('', $record) === '') {
                continue;
            }

            $record['date_reported'] = $this->formatDate($record['date_reported']);
            $records[] = $record;

            if (count($records) > self::MAX_ROWS) {
                throw new \RuntimeException('The tracker exceeds the 2,000-row import limit.');
            }
        }

        if (!$records) {
            throw new \RuntimeException('The tracker contains no task rows.');
        }

        return $records;
    }

    private function findHeaderRow(array $rows)
    {
        foreach ($rows as $index => $row) {
            $keys = [];
            foreach ($row as $heading) {
                $key = $this->headerKey($heading);
                if ($key) {
                    $keys[$key] = true;
                }
            }

            if (count($keys) >= 7 && isset($keys['description'], $keys['status'], $keys['notes_dev_action'])) {
                return $index;
            }
        }

        return null;
    }

    private function headerKey($heading)
    {
        $normalized = strtolower(preg_replace('/[^a-z0-9]+/i', '', trim((string) $heading)));
        $aliases = [
            'bugcr' => 'bug_cr',
            'bugcrno' => 'bug_cr',
            'bugcrnumber' => 'bug_cr',
            'module' => 'module',
            'screenfeature' => 'screen_feature',
            'description' => 'description',
            'type' => 'type',
            'priority' => 'priority',
            'reportedby' => 'reported_by',
            'datereported' => 'date_reported',
            'status' => 'status',
            'notesdevaction' => 'notes_dev_action',
            'notesdeveloperaction' => 'notes_dev_action',
        ];

        return isset($aliases[$normalized]) ? $aliases[$normalized] : null;
    }

    private function displayHeaders(array $keys)
    {
        $labels = [
            'bug_cr' => 'Bug/CR #',
            'module' => 'Module',
            'screen_feature' => 'Screen / Feature',
            'description' => 'Description',
            'type' => 'Type',
            'priority' => 'Priority',
            'reported_by' => 'Reported By',
            'date_reported' => 'Date Reported',
            'status' => 'Status',
            'notes_dev_action' => 'Notes / Dev Action',
        ];

        return array_map(function ($key) use ($labels) {
            return $labels[$key];
        }, $keys);
    }

    private function formatDate($value)
    {
        if ($value === '') {
            return '';
        }

        if (is_numeric($value) && (float) $value > 1000) {
            $timestamp = ((float) $value - 25569) * 86400;
            return gmdate('Y-m-d', (int) $timestamp);
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : $value;
    }

    private function columnIndex($reference)
    {
        if (!preg_match('/^([A-Z]+)/i', $reference, $matches)) {
            return 0;
        }

        $letters = strtoupper($matches[1]);
        $index = 0;
        for ($position = 0; $position < strlen($letters); $position++) {
            $index = ($index * 26) + (ord($letters[$position]) - 64);
        }

        return $index - 1;
    }

    private function detectDelimiter($line)
    {
        $delimiters = [',' => 0, ';' => 0, "\t" => 0];
        foreach ($delimiters as $delimiter => $count) {
            $delimiters[$delimiter] = count(str_getcsv($line, $delimiter));
        }

        arsort($delimiters);
        return (string) key($delimiters);
    }

    private function readZipEntry(\ZipArchive $zip, $name)
    {
        $stat = $zip->statName($name);
        if (!$stat || $stat['size'] > self::MAX_XML_BYTES) {
            throw new \RuntimeException('The Excel worksheet is too large to import safely.');
        }

        $contents = $zip->getFromName($name);
        if ($contents === false) {
            throw new \RuntimeException('The Excel worksheet could not be read.');
        }

        return $contents;
    }

    private function loadXml($xml, $label)
    {
        $previous = libxml_use_internal_errors(true);
        $document = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($document === false) {
            throw new \RuntimeException('The Excel '.$label.' data is invalid.');
        }

        return $document;
    }

    private function nodeText($node)
    {
        $textNodes = $node->xpath('.//*[local-name()="t"]');
        $parts = [];
        foreach ($textNodes ?: [] as $textNode) {
            $parts[] = (string) $textNode;
        }

        return implode('', $parts);
    }
}
