<div class="modal fade" id="createboardModal" tabindex="-1" aria-labelledby="createboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="createboardModalLabel">Add Board</h5>
                <button type="button" class="btn-close" id="addBoardBtn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
             <form method='POST' action='{{url('project-board/'.$project->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="boardName" class="form-label">Board Name</label>
                            <input type="text" class="form-control" id="boardName" name="boardName" placeholder="Enter board name" required>
                        </div>
                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="submit" class="btn btn-success" >Add Board</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>