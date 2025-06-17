  <div class="modal fade" id="addActivity" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-warning-subtle">
                    <h5 class="modal-title" id="addActivityModalLabel">Activity</h5>
                    <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method='POST' action='{{url('task-activity/'.$task->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="sub-tasks" class="form-label">Activity</label>
                                <input type="text" class="form-control" id="sub-tasks" placeholder="Activity" name="task" required>
                            </div>
                            <div class="col-lg-12">
                                <label for="sub-tasks" class="form-label">Hours (<small><i><b>Sample</b> 1.5 for 1 hours and 30 minutes</i></small>)</label>
                                <input type="number" class="form-control" min='0' step='.1' id="hours" placeholder="1.0" name="hours" required>
                            </div>
                            <div class="col-lg-12">
                                <label for="sub-tasks" class="form-label">Date </label>
                                <input type="date" class="form-control" max='{{date('Y-m-d')}}' value='{{date('Y-m-d')}}' id="date" placeholder="1.0" name="date" required>
                            </div>
                        </div>
                        <!--end row-->
                  
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="addMember">Save</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>