<div class="modal fade" id="projects" tabindex="-1" aria-labelledby="projectsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-warning-subtle">
                    <h5 class="modal-title" id="projectsLabel">Projects</h5>
                    <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <!--end row-->
                        <div class="table-responsive table-card">
                            <table class="table table-nowrap table-centered align-middle">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th scope="col">Project Name</th>
                                        <th scope="col">Tasks</th>
                                        <th scope="col">Assignee</th>
                                        <th scope="col">Total Hours</th>
                                    </tr><!-- end tr -->
                                </thead><!-- thead -->
    
                                <tbody>
                                    @foreach($projects->where('completed',0) as $project)
                                    <tr>
                                        <td><a href="{{url('/view-project/'.$project->id)}}" target="_blank">{{$project->name}}</a></td>
                                        <td>{{$project->tasks->where('completed',1)->count()."/".$project->tasks->count()}}</td>
                                        <td>
                                            <div class="avatar-group flex-nowrap">
                                            @foreach($project->users as $member)
                                              <div class="avatar-group-item">
                                                <a href="javascript: void(0);" class="d-inline-block avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                                    <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xs">
                                                </a>
                                              </div>
                                            @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            {{$project->activities->sum('hours')}} hrs
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody><!-- end tbody -->
                            </table><!-- end table -->
                        </div>
    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
</div>
<div class="modal fade" id="ongoing_tasks" tabindex="-1" aria-labelledby="ongoing_tasks_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-warning-subtle">
                <h5 class="modal-title" id="ongoing_tasks_label">Ongoing Tasks</h5>
                <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-nowrap table-centered align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Project</th>
                                <th scope="col">Task</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Status</th>
                                <th scope="col">Assignee</th>
                            </tr>
                        </thead><!-- end thead -->
                        <tbody>
                            @foreach($tasks->sortBy('due_date') as $task)
                            <tr @if($task->due_date < date('Y-m-d')) class='text-danger' @endif>
                                <td><a href="{{ url('/view-project/'.$task->project_id) }}" >{{$task->project->name}}</a></td>
                                <td><a href="{{url('view-project/view-task/'.$task->id)}}" >{{$task->title}}</a></td>
                                <td>{{date('M d',strtotime($task->due_date))}}</td>
                                <td>{{$task->board->board}}</td>
                                <td>
                                    <div class="avatar-group flex-nowrap">
                                    @foreach($task->users as $member)
                                     <div class="avatar-group-item">
                                        <a href="javascript: void(0);" class="d-inline-block avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                                <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xs">
                                            </a>
                                     </div>
                                    @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delayed_tasks" tabindex="-1" aria-labelledby="delayed_tasksLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-warning-subtle">
                <h5 class="modal-title" id="delayed_tasksLabel">Delayed Tasks</h5>
                <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-nowrap table-centered align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Project</th>
                                <th scope="col">Task</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Status</th>
                                <th scope="col">Assignee</th>
                            </tr>
                        </thead><!-- end thead -->
                        <tbody>
                            @foreach($tasks->where('due_date','<',date('Y-m-d')) as $task)
                            <tr @if($task->due_date < date('Y-m-d')) class='text-danger' @endif>
                                <td><a href="{{ url('/view-project/'.$task->project_id) }}" >{{$task->project->name}}</a></td>
                                <td><a href="{{url('view-project/view-task/'.$task->id)}}" >{{$task->title}}</a></td>
                                <td>{{date('M d',strtotime($task->due_date))}}</td>
                                <td>{{$task->board->board}}</td>
                                <td>
                                    <div class="avatar-group flex-nowrap">
                                    @foreach($task->users as $member)
                                     <div class="avatar-group-item">
                                        <a href="javascript: void(0);" class="d-inline-block avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                                <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xs">
                                            </a>
                                     </div>
                                    @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>