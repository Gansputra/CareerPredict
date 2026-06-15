<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SponsorBannerController extends Controller
{
    public function index()
    {
        $sponsors = SponsorBanner::orderBy('order', 'asc')->get();
        return view('admin.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        return view('admin.sponsors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link_url' => 'required|url|max:2048',
            'cta_text' => 'required|string|max:50',
            'order' => 'required|integer|min:0',
            'image' => 'required|image|mimes:webp,png,jpg,jpeg,gif|max:2048',
        ]);

        $imagePath = $request->file('image')->store('sponsors', 'public');

        SponsorBanner::create([
            'title' => $request->title,
            'link_url' => $request->link_url,
            'cta_text' => $request->cta_text,
            'order' => $request->order,
            'is_active' => $request->has('is_active'),
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.sponsors.index')->with('success', 'Banner sponsor berhasil ditambahkan!');
    }

    public function edit(SponsorBanner $sponsor)
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, SponsorBanner $sponsor)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link_url' => 'required|url|max:2048',
            'cta_text' => 'required|string|max:50',
            'order' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:webp,png,jpg,jpeg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'link_url' => $request->link_url,
            'cta_text' => $request->cta_text,
            'order' => $request->order,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if (Storage::disk('public')->exists($sponsor->image_path)) {
                Storage::disk('public')->delete($sponsor->image_path);
            }
            // Upload new image
            $data['image_path'] = $request->file('image')->store('sponsors', 'public');
        }

        $sponsor->update($data);

        return redirect()->route('admin.sponsors.index')->with('success', 'Banner sponsor berhasil diperbarui!');
    }

    public function destroy(SponsorBanner $sponsor)
    {
        // Delete image
        if (Storage::disk('public')->exists($sponsor->image_path)) {
            Storage::disk('public')->delete($sponsor->image_path);
        }

        $sponsor->delete();

        return redirect()->route('admin.sponsors.index')->with('success', 'Banner sponsor berhasil dihapus!');
    }
}
