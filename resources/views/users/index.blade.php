@extends('layouts.header')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection
@section('content')
 @include('error')
 <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Users <button type="button" class="btn btn-success btn-icon waves-effect waves-light" title='New User' data-bs-toggle="modal" data-bs-target="#newUser"><i class=" ri-add-box-line"></i></button></h5>
            </div>
            <div class="card-body">
                <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th >Name</th>
                            <th >Email</th>
                            <th >Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                           
                            <td>
                                {{-- <div class="avatar-circle bg-primary text-white fw-bold text-center rounded-circle" style="width:40px; height:40px; line-height:40px;">
                                    {{ strtoupper(substr($user->name, 0, 3)) }}
                                </div> --}}
                                @if($user->avatar)
                                    <img src="{{asset($user->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="avatar-xs rounded-circle material-shadow">  {{$user->name}}
                                @else
                                  <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white fw-bold text-center rounded-circle me-2" style="width:40px; height:40px; line-height:40px;">
                                        {{ strtoupper(substr($user->name, 0, 3)) }} 
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </div>
                                    
                                @endif
                                </td>
                                <td>{{$user->email}}</td>
                            <td>{{$user->role}}</td>
                            <td>{{$user->status}}</td>
                            <td>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href='#' class="dropdown-item edit-item-btn"  data-bs-toggle="modal" data-bs-target="#editUser{{$user->id}}"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                        <li><a href='#' class="dropdown-item edit-item-btn"  data-bs-toggle="modal" data-bs-target="#uploadAvatar{{$user->id}}"><i class="ri-file-image-line align-bottom me-2 text-muted"></i> Avatar</a></li>  
                                        @if(Auth::user()->id != $user->id)
                                        <li>
                                          
                                            <a  href='#' class="dropdown-item remove-item-btn deactivate-user">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted "></i> Deactivate
                                            </a>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->

@include('users.new_user')
@foreach($users as $user)
    @include('users.edit_user')
    @include('users.change_avatar')
@endforeach

@endsection
@section('js')
 <!-- gridjs js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>

    <script src="{{asset('inside_css/assets/js/pages/datatables.init.js')}}"></script>
    <!-- App js -->
     <script src="{{asset('inside_css/assets/libs/prismjs/prism.js')}}"></script>
@endsection
