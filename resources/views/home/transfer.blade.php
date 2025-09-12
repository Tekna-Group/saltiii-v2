  <div class="modal fade" id="inviteMembersModal{{$task->id}}" tabindex="-1" aria-labelledby="addmemberModalLabel{{$task->id}}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-warning-subtle">
                    <h5 class="modal-title" id="addmemberModalLabel{{$task->id}}">Transfer to</h5>
                    <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method='POST' action='{{url('task-member/'.$task->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                                <div class="mb-3">
                                    <label>Assigned To: </label>
                                    <select type="text" class="form-control required select2 text-dark " style='color:black !important;' name='team_member[]' multiple id='team_member_member' required>
                                        {{-- <option value="">Select Team Member</option> --}}
                                        @foreach($task->project->users as $user)
                                            <option class='text-dark' value="{{$user->id}}" @foreach($task->users as $u) @if($user->id == $u->id) selected @endif @endforeach>{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            <!--end col-->
                            
                            <!--end col-->
                        </div>
                        <!--end row-->
                  
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="addMember">Transfer</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>