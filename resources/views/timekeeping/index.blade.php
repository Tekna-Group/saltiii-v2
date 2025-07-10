@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                <h4 class="card-title flex-grow-1 mb-0">Timekeeping Result</h4>
            </div><!-- end cardheader -->
            <div class="card-body">
                <div class="table-responsive table-card">
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
                                    @foreach($date_ranges as $date)
                                    <td scope="col">{{$activities->where('user_id',$user->id)->where('date',$date)->sum('hours')}}</td>
                                    @endforeach
                                    <td>{{$activities->where('user_id',$user->id)->sum('hours')}}</td>

                                </tr>
                            @endforeach
                            
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>


            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    @foreach($users as $user)
    <div class='col-xl-6'>
         <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 py-1">Activities </h4>
                
            </div><!-- end card header -->
            <div class="card-body">
                <div class="table-responsive table-card" id='table-container'>
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
                                    $rowCount = $dayActivities->count();
                                @endphp

                                @if ($rowCount > 0)
                                    @foreach ($dayActivities as $i => $act)
                                        <tr>
                                            @if ($i === 0)
                                                <td rowspan="{{ $rowCount }}">{{ date('M d - l',strtotime($date)) }}</td>
                                            @endif
                                             @if($dayActivities->count() == 1)
                                            <td rowspan="{{ $rowCount }}">{{ date('M d - l',strtotime($date)) }}</td>
                                            @endif
                                            <td>{{$act->project->name}} - {{ $act->task->title }}</td>
                                            <td>{{$act->activity}}</td>
                                            <td>{{$act->hours}} hrs</td>
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
                                <td>{{$activities->sum('hours')}} hrs</td>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    
    
        $('.select2').select2();

   
});
</script>
@endsection
