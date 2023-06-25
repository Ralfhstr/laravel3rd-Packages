{{-- untuk extends tampilan utama pada layout.app --}}
@extends('layouts.app')

{{-- untuk menunjukkan bagian content yang ditampilkan --}}
@section('content')
{{-- untuk menampilkan view pada default --}}
    @include('default')
@endsection
