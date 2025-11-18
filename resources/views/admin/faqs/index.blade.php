@extends('admin.master')
@section('title', 'إدارة الأسئلة الشائعة')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">الأسئلة الشائعة</h1>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة سؤال
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الأسئلة</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>السؤال</th>
                            <th>ترتيب العرض</th>
                            <th>الحالة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td>{{ $faq->id }}</td>
                                    <td>
                                        <div>{{ $faq->getTranslatedQuestion('ar') }}</div>
                                        <div class="text-muted small">{{ $faq->getTranslatedQuestion('en') }}</div>
                                    </td>
                                <td>{{ $faq->display_order }}</td>
                                <td>
                                    <span class="badge badge-{{ $faq->is_active ? 'success' : 'secondary' }}">
                                        {{ $faq->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    لا توجد أسئلة شائعة مضافة بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($faqs->hasPages())
            <div class="card-footer">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

