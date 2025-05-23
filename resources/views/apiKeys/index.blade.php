@extends('layouts.header')
@section('css')
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Developer Plan</h5>
                <div class="progress animated-progress custom-progress mb-1">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 38%" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="text-muted mb-2">You used 215 of 2000 of your API</p>
                <div class="text-end">
                    <button type="button" class="btn btn-primary btn-sm create-btn" data-bs-toggle="modal" data-bs-target="#api-key-modal">Create API Key</button>
                </div>
            </div>
        </div>
    </div><!--end col-->
    <div class="col-lg-4">
        <div class="card card-animate card-height-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Successful conversions</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="50"></span></h2>
                        <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0"><i class="ri-arrow-up-line align-middle"></i> 8.24 % </span> 7 last week</p>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                <i data-feather="check-circle" class="text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div><!--end col-->
    <div class="col-lg-4">
        <div class="card card-animate card-height-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Failed conversions</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="8"></span></h2>
                        <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0"><i class="ri-arrow-down-line align-middle"></i> 25.87 % </span> 7 last week</p>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                <i data-feather="alert-octagon" class="text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div><!--end col-->
</div>
<!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="apiKeyList">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title flex-grow-1 mb-0">API Keys</h5>
                <div class="d-flex gap-1 flex-wrap">
                    <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                    <button type="button" class="btn btn-primary create-btn" data-bs-toggle="modal" data-bs-target="#api-key-modal"><i class="ri-add-line align-bottom me-1"></i> Add API Key</button>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card mb-3">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort d-none" data-sort="id" scope="col">Id</th>
                                    <th class="sort" data-sort="name" scope="col">Name</th>
                                    <th class="sort" data-sort="createBy" scope="col">Created By</th>
                                    <th class="sort" data-sort="apikey" scope="col">API Key</th>
                                    <th class="sort" data-sort="status" scope="col">Status</th>
                                    <th class="sort" data-sort="create_date" scope="col">Create Date</th>
                                    <th class="sort" data-sort="expiry_date" scope="col">Expiry Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chk_child" value="option1">
                                        </div>
                                    </th>
                                    <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ001</a></td>
                                    <td class="name">Streamlab</td>
                                    <td class="createBy">Nicholas Ball</td>
                                    <td class="apikey">
                                        <input type="text" class="form-control apikey-value" readonly value="b5815DE8A7224438932eb296Z5">
                                    </td>
                                    <td class="status"><span class="badge bg-danger-subtle text-danger">Disable</span></td>
                                    <td class="create_date">24 Sep, 2022</td>
                                    <td class="expiry_date">24 Jan, 2023</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item edit-item-btn" href="#api-key-modal" data-bs-toggle="modal">Rename</a></li>
                                                <li><a class="dropdown-item regenerate-api-btn" href="#api-key-modal" data-bs-toggle="modal">Regenerate Key</a></li>
                                                <li><a class="dropdown-item disable-btn" href="javascript:void(0);">Disable</a></li>
                                                <li><a class="dropdown-item remove-item-btn" data-bs-toggle="modal" href="#deleteApiKeyModal">Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted mb-0">We've searched more than 150+ API Keys We did not find any API for you search.</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <div class="pagination-wrap hstack gap-2">
                            <a class="page-item pagination-prev disabled" href="#">
                                Previous
                            </a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">
                                Next
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>

@endsection
@section('js')
@endsection
