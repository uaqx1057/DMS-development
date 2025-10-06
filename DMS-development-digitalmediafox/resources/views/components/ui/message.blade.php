@if(session('warning'))

<div class="alert alert-warning alert-dismissible fade show material-shadow" role="alert">
    <strong>@translate('Warning') !</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif(session('error'))
<div class="alert alert-danger alert-dismissible fade show material-shadow" role="alert">
    <strong>@translate('Error') !</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif(session('success'))
<div class="alert alert-success alert-dismissible fade show material-shadow" role="alert">
    <strong>@translate('Success') !</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
