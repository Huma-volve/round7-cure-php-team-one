@extends('admin.master')
@section('title', 'تعديل سؤال شائع')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل سؤال شائع</h1>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">بيانات السؤال</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
                @csrf
                @method('PUT')
                @include('admin.faqs._form', ['faq' => $faq])

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">تحديث</button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

