<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <div><h5 class="modal-title">Create a sub-project</h5><p class="text-muted mb-0 mt-1 fs-12">Organize a related stream of work under {{ $project->name }}.</p></div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method='POST' action='{{ url('new-project') }}' id="subProjectForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
            <input type="hidden" id="projectId">
            <div class="mb-3">
                <label for="projectName">Project name</label>
                <input type="text" class="form-control" name='name' id="projectName" placeholder="Example: Mobile onboarding" maxlength="255" required>
            </div>
            <div class="mb-3">
                <label for="projectDescription">Description <small class="text-muted fw-normal">Optional</small></label>
                <textarea class="form-control" name="description" id="projectDescription" rows="3" placeholder="What outcome should this sub-project deliver?"></textarea>
            </div>
            <div class="mb-3">
                <label for="parentProject">Parent project</label>
                <select class="form-select select2" name="parent_id" id="parentProject">
                    <option value="">No parent project</option>
                    @foreach($projects as $parentProject)
                        <option value="{{ $parentProject->id }}" @if(isset($project) && $project->id == $parentProject->id) selected @endif>
                            {{ $parentProject->parent ? $parentProject->parent->name.' > ' : '' }}{{ $parentProject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="subProjectMembers">Team members</label>
                <select class="form-control required select2" name='team_member[]' multiple id='subProjectMembers' required>
                {{-- <option value="">Select Team Member</option> --}}
                @foreach($users as $user)
                    <option value="{{$user->id}}" @if(isset($project) && $project->users->contains('id', $user->id)) selected @endif>{{$user->name}}</option>
                @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="projectStatus">Status</label>
                <select class="form-select" id="projectStatus" name="status" required>
                    <option value='To be started'>To be started</option>
                    <option value='In Progress'>In Progress</option>
                    <option value='Completed'>Completed</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="projectImage">Project icon <small class="text-muted fw-normal">Optional</small></label>
                <input type="file" class="form-control" name='icon' id="projectImage" accept="image/*" >
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="ri-check-line me-1" aria-hidden="true"></i> Create sub-project</button>
            </div>
            </form>
        </div>
    </div>
</div>
