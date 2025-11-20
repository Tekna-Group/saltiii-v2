<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class Discord
{
    public static function send($data = [])
    {
        $webhook = env('DISCORD_WEBHOOK');
        if (!$webhook) {
            \Log::error("Discord webhook not configured.");
            return;
        }

        $client = new Client();

        // Build the embed
        $embed = [
            "title" => $data['action'],
            "description" =>
                "**Project:** {$data['project']}\n" .
                "**Task:** {$data['task']}",
            "color" => 5814783, // Blue-ish accent
            "fields" => []
        ];

        // Old value
        $embed['fields'][] = [
            "name" => "Update",
            "value" => ($data['old_value'] ?: "N/A") . " ➜ " . ($data['new_value'] ?: "N/A"),
            "inline" => false
        ];

      

        // Remarks (optional)
        if (!empty($data['remarks'])) {
            $embed['fields'][] = [
                "name" => "Remarks",
                "value" => $data['remarks'],
                "inline" => false
            ];
        }

        // File link (optional)
        if (!empty($data['file'])) {
            $embed['fields'][] = [
                "name" => "File",
                "value" => $data['file'],
                "inline" => false
            ];
        }

        // Task link
        $embed['fields'][] = [
            "name" => "Task Link",
            "value" => "[Click to open]({$data['link']})",
            "inline" => false
        ];
        $buttonComponent = [
            [
                "type" => 1,
                "components" => [
                    [
                        "type" => 2,
                        "style" => 5, // Link button
                        "label" => "Open Task",
                        "url" => $data['link']
                    ]
                ]
            ]
        ];
        // Final payload
        $payload = [
            "content" => "**{$data['action']} by {$data['user']}**",
            "embeds" => [$embed],
            "components" => $buttonComponent
        ];

        try {
            $client->post($webhook, [
                'json' => $payload
            ]);
        } catch (\Exception $e) {
            \Log::error("Discord webhook failed: " . $e->getMessage());
        }
    }
}
