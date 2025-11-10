@extends('admin.master')

@section('content')
<div class="container-fluid">
	<h1 class="h3 mb-4 text-gray-800">Profile</h1>

	@if (session('status'))
		<div class="alert alert-success">{{ session('status') }}</div>
	@endif

	<div class="row">
		<div class="col-lg-6">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Update Profile</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('admin.account.profile.update') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="form-group">
							<label for="name">Name</label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
							@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="form-group">
							<label for="email">Email</label>
							<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
							@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="form-group">
							<label for="avatar">Profile Image</label>
							<input type="file" class="form-control-file @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
							@error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
						</div>
						@if($user->profile_image_path)
							<div class="mb-3">
								<img src="{{ asset('storage/'.$user->profile_image_path) }}" class="img-thumbnail" style="max-height: 120px;">
							</div>
						@endif
						<button type="submit" class="btn btn-primary">Save</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


