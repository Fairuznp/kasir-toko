@extends('layouts.main', ['title' => 'User'])

@section('title-content')
  <i class="fas fa-user-tie mr-2"></i>
  User
@endsection

@section('content')
  @if (session('store') == 'success')
    <x-alert type="success">
      <strong>Berhasil dibuat!</strong> User berhasil dibuat.
    </x-alert>
  @endif

  @if (session('update') == 'success')
    <x-alert type="success">
      <strong>Berhasil diupdate!</strong> User berhasil diupdate.
    </x-alert>
  @endif

  @if (session('destroy') == 'success')
    <x-alert type="success">
      <strong>Berhasil dihapus!</strong> User berhasil dihapus.
    </x-alert>
  @endif

  <div class="card card-orange card-outline shadow">
    <div class="card-header bg-white border-bottom">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div class="mb-3 mb-md-0">
          <a href="{{ route('user.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus mr-2"></i> 
            <span class="d-none d-sm-inline">Tambah User</span>
            <span class="d-sm-none">Tambah</span>
          </a>
        </div>
        <div>
          <form action="?" method="get" class="d-flex">
            <div class="input-group">
              <input type="text" class="form-control" name="search" value="<?= request()->search ?>" placeholder="Nama, Username">
              <div class="input-group-append">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <!-- Desktop Table -->
      <div class="d-none d-md-block">
        <table class="table table-striped table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th width="10%">#</th>
              <th width="35%">Nama</th>
              <th width="35%">Username</th>
              <th width="20%" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $key => $user)
              <tr>
                <td>{{ $users->firstItem() + $key }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle text-primary mr-2"></i>
                    {{ $user->nama }}
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <i class="fas fa-at text-success mr-2"></i>
                    {{ $user->username }}
                  </div>
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <a href="{{ route('user.edit', ['user' => $user->id]) }}"
                       class="btn btn-sm btn-outline-success" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" data-toggle="modal" data-target="#modalDelete"
                            data-url="{{ route('user.destroy', ['user' => $user->id]) }}"
                            class="btn btn-sm btn-outline-danger btn-delete" title="Hapus">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-5">
                  <div class="text-muted">
                    <i class="fas fa-user-tie fa-3x mb-3"></i>
                    <h5>Belum Ada User</h5>
                    <p>Silakan tambah user terlebih dahulu</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
      <div class="d-md-none">
        @forelse ($users as $key => $user)
          <div class="card border-0 border-bottom rounded-0">
            <div class="card-body py-3">
              <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center mb-1">
                    <span class="badge badge-secondary mr-2">{{ $users->firstItem() + $key }}</span>
                    <i class="fas fa-user-circle text-primary mr-2"></i>
                    <h6 class="mb-0 font-weight-semibold">{{ $user->nama }}</h6>
                  </div>
                  <div class="text-sm">
                    <i class="fas fa-at text-success mr-1"></i>
                    <strong>Username:</strong> {{ $user->username }}
                  </div>
                </div>
                <div class="btn-group" role="group">
                  <a href="{{ route('user.edit', ['user' => $user->id]) }}"
                     class="btn btn-sm btn-outline-success">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button type="button" data-toggle="modal" data-target="#modalDelete"
                          data-url="{{ route('user.destroy', ['user' => $user->id]) }}"
                          class="btn btn-sm btn-outline-danger btn-delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-5">
            <div class="text-muted">
              <i class="fas fa-user-tie fa-3x mb-3"></i>
              <h5>Belum Ada User</h5>
              <p>Silakan tambah user terlebih dahulu</p>
              <a href="{{ route('user.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus mr-2"></i>Tambah User Pertama
              </a>
            </div>
          </div>
        @endforelse
      </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small d-none d-md-block">
          Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} data
        </div>
        <div class="ml-auto">
          {{ $users->links('vendor.pagination.bootstrap-4') }}
        </div>
      </div>
    </div>
    @endif
  </div>

<style>
  .card { border-radius: 1rem; border: none; }
  .card-header { border-radius: 1rem 1rem 0 0 !important; }
  .shadow { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
  .text-sm { font-size: 0.875rem; }
  @media (max-width: 767.98px) { .card-body { padding: 1rem !important; } .btn-lg { padding: 0.5rem 1rem; font-size: 1rem; } }
  .btn:hover { transform: translateY(-1px); transition: all 0.3s ease; }
  .table th { border-top: none; font-weight: 600; font-size: 0.875rem; color: #495057; }
  .card.border-bottom:last-child { border-bottom: none !important; }
  .btn-group .btn { border-radius: 0.25rem !important; margin-left: 2px; }
  .btn-group .btn:first-child { margin-left: 0; }
</style>
@endsection

@push('modals')
  <x-modal-delete />
@endpush