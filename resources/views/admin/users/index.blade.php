@extends('admin.master')
@section('title' , __('users.Users'))
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">{{ __('users.Users') }}</h1>
  <div class="mb-3">
    <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex">
      <input type="text" name="q" class="form-control me-2" placeholder=" {{ __('users.Search by name or email') }}  "   value="{{ request('q') }}">
      <button type="submit" class="btn btn-primary">{{ __('users.Search') }}</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
            <th>{{ __('#') }}</th>
          <th>{{ __('users.Name') }}</th>
          <th>{{ __('users.Email') }}</th>
          <th>{{ __('users.Created At') }}</th>
          <th>{{ __('users.Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
            <td>{{ $user->name ?? '-' }}</td>
            <td>{{ $user->email ?? '-' }}</td>
            <td>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : '-' }}</td>
            <td>
              <div class="btn-group" role="group">
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">
                  <i class="fas fa-eye"></i> {{ __('users.View') }}
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                  <i class="fas fa-edit"></i> {{ __('users.Edit') }}
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <i class="fas fa-trash"></i> {{ __('users.Delete') }}
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">{{ __('users.No results found') }}  </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $users->appends(request()->query())->links() }}
</div>
@endsection

