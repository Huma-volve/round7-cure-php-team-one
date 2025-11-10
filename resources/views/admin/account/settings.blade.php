@extends('admin.master')

@section('content')
<div class="container-fluid">
	<h1 class="h3 mb-4 text-gray-800">Settings</h1>

	@if (session('status'))
		<div class="alert alert-success">{{ session('status') }}</div>
	@endif

	<div class="row">
		<div class="col-lg-6">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('admin.account.settings.password') }}" method="POST">
						@csrf
						@method('PUT')
						<div class="form-group">
							<label for="current_password">Current Password</label>
							<input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
							@error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="form-group">
							<label for="password">New Password</label>
							<input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
							@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="form-group">
							<label for="password_confirmation">Confirm Password</label>
							<input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
						</div>
						<button type="submit" class="btn btn-primary">Update Password</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Language</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('admin.account.settings.language') }}" method="POST">
						@csrf
						@method('PUT')
						<div class="form-group">
							<label for="preferred_locale">Preferred Language</label>
							<select id="preferred_locale" name="preferred_locale" class="form-control @error('preferred_locale') is-invalid @enderror">
								<option value="en" {{ old('preferred_locale', $user->preferred_locale ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
								<option value="ar" {{ old('preferred_locale', $user->preferred_locale ?? 'en') === 'ar' ? 'selected' : '' }}>العربية</option>
							</select>
							@error('preferred_locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<button type="submit" class="btn btn-primary">Save Language</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


