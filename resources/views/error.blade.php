@if (count($errors))
 
    @foreach($errors->all() as $error)
    <div class="alert alert-danger" role="alert">
        <strong>Error!</strong> {{ $error }}
    </div>
        <br>
    @endforeach
@endif