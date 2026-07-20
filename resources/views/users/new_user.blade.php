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
                                <label for="hourly_rate" class="form-label">Hourly Rate</label>
                                <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" placeholder="00.00" value="{{ old('hourly_rate') }}" required>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="wallet_address" class="form-label">Wallet Address</label>
                                <input type="text" class="form-control" id="wallet_address" name="wallet_address" placeholder="Solana wallet address" value="{{ old('wallet_address') }}">
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="wallet_network" class="form-label">Wallet Network</label>
                                <input type="text" class="form-control" id="wallet_network" name="wallet_network" placeholder="Solana" value="{{ old('wallet_network', 'Solana') }}">
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="stripe_account_id" class="form-label">Stripe Account ID</label>
                                <input type="text" class="form-control" id="stripe_account_id" name="stripe_account_id" placeholder="acct_..." value="{{ old('stripe_account_id') }}">
                                <small class="text-muted">Required for Stripe salary payouts. Use Stripe Connect ID like acct_..., not a bank account number.</small>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div>
                                <label for="airwallex_beneficiary_id" class="form-label">Airwallex Beneficiary ID</label>
                                <input type="text" class="form-control" id="airwallex_beneficiary_id" name="airwallex_beneficiary_id" placeholder="Beneficiary ID from Airwallex" value="{{ old('airwallex_beneficiary_id') }}">
                                <small class="text-muted">Create the employee as a PHP bank beneficiary in Airwallex, then paste the beneficiary ID here.</small>
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
