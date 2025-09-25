  <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-warning-subtle">
                    <h5 class="modal-title" id="editTaskModalModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" id="btn-close-member" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method='POST' action='{{url('edit-task-description/'.$task->id)}}' onsubmit="show();"   enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                                <div class="mb-3">
                                   <div class="col-lg-12">
                                        <label for="task-description" class="form-label">Task Descrwiption</label>
                                        <textarea class="form-control summernote" id="task-description" rows="3" name="description" placeholder="Task description" required>{!!$task->description!!}</textarea>
                                    </div>
                                </div>
                            <!--end col-->
                            
                            <!--end col-->
                        </div>
                        <!--end row-->
                  
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>