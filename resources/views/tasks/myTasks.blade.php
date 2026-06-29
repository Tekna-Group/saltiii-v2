@extends('layouts.header')
@section('css')
<style>
     .offcanvas.offcanvas-end {
      width: 30% !important;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

  <!-- Optional FilePond plugins -->
  <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet"/>
@endsection
@section('content')
<div class="row g-3">
    @php
        $sections = [
            'Delayed' => ['tasks' => $tasks->where('due_date', '<', now()), 'color' => 'danger'],
            'Due Today' => ['tasks' => $tasks->where('due_date', now()->format('Y-m-d')), 'color' => 'warning'],
            'Not Yet Delayed' => ['tasks' => $tasks->where('due_date', '>', now()), 'color' => 'success'],
        ];
        $key = 0;
    @endphp
    
    @foreach($sections as $sectionTitle => $sectionData)
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 fw-bold text-{{$sectionData['color']}}">
                    {{$sectionTitle}} 
                    <span class="badge bg-{{$sectionData['color']}}">{{ $sectionData['tasks']->count() }}</span>
                </h4>
                <div class="flex-shrink-0 ms-3">
                    <input type="text" 
                           class="form-control form-control-sm" 
                           placeholder="Search tasks..." 
                           id="searchTasks{{$key}}">
                </div>
            </div><!-- end card header -->
            <div class="tasks-scroll px-2" style="height:500px; overflow-y:auto;" id="tasksContainer{{$key}}">
            <div class="card-body">
                @forelse($sectionData['tasks'] as $task)
                    <a href="#" data-bs-toggle="offcanvas" data-bs-target="#taskDetails{{$task->id}}" id="taskCard{{$task->id}}" class="text-decoration-none task-item">
                        <div class="card shadow-sm mb-3 card-hover" 
                            style="background-color: #FFF7E6; /* pastel yellow */ 
                                    border: 1px solid #FFE3B3; 
                                    border-radius: 10px; 
                                    transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="me-2 flex-grow-1">
                                        <h6 class="fw-semibold mb-1 text-truncate" title="{{ $task->title }}">
                                            {{ strlen($task->title) > 20 ? substr($task->title, 0, 20) . '…' : $task->title }}
                                            -  
                                            <span class="badge 
                                                @if($task->priority == 'High') text-white" style="background-color:#FF8A80;" 
                                                @elseif($task->priority == 'Medium') text-dark" style="background-color:#FFD180;" 
                                                @else text-white" style="background-color:#B9F6CA;" 
                                                @endif">
                                                {{$task->priority}}
                                            </span>
                                        </h6>
                                    </div>
                                    <small class="text-muted">
                                        <i class="ri-time-line"></i> <span>{{ date('M d, Y', strtotime($task->due_date)) }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center bg-white small text-muted border-top-0">
                                <ul class="list-inline mb-0 d-flex align-items-center gap-2">
                                    <li class="list-inline-item">
                                        <i class="ri-timer-fill"></i> {{$task->activities->sum('hours')}} hrs
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="ri-question-answer-line"></i> {{$task->comments->count()}}
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="ri-attachment-2"></i> {{$task->attachments->count()}}
                                    </li>
                                </ul>
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-muted py-3">No tasks</div>
                @endforelse
            </div>
            </div>
        </div><!-- end card -->
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchTasks{{$key}}');
            const tasksContainer = document.getElementById('tasksContainer{{$key}}');
            const tasks = tasksContainer.querySelectorAll('.task-item');
        
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                tasks.forEach(task => {
                    const title = task.querySelector('h6').textContent.toLowerCase();
                    if(title.includes(filter)) {
                        task.style.display = '';
                    } else {
                        task.style.display = 'none';
                    }
                });
            });
        });
        </script>
    @php $key++; @endphp
    @endforeach
</div>
@foreach($tasks as $task)
    <div class="offcanvas offcanvas-end" tabindex="-1" id="taskDetails{{$task->id}}" data-id="taskDetails{{ $task->id }}">
        <div class="offcanvas-header d-flex justify-content-between align-items-center">
  
            <h5 class="offcanvas-title editable-title mb-0" 
                id="taskTitle{{$task->id}}" 
                ondblclick="makeTitleEditable({{$task->id}})">
                {{$task->title}}
            </h5>

            <!-- Right Section: View Button + Close Button -->
            <div class="d-flex align-items-center gap-2">
                <!-- View Button -->
                <a href='{{ url("/view-project/view-task/".$task->id) }}' 
                   class="btn btn-outline-primary btn-sm btn-icon waves-effect waves-light material-shadow-none"
                   title="View Task">
                   <i class="ri-fullscreen-line"></i>
                </a>
            
                <!-- Delete Button -->
                <button type="button" 
                        class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-light material-shadow-none"
                        title="Delete Task"
                        onclick="deleteTask({{ $task->id }})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            
                <!-- Close Button -->
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        </div>
        <div class="offcanvas-body">
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">
                                <a href="{{ url('view-project/'.$task->project_id) }}" ><h4 class="mb-3 fw-bold text-uppercase">{{$task->project->name}}</h4></a>
                                <h6 class="mb-3 fw-semibold text-uppercase">Description</h6>
                                <p>{{$task->description}}</p>

                    

                                <div class="pt-3 border-top border-top-dashed mt-4">
                                    <div class="row gy-3">

                                        <div class="col-lg-6 col-sm-6">
                                            <div>
                                                <p class="mb-2 text-uppercase fw-medium">Create Date :</p>
                                                <h5 class="fs-15 mb-0">{{date('M d, Y',strtotime($task->created_at))}}</h5>
                                            </div>
                                        </div>
                                       <div class="col-lg-6 col-sm-6">
                                            <div>
                                                <p class="mb-2 text-uppercase fw-medium">Due Date :</p>
                                                <button class="btn btn-outline-primary btn-sm due-date" 
                                                    data-id="{{ $task->id }}" 
                                                    onclick="makeDueDateEditable(this)">
                                                {{ date('M d, Y', strtotime($task->due_date)) }}
                                                </button>
                                                <span class="due-date-raw" data-id="{{ $task->id }}" style="display:none;">
                                                    {{ date('Y-m-d', strtotime($task->due_date)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div>
                                                <p class="mb-2 text-uppercase fw-medium">Priority :</p>
                                                <div class="badge ">
                                                    
                                                   <select class="form-control" 
                                                        id="priority-field-{{$task->id}}" 
                                                        name="priority" 
                                                        data-id="{{$task->id}}" 
                                                        onchange="updatePriority(this)" 
                                                        required>
                                                    <option value="High" @if($task->priority == "High") selected @endif>High</option>
                                                    <option value="Medium" @if($task->priority == "Medium") selected @endif>Medium</option>
                                                    <option value="Low" @if($task->priority == "Low") selected @endif>Low</option>
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div>
                                                <p class="mb-2 text-uppercase fw-medium">Status :</p>
                                                <div class="badge fs-12"> <select id="project_board_id_{{$task->id}}" 
                                                    class="form-select" 
                                                    data-id="{{$task->id}}" 
                                                    onchange="updateTaskBoard(this)">
                                                @foreach($boards->where('project_id',$task->project_id) as $board)
                                                    <option value="{{ $board->id }}" 
                                                        @if($task->project_board_id == $board->id) selected @endif>
                                                        {{ $board->board }}
                                                    </option>
                                                @endforeach
                                            </select></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                              
                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                    <div class="card">
                        <div class="card-header">
                            <div>
                               <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#comments-{{$task->id}}" role="tab">
                                            Comments
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#attachments-{{$task->id}}" role="tab">
                                            Attachments({{$task->attachments->count()}})
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#time-{{$task->id}}" role="tab">
                                            Time Entries ({{$task->activities->count()}})
                                        </a>
                                    </li>
                                </ul>
                                <!--end nav-->
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="comments-{{$task->id}}" role="tabpanel">
                                    <h5 class="card-title mb-4">Comments</h5>
                                    <div id="commentsContainer{{ $task->id }}" data-simplebar style="height: 200px;" class="px-3 mx-n3 mb-2">
                                        @foreach($task->comments->sortByDesc('created_at') as $comment)
                                            <div class="d-flex mb-4">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ $comment->user->avatar }}" 
                                                        onerror="this.src='{{ url('images/Favicon.png') }}';" 
                                                        alt="" 
                                                        class="avatar-xs rounded-circle material-shadow" />
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="fs-13">
                                                        <a href="#">{{ $comment->user->name }}</a>
                                                        <small class="text-muted">{{ date('d M, Y - h:i A', strtotime($comment->created_at)) }}</small>
                                                    </h5>
                                                    <p class="text-muted">{!! $comment->comment !!}</p>
                                                    @if($comment->file_path)
                                                        <a href="{{ $comment->file_path }}" class="btn btn-sm btn-success mt-2" target="_blank">
                                                            View Attachment
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <form id="taskCommentForm{{$task->id}}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row g-3 align-items-end">
                                            
                                            <!-- Comment Textarea -->
                                            <div class="col-lg-12 mt-5">
                                                <label for="commentText{{$task->id}}" class="form-label">Leave a Comment</label>
                                                <textarea class="form-control bg-light border-light" 
                                                        id="commentText{{$task->id}}" 
                                                        name="comment" 
                                                        rows="4" 
                                                        placeholder="Enter comments"
                                                        required></textarea>
                                            </div>

                                            <!-- File Attachment -->
                                            <div class="col-lg-12">
                                                <input type="file" 
                                                    class="form-control" 
                                                    {{-- id="proof{{$task->id}}"  --}}
                                                    name="proof" 
                                                    data-max-file-size="3MB" 
                                                    data-max-files="1" />
                                            </div>

                                            <!-- Post Button -->
                                            <div class="col-lg-12 text-end mt-4">
                                                <button type="submit" 
                                                        id="postBtn{{$task->id}}" 
                                                        class="btn btn-success">
                                                    Post Comment
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    @include('tasks.saveComment')
                                </div>

                                <!--end tab-pane-->
                                <div class="tab-pane" id="attachments-{{$task->id}}" role="tabpanel">
                                      
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless align-middle mb-0">
                                            <thead class="table-light text-muted">
                                                <tr>
                                                    <th scope="col">File Name</th>
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
                                <div class="tab-pane" id="time-{{$task->id}}" role="tabpanel">
                                    <h6 class="card-title mb-4 pb-2">
                                        Time Entries 
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addActivity{{$task->id}}">
                                            <i class="ri-time-line align-bottom me-1"></i> Add
                                        </button>
                                    </h6>
                                
                                    <div class="table-responsive table-card">
                                        <table class="table align-middle mb-0" id="activitiesTable{{$task->id}}">
                                            <thead class="table-light text-muted">
                                                <tr>
                                                    <th>Member</th>
                                                    <th>Date</th>
                                                    <th>Hours</th>
                                                    <th>Activity</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($task->activities as $activity)
                                                <tr id="activityRow{{$activity->id}}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{asset($activity->user->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xxs">
                                                            <div class="flex-grow-1 ms-2">
                                                                {{$activity->user->name}}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{date('d M, Y',strtotime($activity->date))}}</td>
                                                    <td>{{$activity->hours}} hrs</td>
                                                    <td>{{$activity->activity}}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary editActivityBtn" 
                                                                data-id="{{ $activity->id }}" 
                                                                data-task="{{ $activity->activity }}" 
                                                                data-hours="{{ $activity->hours }}" 
                                                                data-date="{{ $activity->date }}">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                        @if(auth()->user()->id == $activity->user_id && !$activity->timekeeping_from)
                                                        <button class="btn btn-sm btn-danger deleteActivityBtn" data-id="{{$activity->id}}">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                              
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--edn tab-pane-->

                            </div>
                            <!--end tab-content-->
                        </div>
                    </div>
                    <!-- end card -->
                </div>
    
            </div>
        </div>
    </div>
    @include('tasks.addActivity')
  
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addActivityForm' + {{ $task->id }});
        
        form.addEventListener('submit', async function (e) {
            e.preventDefault(); // ❌ Stop page refresh
            console.log('Submit intercepted for AJAX');
    
            const formData = new FormData(form);
    
            try {
                const response = await fetch(`{{ url('task-activity/api') }}/{{ $task->id }}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
    
                const data = await response.json();
                console.log('AJAX response:', data);
    
                if (data.success) {
                    // Dynamically add row
                    const tableBody = document.querySelector('#activitiesTable' + {{ $task->id }} + ' tbody');
                    const newRow = document.createElement('tr');
                    newRow.id = 'activityRow' + data.activity.id;
                    newRow.innerHTML = `
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${data.activity.user_avatar ? `{{ asset('') }}${data.activity.user_avatar}` : `{{ asset('images/Favicon.png') }}`}" 
                                    onerror="this.src='{{ asset('images/Favicon.png') }}';" 
                                    alt="User Avatar" 
                                    class="rounded-circle avatar-xxs rounded-circle material-shadow" />
                                <div class="flex-grow-1 ms-2">${data.activity.user_name}</div>
                            </div>
                        </td>
                        <td>${data.activity.date}</td>
                        <td>${data.activity.hours} hrs</td>
                        <td>${data.activity.task}</td>
                        <td>
                            
                            <button class="btn btn-sm btn-primary editActivityBtn" 
                                data-id="${data.activity.id}" 
                                data-task="${data.activity.task}" 
                                data-hours="${data.activity.hours}" 
                                data-date="${data.activity.date_old}">
                            <i class="ri-edit-line"></i>
                        </button>
                            <button class="btn btn-sm btn-danger deleteActivityBtn" data-id="${data.activity.id}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    `;
                    tableBody.prepend(newRow);
    
                    // Reset form & close modal
                    form.reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addActivity' + {{ $task->id }}));
                    modal.hide();
    
                    Toastify({
                        text: "Activity added successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#4CAF50"
                    }).showToast();
                }
    
            } catch (err) {
                console.error(err);
                Toastify({
                    text: "Error adding activity!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#FF0000"
                }).showToast();
            }
        });
    });
    
    </script>
    
@endforeach
@include('tasks.editActivity')
@endsection
@section('js')

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

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
<script>
      // Generic toast helper (top-right)
  function showToast(message, type = 'info', duration = 1000) {
    const bg = {
      success: "linear-gradient(to right, #28a745, #2ecc71)",
      error:   "linear-gradient(to right, #dc3545, #e74c3c)",
      info:    "linear-gradient(to right, #007bff, #36a2ff)"
    }[type] || "linear-gradient(to right, #333, #555)";

    Toastify({
      text: message,
      duration: duration,
      close: true,
      gravity: "top",          // top or bottom
      position: "right",      // left, center or right
      stopOnFocus: true,      // stop timeout on hover/focus
      style: {
        background: bg,
        color: "#fff",
        boxShadow: "0 6px 18px rgba(0,0,0,0.15)"
      }
    }).showToast();
  }

  // Convenience wrappers
  function showToastSuccess(msg, duration) { showToast(msg, 'success', duration); }
  function showToastError(msg, duration)   { showToast(msg, 'error', duration); }
  function showToastInfo(msg, duration)    { showToast(msg, 'info', duration); }

  function makeTitleEditable(id) {
    const titleElement = document.getElementById('taskTitle'+id);

    // Prevent multiple input fields
    if (titleElement.querySelector('input')) return;

    // Get current title text
    const currentTitle = titleElement.textContent.trim();

    // Replace with input field
   titleElement.innerHTML = `
    <input type="text" class="editable-input form-control" id="titleInput${id}" value="${currentTitle}" autofocus />
  `;

    const input = document.getElementById('titleInput'+id);

    // Save on Enter key
   input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      saveTitle(id);
        // showToastSuccess('Title updated');
    }
  });

    // Save on blur (click outside)
     input.addEventListener('blur', function() {
        saveTitle(id);
        //   showToastSuccess('Title updated');
    });
  }

  async function saveTitle(id) {
    const titleElement = document.getElementById('taskTitle' + id);
    const input = document.getElementById('titleInput' + id);

    if (!input) return;

    const newTitle = input.value.trim() || 'Untitled Task';

    try {
        const formData = new FormData();
        formData.append('title', newTitle);

        const response = await fetch(`{{ url('/tasks') }}/${id}/update-title`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Update title in the UI
            titleElement.textContent = newTitle;

            // ✅ Show success toast
            Toastify({
                text: "Title updated successfully!",
                duration: 3000,             // 3 seconds
                gravity: "top",             // top or bottom
                position: "right",          // left, center, or right
                backgroundColor: "#4CAF50", // green for success
                stopOnFocus: true           // prevent dismiss on hover
            }).showToast();
        } else {
            // ❌ Show error toast
            Toastify({
                text: "Failed to update title. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#FF0000",
                stopOnFocus: true
            }).showToast();
        }
    } catch (error) {
        console.error('[ERROR] AJAX request failed:', error);

        // ❌ Show error toast
        Toastify({
            text: "Something went wrong. Please try again later.",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#FF0000",
            stopOnFocus: true
        }).showToast();
    }
    }
    function formatDateOnlyForDisplay(dateValue, options = { month: 'short', day: '2-digit', year: 'numeric' }) {
        const parts = (dateValue || '').split('-');

        if (parts.length !== 3) {
            return dateValue || '';
        }

        const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
        return date.toLocaleDateString('en-US', options);
    }

    function makeDueDateEditable(element) 
    {
    const id = element.getAttribute('data-id');

    // Prevent multiple inputs
    if (element.querySelector('input')) return;

    // Get current date
    const rawSpan = document.querySelector(`.due-date-raw[data-id="${id}"]`);
    let currentDate = rawSpan ? rawSpan.textContent.trim() : element.textContent.trim();

    // Format YYYY-MM-DD for input
    const parts = currentDate.split('-'); 
    const formattedDate = (parts.length === 3) 
        ? `${parts[0]}-${parts[1]}-${parts[2]}`
        : currentDate;

    // Replace content with input
    element.innerHTML = `<input type="date" class="form-control form-control-sm" data-id="${id}" value="${formattedDate}" autofocus />`;

    const input = element.querySelector('input');

    // Save on Enter
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') saveDueDate(input);
    });

    // Save on blur
    input.addEventListener('blur', function() {
        saveDueDate(input);
    });
    }

    async function saveDueDate(input) {
        const id = input.getAttribute('data-id');
        const newDate = input.value;

        // Find elements to update
        const dueDateDiv = document.querySelector(`.due-date[data-id="${id}"]`);
        const rawSpan = document.querySelector(`.due-date-raw[data-id="${id}"]`);

        // Format for display
        const formattedDisplay = formatDateOnlyForDisplay(newDate);

        // Update UI
        if (dueDateDiv) dueDateDiv.textContent = formattedDisplay;
        if (rawSpan) rawSpan.textContent = newDate;

        // Send AJAX
        try {
            const response = await fetch(`{{ url('/tasks') }}/${id}/update-due-date`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ due_date: newDate })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const savedDate = data.due_date || newDate;
                if (dueDateDiv) dueDateDiv.textContent = formatDateOnlyForDisplay(savedDate);
                if (rawSpan) rawSpan.textContent = savedDate;

                Toastify({
                    text: "Due date updated successfully!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4CAF50",
                    stopOnFocus: true
                }).showToast();
            } else {
                Toastify({
                    text: data.message || "Failed to update due date",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#FF0000",
                    stopOnFocus: true
                }).showToast();
            }
        } catch (error) {
            console.error(error);
            Toastify({
                text: "Network error while updating due date",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#FF0000",
                stopOnFocus: true
            }).showToast();
        }
    }
</script>
<script>
    async function updatePriority(selectElement) {
    const taskId = selectElement.getAttribute('data-id');
    const newPriority = selectElement.value;

    if (!newPriority) return;

    try {
        const response = await fetch(`{{ url('/tasks/update-priority') }}/${taskId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ priority: newPriority })
        });

        const data = await response.json();

        if (data.success) {
            // Update the badge text and style dynamically
            const badge = document.getElementById('task_status' + taskId);
            badge.textContent = newPriority;

            // Reset existing classes first
            badge.className = 'badge';

            // Apply new style based on priority
            if (newPriority === 'High') {
                badge.classList.add('bg-danger-subtle', 'text-danger');
            } else if (newPriority === 'Medium') {
                badge.classList.add('bg-warning-subtle', 'text-warning');
            } else {
                badge.classList.add('bg-success-subtle', 'text-success');
            }

            // Show success toast
            Toastify({
                text: "Priority updated successfully!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#28a745",
                stopOnFocus: true
            }).showToast();
        } else {
            Toastify({
                text: "Failed to update priority.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                stopOnFocus: true
            }).showToast();
        }
    } catch (error) {
        console.error('Error updating priority:', error);

        Toastify({
            text: "An error occurred. Please try again.",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
            stopOnFocus: true
        }).showToast();
    }
    }
</script>
<script>
    async function updateTaskBoard(selectElement) {
        const taskId = selectElement.getAttribute('data-id');
        const boardId = selectElement.value;

        if (!boardId) return; // Skip if no board selected

        try {
            const response = await fetch(`{{ url('/tasks/update-board/api') }}/${taskId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ project_board_id: boardId })
            });

            const data = await response.json();

            if (data.success) {
                // Update the badge text dynamically
                // const badge = document.getElementById('task_board_status' + taskId);
                // badge.textContent = data.board_name || 'Updated';

                // Show success toast
             
                Toastify({
                    text: "Status updated successfully!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745",
                    stopOnFocus: true
                }).showToast();
            } else {
                Toastify({
                    text: "Failed to update Status.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    stopOnFocus: true
                }).showToast();
            }
        } catch (error) {
            console.log('Error updating task board:', error);

            Toastify({
                text: "An error occurred. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                stopOnFocus: true
            }).showToast();
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function deleteTask(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This task will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform AJAX delete
                fetch(`{{ url('/tasks/${id}/archive') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const offcanvasEl = document.getElementById('taskDetails' + id);
                        if (offcanvasEl) {
                            // Get existing instance or create one
                            let bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                            if (!bsOffcanvas) {
                                bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
                            }
                            bsOffcanvas.hide();
                        }
                        Toastify({
                            text: 'Task deleted successfully!',
                            duration: 2000,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#4CAF50'
                        }).showToast();
    
                        // Optional: remove task card from UI
                        const taskCard = document.getElementById('taskCard' + id);
                        if (taskCard) taskCard.remove();
    
                    } else {
                        Toastify({
                            text: data.message || 'Failed to delete task',
                            duration: 3000,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#FF0000'
                        }).showToast();
                    }
                })
                .catch(err => {
                    console.error(err);
                    Toastify({
                        text: 'Network error while deleting task',
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#FF0000'
                    }).showToast();
                });
            }
        });
    }
    </script>

<script>
        document.addEventListener('DOMContentLoaded', function () {

// Delegate click for dynamically added rows
document.addEventListener('click', async function(e) {
    if (e.target.closest('.deleteActivityBtn')) {
        const btn = e.target.closest('.deleteActivityBtn');
        const activityId = btn.getAttribute('data-id');

        // SweetAlert2 confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('activity/destroy') }}/${activityId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    console.log('Delete response:', data);

                    if (data.success) {
                        // Remove row
                        const row = document.getElementById('activityRow' + activityId);
                        if (row) row.remove();

                        Swal.fire(
                            'Deleted!',
                            'Activity has been deleted.',
                            'success'
                        );
                    } else {
                        Swal.fire('Error!', 'Failed to delete activity.', 'error');
                    }

                } catch (err) {
                    console.error(err);
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            }
        });
    }
});

});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const offcanvasEl = document.getElementById('editActivityOffcanvas');
    const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
    const form = document.getElementById('editActivityForm');

    document.addEventListener('click', function (e) {
        if (e.target.closest('.editActivityBtn')) {
            const btn = e.target.closest('.editActivityBtn');
            const id = btn.dataset.id;
            const task = btn.dataset.task;
            const hours = btn.dataset.hours;
            const date = btn.dataset.date;

            document.getElementById('editActivityId').value = id;
            document.getElementById('editActivityTask').value = task;
            document.getElementById('editActivityHours').value = hours;
            document.getElementById('editActivityDate').value = date;

            offcanvas.show();
        }
    });

    // Handle AJAX submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const activityId = document.getElementById('editActivityId').value;
        const formData = new FormData(form);

        try {
            const response = await fetch(`{{ url('task-activity/api') }}/edit/${activityId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            if (data.success) {
                // Update table row dynamically
                const row = document.getElementById('activityRow' + activityId);
                row.querySelector('td:nth-child(2)').textContent = data.activity.date;
                row.querySelector('td:nth-child(3)').textContent = data.activity.hours + ' hrs';
                row.querySelector('td:nth-child(4)').textContent = data.activity.task;

                offcanvas.hide();

                Swal.fire('Success', 'Activity updated successfully!', 'success');
            } else {
                Swal.fire('Error', 'Failed to update activity', 'error');
            }

        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Something went wrong', 'error');
        }
    });
});
</script>

@endsection
