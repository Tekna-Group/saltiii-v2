@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- dropzone css -->
  <!-- FilePond styles -->
  <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

  <!-- Optional FilePond plugins -->
  <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.min.js"></script>
  <style>
    .filepond--item {
        width: calc(50% - 0.5em);
    }
    .file-upload-container {
      max-width: 500px;
      margin: auto;
      padding: 20px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-xxl-12 mb-2">
       
    </div>
    <div class="col-xxl-3">
       
        <div class="card">
            <div class="card-header">
                <a href="{{url('view-project/'.$task->project_id)}}" class="btn btn-primary  p-2">
                    <i class="ri-arrow-left-line fs-4"></i> Back
                  </a>
            </div>
            <!--end card-header-->
            <div class="card-body text-center">
                
               
                
                <div class="mb-2">
                    <lord-icon src="https://cdn.lordicon.com/kbtmbyzy.json" trigger="loop" colors="primary:#405189,secondary:#02a8b5" style="width:90px;height:90px"></lord-icon>
                </div>
                <h3 class="mb-1">{{$task->activities->sum('hours')}} hrs</h3>
                <h6 class="card-title mb-3 flex-grow-1 text-start text-center">
                    Total Hours</h6>
                <div class="hstack gap-2 justify-content-center">
                   
                </div>
            </div>
        </div>
        <!--end card-->
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-4">
                  <form action="{{ url('/tasks/update-board/' . $task->id) }}" method="POST">
                    @csrf
                    
                    <select id="project_board_id" class="form-select" name="project_board_id" onchange="this.form.submit()">
                        <option value="">Select Task Board</option>
                        @foreach($boards->sortBy('position') as $board)
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
                                <td class="d-flex justify-content-between align-items-center">
                                    <span>{{ $task->project->name }}</span>
                                    <!-- Button trigger modal -->
                                    @if($task->completed == 0)
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
                                        Transfer
                                    </button>
                                    @endif
                                </td>
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
                    <h6 class="mb-3 fw-semibold text-uppercase">Summary <button type="button"  data-bs-toggle="modal" data-bs-target="#editTaskModal" class="btn btn-sm btn-outline-primary"  >
                    <i class="ri-edit-line"></i> Edit
                </button></h6>
                    <p>{!!$task->description!!}</p>
                    
                </div>
            </div>
        </div>
        <!--end card-->
        <div class="card">
            <div class="card-header">
                <div>
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item active">
                            <a class="nav-link" data-bs-toggle="tab" href="#home-1" role="tab">
                                Comments ({{$task->comments->count()}})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#messages-1" role="tab">
                                Attachments ({{$task->attachments->count()}})
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link " data-bs-toggle="tab" href="#profile-1" role="tab" aria-selected="true">
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
                                    <p > {!! preg_replace_callback(
                                        '/@([A-Za-z0-9_]+(?:\s[A-Za-z0-9_]+)*)\b/',
                                        function($matches) {
                                            $username = trim($matches[1]);
                                    
                                            $user = \App\User::where('name', $username)->first();
                                    
                                            if ($user) {
                                                return '<a href="#"  class="text-primary">@'.$username.'</a>';
                                            }
                                    
                                            return '@'.$username;
                                        },
                                        ($comment->comment)
                                    ) !!}</p>
                                    
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <form method='POST' action='{{url('task-comment/'.$task->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label for="exampleFormControlTextarea1" class="form-label">Leave a Comments</label>
                                    <textarea class="form-control bg-light border-light" id="exampleFormControlTextarea1"  name='comment' rows="3" placeholder="Type your comment and use @ to tag users" required></textarea>
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
                    <div class="tab-pane " id="profile-1" role="tabpanel">
                        <h6 class="card-title mb-4 pb-2">Time Entries <button class="btn btn-success btn-sm" href="#addActivity" data-bs-toggle="modal"><i class="ri-time-line align-bottom me-1"></i> Add Activity</button></h6>
                        <div  class="table-responsive table-card">
                              <table class="table table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">User</th>
                                        </tr>
                                    </thead>
                                </table>
                                    <div style="max-height: 260px; overflow-y: auto;">
                                    <table  class="table align-middle mb-0">
                                        
                                        <tbody>
                                            @foreach($task->activities->sortByDesc('date') as $activity)
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
                                                <td>{{$activity->activity}} 
                                                    @if(auth()->user()->id == $activity->user_id)
                                                    <form action="{{ url('activity/destroy', $activity->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this activity?')" class="btn btn-sm btn-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
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
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel-{{ $task->id }}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="transferModalLabel-{{ $task->id }}">Transfer Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ url('tasks/transfer', $task->id) }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="project_id-{{ $task->id }}" class="form-label">Select New Project</label>
              <select name="project_id" id="project_id-{{ $task->id }}" class="form-select select2" required>
                @foreach($projects as $project)
                  <option value="{{ $project->id }}" {{ $project->id == $task->project_id ? 'selected' : '' }}>
                    {{ $project->name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Confirm Transfer</button>
          </div>
        </form>
      </div>
    </div>
</div>
@include('tasks.change_member')
@include('tasks.add_activity')
@include('tasks.edit_task')

@endsection

@section('js')

<!-- FilePond library -->
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

<!-- Optional plugins -->
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

<script>
  // Register FilePond plugins
  FilePond.registerPlugin(
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview
  );

  // Turn all file input elements into FilePond
  FilePond.create(document.querySelector('.filepond'), {
    acceptedFileTypes: ['image/*', 'application/pdf'],
    allowMultiple: true,
    maxFiles: 1,
     // Keep the original input so form can submit normally
     storeAsFile: true,
    labelIdle: 'Drag & Drop your files or <span class="filepond--label-action">Browse</span>'
  });
</script>


{{-- <script src="{{asset('inside_css/assets/js/pages/form-file-upload.init.js')}}"></script> --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
    
    // Initialize Select2 inside modals
    $('.modal').on('shown.bs.modal', function () {
    let $select = $(this).find('.select2');

    $select.select2({
        dropdownParent: $(this),
        templateResult: function (data) {
            if (!data.id) return data.text; // placeholder

            // Get current selected values
            let selectedValues = $select.val() || [];

            // Hide from list if already selected
            if (selectedValues.includes(data.id)) {
                return null;
            }

            return data.text;
        }
    }).on('change', function () {
        // Force Select2 to re-render results without flicker
        $select.select2('destroy').select2({
            dropdownParent: $(this).closest('.modal'),
            templateResult: function (data) {
                if (!data.id) return data.text;
                let selectedValues = $select.val() || [];
                if (selectedValues.includes(data.id)) {
                    return null;
                }
                return data.text;
            }
        });
    });
});

});
</script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
  <script>
    $('.summernote').summernote({
      height: 250,
      toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link','picture','video']],
        ['view', ['codeview']]
      ]
    });
    // Summernote keeps content in the textarea, so no extra sync needed.
  </script>

<script>
    var tribute = new Tribute({
        values: function (text, cb) {
            fetch("{{ url('/users/search') }}?q=" + text)
                .then(res => res.json())
                .then(users => {
                    cb(users.map(user => {
                        return { key: user.name, value: user.id };
                    }));
                });
        },
        selectTemplate: function (item) {
            return '@' + item.original.key; // Insert @username into textarea
        }
    });

    tribute.attach(document.getElementById('exampleFormControlTextarea1'));
</script>
@endsection