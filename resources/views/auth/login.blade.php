@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: blue;
    }
</style>
<div class="container-sm my-5 py-5">
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="row justify-content-center">
            <div class="p-5 bg-light rounded-3 border col-xl-4">
                <div class="mb-5 text-center"> <i class="bi-hexagon-fill me-2" style="font-size: 50px; color: rgb(49, 108, 244);"></i>
                    <h3>Employee Data Master</h3>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-15 mb-4">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter Your Email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-15 mb-2">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter Your Password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <hr>

                <div class="row my-3 pt-4">
                    <div class="col-md-15">
                        <button type="submit" class="btn btn-primary btn-lg px-0 w-100">
                            <i class="bi bi-box-arrow-in-right"></i> {{ __('Log In') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
