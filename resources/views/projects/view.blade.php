@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .kanban-board {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      /* padding: 20px; */
    }
    .kanban-column {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      flex: 1;
      min-width: 300px;
      display: flex;
      flex-direction: column;
    }
    .kanban-header {
      padding: 15px;
      font-weight: bold;
      border-bottom: 1px solid #dee2e6;
      display: flex;
      justify-content: space-between;
      align-items: center;
  
    }
    .kanban-items::-webkit-scrollbar {
    width: 6px;
}

.kanban-items::-webkit-scrollbar-track {
    background: transparent;
}

.kanban-items::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}
    .kanban-items {
      flex-grow: 1;
      padding: 15px;
      min-height: 400px;
    }
    .kanban-card {
      background: #e9ecef;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 10px;
      cursor: grab;
    }
    .kanban-card.dragging {
      opacity: 0.5;
    }
  </style>
  
<style>
    .kanban-board-container {
        display: flex;
        overflow-x: auto;
        padding: 10px;
    }
    .kanban-board-wrapper {
        display: flex;
        gap: 15px;
        min-height: 40vh;
    }
    .kanban-column {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        min-width: 280px;
        display: flex;
        flex-direction: column;
    }
    .kanban-header {
        background: #e9ecef;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
    }
    .kanban-items {
        flex: 1;
        padding: 10px;
        /* min-height: 100px; */
            max-height: 400px; /* Adjust height based on your layout */
        overflow-y: auto;  /* Vertical scroll only when needed */
        overflow-x: hidden; /* Prevent horizontal scroll */
        padding-right: 8px; /* Space for scrollbar */
        scrollbar-width: thin; /* For Firefox */
        scrollbar-color: #ccc transparent; /* Scrollbar style */
    }
    .kanban-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: grab;
    }
    .kanban-card.dragging {
        opacity: 0.5;
    }
</style>
@endsection
@section('content')
 <div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-warning-subtle">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <div class="avatar-title bg-white rounded-circle">
                                            <img src="{{asset($project->icon)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="avatar-xs">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 @if(auth()->user()->role == "Admin") id="editable-project-name" @endif class="fw-bold" data-id="{{ $project->id }}">
                                            {{ $project->name }}
                                        </h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            {{-- <div><i class="ri-building-line align-bottom me-1"></i> Themesbrand</div> --}}
                                            {{-- <div class="vr"></div> --}}
                                            <div>Created Date : <span class="fw-medium">{{date('d M, Y',strtotime($project->created_at))}}</span></div>
                                            <div class="vr"></div>
                                            <div>Last Update : <span class="fw-medium">{{date('d M, Y',strtotime($project->updated_at))}}</span></div>
                                            <div class="vr"></div>
                                            {{-- <div class="badge rounded-pill bg-info fs-12">New</div> --}}
                                            <div class="badge rounded-pill bg-danger fs-12">High</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                                <button type="button" class="btn py-0 fs-16 favourite-btn material-shadow-none active">
                                    <i class="ri-star-fill"></i>
                                </button>
                                <button type="button" class="btn py-0 fs-16 text-body material-shadow-none">
                                    <i class="ri-share-line"></i>
                                </button>
                                <button type="button" class="btn py-0 fs-16 text-body material-shadow-none">
                                    <i class="ri-flag-line"></i>
                                </button>
                            </div>
                        </div> --}}
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tasks-overview" role="tab">
                                Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-comments" role="tab">
                                Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-activities" role="tab">
                                Activities
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tab-content text-muted">
            <div class="tab-pane fade show active" id="tasks-overview" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-lg-auto">
                                <div class="hstack gap-2">
                                    @if(auth()->user()->role == 'Admin')
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createboardModal"><i class="ri-add-line align-bottom me-1"></i> Create Board</button>
                                    @endif
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-auto">
                                <div class="search-box">
                                    <input type="text" class="form-control search" id="search-task-options" placeholder="Search for tasks....">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-auto ms-sm-auto">
                                <div class="avatar-group" id="newMembar">
                                    @foreach($project->users as $member)
                                    <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                        <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xs">
                                    </a>
                                    @endforeach
                                     @if(auth()->user()->role == 'Admin')
                                    <a href="#addmemberModal" data-bs-toggle="modal" class="avatar-group-item material-shadow">
                                        <div class="avatar-xs">
                                            <div class="avatar-title rounded-circle">
                                                +
                                            </div>
                                        </div>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end card-body-->
                </div>
                  <div class="kanban-board-container">
                      <div class="kanban-board-wrapper" id="kanbanBoard">
                          <!-- Columns will be dynamically rendered -->
                      </div>
                  </div>
                  <!-- Modals -->
                 
                  <div class="modal fade" id="statusModal" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Status</h5>
                          <button type="button" class="btn-close" data-dismiss="modal"></button>
                        </div>
                        <form id="editBoardForm" method="POST" action="{{ url('project/edit-board') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="statusId" id="statusId">
                                <div class="mb-3">
                                    <label class="form-label">Status Name</label>
                                    <input type="text" name="statusName" class="form-control" id="statusName">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                      </div>
                    </div>
                  </div>
             </div>
        </div>
    </div>
</div>

@include('projects.new-board')
@include('projects.add_member')
@include('projects.add_task')
@endsection
@section('js')
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
<script>
        function getInitials(name) {
            const words = name.trim().split(' ');
            let initials = words[0].charAt(0).toUpperCase();
            if (words.length > 1) {
                initials += words[1].charAt(0).toUpperCase();
            }
            return initials;
        }
</script>


<script>
    document.getElementById('search-task-options').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase().trim();

    // Loop through each kanban column
    document.querySelectorAll('.kanban-items').forEach(column => {
        const tasks = column.querySelectorAll('.task-card');
        let hasVisibleTasks = false;

        tasks.forEach(task => {
            const taskText = task.innerText.toLowerCase();

            if (taskText.includes(searchValue)) {
                task.style.display = ''; // Show matching task
                hasVisibleTasks = true;
            } else {
                task.style.display = 'none'; // Hide non-matching task
            }
        });

        // Optional: Show "No tasks found" message if all tasks are hidden
        let noTaskMsg = column.querySelector('.no-tasks-msg');

        if (!hasVisibleTasks) {
            if (!noTaskMsg) {
                noTaskMsg = document.createElement('div');
                noTaskMsg.className = 'no-tasks-msg text-muted text-center p-2';
                noTaskMsg.innerText = 'No matching tasks';
                column.appendChild(noTaskMsg);
            }
        } else if (noTaskMsg) {
            noTaskMsg.remove();
        }
    });
});
    let boardData = @json($boardData); // Laravel data for boards and tasks

    console.log("Loaded board data:", boardData);

    // ====== Render the whole board ======
    function renderBoard() {
        const board = document.getElementById('kanbanBoard');
        board.innerHTML = '';

        boardData.forEach(column => {
              const isAdmin = @json(auth()->user()->role === 'Admin');
            const columnDiv = document.createElement('div');
            columnDiv.className = 'kanban-column';
                 if (isAdmin) {
            columnDiv.setAttribute('draggable', 'true'); // allow column to be dragged
              }
            columnDiv.dataset.id = column.id;

            columnDiv.innerHTML = `
                <div class="kanban-header">
                    <span class="fw-bold" id="status-name-${column.id}">${column.name} <button class="btn btn-sm btn-outline-primary me-1" onclick="addTask('${column.id}')">+Add Task</button></span>
                    
                    <div>
                        <!-- Edit button (visible to all) -->
                           @if(auth()->user()->role == 'Admin')
                        <button class="btn btn-sm btn-outline-secondary me-1" 
                                onclick="editStatus('${column.id}')" 
                                data-bs-toggle="tooltip" 
                                title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <!-- Delete button (only for admins) -->
                     
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteStatus('${column.id}')" 
                                    data-bs-toggle="tooltip" 
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="kanban-items" ondragover="allowDrop(event)" ondrop="dropTask(event, '${column.id}')">
                    ${column.tasks.map(task => renderTask(task)).join('')}
                </div>

                <div class="p-2">
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="addTask('${column.id}')">+ Add Task</button>
                </div>
            `;

            board.appendChild(columnDiv);
        });

        enableColumnDrag(); // enable dragging for columns
    }

    // ====== Render a single task card ======
    function renderTask(task) {
    const today = new Date().toISOString().split('T')[0];
        console.log(task.users);
    return `
        <div id="task-${task.id}" class="kanban-card tasks-box task-card"
            draggable="true" ondragstart="dragTask(event)">
            <div class="card-body">
                <div class="d-flex mb-2">
                    <div class="flex-grow-1">
                        <h6 class="fs-15 mb-0 text-truncate task-title">
                            <span onclick="window.location.href='view-task/${task.id}'" class="d-block task-link">
                                <div class="d-flex justify-content-between align-items-start ${task.users && task.users.some(user => user.id === {{ auth()->id() }}) ? 'text-warning' : ''}"">
                                    <div>
                                        ${task.completed == 1 ? '<i class="text-success ri-checkbox-circle-fill align-middle me-1"></i>' : ''}
                                        ${(task.due_date && task.due_date < today && task.completed == 0)
                                            ? '<i class="text-danger ri-error-warning-fill align-middle me-1"></i>' : ''}
                                        ${task.name.length > 20 ? task.name.substring(0, 15) + "..." : task.name}
                                    </div>
                                    <div class="text-muted ms-2 ${task.users && task.users.some(user => user.id === {{ auth()->id() }}) ? 'text-warning' : ''}">
                                        <small>#${task.id}</small>
                                    </div>
                                </div>
                            </span>
                        </h6>
                    </div>
                </div>
            </div>

            <div class="card-footer border-top-dashed">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <span class="text-muted">
                            <i class="ri-time-line align-bottom"></i> ${task.due_date || 'No Due Date'}
                        </span>
                    </div>
                    <div class="flex-shrink-0">
                        <ul class="link-inline mb-0">
                            <li class="list-inline-item">
                                <i class="ri-timer-fill"></i> ${parseFloat(Number(task.hours).toFixed(2))}
                            </li>
                            <li class="list-inline-item">
                                <i class="ri-question-answer-line align-bottom"></i> ${task.comments}
                            </li>
                            <li class="list-inline-item">
                                <i class="ri-attachment-2 align-bottom"></i> ${task.attachments}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            ${task.completed == 1 ? `
                <button class="btn btn-sm btn-outline-secondary archive-task-btn" onclick="archiveTask(${task.id})">
                    <i class="ri-archive-2-line"></i> Archive
                </button>
            ` : ''}
        </div>
    `;
}

    // ====== Enable column dragging ======
    function enableColumnDrag() {
        const columns = document.querySelectorAll('.kanban-column');

        columns.forEach(col => {
            col.addEventListener('dragstart', dragColumnStart);
            col.addEventListener('dragover', allowDrop);
            col.addEventListener('drop', dropColumn);
        });
    }

    // Track the column being dragged
    let draggedColumnId = null;

    function dragColumnStart(event) {
        draggedColumnId = event.target.dataset.id;
        event.dataTransfer.effectAllowed = 'move';
    }

    function dropColumn(event) {
        event.preventDefault();

        const targetColumnId = event.target.closest('.kanban-column').dataset.id;
        if (!draggedColumnId || draggedColumnId === targetColumnId) return;

        // Reorder columns in local data
        const draggedIndex = boardData.findIndex(col => col.id == draggedColumnId);
        const targetIndex = boardData.findIndex(col => col.id == targetColumnId);

        const [movedColumn] = boardData.splice(draggedIndex, 1);
        boardData.splice(targetIndex, 0, movedColumn);

        // Send order to backend
        $.ajax({
            url: "{{ url('/update-column-order') }}",
            method: 'POST',
            data: {
                order: boardData.map(col => col.id),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Toastify({
                  text: "Column order updated",
                  duration: 3000,
                  close: true,
                  gravity: "top", // top or bottom
                  position: "right", // left, center or right
                  backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                  stopOnFocus: true,
              }).showToast();
            },
            error: function (xhr) {
                Toastify({
                    text: "Error updating. Please try again.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    stopOnFocus: true,
                }).showToast();
            }
        });

        renderBoard();
    }

    // ====== Task Dragging ======
    function dragTask(ev) {
        ev.dataTransfer.setData('text/plain', ev.target.id);
    }

    function allowDrop(ev) {
        ev.preventDefault();
    }

    function dropTask(ev, columnId) {
        ev.preventDefault();
        const draggedTaskId = ev.dataTransfer.getData('text/plain').replace('task-', '');

        if (!draggedTaskId || !columnId) return;

        // Move task locally
        let movedTask = null;
        boardData.forEach(col => {
            const index = col.tasks.findIndex(t => t.id == draggedTaskId);
            if (index > -1) {
                movedTask = col.tasks.splice(index, 1)[0];
            }
        });

        if (movedTask) {
            const targetCol = boardData.find(col => col.id == columnId);
            if (targetCol) targetCol.tasks.push(movedTask);
        }

        // Update on server
        $.ajax({
            url: "{{ url('/update-task-column') }}",
            method: 'POST',
            data: {
                task_id: draggedTaskId,
                project_board_id: columnId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Toastify({
                  text: "Task updated successfully!",
                  duration: 3000,
                  close: true,
                  gravity: "top", // top or bottom
                  position: "right", // left, center or right
                  backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                  stopOnFocus: true,
              }).showToast();
            },
            error: function (xhr) {
                Toastify({
                    text: "Error updating task. Please try again.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    stopOnFocus: true,
                }).showToast();
            }
        });

        renderBoard();
    }

    // ====== Status CRUD ======
    function addStatus() {
        document.getElementById('statusId').value = '';
        document.getElementById('statusName').value = '';
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }

    function saveStatus() {
        const id = document.getElementById('statusId').value;
        const name = document.getElementById('statusName').value;
        if (!name) return alert('Please enter status name');

        const existing = boardData.find(c => c.id == id);
        if (existing) {
            existing.name = name;
        } else {
            boardData.push({ id: Date.now().toString(), name, tasks: [] });
        }
        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
        renderBoard();
    }

    function editStatus(id) {
        const column = boardData.find(c => c.id == id);
        document.getElementById('statusId').value = column.id;
        document.getElementById('statusName').value = column.name;
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }

   function deleteStatus(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the status and its tasks!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: "{{ url('/statuses') }}/" + id, // Laravel style URL
                method: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                },
                success: function(response) {
                    if (response.success) {
                        // Remove column from local boardData
                        boardData = boardData.filter(c => c.id != id);
                        renderBoard();

                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The status has been deleted.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to delete the status.',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the status.',
                        icon: 'error'
                    });
                    console.error(xhr.responseText);
                }
            });

        }
    });
  }

      // ====== Tasks ======
      function addTask(columnId) {
          document.getElementById('taskColumn').value = columnId;
          new bootstrap.Modal(document.getElementById('creatertaskModal')).show();
      }
  function archiveTask(taskId) {
    const archiveUrl = `{{ url('tasks') }}/${taskId}/archive`;
      // Show SweetAlert confirmation dialog
      Swal.fire({
          title: "Are you sure?",
          text: "This task will be archived and moved out of the active board.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, archive it!"
      }).then((result) => {
          if (result.isConfirmed) {
              // Send AJAX request using fetch
              fetch(archiveUrl, {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                  }
              })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Remove task from UI
                      document.getElementById(`task-${data.task_id}`).remove();

                      // Show success Toastify
                      Toastify({
                          text: "Task archived successfully!",
                          duration: 3000,
                          gravity: "top",
                          position: "right",
                          backgroundColor: "#28a745",
                          stopOnFocus: true
                      }).showToast();
                  } else {
                      // Show error Toastify
                      Toastify({
                          text: "Failed to archive task.",
                          duration: 3000,
                          gravity: "top",
                          position: "right",
                          backgroundColor: "#dc3545",
                          stopOnFocus: true
                      }).showToast();
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  Toastify({
                      text: "An error occurred. Please try again later.",
                      duration: 3000,
                      gravity: "top",
                      position: "right",
                      backgroundColor: "#dc3545",
                      stopOnFocus: true
                  }).showToast();
              });
          }
      });
  }


    // Initial render
    renderBoard();
</script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
  $(document).ready(function() {
      let originalText = '';

      // Use event delegation so double-click works even after replacement
      $(document).on('dblclick', '#editable-project-name', function() {
          let $this = $(this);
          let currentText = $this.text().trim();
          originalText = currentText; // Store original text

          // Replace the h4 with an input
          let input = $('<input>', {
              type: 'text',
              class: 'form-control form-control-sm',
              value: currentText,
              id: 'project-name-input'
          });

          $this.replaceWith(input);
          input.focus().select();

          // Save on Enter key
          input.on('keypress', function(e) {
              if (e.which === 13) { // Enter key
                  saveProjectName(input, $this.data('id'));
              }
          });

          // Save on blur (optional)
          input.on('blur', function() {
              saveProjectName(input, $this.data('id'));
          });
      });

      function saveProjectName(input, projectId) {
          let newName = input.val().trim();

          if (newName === '') {
              revertToText(originalText, projectId);
              return;
          }

          $.ajax({
              url: "{{ url('/project/edit/') }}/" + projectId,
              method: 'POST', // using POST instead of PUT
              data: {
                  _token: '{{ csrf_token() }}',
                  name: newName
              },
              success: function(response) {
                  revertToText(response.name, projectId);

                  Toastify({
                      text: "Project name updated successfully!",
                      duration: 3000,
                      gravity: "top",
                      position: "right",
                      backgroundColor: "#4CAF50",
                      close: true
                  }).showToast();
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
                  revertToText(originalText, projectId);

                  let message = "Error updating project name. Please try again.";

                  if (xhr.responseJSON && xhr.responseJSON.errors) {
                      message = Object.values(xhr.responseJSON.errors)[0][0];
                  }

                  Toastify({
                      text: message,
                      duration: 4000,
                      gravity: "top",
                      position: "right",
                      backgroundColor: "#F44336",
                      close: true
                  }).showToast();
              }
          });
      }

      function revertToText(name, projectId) {
          let newH4 = $('<h4>', {
              id: 'editable-project-name',
              class: 'fw-bold',
              'data-id': projectId,
              text: name
          });

          $('#project-name-input').replaceWith(newH4);
      }
  });
</script>
<script>
$(document).ready(function() {
    $('#editBoardForm').on('submit', function(e) {
        e.preventDefault(); // Prevent page refresh

        let formData = $(this).serialize();
        const statusId = $('#statusId').val();
        const statusName = $('#statusName').val();
        $.ajax({
            url: "{{ url('project/edit-board') }}", // ✅ Manual URL
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // ✅ CSRF Token
                statusId: $('#statusId').val(),
                statusName: $('#statusName').val()
            },
            success: function(response) {
                if (response.success) {
                    // Hide the modal
                    document.getElementById('status-name-' + statusId).textContent = $('#statusName').val();
                    $('#statusModal').modal('hide');

                    // Refresh the board
                    // renderBoard();

                    // Show success toast
                    Toastify({
                        text: response.message || "Status updated successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                    }).showToast();
                } else {
                    // Show error toast
                    $(`#status-name-${statusId}`).text(statusName);
                    Toastify({
                        text: response.message || "Failed to update status.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                    }).showToast();
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Toastify({
                    text: "An error occurred while saving the status.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
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
@endsection
