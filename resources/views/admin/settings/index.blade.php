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
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $settings['phone'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label for="address">{{ __('settings.address') }}</label>
                            <textarea class="form-control" id="address" name="address">{{ $settings['address'] ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('settings.description') }}</label>
                            <textarea class="form-control" id="description" name="description">{{ $settings['description'] ?? '' }}</textarea>
                        </div>

                        <hr>
                        <h5 class="mb-3">{{ __('settings.footer_section') }}</h5>

                        <div class="form-group">
                            <label for="footer_tagline">{{ __('settings.footer_tagline') }}</label>
                            <textarea class="form-control" id="footer_tagline" name="footer_tagline" rows="3">{{ $settings['footer_tagline'] ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="footer_phone">{{ __('settings.footer_phone') }}</label>
                            <input type="text" class="form-control" id="footer_phone" name="footer_phone" value="{{ $settings['footer_phone'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label for="footer_email">{{ __('settings.footer_email') }}</label>
                            <input type="email" class="form-control" id="footer_email" name="footer_email" value="{{ $settings['footer_email'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label for="footer_address">{{ __('settings.footer_address') }}</label>
                            <textarea class="form-control" id="footer_address" name="footer_address" rows="2">{{ $settings['footer_address'] ?? '' }}</textarea>
                        </div>

                        <hr>
                        <h5 class="mb-3">{{ __('settings.social_section') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="social_facebook">{{ __('settings.social_facebook') }}</label>
                                    <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="social_whatsapp">{{ __('settings.social_whatsapp') }}</label>
                                    <input type="url" class="form-control" id="social_whatsapp" name="social_whatsapp" value="{{ $settings['social_whatsapp'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="social_youtube">{{ __('settings.social_youtube') }}</label>
                                    <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="social_linkedin">{{ __('settings.social_linkedin') }}</label>
                                    <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" value="{{ $settings['social_linkedin'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('settings.save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
