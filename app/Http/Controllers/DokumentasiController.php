<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Infografis;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class DokumentasiController extends Controller
{
    // --- ALBUM ---
    public function album()
    {
        $albums = Album::latest()->paginate(9);
        return view('dokumentasi.album', compact('albums'));
    }

    public function storeAlbum(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal' => 'nullable|date',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('album', 'public');
        }

        Album::create($data);

        return back()->with('success', 'Album berhasil ditambahkan!');
    }

    public function updateAlbum(Request $request, Album $album)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal' => 'nullable|date',
        ]);

        if ($request->hasFile('gambar')) {
            if ($album->gambar) {
                Storage::disk('public')->delete($album->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('album', 'public');
        }

        $album->update($data);

        return back()->with('success', 'Album berhasil diperbarui!');
    }

    public function destroyAlbum(Album $album)
    {
        if ($album->gambar) {
            Storage::disk('public')->delete($album->gambar);
        }
        $album->delete();
        return back()->with('success', 'Album berhasil dihapus!');
    }

    // --- INFOGRAFIS ---
    public function infografis()
    {
        $infografis = Infografis::latest()->paginate(8);
        return view('dokumentasi.infografis', compact('infografis'));
    }

    public function storeInfografis(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('infografis', 'public');
        }

        Infografis::create($data);

        return back()->with('success', 'Infografis berhasil ditambahkan!');
    }

    public function updateInfografis(Request $request, Infografis $infografis)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('gambar')) {
            if ($infografis->gambar) {
                Storage::disk('public')->delete($infografis->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('infografis', 'public');
        }

        $infografis->update($data);

        return back()->with('success', 'Infografis berhasil diperbarui!');
    }

    public function destroyInfografis(Infografis $infografis)
    {
        if ($infografis->gambar) {
            Storage::disk('public')->delete($infografis->gambar);
        }
        $infografis->delete();
        return back()->with('success', 'Infografis berhasil dihapus!');
    }

    // --- VIDEO ---
    public function video()
    {
        $videos = Video::latest()->paginate(6);
        return view('dokumentasi.video', compact('videos'));
    }

    public function storeVideo(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'url' => 'required|url',
            'deskripsi' => 'nullable|string',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('video', 'public');
        }

        Video::create($data);

        return back()->with('success', 'Video berhasil ditambahkan!');
    }

    public function updateVideo(Request $request, Video $video)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'url' => 'required|url',
            'deskripsi' => 'nullable|string',
        ]);

        if ($request->hasFile('gambar')) {
            if ($video->gambar) {
                Storage::disk('public')->delete($video->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('video', 'public');
        }

        $video->update($data);

        return back()->with('success', 'Video berhasil diperbarui!');
    }

    public function destroyVideo(Video $video)
    {
        if ($video->gambar) {
            Storage::disk('public')->delete($video->gambar);
        }
        $video->delete();
        return back()->with('success', 'Video berhasil dihapus!');
    }

    public function publicVideo()
    {
        $videos = Video::latest()->get();
        return view('video', compact('videos'));
    }

    public function publicAlbum()
    {
        $albums = Album::latest()->get();
        return view('album', compact('albums'));
    }

    public function publicInfografis()
    {
        $infografis = Infografis::latest()->get();
        return view('infografis', compact('infografis'));
    }
}
