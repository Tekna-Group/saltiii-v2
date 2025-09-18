 <div class="modal fade" id="creatertaskModal" tabindex="-1" aria-labelledby="creatertaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="creatertaskModalLabel">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method='POST' action='{{url('new-task-home')}}' onsubmit="show();"   enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                           
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="project" class="form-label">Project</label>
                            <select type="text" class="form-control required select2" name='project' multiple id='project' required>
                                {{-- <option value="">Select Team Member</option> --}}
                                @foreach($projects as $project)
                                    <option value="{{$project->id}}">{{$project->name}}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-lg-12">
                            <label for="sub-tasks" class="form-label">Task</label>
                            <input type="text" class="form-control" id="sub-tasks" placeholder="Task" name="task" required>
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="task-description" class="form-label">Task Description</label>
                            <textarea class="form-control summernote" id="task-description" rows="3" name="description" placeholder="Task description" ></textarea>
                        </div>
                        <!--end col-->
                        <!--end col-->
                        <!--end col-->
                        <div class="col-lg-4">
                            <label for="due-date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due-date" data-provider="flatpickr" name='dueDate' placeholder="Select date" required>
                        </div>
                        <!--end col-->
                        <div class="col-lg-4">
                            <label for="priority-field" class="form-label">Priority</label>
                            <select class="form-control" id="priority-field" name='priority' required>
                                <option value="">Priority</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <!--end col-->
                        <!--end col-->
                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add Task</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div>
    </div>
</div>