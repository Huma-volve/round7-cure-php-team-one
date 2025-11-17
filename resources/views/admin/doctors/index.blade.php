@extends('admin.master')
@section('title', 'الأطباء')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('doctors.Doctors') }}</h1>
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('doctors.Add New Doctor') }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.doctors.index') }}" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control"
                    placeholder="{{ __('doctors.Search by name or email') }}" value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">{{ __('doctors.All statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('doctors.Active') }}
                    </option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                        {{ __('doctors.Inactive') }}</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>
                        {{ __('doctors.Suspended') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('doctors.Search') }}</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('doctors.Name') }}</th>
                        <th>{{ __('doctors.Email') }}</th>
                        <th> {{ __('doctors.Specialty') }}</th>
                        <th>{{ __('doctors.License Number') }}</th>
                        <th> {{ __('doctors.Session Price') }} </th>
                        <th> {{ __('doctors.Status') }}</th>
                        <th>{{ __('doctors.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                        <tr @if ($doctor->trashed()) class="table-secondary opacity-0.5" @endif>
                            <td>{{ ($doctors->currentPage() - 1) * $doctors->perPage() + $loop->iteration }}</td>
                            <td>{{ $doctor->user->name ?? '-' }}</td>
                            <td>{{ $doctor->user->email ?? '-' }}</td>
                            <td>{{ $doctor->specialty->name ?? '-' }}</td>
                            <td>{{ $doctor->license_number ?? '-' }}</td>
                            <td>{{ $doctor->session_price ?? '-' }} EGP</td>
                            <td>
                                @if ($doctor->trashed())
                                    <span class="badge badge-secondary">-</span>
                                @else
                                    <span
                                        class="badge badge-{{ $doctor->status == 'active' ? 'success' : ($doctor->status == 'suspended' ? 'danger' : 'warning') }}">
                                        {{ $doctor->status == 'active' ? __('doctors.Active') : ($doctor->status == 'suspended' ? __('doctors.Suspended') : __('doctors.Inactive')) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if (!$doctor->trashed())
                                        <a href="{{ route('admin.doctors.show', $doctor->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('doctors.View') }}
                                        </a>
                                        <a href="{{ route('admin.doctors.edit', $doctor->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> {{ __('doctors.Edit') }}
                                        </a>
                                        <form action="{{ route('admin.doctors.toggleStatus', $doctor->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-{{ $doctor->status == 'active' ? 'warning' : 'success' }}">
                                                <i class="fas fa-{{ $doctor->status == 'active' ? 'ban' : 'check' }}"></i>
                                                {{ $doctor->status == 'active' ? __('doctors.Suspend') : __('doctors.Enable') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('{{ __('doctors.Are you sure you want to delete this doctor?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> {{ __('doctors.Delete') }}
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-ban"></i> {{ __('doctors.Deleted') }}
                                        </button>
                                        <form action="{{ route('admin.doctors.restore', $doctor->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success"> {{ __('doctors.Restore') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ __('doctors.No results found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $doctors->appends(request()->query())->links() }}
    </div>
@endsection
