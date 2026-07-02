 <div class="modal fade" id="editUser{{$user->id}}" tabindex="-1" aria-labelledby="newUserLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newUserLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method='POST' action='edit-user/{{$user->id}}' onsubmit="show();"   enctype="multipart/form-data">
                     @csrf   
                    <div class="row g-3">
                        <div class="col-xxl-12">
                            <div>
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name='name' value="{{$user->name}}" placeholder="Enter Name" Required>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name='email' value="{{$user->email}}" placeholder="Email" Required>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="hourly_rate" class="form-label">Hourly Rate</label>
                                <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" placeholder="00.00" value="{{ optional($user->salary)->salary }}">
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="wallet_address" class="form-label">Wallet Address</label>
                                <input type="text" class="form-control" id="wallet_address" name="wallet_address" placeholder="Solana wallet address" value="{{ $user->wallet_address }}">
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="wallet_network" class="form-label">Wallet Network</label>
                                <input type="text" class="form-control" id="wallet_network" name="wallet_network" placeholder="Solana" value="{{ $user->wallet_network ?? 'Solana' }}">
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="stripe_account_id" class="form-label">Stripe Account ID</label>
                                <input type="text" class="form-control" id="stripe_account_id" name="stripe_account_id" placeholder="acct_..." value="{{ $user->stripe_account_id }}">
                                <small class="text-muted">Required for Stripe salary payouts. Use Stripe Connect ID like acct_..., not a bank account number.</small>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="role" class="form-label">Role</label>
                                <select class='form-control' name='role' required>
                                    <option value=''>Select</option>
                                    <option value='Admin'  @if($user->role == "Admin") selected @endif>Admin</option>
                                    <option value='Project Lead'  @if($user->role == "Project Lead") selected @endif>Project Lead</option>
                                    <option value='Member'  @if($user->role == "Member") selected @endif>Member</option>
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
