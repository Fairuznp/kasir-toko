@extends('layouts.main', ['title' => 'Profile'])

@section('title-content')
    <i class="fas fa-user mr-2"></i>
    Profile
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 col-xl-4">
        @if (session('update') == 'success')
            <x-alert type="success">
                <strong>Berhasil diupdate!</strong> Profile berhasil diupdate.
            </x-alert>
        @endif

        <div class="card card-orange card-outline shadow">
            <div class="card-header bg-white border-bottom">
                <h3 class="card-title mb-0">
                    <i class="fas fa-id-card mr-2 text-orange"></i>
                    Informasi Profile
                </h3>
            </div>
            
            <div class="card-body p-3 p-md-4">
                <div class="profile-info">
                    <div class="info-item mb-3">
                        <label class="font-weight-semibold text-dark">
                            <i class="fas fa-user text-info mr-2"></i>
                            Nama Lengkap
                        </label>
                        <p class="mb-0 text-muted">{{ $user->nama }}</p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="font-weight-semibold text-dark">
                            <i class="fas fa-at text-success mr-2"></i>
                            Username
                        </label>
                        <p class="mb-0 text-muted">{{ $user->username }}</p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="font-weight-semibold text-dark">
                            <i class="fas fa-calendar-plus text-warning mr-2"></i>
                            Dibuat Tanggal
                        </label>
                        <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div class="info-item">
                        <label class="font-weight-semibold text-dark">
                            <i class="fas fa-calendar-edit text-secondary mr-2"></i>
                            Diupdate Tanggal
                        </label>
                        <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($user->updated_at)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light border-top text-center">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .card { border-radius: 1rem; border: none; }
    .card-header { border-radius: 1rem 1rem 0 0 !important; }
    .shadow { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    .profile-info .info-item { border-bottom: 1px solid #f8f9fa; padding-bottom: 1rem; }
    .profile-info .info-item:last-child { border-bottom: none; padding-bottom: 0; }
    @media (max-width: 767.98px) { .card-body { padding: 1.5rem !important; } }
    .btn:hover { transform: translateY(-1px); transition: all 0.3s ease; }
</style>
@endsection