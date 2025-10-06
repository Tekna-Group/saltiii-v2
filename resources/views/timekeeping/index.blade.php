@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>

  
    .wrap-text {
  word-wrap: break-word;
  overflow-wrap: break-word;
}

</style>
<style>
    /* Ensure table cells wrap content */
    #report th, 
    #report td {
        white-space: normal !important; /* allow wrapping */
        word-wrap: break-word;
        word-break: break-word;
        vertical-align: middle;
        max-width: 150px; /* optional: limit width so wrapping occurs */
    }

    /* Optional: wrap header text neatly */
    #report th.wrap-text small {
        display: block;
        white-space: normal;
        text-align: center;
    }

    /* Optional: make table responsive */
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12 col-md-12">
        <div class='card'>
            <div class="card-header">
                <h5 class="card-title mb-0">Generate Timekeeping</h5>
            </div>
            <form onsubmit="show();"   enctype="multipart/form-data">
                <div class='card-body'>
                    <div class="row">
                        <div class="col-xl-2 col-md-2">
                            <label for="members" class="form-label">Members</label>
                            <select class='form-control select2' id='members' name='members' required>
                                <option value='ALL'>ALL</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <label for="boardName" class="form-label">Date From</label>
                            <input type='date' name='date_from' value='{{$date_from}}' class='form-control form-control-sm' required>
                            
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <label for="boardName" class="form-label">Date To</label>
                            <input type='date' name='date_to' value='{{$date_to}}' class='form-control form-control-sm' required>
                            
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <label for="boardName" class="form-label">&nbsp;</label><br>
                            <button type="submit" class="btn btn-success" >Generate</button>
                            
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@if($date_from)
<div class="row">
    <div class="col-xl-12">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title flex-grow-1 mb-0">Projects Hours <button class='btn btn-sm btn-info' onclick="exportTableToExcel('report','WE{{date('ymd',strtotime($saturday))}}.xls')">Export to Excel</button></h4>
            </div><!-- end cardheader -->
            <div class="card-body">
                <div class="table-responsive table-card" >
                    <table class="table  table-centered align-middle" id="report">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th scope="col">Personnel</th>
                                <th scope="col">Week Ending</th>
                                <th scope="col">No. of Hours</th>
                                @foreach($projects as $project)
                                <th class="wrap-text" style="word-wrap: break-word; ">
                                    <small>{!! preg_replace("/\s+/", "\n", $project->name) !!}</small>
                                </th>
                                @endforeach
                                 <th>Total</th>
                            </tr><!-- end tr -->
                        </thead><!-- thead -->

                        <tbody>
                            @foreach($users->sortBy('name') as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td>WE {{date('ymd',strtotime($saturday))}}</td>
                                    <td>{{number_format($activities->where('user_id',$user->id)->sum('hours'),2)}}</td>
                                    @foreach($projects as $project)
                                        <td class="wrap-text" >{{number_format($activities->where('user_id',$user->id)->where('project_id',$project->id)->sum('hours'),2)}}</td>
                                    @endforeach
                                    <td>{{number_format($activities->where('user_id',$user->id)->sum('hours'),2)}}</td>
                                </tr>
                            @endforeach
                            
                            
                            
                        </tbody><!-- end tbody -->
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end">Total</td>
                                <td>{{$activities->sum('hours')}}</td>
                                @foreach($projects as $project)
                                    <td class="wrap-text" style="word-wrap: break-word">{{number_format($activities->where('project_id',$project->id)->sum('hours'),2)}}</td>
                                @endforeach
                                <td>{{number_format($activities->sum('hours'),2)}}</td>
                            </tr>
                        </tfoot>
                    </table><!-- end table -->
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title flex-grow-1 mb-0">Timekeeping Result <button class='btn btn-sm btn-danger' onclick="printTable('')">Print</button></h4>
            </div><!-- end cardheader -->
            <div class="card-body">
                <div class="table-responsive table-card" id='table-container'>
                    <table class="table table-nowrap table-centered align-middle">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th scope="col">Name</th>
                                @foreach($date_ranges as $date)
                                 <th scope="col">{{date('d M, Y',strtotime($date))}}</th>
                                @endforeach
                                 <th>Total</th>
                            </tr><!-- end tr -->
                        </thead><!-- thead -->

                        <tbody>
                       
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    @php
                                        $totalHours = 0;
                                    @endphp
                                    @foreach($date_ranges as $date)
                                    <td scope="col">{{$activities->where('user_id',$user->id)->where('date',$date)->sum('hours')}}</td>
                                    @php
                                        $totalHours = $totalHours+$activities->where('user_id',$user->id)->where('date',$date)->sum('hours');
                                    @endphp
                                    @endforeach
                                    <td>{{$totalHours}}</td>

                                </tr>
                            @endforeach
                            
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>


            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    @foreach($users as $user)
    @php
        $totalHours = 0;
    @endphp
    <div class='col-xl-6'>
         <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 py-1">Activities <button class='btn btn-sm btn-danger' onclick="printTable({{$user->id}})">Print</button></h4>
                
            </div><!-- end card header -->
            <div class="card-body">
                <div class="table-responsive table-card" id='table-container{{$user->id}}'>
                    <table  class="table table-bordered table-nowrap table-centered align-middle mb-0">
                        <thead class="table-light">
                            
                            <tr>
                                <th scope="col" colspan=2>Name: {{$user->name}}</th>
                                <th scope="col" colspan=2>Date: {{date('M d',strtotime($last_sunday))}} - {{date('M d',strtotime($saturday))}}</th>
                            </tr>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Project/Task</th>
                                <th scope="col">Activity</th>
                                <th scope="col">Hours</th>
                            </tr>
                        </thead><!-- end thead -->
                       <tbody>
                            @for ($date = $last_sunday; $date <= $saturday; $date = date('Y-m-d', strtotime($date . ' +1 day')))
                                @php
                                    $dayActivities = $activities->where('user_id',$user->id)->where('date', $date);
                                    $totalHours = $totalHours + $dayActivities->sum('hours');
                                    $rowCount = $dayActivities->count();
                                @endphp

                                @if ($rowCount > 0)
                                     @foreach ($dayActivities as $act)
                                    <tr>
                                        @if ($loop->first)
                                            <td rowspan="{{ $rowCount }}">{{ date('M d - l',strtotime($date)) }}</td>
                                        @endif
                                        <td class="wrap-text">{{ $act->project->name }} - {{ $act->task->title }}</td>
                                        <td class="wrap-text">{{ $act->activity }}</td>
                                        <td>{{ $act->hours }} hrs</td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td>{{ date('M d - l',strtotime($date)) }}</td>
                                        <td colspan='3' class='text-center text-danger'>No activities</td>
                                    </tr>
                                @endif
                            @endfor
                            <tr>
                                <td colspan='2'></td>
                            <td class="text-right">Total</td>
                                <td>{{$totalHours}} hrs</td>
                            </tr>
                        </tbody>
                    </table><!-- end table -->
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div>
    @endforeach
</div>
@endif
@endsection
@section('js')
<script>
    function printTable(id) {
      const printContents = document.getElementById('table-container'+id).innerHTML;
      const originalContents = document.body.innerHTML;

      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
    }
  </script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    
    
        $('.select2').select2();

   
});
</script>
<script>
    function exportTableToExcel(tableId, filename = 'table.xls') {
      const table = document.getElementById(tableId);
      if (!table) return;
    
      const html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
          <head>
            <meta charset="UTF-8">
            <!-- Optional: default text as text to preserve leading zeros -->
            <style> td { mso-number-format:"\@"; } </style>
          </head>
          <body>${table.outerHTML}</body>
        </html>`;
    
      const blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel' });
      const url = URL.createObjectURL(blob);
    
      const a = document.createElement('a');
      a.href = url;
      a.download = filename.endsWith('.xls') ? filename : (filename + '.xls');
      document.body.appendChild(a);
      a.click();
      a.remove();
    
      setTimeout(() => URL.revokeObjectURL(url), 0);
    }
    </script>
@endsection
