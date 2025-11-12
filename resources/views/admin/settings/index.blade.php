@extends('admin.master')
@section('title', __('settings.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('settings.settings') }}</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>{{ __('settings.errors') }}</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ __('settings.success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="logo">{{ __('settings.logo') }}</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                            @if(!empty($settings['logo']))
                                <img src="{{ asset('storage/' . $settings['logo']) }}" alt="{{ $settings['app_name'] ?? 'Logo' }}" width="100" height="100">
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="app_name">{{ __('settings.site_name') }}</label>
                            <input type="text" class="form-control" id="app_name" name="app_name" value="{{ $settings['app_name'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label for="contact_email">{{ __('settings.contact_email') }}</label>
                            <input type="email" class="form-control" id="contact_email" name="email" value="{{ $settings['email'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label for="phone">{{ __('settings.phone') }}</label>
                            <input type="number" class="form-control" id="phone" name="phone" value="{{ $settings['phone'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label for="address">{{ __('settings.address') }}</label>
                            <textarea class="form-control" id="address" name="address">{{ $settings['address'] ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('settings.description') }}</label>
                            <textarea class="form-control" id="description" name="description">{{ $settings['description'] ?? '' }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('settings.save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
