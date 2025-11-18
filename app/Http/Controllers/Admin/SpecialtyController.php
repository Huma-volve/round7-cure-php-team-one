<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpecialtyController extends Controller
{
    /**
     * عرض قائمة التخصصات
     */
    public function index(Request $request)
    {

        $query = $request->input('q');

        if ($query) {
            $specialties = Specialty::scopeSearch(  $query , $query)->paginate();
            return view('admin.specialties.index', compact('specialties'));
        }
        $specialties = Specialty::latest()->paginate();
        return view('admin.specialties.index', compact('specialties'));
    }

    /**
     * عرض نموذج إنشاء تخصص جديد
     */
    public function create()
    {
        return view('admin.specialties.create');
    }

    /**
     * حفظ تخصص جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // رفع الصورة
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('specialties', 'public');
            $validated['image'] = $imagePath;
        }

        Specialty::create($validated);

        return redirect()->route('admin.specialties.index')
            ->with('success', 'تم إنشاء التخصص بنجاح');
    }

    /**
     * عرض تخصص محدد
     */
    public function show(Specialty $specialty)
    {
            $doctors = $specialty->doctors()->with('user')->get();

        return view('admin.specialties.show', compact('doctors', 'specialty'));
    }

    /**
     * عرض نموذج تعديل تخصص
     */
    public function edit(Specialty $specialty)
    {
        return view('admin.specialties.edit', compact('specialty'));
    }

    /**
     * تحديث التخصص
     */
    public function update(Request $request, Specialty $specialty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name,' . $specialty->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // تحديث الصورة إذا تم رفع جديدة
      // في store و update functions
if ($request->hasFile('image')) {
    $imagePath = $request->file('image')->store('specialties', 'public');
    $validated['image'] = $imagePath;

        } else {
            unset($validated['image']);
        }

        $specialty->update($validated);

        return redirect()->route('admin.specialties.index')
            ->with('success', 'تم تحديث التخصص بنجاح');
    }

    /**
     * حذف تخصص
     */
    public function destroy(Specialty $specialty)
    {
        // حذف الصورة من التخزين
        if ($specialty->image) {
            Storage::disk('public')->delete($specialty->image);
        }

        $specialty->delete();

        return redirect()->route('admin.specialties.index')
            ->with('success', 'تم حذف التخصص بنجاح');
    }


}
