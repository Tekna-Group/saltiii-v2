@extends('layouts.header')
@section('css')
   <link rel="stylesheet"
      href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @endsection
@section('content')
<div class="container">
    <h4>Workflow Management</h4>

    <div class="form-group">
        <label>Select Project</label>
        <select id="project" class="form-control">
            <option value="">-- Select Project --</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>

    <div id="workflowArea" class="row"></div>
</div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    
$(function () {

    let baseUrl = "{{ url('/') }}";

    $('#project').change(function () {
        let projectId = $(this).val();
        $('#workflowArea').empty();
        if (!projectId) return;

        $.get(baseUrl + '/workflow/boards/' + projectId, function (res) {

            res.boards.forEach(board => {

                $('#workflowArea').append(`
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <strong>${board.board}</strong>
                            </div>
                            <div class="card-body">

                                <!-- Allowed Moves -->
                                <p class="text-muted small mb-1">Allowed Moves</p>
                                <ul class="list-group sortable" data-from="${board.id}" style="min-height:80px"></ul>

                                <!-- Auto Assign Users -->
                                <div class="mt-3 border-top pt-2">
                                    <h6 class="mb-2">Auto Assign Users</h6>

                                    <form method="POST" action="${baseUrl}/workflow/assign/save">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="project_id" value="${board.project_id}">
                                        <input type="hidden" name="board_id" value="${board.id}">

                                        <div class="form-group mb-2">
                                            <select name="user_ids[]" class="form-control assign-users" multiple>
                                                ${res.users.map(user => `
                                                    <option value="${user.id}"
                                                        ${res.assignedUsers[board.id] && res.assignedUsers[board.id].some(u => u.user_id == user.id) ? 'selected' : ''}>
                                                        ${user.name}
                                                    </option>
                                                `).join('')}
                                            </select>
                                            <small class="text-muted">Select users to auto-assign on task enter</small>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>If no user assigned</label>
                                            <select name="fallback_rule" class="form-control">
                                                <option value="keep">Keep current assignees</option>
                                                <option value="mover">Assign task mover</option>
                                                <option value="random">Assign random member</option>
                                                <option value="project_owner">Assign project owner</option>
                                            </select>
                                        </div>

                                        <button class="btn btn-sm btn-outline-primary">Save</button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                `);
            });

            // Populate allowed transitions
            res.transitions.forEach(t => {
                let toBoard = res.boards.find(b => b.id === t.to_board_id);
                if (toBoard) {
                    $(`ul[data-from="${t.from_board_id}"]`).append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            data-to="${toBoard.id}">
                            <span>${toBoard.board}</span>
                            <button class="btn btn-sm btn-outline-danger remove-transition">
                                ✕
                            </button>
                        </li>

                    `);
                }
            });
            $('#workflowArea').find('.assign-users').select2({
                placeholder: 'Select users',
                width: '100%'
            });
            enableDrag(projectId);
        });
    });

    function enableDrag(projectId) {

        $('.sortable').sortable({
    connectWith: '.sortable',
    receive: function (event, ui) {
        let fromBoard = $(this).data('from');
        let toBoard   = ui.item.data('to');

        saveTransition(projectId, fromBoard, toBoard, true);
    }
}).disableSelection();
    }

    function saveTransition(projectId, from, to, allowed) {
        $.post(baseUrl + '/workflow/transition/save', {
            _token: "{{ csrf_token() }}",
            project_id: projectId,
            from_board_id: from,
            to_board_id: to,
            is_allowed: allowed ? 1 : 0
        });
    }
    $(document).on('click', '.remove-transition', function () {

    let li = $(this).closest('li');
    let toBoard = li.data('to');
    let fromBoard = li.closest('.sortable').data('from');
    let projectId = $('#project').val();

    if (!confirm('Disallow this transition?')) return;

    saveTransition(projectId, fromBoard, toBoard, false);
    li.remove();
});

});


</script>
@endsection
