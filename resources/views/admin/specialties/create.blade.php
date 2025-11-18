@extends('admin.master')
@section('title', __('specialties.Create Specialty'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{__('specialties.Create Specialty')}}</h1>
        <a href="{{ route('admin.specialties.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('specialties.Back to Specialties') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{__('specialties.Specialty Information')}}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.specialties.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('specialties.Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="{{ __('specialties.Enter specialty name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="image">{{ __('specialties.Image') }} <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror"
                                       id="image" name="image" accept="image/*" required>
                                <label class="custom-file-label" for="image">{{ __('specialties.Choose Image') }}</label>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                {{ __('specialties.Allowed formats: JPEG, PNG, JPG, GIF. Max size: 2MB') }}
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Image Preview --}}
                <div class="row mb-3">
                    <div class="col-md-6 offset-md-6">
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img id="preview" src="#" alt="{{ __('specialties.Image Preview') }}"
                                 style="max-width: 200px; max-height: 200px; border-radius: 5px; border: 1px solid #ddd;">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('specialties.Save') }}
                    </button>
                    <a href="{{ route('admin.specialties.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> {{ __('specialties.Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview image before upload
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    const file = e.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }

        reader.readAsDataURL(file);

        // Update file input label
        const fileName = file.name;
        const nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    } else {
        previewContainer.style.display = 'none';
        const nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = '{{ __("specialties.Choose Image") }}';
    }
});

// Bootstrap file input label
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = document.getElementById("image").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>

<style>
.custom-file-input:lang(ar) ~ .custom-file-label::after {
    content: "اختيار";
}
</style>
@endsection
