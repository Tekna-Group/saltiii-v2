<div class="modal fade" id="addActivity{{$task->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-warning-subtle">
                <h5 class="modal-title">Add Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addActivityForm{{$task->id}}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="task_id" value="{{$task->id}}">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Activity</label>
                            <input type="text" class="form-control" name="task" placeholder="Activity" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Hours</label>
                            <input type="number" class="form-control" step="0.01" min="0" name="hours" placeholder="1.0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" min='{{ date('Y-m-d', strtotime('-1 day')) }}' max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea class="form-control" name="comments"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Proof (Optional)</label>
                            <input type="file" name="proof" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>