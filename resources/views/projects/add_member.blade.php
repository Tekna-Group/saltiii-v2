  <div class="modal fade" id="addmemberModal" tabindex="-1" aria-labelledby="addmemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header p-3">
                    <div><h5 class="modal-title" id="addmemberModalLabel">Project team</h5><p class="text-muted mb-0 mt-1 fs-12">Choose who can access and contribute to this project.</p></div>
                    <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method='POST' action='{{url('project-member/'.$project->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                                <div class="mb-3">
                                    <label for="team_member_member">Team members</label>
                                    <select type="text" class="form-control required select2" name='team_member[]' multiple id='team_member_member' required>
                                        {{-- <option value="">Select Team Member</option> --}}
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}" @foreach($project->users as $u) @if($user->id == $u->id) selected @endif @endforeach>{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            <!--end col-->
                            
                            <!--end col-->
                        </div>
                        <!--end row-->
                  
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="addMember"><i class="ri-check-line me-1" aria-hidden="true"></i> Save team</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
