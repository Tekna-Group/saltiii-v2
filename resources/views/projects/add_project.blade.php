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