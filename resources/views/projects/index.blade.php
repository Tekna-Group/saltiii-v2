@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')

<div class="row g-4 mb-3">
    <div class="col-sm-auto">
        <div>
            <a data-bs-toggle="modal" data-bs-target="#projectModal" class="btn btn-soft-secondary"><i class="ri-add-line align-bottom me-1"></i> Add New</a>
        </div>
    </div>
    <div class="col-sm">
        <div class="d-flex justify-content-sm-end gap-2">
            <div class="search-box ms-2">
                <input type="text" class="form-control" placeholder="Search...">
                <i class="ri-search-line search-icon"></i>
            </div>

        </div>
    </div>
</div><!-- end row -->  
<div class="row">
    @foreach($projects as $project)
        <div class="col-xxl-3 col-sm-6 project-card">
            <div class="card card-height-100">
                <div class="card-body">
                    <div class="d-flex flex-column h-100">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-4">Last Update: {{date('M d, Y H:i a',strtotime($project->updated_at))  }}</p>
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-warning-subtle rounded p-2">
                                        <img src="{{asset($project->icon)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="img-fluid p-1">
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1 fs-15"><a href="{{url('/view-project/'.$project->id)}}" class="text-body">{{$project->name}}</a></h5>
                                <p class="text-muted text-truncate-two-lines mb-3">{{$project->description}}</p>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex mb-2">
                                <div class="flex-grow-1">
                                    <div>Tasks</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div><i class="ri-list-check align-bottom me-1 text-muted"></i> {{$project->tasks->where('completed',1)->count()}}/{{$project->tasks->count()}}</div>
                                </div>
                            </div>
                            <div class="progress progress-sm animated-progress">
                                <div class="progress-bar bg-success" role="progressbar" aria-valuenow="34" aria-valuemin="0" aria-valuemax="100" style="width: 0/0%;"></div><!-- /.progress-bar -->
                            </div><!-- /.progress -->
                        </div>
                    </div>

                </div>
                <!-- end card body -->
                <div class="card-footer bg-transparent border-top-dashed py-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="text-muted">
                                <i class="ri-calendar-event-fill me-1 align-bottom"></i> {{date('d M, Y',strtotime($project->created_at))}}
                            </div>
                            <!-- end card -->
                        </div>
                    </div>
                </div>
                <!-- end card footer -->
            </div>
            <!-- end card -->
        </div>
    @endforeach
    <!-- end col -->

    <!-- end col -->
</div>
<!-- end row -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Project</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form method='POST' action='new-project' onsubmit="show();"   enctype="multipart/form-data">
            @csrf
          <div class="modal-body">
            <input type="hidden" id="projectId">
            <div class="mb-3">
              <label>Project Name</label>
              <input type="text" class="form-control" name='name' id="projectName" required>
            </div>
            <div class="mb-3">
              <label>Team Member</label>
              <select type="text" class="form-control required select2" name='team_member[]' multiple id='team_member' required>
                {{-- <option value="">Select Team Member</option> --}}
                @foreach($users as $user)
                  <option value="{{$user->id}}">{{$user->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label>Status</label>
              <select class="form-select" id="projectStatus" name="status" required>
                 <option value='To be started'>To be started</option>
                <option value='In Progress'>In Progress</option>
                <option value='Completed'>Completed</option>
              </select>
            </div>
            <div class="mb-3">
              <label>Project Icon</label>
              <input type="file" class="form-control" name='icon' id="projectImage" accept="image/*" >
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
          </form>
        </div>
    </div>
  </div>
  
@endsection
@section('js')
<script src="{{asset('inside_css/assets/js/pages/project-list.init.js')}}"></script>
<!-- Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    
    // Initialize Select2 inside modals
    $('#projectModal').on('shown.bs.modal', function () {
        $('.select2').select2({
            dropdownParent: $('#projectModal')
        });
    });

   
});
</script>
@endsection
