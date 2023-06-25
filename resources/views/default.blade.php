<div class="container mt-4">
    {{-- menampikan data yang dipassing pada controller --}}
    <h4>{{ $pageTitle }}</h4>
    <hr>
    <div class="d-flex align-items-center py-2 px-4 bg-light rounded-3 border">
        <div class="bi-house-fill me-3 fs-1"></div>
        <h4 class="mb-0">Hello! {{ Auth::user()->name }}.</h4>
    </div>
</div>
