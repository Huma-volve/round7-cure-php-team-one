@extends('admin.master')
@section('title', __('specialties.Specialties'))

@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('specialties.Specialties') }}</h1>
    <a href="{{ route('admin.specialties.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> {{ __('specialties.Add New Specialty') }}
    </a>
  </div>

  <form method="GET" action="{{ route('admin.specialties.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="text" name="q" class="form-control" placeholder="{{ __('specialties.Search by name') }}" value="{{ request('q') }}">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">{{ __('specialties.Search') }}</button>
    </div>
    <div class="col-md-2">
      <a href="{{ route('admin.specialties.index') }}" class="btn btn-secondary w-100">{{ __('specialties.Reset') }}</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>

          <th>{{ __('#') }}</th>
          <th>{{ __('specialties.Image') }}</th>
          <th>{{ __('specialties.Name') }}</th>
          <th>{{ __('specialties.Created At') }}</th>
          <th>{{ __('specialties.Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($specialties as $specialty)
          <tr>
             <td>{{ ($specialties->currentPage() - 1) * $specialties->perPage() + $loop->iteration }}</td>
            <td>
              @if($specialty->image)
                <img src="{{ '/storage/' . $specialty->image }}"
                     alt="{{ $specialty->name }}"
                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
              @else
                <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-image text-muted"></i>
                </div>
              @endif
            </td>
            <td>{{ $specialty->name }}</td>
            <td>{{ $specialty->created_at->format('Y-m-d H:i') }}</td>
            <td>
              <div class="btn-group" role="group">
                <a href="{{ route('admin.specialties.show', $specialty->id) }}" class="btn btn-sm btn-info" title="{{ __('specialties.View') }}">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.specialties.edit', $specialty->id) }}" class="btn btn-sm btn-primary" title="{{ __('specialties.Edit') }}">
                  <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.specialties.destroy', $specialty->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('specialties.Delete Confirmation') }}');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" title="{{ __('specialties.Delete') }}">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">{{ __('specialties.No Specialties Found') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>


    @if($specialties->hasPages())
    <div class="d-flex justify-content-center mt-4">
      {{ $specialties->appends(request()->query())->links() }}
    </div>
  @endif

</div>
@endsection
