@extends('layouts.header')

@section('content')
<div class=" mt-4">

    <!-- HEADER -->
    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex align-items-center">
            <h4 class="mb-0 mr-auto">
                📊 Project Diagrams
            </h4>

         <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#diagramModal">
        + New Diagram
        </button>
        </div>
    </div>
    
<!-- Modal -->
<div class="modal fade" id="diagramModal" tabindex="-1" aria-labelledby="diagramModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="diagramModalLabel">Create New Diagram</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <!-- Modal Body with Form -->
      <div class="modal-body">
        <form action="{{ url('/diagram/store') }}" method="POST" class="d-flex align-items-center">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project_id }}">

            <input type="text"
                   name="name"
                   class="form-control form-control-sm me-2"
                   placeholder="Diagram name"
                   required>

            <button type="submit" class="btn btn-primary btn-sm">
                Create
            </button>
        </form>
      </div>
      
    </div>
  </div>
</div>
    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Diagram Name</th>
                        <th>Created</th>
                        <th class="text-right" width="160">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($diagrams as $diagram)
                    <tr>
                        <td>
                            <strong>{{ $diagram->name }}</strong>
                        </td>
                        <td class="text-muted">
                            {{ $diagram->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('/diagram/editor/'.$diagram->id) }}"
                               class="btn btn-sm btn-outline-success">
                                Open Editor
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="text-muted mb-2">
                                No diagrams created yet
                            </div>
                            <small>Create your first process flow diagram.</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
