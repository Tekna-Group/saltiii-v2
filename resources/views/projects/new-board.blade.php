<div class="modal fade" id="createboardModal" tabindex="-1" aria-labelledby="createboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header p-3">
                <div><h5 class="modal-title" id="createboardModalLabel">Create a status</h5><p class="text-muted mb-0 mt-1 fs-12">Add another stage to the project workflow.</p></div>
                <button type="button" class="btn-close" id="addBoardBtn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
             <form method='POST' action='{{url('project-board/'.$project->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="boardName" class="form-label">Status name</label>
                            <input type="text" class="form-control" id="boardName" name="boardName" placeholder="Example: Ready for review" maxlength="80" required>
                        </div>
                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary"><i class="ri-check-line me-1" aria-hidden="true"></i> Create status</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
