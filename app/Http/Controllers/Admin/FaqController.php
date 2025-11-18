<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::orderBy('display_order')->orderByDesc('created_at')->paginate(15);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('admin.faqs.create');
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        $data = $this->prepareFaqData($request->validated());
        Faq::create($data);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'تم إنشاء سؤال شائع جديد بنجاح.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $data = $this->prepareFaqData($request->validated());
        $faq->update($data);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'تم تحديث السؤال الشائع بنجاح.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'تم حذف السؤال الشائع بنجاح.');
    }

    protected function prepareFaqData(array $data): array
    {
        $data['question'] = $data['question_en'];
        $data['answer'] = $data['answer_en'];

        if (empty($data['question_ar'])) {
            $data['question_ar'] = $data['question_en'];
        }

        if (empty($data['answer_ar'])) {
            $data['answer_ar'] = $data['answer_en'];
        }

        return $data;
    }
}

