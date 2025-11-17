@extends('admin.master')
@section('title', __('patients.Patients'))

@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('patients.Patients') }}</h1>
    <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i>{{ __('patients.Add New Patient') }}
    </a>
  </div>

  <form method="GET" action="{{ route('admin.patients.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="text" name="q" class="form-control" placeholder="{{ __('patients.Search by name or email') }}" value="{{ request('q') }}">
    </div>
    <div class="col-md-3">
      <select name="gender" class="form-control">
        <option value="">{{ __('patients.All Genders') }}</option>
        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>{{ __('patients.Male') }}</option>
        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>{{ __('patients.Male') }}</option>
        <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>{{ __('patients.Other') }}</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">{{ __('patients.Search') }}</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('patients.Name') }}</th>
          <th>{{ __('patients.Email') }}</th>
          <th>{{ __('patients.Gender') }}</th>
          <th> {{ __('patients.Birthdate') }}</th>
          <th>{{ __('patients.Created At') }}</th>
          <th>{{ __('patients.Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($patients as $patient)
          <tr>
            <td>{{ ($patients->currentPage() - 1) * $patients->perPage() + $loop->iteration }}</td>
            <td>{{ $patient->user->name ?? '-' }}</td>
            <td>{{ $patient->user->email ?? '-' }}</td>
            <td>
              @if($patient->gender)
                <span class="badge badge-info">
                  {{ $patient->gender == 'male' ? 'ذكر' : ($patient->gender == 'female' ? 'أنثى' : 'آخر') }}
                </span>
              @else
                -
              @endif
            </td>
            <td>{{ $patient->birthdate ? $patient->birthdate->format('Y-m-d') : '-' }}</td>
            <td>{{ $patient->created_at->format('Y-m-d H:i') }}</td>
            <td>
              <div class="btn-group" role="group">
                <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-sm btn-info">
                  <i class="fas fa-eye"></i> {{ __('patients.View') }}
                </a>
                <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-sm btn-primary">
                  <i class="fas fa-edit"></i>  {{ __('patients.Edit') }}
                </a>
                <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المريض؟');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> {{ __('patients.Delete') }}
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">{{ __('patients.No Results Found') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $patients->appends(request()->query())->links() }}
</div>
@endsection

