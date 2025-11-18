@extends('admin.master')
@section('title' , __('admins.Admins'))
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">{{ __('admins.Admins') }}</h1>
  <div class="mb-3">
    <form method="GET" action="{{ route('admin.admins.index') }}" class="d-flex">
      <input type="text" name="q" class="form-control me-2" placeholder=" {{ __('admins.Search by name or email') }}  "   value="{{ request('q') }}">
      <button type="submit" class="btn btn-primary">{{ __('admins.Search') }}</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
            <th>{{ __('#') }}</th>
          <th>{{ __('admins.Name') }}</th>
          <th>{{ __('admins.Email') }}</th>
          <th>{{ __('admins.Created At') }}</th>
     
        </tr>
      </thead>
      <tbody>
        @forelse($admins as $admin)
          <tr>
            <td>{{ ($admins->currentPage() - 1) * $admins->perPage() + $loop->iteration }}</td>
            <td>{{ $admin->name ?? '-' }}</td>
            <td>{{ $admin->email ?? '-' }}</td>
            <td>{{ $admin->created_at ? $admin->created_at->format('Y-m-d H:i') : '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">{{ __('admins.No results found') }}  </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $admins->appends(request()->query())->links() }}
</div>
@endsection

