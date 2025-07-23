@extends('layouts.header')
@section('css')

@endsection
@section('content')
<div class="row">
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title mb-3 flex-grow-1 text-start">Total Hours</h6>
                <div class="mb-2">
                    <lord-icon src="https://cdn.lordicon.com/kbtmbyzy.json" trigger="loop" colors="primary:#405189,secondary:#02a8b5" style="width:90px;height:90px"></lord-icon>
                </div>
                <h3 class="mb-1">{{$task->activities->sum('hours')}} hrs</h3>
                <div class="hstack gap-2 justify-content-center">
                   @if ($task->completed == 1)
                        <div class="hstack gap-2 justify-content-center">
                            <button class="btn btn-success btn-sm" disabled>
                                <i class="ri-check-line align-bottom me-1"></i> Completed
                            </button>
                        </div>
                    @else
                        <form action="{{ url('/task/complete/' . $task->id) }}" method="POST" class="d-inline">
                            @csrf
                            {{-- @method('PATCH') --}}
                            <div class="hstack gap-2 justify-content-center">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="ri-play-circle-line align-bottom me-1"></i> Complete
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <!--end card-->
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-4">
                  <form action="{{ url('/tasks/update-board/' . $task->id) }}" method="POST">
                    @csrf
                    
                    <select class="form-control" name="project_board_id" onchange="this.form.submit()">
                        <option value="">Select Task board</option>
                        @foreach($boards as $board)
                            <option value="{{ $board->id }}" @if($task->project_board_id == $board->id) selected @endif>
                                {{ $board->board }}
                            </option>
                        @endforeach
                    </select>
                </form>
                </div>
                <div class="table-card">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-medium">Tasks No</td>
                                <td>#{{$task->id}}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Tasks</td>
                                <td>{{$task->title}}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Project</td>
                                <td>{{$task->project->name}}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Priority</td>
                                <td><span class="badge bg-danger-subtle text-danger">{{$task->priority}}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Due Date</td>
                                <td>{{date('d M, Y',strtotime($task->due_date))}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!--end table-->
                </div>
            </div>
        </div>
        <!--end card-->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <h6 class="card-title mb-0 flex-grow-1">Assigned To</h6>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-soft-danger btn-sm material-shadow-none" data-bs-toggle="modal" data-bs-target="#inviteMembersModal"><i class="ri-share-line me-1 align-bottom"></i> Assigned Member</button>
                    </div>
                </div>
                <ul class="list-unstyled vstack gap-3 mb-0">
                    @foreach($task->users as $member)
                    <li>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="avatar-xs rounded-circle material-shadow">
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-1"><a href="pages-profile.html">{{$member->name}}</a></h6>
                                <p class="text-muted mb-0">{{$task->activities->where('user_id',$member->id)->sum('hours')}} hrs </p>
                            </div>
                            
                        </div>
                    </li>
                    @endforeach
                    
                </ul>
            </div>
        </div>
        <!--end card-->
        <!--end card-->
    </div>
    <!---end col-->
    <div class="col-xxl-9">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">
                    <h6 class="mb-3 fw-semibold text-uppercase">Summary</h6>
                    <p>{{$task->description}}</p>
                    
                </div>
            </div>
        </div>
        <!--end card-->
        <div class="card">
            <div class="card-header">
                <div>
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home-1" role="tab">
                                Comments ({{$task->comments->count()}})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#messages-1" role="tab">
                                Attachments File ({{$task->attachments->count()}})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#profile-1" role="tab">
                                Time Entries ({{$task->activities->count()}})
                            </a>
                        </li>
                    </ul>
                    <!--end nav-->
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="home-1" role="tabpanel">
                        <h5 class="card-title mb-4">Comments</h5>
                        <div data-simplebar style="height: 300px;" class="px-3 mx-n3 mb-2">
                            @foreach($task->comments->sortByDesc('created_at') as $comment)
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <img src="{{$comment->user->avatar}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fs-13"><a href="pages-profile.html">{{$comment->user->name}}</a> <small class="text-muted">{{date('d M, Y - H:i a',strtotime($comment->created_at))}}</small></h5>
                                    <p class="text-muted">{{$comment->comment}}</p>
                                    
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <form method='POST' action='{{url('task-comment/'.$task->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label for="exampleFormControlTextarea1" class="form-label">Leave a Comments</label>
                                    <textarea class="form-control bg-light border-light" id="exampleFormControlTextarea1" name='comment' rows="3" placeholder="Enter comments" required></textarea>
                                </div>
                                <!--end col-->
                                <div class="col-12 text-end">
                                    {{-- <button type="button" class="btn btn-ghost-secondary btn-icon waves-effect me-1"><i class="ri-attachment-line fs-16"></i></button> --}}
                                    <button type='submit'  class="btn btn-success">Post Comments</button>
                                </div>
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="messages-1" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6">
                                    <form method='POST' action='{{url('task-attachment/'.$task->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                                                @csrf
                                    <div class="card">
                                        <div class="input-group">
                                        
                                                    <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" name='file' aria-label="Upload" required>
                                                    <button type='submit' class="btn btn-outline-success material-shadow-none" type="button" id="inputGroupFileAddon04">Upload</button>
                                            
                                        </div>
                                    </div>
                                    </form>
                                    <!-- end card -->
                                </div> <!-- end col -->
                            </div>
                        <div class="table-responsive table-card">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">File Name</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Upload Date</th>
                                        <th scope="col">Uploaded by</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($task->attachments as $attachment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-info-subtle text-info rounded fs-20">
                                                        <i class="ri-folder-line"></i>
                                                    </div>
                                                </div>
                                                <div class="ms-3 flex-grow-1">
                                                    <h6 class="fs-15 mb-0"><a href="{{url($attachment->file)}}" target='_blank'>{{$attachment->name}}</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{$attachment->file_type}} File</td>
                                        <td>{{$attachment->file_size}} MB</td>
                                        <td>{{date('d M, Y',strtotime($attachment->created_at))}}</td>
                                        <td>{{$attachment->user->name}}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="btn btn-light btn-icon" id="dropdownMenuLink3" data-bs-toggle="dropdown" aria-expanded="true">
                                                    <i class="ri-equalizer-fill"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink3" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 23px);">
                                                    <li><a class="dropdown-item" href="{{url($attachment->file)}}" target='_blank'><i class="ri-eye-fill me-2 align-middle"></i>View</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-5-line me-2 align-middle"></i>Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!--end table-->
                        </div>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="profile-1" role="tabpanel">
                        <h6 class="card-title mb-4 pb-2">Time Entries <button class="btn btn-success btn-sm" href="#addActivity" data-bs-toggle="modal"><i class="ri-time-line align-bottom me-1"></i> Add Activity</button></h6>
                        <div class="table-responsive table-card">
                            <table class="table align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Member</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Hours</th>
                                        <th scope="col">Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($task->activities as $activity)
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center">
                                                <img src="{{asset($activity->user->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xxs">
                                                <div class="flex-grow-1 ms-2">
                                                    <a href="#" class="fw-medium">{{$activity->user->name}}</a>
                                                </div>
                                            </div>
                                        </th>
                                        <td>{{date('d M, Y',strtotime($activity->date))}}</td>
                                        <td>{{$activity->hours}} hrs</td>
                                        <td>{{$activity->activity}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!--end table-->
                        </div>
                    </div>
                    <!--edn tab-pane-->

                </div>
                <!--end tab-content-->
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@include('tasks.add_activity')
@endsection

@section('js')

@endsection