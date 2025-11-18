<div class="form-group">
    <label for="question_en">السؤال (English) <span class="text-danger">*</span></label>
    <input type="text" name="question_en" id="question_en"
           class="form-control @error('question_en') is-invalid @enderror"
           value="{{ old('question_en', optional($faq)->question_en ?? optional($faq)->question) }}" required>
    @error('question_en')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="answer_en">الإجابة (English) <span class="text-danger">*</span></label>
    <textarea name="answer_en" id="answer_en" rows="5"
              class="form-control @error('answer_en') is-invalid @enderror"
              required>{{ old('answer_en', optional($faq)->answer_en ?? optional($faq)->answer) }}</textarea>
    @error('answer_en')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="question_ar">السؤال (عربي)</label>
    <input type="text" name="question_ar" id="question_ar"
           class="form-control @error('question_ar') is-invalid @enderror"
           value="{{ old('question_ar', optional($faq)->question_ar ?? optional($faq)->question) }}">
    @error('question_ar')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="answer_ar">الإجابة (عربي)</label>
    <textarea name="answer_ar" id="answer_ar" rows="5"
              class="form-control @error('answer_ar') is-invalid @enderror">{{ old('answer_ar', optional($faq)->answer_ar ?? optional($faq)->answer) }}</textarea>
    @error('answer_ar')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="display_order">ترتيب العرض</label>
        <input type="number" min="0" name="display_order" id="display_order"
               class="form-control @error('display_order') is-invalid @enderror"
               value="{{ old('display_order', optional($faq)->display_order ?? 0) }}">
        @error('display_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group col-md-6 d-flex align-items-center">
        <div class="custom-control custom-switch mt-4">
            <input type="checkbox" class="custom-control-input" id="is_active"
                   name="is_active" value="1"
                   {{ old('is_active', optional($faq)->is_active ?? true) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_active">نشط</label>
        </div>
    </div>
</div>

