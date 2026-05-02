<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    private function productManagementRedirect(string $message)
    {
        return redirect()
            ->route('admin.dashboard', ['section' => 'products'])
            ->with('success', $message);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('parent')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $data = $request->only(['name', 'description', 'parent_id']);
        $data['is_active'] = $request->boolean('status', true);
        $data['slug'] = Str::slug($request->name);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        $category = Category::create($data);

        return $this->productManagementRedirect('Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category->load('parent', 'children'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $data = $request->only(['name', 'description', 'parent_id']);
        $data['is_active'] = $request->boolean('status', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $imagePath = $request->file('image')->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        // Update slug if name changed
        if ($request->name !== $category->name) {
            $data['slug'] = Str::slug($request->name);
        }

        $category->update($data);

        return $this->productManagementRedirect('Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Delete associated image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return $this->productManagementRedirect('Category deleted successfully.');
    }

    /**
     * Get categories for dropdown
     */
    public function getCategories()
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return response()->json($categories);
    }
}
