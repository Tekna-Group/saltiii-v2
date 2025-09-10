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
    @endphp

    @foreach($sections as $sectionTitle => $sectionData)
    <div class="col-md-4">
        <!-- Section Header -->
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-{{$sectionData['color']}}">
                {{$sectionTitle}} <span class="badge bg-{{$sectionData['color']}}">{{ $sectionData['tasks']->count() }}</span>
            </h5>
        </div>

        <!-- Scrollable Tasks List -->
        <div class="tasks-scroll px-2" style="height:500px; overflow-y:auto;">
            @forelse($sectionData['tasks'] as $task)
            <a href="#" data-bs-toggle="offcanvas" data-bs-target="#taskDetails{{$task->id}}" class="text-decoration-none">
                <div class="card shadow-sm mb-3 card-hover">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="me-2">
                                <h6 class="fw-semibold mb-1 text-truncate" title="{{$task->title}}">
                                    {{$task->title}} -  <span class="badge 
                                    @if($task->priority == 'High') bg-danger text-white 
                                    @elseif($task->priority == 'Medium') bg-warning text-dark 
                                    @else bg-success text-white 
                                    @endif">
                                    {{$task->priority}}
                                </span>
                                </h6>
                               
                            </div>
                            <small class="text-muted">
                                <i class="ri-time-line"></i> <span>{{ $task->due_date }}</span>
                            </small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center bg-light small text-muted">
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
    @endforeach
</div>


@foreach($tasks as $task)
    <div class="offcanvas offcanvas-end" tabindex="-1" id="taskDetails{{$task->id}}">
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
                                                <button class="btn btn-outline-primary btn-sm" id="dueDate{{$task->id}}" onclick="makeDueDateEditable({{$task->id}})">
                                                {{ date('M d, Y', strtotime($task->due_date)) }}
                                                </button>
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
                                            <div class="col-lg-12">
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
                                                    class="filepond" 
                                                    id="proof{{$task->id}}" 
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
                               <div class="tab-pane" id="time-{{$task->id}}" role="tabpanel">
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
                                        <!--end table-->
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
@endforeach
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
        showToastSuccess('Title updated');
    }
  });

    // Save on blur (click outside)
     input.addEventListener('blur', function() {
        saveTitle(id);
          showToastSuccess('Title updated');
    });
  }

  function saveTitle(id) {
    const titleElement = document.getElementById('taskTitle'+id);
    const input = document.getElementById('titleInput'+id);
    if (!input) return;

    const newTitle = input.value.trim() || 'Untitled Task';
    titleElement.textContent = newTitle;
  }
  function makeDueDateEditable(id) {
  const dateElement = document.getElementById('dueDate' + id);

  // Prevent multiple inputs
  if (dateElement.querySelector('input')) return;

  // Get current date from text content
  const currentDateText = dateElement.textContent.trim();
  const currentDate = new Date(currentDateText);
  const formattedDate = currentDate.toISOString().split('T')[0]; // YYYY-MM-DD

  // Replace with a date input field
  dateElement.innerHTML = `
    <input type="date" class="form-control form-control-sm" id="dueDateInput${id}" value="${formattedDate}" autofocus />
  `;

  const input = document.getElementById('dueDateInput' + id);

  // Save when pressing Enter
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      saveDueDate(id);
    }
  });

  // Save when clicking outside
  input.addEventListener('blur', function() {
    saveDueDate(id);
  });
}

async function saveDueDate(id) {
  const dateElement = document.getElementById('dueDate' + id);
  const spanDueDate = document.getElementById('due_date_span' + id);
  console.log(id);
  const input = document.getElementById('dueDateInput' + id);
  if (!input) return;

  const newDate = input.value;



  // Format for display (e.g., Sep 10, 2025)
  const formattedDisplayDate = new Date(newDate).toLocaleDateString('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric'
  });

  // Update UI immediately
   spanDueDate.textContent =input.value;
  dateElement.textContent = formattedDisplayDate;

  // Send to server via AJAX
  try {
    const response = await fetch("{{ url('/tasks') }}/" + id + "/update-due-date", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ due_date: newDate })
    });

    const data = await response.json();
    if (response.ok && data.success) {
      showToastSuccess('Due date updated successfully');
    } else {
      showToastError(data.message || 'Failed to update due date');
    }
  } catch (error) {
    console.error(error);
    showToastError('Network error while updating due date');
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
                    text: "Task board updated successfully!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745",
                    stopOnFocus: true
                }).showToast();
            } else {
                Toastify({
                    text: "Failed to update task board.",
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



@endsection
