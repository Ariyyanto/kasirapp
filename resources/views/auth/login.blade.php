@extends('layouts.app')

@section('title', 'Login - Sistem Prediksi Stok')

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .form-control {
        transition: all 0.3s ease;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(78, 84, 200, 0.25) !important;
        background-color: rgba(245, 247, 250, 0.9) !important;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
    }
</style>

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="text-center p-5 bg-white rounded-4 shadow-lg" style="max-width: 400px; width: 100%; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(10px);">
        <!-- Luxurious Header -->
        <div class="mb-4">
            <div class="mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#4e54c8" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"/>
                </svg>
            </div>
            <h3 class="fw-bold text-gradient" style="background: linear-gradient(to right, #4e54c8, #8f94fb); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sistem Prediksi Stok</h3>
            <p class="text-muted mb-0">Toko Arnis</p>
            <div class="mt-2" style="height: 2px; background: linear-gradient(to right, transparent, #4e54c8, #8f94fb, transparent);"></div>
        </div>
        
        <!-- Luxurious Login Form -->
        <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-4">
            @csrf
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show small rounded-pill" role="alert" style="backdrop-filter: blur(5px);">
                    {{ session('error') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Username Field -->
            <div class="mb-3 position-relative">
                <input type="text" 
                       class="form-control text-center px-4 py-3 rounded-pill border-0 shadow-sm" 
                       id="username" 
                       name="username" 
                       placeholder="Username" 
                       required
                       style="background-color: rgba(245, 247, 250, 0.7);">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4e54c8" class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                    </svg>
                </span>
            </div>
            
            <!-- Password Field -->
            <div class="mb-4 position-relative">
                <input type="password" 
                       class="form-control text-center px-4 py-3 rounded-pill border-0 shadow-sm" 
                       id="password" 
                       name="password" 
                       placeholder="Password" 
                       required
                       style="background-color: rgba(245, 247, 250, 0.7);">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4e54c8" class="bi bi-lock-fill" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                    </svg>
                </span>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" 
                    class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow" 
                    style="background: linear-gradient(to right, #4e54c8, #8f94fb); border: none; letter-spacing: 1px;">
                LOGIN
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                </svg>
            </button>
        </form>
        
        <!-- Luxurious Footer -->
        <div class="mt-4 pt-3">
            <p class="small text-muted mb-0">© {{ date('Y') }} Sistem Prediksi Stok • Premium Edition</p>
        </div>
    </div>
</div>

@endsection