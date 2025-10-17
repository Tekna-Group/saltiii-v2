<div class="modal fade" id="addActivity" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-warning-subtle">
                <h5 class="modal-title" id="addActivityModalLabel">Activity</h5>
                <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method='POST' action='{{url('activity')}}' onsubmit="show();"   enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <label for="task" class="form-label">Task</label>
                            <select name='task_id' class='form-control select2' required>
                                <option value=''>-- Select Task --</option>
                                @foreach($tasks as $t)
                                    <option value='{{$t->id}}'>{{$t->title}} ({{$t->project->name}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <label for="activity" class="form-label">Activity</label>
                            <input type="text" class="form-control" id="activity" placeholder="Activity" name="task" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="hours" class="form-label">Hours (<small><i><b>Sample</b> 1.5 for 1 hours and 30 minutes</i></small>)</label>
                            <input type="number" class="form-control" min='0' step='.01' id="hours" placeholder="1.01" name="hours" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="date" class="form-label">Date </label>
                            <input type="date" class="form-control" max='{{date('Y-m-d')}}' value='{{date('Y-m-d')}}' min='{{ date('Y-m-d', strtotime('-1 day')) }}' id="date" placeholder="1.0" name="date" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="remarks" class="form-label">Remarks / Comments <small><i>(Optional)</i></small></label>
                            <textarea class='form-control' name='comments' ></textarea>
                        </div>
                       
                        <div class="col-lg-12">
                            <label for="" class="form-label">Proof</label>
                            <input type="file" class="filepond" name="proof" data-max-file-size="3MB" data-max-files="1" />
                        </div>
                    </div>
                    <!--end row-->
              
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" >Save</button>
                    </div>
                </form>
        </div>
    </div>
</div>