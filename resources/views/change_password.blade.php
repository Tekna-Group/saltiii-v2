<div class="modal fade" id="editUserPassword" tabindex="-1" role="dialog" aria-labelledby="editUserPassworddata" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserPassword">Change Password</h5>
              
            </div>
            <form method='POST' action='update-user-password' onsubmit='show()' enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class='col-md-12 form-group'>
                           New Password
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class='col-md-12 form-group'>
                            Confirm Password
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
