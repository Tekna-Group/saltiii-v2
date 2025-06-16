 <div class="modal fade" id="creatertaskModal" tabindex="-1" aria-labelledby="creatertaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="creatertaskModalLabel">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <label for="projectName" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="projectName" placeholder="Enter project name">
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="sub-tasks" class="form-label">Task Title</label>
                            <input type="text" class="form-control" id="sub-tasks" placeholder="Task title">
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="task-description" class="form-label">Task Description</label>
                            <textarea class="form-control" id="task-description" rows="3" placeholder="Task description"></textarea>
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="formFile" class="form-label">Tasks Images</label>
                            <input class="form-control" type="file" id="formFile">
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="tasks-progress" class="form-label">Add Team Member</label>
                            <div data-simplebar style="height: 95px;">
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="anna-adame">
                                            <label class="form-check-label d-flex align-items-center" for="anna-adame">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-1.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Anna Adame
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="frank-hook">
                                            <label class="form-check-label d-flex align-items-center" for="frank-hook">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Frank Hook
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="alexis-clarke">
                                            <label class="form-check-label d-flex align-items-center" for="alexis-clarke">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Alexis Clarke
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="herbert-stokes">
                                            <label class="form-check-label d-flex align-items-center" for="herbert-stokes">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Herbert Stokes
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="michael-morris">
                                            <label class="form-check-label d-flex align-items-center" for="michael-morris">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-7.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Michael Morris
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="nancy-martino">
                                            <label class="form-check-label d-flex align-items-center" for="nancy-martino">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-5.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Nancy Martino
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="thomas-taylor">
                                            <label class="form-check-label d-flex align-items-center" for="thomas-taylor">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-8.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Thomas Taylor
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" value="" id="tonya-noble">
                                            <label class="form-check-label d-flex align-items-center" for="tonya-noble">
                                                <span class="flex-shrink-0">
                                                    <img src="assets/images/users/avatar-10.jpg" alt="" class="avatar-xxs rounded-circle" />
                                                </span>
                                                <span class="flex-grow-1 ms-2">
                                                    Tonya Noble
                                                </span>
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-lg-4">
                            <label for="due-date" class="form-label">Due Date</label>
                            <input type="text" class="form-control" id="due-date" data-provider="flatpickr" placeholder="Select date">
                        </div>
                        <!--end col-->
                        <div class="col-lg-4">
                            <label for="categories" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="categories" placeholder="Enter tag">
                        </div>
                        <!--end col-->
                        <div class="col-lg-4">
                            <label for="tasks-progress" class="form-label">Tasks Progress</label>
                            <input type="text" class="form-control" maxlength="3" id="tasks-progress" placeholder="Enter progress">
                        </div>
                        <!--end col-->
                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success">Add Task</button>
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