<div class="modal fade" id="addActivity" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header p-3">
                <div>
                    <h5 class="modal-title" id="addActivityModalLabel">Log time</h5>
                    <p class="text-muted mb-0 mt-1 fs-12">Add a clear record of the work you completed.</p>
                </div>
                <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method='POST' action='{{url('activity')}}' onsubmit="show();"   enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <label for="activity-task" class="form-label">Work item</label>
                            <select name='task_id' id="activity-task" class='form-control select2' required>
                                <option value=''>Select a task</option>
                                @foreach($tasks as $t)
                                    <option value='{{$t->id}}'>{{$t->title}} ({{$t->project->name}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <label for="activity" class="form-label">What did you work on?</label>
                            <input type="text" class="form-control" id="activity" placeholder="Example: Finished the client onboarding flow" name="task" maxlength="160" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="hours" class="form-label">Hours <small class="text-muted fw-normal">Use 1.5 for 1 hour 30 minutes</small></label>
                            <input type="number" class="form-control" min='0.01' max="24" step='.01' id="hours" placeholder="1.5" name="hours" inputmode="decimal" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="date" class="form-label">Work date</label>
                            <input type="date" class="form-control" max='{{date('Y-m-d')}}' value='{{date('Y-m-d')}}' min='{{ date('Y-m-d', strtotime('-1 day')) }}' id="date" name="date" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="remarks" class="form-label">Notes <small class="text-muted fw-normal">Optional</small></label>
                            <textarea class='form-control' id="remarks" name='comments' rows="3" placeholder="Add context that will help with review"></textarea>
                        </div>
                       
                        <div class="col-lg-12">
                            <label for="activity-proof" class="form-label">Supporting file <small class="text-muted fw-normal">Optional, up to 3 MB</small></label>
                            <input type="file" class="filepond" id="activity-proof" name="proof" data-max-file-size="3MB" data-max-files="1" />
                        </div>
                    </div>
                    <!--end row-->
              
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="ri-check-line me-1" aria-hidden="true"></i> Save time entry</button>
                    </div>
                </form>
        </div>
    </div>
</div>
