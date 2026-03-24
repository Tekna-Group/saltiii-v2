@extends('layouts.header')

@section('content')

<div class="container py-5">

    <!-- Title -->
    <div class="text-center mb-4">
        <h2 class="fw-bold">Workflow Store</h2>
        <p class="text-muted">Browse and duplicate ready-made process flows</p>
    </div>

    <!-- Search -->
    <div class="search-box mb-5">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Search workflows...">
        </div>
    </div>

    <!-- Workflow Cards -->
    <div class="row g-4" id="workflowContainer">

        @foreach($diagrams as $project)
            
            <div class="col-md-4 workflow-item">
                <div class="card p-4 h-100">

                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3 p-0 overflow-hidden">
                            <img src="{{asset('images/Favicon.png')}}" 
                                alt="Workflow" 
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <h5 class="mb-0">{{ $project->name }}</h5>
                    </div>

                    {{-- <p class="text-muted small">
                        {{ Str::limit(strip_tags($project->diagram_json), 80) }}
                    </p> --}}

                    <div class="mt-auto">

                        <div class="mb-2">
                            <small class="text-muted">
                                By {{ $project->creator->name ?? 'Unknown' }}
                            </small>
                        </div>

                        <form action="{{ url('/diagram/editor/view/'.$project->id) }}" method="get">
                            @csrf
                            <button class="btn btn-primary w-100">
                                <i class="bi bi-copy"></i> View Template
                            </button>
                        </form>

                    </div>

                </div>
            </div>
        @endforeach

    </div>
</div>

@endsection

@section('css')
<style>
body {
    background: #f5f7fb;
}

.card {
    border: none;
    border-radius: 15px;
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    border-radius: 12px;
    background: #e9f2ff;
    color: #0d6efd;
}

.search-box {
    max-width: 500px;
    margin: auto;
}
</style>
@endsection

@section('scripts')
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let items = document.querySelectorAll('.workflow-item');

    items.forEach(item => {
        let text = item.innerText.toLowerCase();
        item.style.display = text.includes(value) ? '' : 'none';
    });
});
</script>
@endsection