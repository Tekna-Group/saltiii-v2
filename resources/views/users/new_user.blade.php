 <div class="modal fade" id="newUser" tabindex="-1" aria-labelledby="newUserLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newUserLabel">New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method='POST' action='{{url('new-user')}}' onsubmit="show();"  enctype="multipart/form-data">
                     @csrf   
                    <div class="row g-3">
                        <div class="col-xxl-12">
                            <div>
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name='name' placeholder="Enter Name" Required>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name='email' placeholder="Email" Required>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" name='position'  placeholder="position" Required>
                            </div>
                        </div>
                      <div class='col-md-12'>
                        Password:
                            <input type="password" class="form-control-sm form-control "  placeholder="******"  name="password" required/>
                        </div>
                        <div class='col-md-12'>
                            Password Confirmation:
                            <input type="password" class="form-control-sm form-control "  placeholder="******"   name="password_confirmation" required/>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="role" class="form-label">Role</label>
                                <select class='form-control' name='role' required>
                                    <option value=''>Select</option>
                                    <option value='Admin'>Admin</option>
                                    <option value='Project Lead'>Project Lead</option>
                                    <option value='Member'>Member</option>
                                    <option value='Timekeeper'>Timekeeper</option>
                                </select>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
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