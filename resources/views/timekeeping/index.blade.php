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
