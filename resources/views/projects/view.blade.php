@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
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
                                        <h4 class="fw-bold">{{$project->name}}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            {{-- <div><i class="ri-building-line align-bottom me-1"></i> Themesbrand</div> --}}
                                            {{-- <div class="vr"></div> --}}
                                            <div>Create Date : <span class="fw-medium">{{date('d M, Y',strtotime($project->created_at))}}</span></div>
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
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createboardModal"><i class="ri-add-line align-bottom me-1"></i> Create Board</button>
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
                                    <a href="#addmemberModal" data-bs-toggle="modal" class="avatar-group-item material-shadow">
                                        <div class="avatar-xs">
                                            <div class="avatar-title rounded-circle">
                                                +
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end card-body-->
                </div>
                    <div class="kanban-board" id="kanbanBoard">
                      <!-- Columns will be inserted dynamically -->
                      
                    </div>
                  <!-- Modals -->
                 
                  <div class="modal fade" id="statusModal" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Status</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method='POST' action='{{url('project/edit-board')}}' onsubmit="show();"   enctype="multipart/form-data">
                            @csrf
                        <div class="modal-body">
                          <input type="hidden" name='statusId' id="statusId">
                          <div class="mb-3">
                            <label class="form-label">Status Name</label>
                            <input type="text" name='statusName' class="form-control" id="statusName">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type='submit' class="btn btn-primary" onclick="saveStatus()">Save</button>
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
    $('#addmemberModal').on('shown.bs.modal', function () {
        $('.select2').select2({
            dropdownParent: $('#addmemberModal')
        });
    });
    $('#creatertaskModal').on('shown.bs.modal', function () {
        $('.select2').select2({
            dropdownParent: $('#creatertaskModal')
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
  
    let boardData = @json($boardData);
    console.log(boardData);
    
    function renderBoard() {
      const board = document.getElementById('kanbanBoard');
      board.innerHTML = '';
      boardData.forEach(column => {
        const columnDiv = document.createElement('div');
        columnDiv.className = 'kanban-column';
        columnDiv.dataset.id = column.id;
        columnDiv.innerHTML = `
          <div class="kanban-header">
            <span>${column.name} <button class="btn btn-sm btn-outline-primary" onclick="addTask('${column.id}')">+</button></span>
            
            <div>
                
              <button class="btn btn-sm btn-outline-secondary" onclick="editStatus('${column.id}')">Edit</button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteStatus('${column.id}')">Delete</button>
            </div>
          </div>
          <div class="kanban-items" ondragover="allowDrop(event)" ondrop="drop(event, '${column.id}')">
            ${column.tasks.map(task => `
              <div class=" card tasks-box" draggable="true" ondragstart="drag(event, '${task.id}')">
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1">
                            <h6 class="fs-15 mb-0 text-truncate task-title"><a href='view-task/${task.id}' class="d-block">${task.name}</a></h6>
                        </div>
                    </div>
                    <p class="text-muted">${task.description}</p>
                                            
                </div>
                <div class="card-footer border-top-dashed">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <span class="text-muted"><i class="ri-time-line align-bottom"></i> ${task.due_date ? task.due_date : 'No Due Date'}</span>
                        </div>
                        <div class="flex-shrink-0">
                            <ul class="link-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="javascript:void(0)" class="text-muted"><i class="ri-timer-fill"></i> 0</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript:void(0)" class="text-muted"><i class="ri-question-answer-line align-bottom"></i> 0</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript:void(0)" class="text-muted"><i class="ri-attachment-2 align-bottom"></i> 0</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
               
              </div>`).join('')}
          </div>
          <div class="p-2">
            <button class="btn btn-sm btn-outline-primary w-100" onclick="addTask('${column.id}')">+ Add Task</button>
          </div>
        `;
        board.appendChild(columnDiv);
      });
    }
    
    function addStatus() {
      document.getElementById('statusId').value = '';
      document.getElementById('statusName').value = '';
      new bootstrap.Modal(document.getElementById('statusModal')).show();
    }
    
    function saveStatus() {
      const id = document.getElementById('statusId').value;
    //   alert(id);
      const name = document.getElementById('statusName').value;
      if (!name) return alert('Please enter status name');
    
      const existing = boardData.find(c => c.id == id);
      if (existing) {
        existing.name = name;
      } else {
        boardData.push({ id, name, tasks: [] });
      }
      bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
      renderBoard();
    }
    
    function editStatus(id) {
        console.log(boardData);
      const column = boardData.find(c => c.id == id);
     
      document.getElementById('statusId').value = column.id;
      document.getElementById('statusName').value = column.name;
      new bootstrap.Modal(document.getElementById('statusModal')).show();
    }
    
    function deleteStatus(id) {
      if (confirm('Are you sure?')) {
        boardData = boardData.filter(c => c.id != id);
        renderBoard();
      }
    }
    
    function addTask(columnId) {
      document.getElementById('taskColumn').value = columnId;
      new bootstrap.Modal(document.getElementById('creatertaskModal')).show();
    }
    
    function editTask(columnId, taskId) {
      const column = boardData.find(c => c.id == columnId);
      const task = column.tasks.find(t => t.id == taskId);
      document.getElementById('taskColumn').value = columnId;
      document.getElementById('taskId').value = taskId;
      document.getElementById('taskName').value = task.name;
      new bootstrap.Modal(document.getElementById('taskModal')).show();
    }
    
    function saveTask() {
      const columnId = document.getElementById('taskColumn').value;
      const taskId = document.getElementById('taskId').value;
      const taskName = document.getElementById('taskName').value;
    
      if (!taskName) return alert('Please enter task name');
    
      const column = boardData.find(c => c.id == columnId);
      const taskIndex = column.tasks.findIndex(t => t.id == taskId);
      if (taskIndex > -1) {
        column.tasks[taskIndex].name = taskName;
      } else {
        column.tasks.push({ id: taskId, name: taskName });
      }
      bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
      renderBoard();
    }
    
    function deleteTask(columnId, taskId) {
      const column = boardData.find(c => c.id === columnId);
      column.tasks = column.tasks.filter(t => t.id !== taskId);
      renderBoard();
    }
    
    let draggedTaskId = '';
    
    function drag(ev, taskId) {
      draggedTaskId = taskId;
    }
    
    function allowDrop(ev) {
      ev.preventDefault();
    }
    
    function drop(ev, columnId) {
      if (!draggedTaskId) return;
      boardData.forEach(col => {
        const taskIndex = col.tasks.findIndex(t => t.id === draggedTaskId);
        if (taskIndex > -1) {
          const task = col.tasks.splice(taskIndex, 1)[0];
          const newColumn = boardData.find(c => c.id === columnId);
          newColumn.tasks.push(task);
        }
      });
      draggedTaskId = '';
      renderBoard();
    }
    
    renderBoard();
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
