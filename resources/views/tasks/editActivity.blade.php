<div class="offcanvas offcanvas-end" tabindex="-1" id="editActivityOffcanvas" aria-labelledby="editActivityLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="editActivityLabel">Edit Activity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editActivityForm">
            @csrf
            <input type="hidden" name="activity_id" id="editActivityId">
            <div class="mb-3">
                <label class="form-label">Activity</label>
                <input type="text" class="form-control" name="task" id="editActivityTask" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Hours</label>
                <input type="number" step="0.01" min="0" class="form-control" name="hours" id="editActivityHours" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="date" id="editActivityDate" readonly required>
            </div>
            <div class="mb-3 text-end">
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>
</div>