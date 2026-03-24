@if (session('status'))
    <div class="alert alert-success b-round mt-3">
        {{ session('status') }}
    </div>
@endif
@if (session('failed'))
    <div class="alert alert-danger b-round mt-3">
        {{ session('failed') }}
    </div>
@endif
@if (session('delete'))
    <div class="alert alert-warning b-round mt-3">
        {{ session('delete') }}
    </div>
@endif
