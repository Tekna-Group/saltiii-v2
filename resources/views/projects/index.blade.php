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
        <div class="card">
            <div class="card-body">
                <div class="p-3 mt-n3 mx-n3 bg-secondary-subtle rounded-top">
                    <div class="d-flex gap-1 align-items-center justify-content-end my-n2">
                        <button type="button" class="btn avatar-xs p-0 favourite-btn active">
                            <span class="avatar-title bg-transparent fs-15">
                                <i class="ri-star-fill"></i>
                            </span>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-1 mt-n1 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i data-feather="more-horizontal" class="icon-sm"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{url('view-project')}}"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                    View</a>
                                <a class="dropdown-item" href="{{url('view-project')}}"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                    Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                    Remove</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center pb-3">
                        <img src="{{asset($project->icon)}}" onerror="this.src='{{ URL::asset('/images/Favicon.png') }}';" alt="" height="32">
                    </div>
                </div>

                <div class="py-3">
                    <h5 class="fs-14 mb-3"><a href="{{url('view-project/'.$project->id)}}" class="text-body">{{$project->name}}</a></h5>
                    <div class="row gy-3">
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Status</p>
                                <div class="badge bg-warning-subtle text-warning fs-12">{{$project->status}}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            {{-- <div>
                                <p class="text-muted mb-1">Deadline</p>
                                <h5 class="fs-14">{{date('M d,Y',strtotime($project->due_date))}}</h5>
                            </div> --}}
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <p class="text-muted mb-0 me-2">Team :</p>
                        <div class="avatar-group">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Terry Moberly">
                                <div class="avatar-xxs">
                                    <div class="avatar-title rounded-circle bg-success">
                                        T
                                    </div>
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ruby Miller">
                                <div class="avatar-xxs">
                                    <img src="assets/images/users/avatar-5.jpg" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Add Members">
                                <div class="avatar-xxs">
                                    <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                        +
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1">
                            <div>Tasks</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div><i class="ri-list-check align-bottom me-1 text-muted"></i> 17/20
                            </div>
                        </div>
                    </div>
                    <div class="progress progress-sm animated-progress">
                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="71" aria-valuemin="0" aria-valuemax="100" style="width: 71%;">
                        </div><!-- /.progress-bar -->
                    </div><!-- /.progress -->
                </div>

            </div>
            <!-- end card body -->
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
                <option>Pending</option>
                <option>In Progress</option>
                <option>Completed</option>
              </select>
            </div>
            <div class="mb-3">
              <label>Project Icon</label>
              <input type="file" class="form-control" name='icon' id="projectImage" accept="image/*" required>
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
