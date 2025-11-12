@extends('admin.master')

@section('title', __('Payment Methods'))

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Payment Methods') }}</h1>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to user') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ $user->name }}</h6>
                <span class="badge badge-info">{{ __('Total Methods') }}: {{ $methods->count() }}</span>
            </div>
            <div class="card-body">
                @if ($methods->isEmpty())
                    <p class="text-center text-muted">{{ __('No payment methods found for this user.') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Provider') }}</th>
                                    <th>{{ __('Brand') }}</th>
                                    <th>{{ __('Last 4') }}</th>
                                    <th>{{ __('Expiry') }}</th>
                                    <th>{{ __('Gateway') }}</th>
                                    <th>{{ __('Default') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($methods as $method)
                                    <tr class="{{ $method->trashed() ? 'table-secondary' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $method->provider)) }}</td>
                                        <td>{{ $method->brand ?? '-' }}</td>
                                        <td>{{ $method->last4 ? '**** **** **** ' . $method->last4 : '-' }}</td>
                                        <td>
                                            @if ($method->exp_month && $method->exp_year)
                                                {{ sprintf('%02d/%s', $method->exp_month, substr($method->exp_year, -2)) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ strtoupper($method->gateway) }}</td>
                                        <td>
                                            @if ($method->is_default && !$method->trashed())
                                                <span class="badge badge-success">{{ __('Default') }}</span>
                                            @else
                                                <span class="badge badge-light">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($method->trashed())
                                                <span class="badge badge-warning">{{ __('Deleted') }}</span>
                                            @else
                                                <span class="badge badge-primary">{{ __('Active') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if (!$method->trashed() && !$method->is_default)
                                                    <form action="{{ route('admin.users.payment-methods.set-default', [$user->id, $method->id]) }}"
                                                          method="POST" class="mr-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-check"></i> {{ __('Set default') }}
                                                        </button>
                                                    </form>
                                                @endif

                                                @if (!$method->trashed())
                                                    <form action="{{ route('admin.users.payment-methods.destroy', [$user->id, $method->id]) }}"
                                                          method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.payment-methods.restore', [$user->id, $method->id]) }}"
                                                          method="POST" class="mr-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-undo"></i> {{ __('Restore') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

