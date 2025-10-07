@extends('layouts.app')

@section('content')
<div class="container text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>🎯 Admin Dashboard</h3>
        <div>
            <!-- ✅ ปุ่มไปหน้าเดียวที่จัดการทุกอย่าง -->
            <a href="{{ route('admin.movies') }}" class="btn btn-light btn-sm">
                🎬 Manage All (Movies / Theatres / Showtimes)
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card bg-danger text-white text-center shadow-sm">
                <div class="card-body">
                    <h2 class="fw-bold">{{ $movieCount }}</h2>
                    <p class="mb-0">Movies</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white text-center shadow-sm">
                <div class="card-body">
                    <h2 class="fw-bold">{{ $showtimeCount }}</h2>
                    <p class="mb-0">Showtimes</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-secondary text-white text-center shadow-sm">
                <div class="card-body">
                    <h2 class="fw-bold">{{ $theatreCount }}</h2>
                    <p class="mb-0">Theatres</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <p class="text-muted small text-center">
            Cinema Tix Admin Panel — manage all data in one place 💼
        </p>
    </div>
</div>
@endsection

