<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Untuk mengambil daftar data destinasi
        $destination = Destination::query()->latest()->get();
        return response()->json([
            'success' => true,
            'message' => "Daftar destinasi berhasil diambil",
            'data' => $destination
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Menambah destinasi (Hanya Admin)
        $validatorDestination = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255']
        ]);

        // Membuat slug otomatis diambil dari kolom name (Kawah ijen -> kawah-ijen)
        $validatorDestination['slug'] = $this->generateUniqueSlug($validatorDestination['name']);
        $destination = Destination::create($validatorDestination);
        return response()->json([
            'success' => true,
            'message' => 'Destinasi berhasil ditambahkan',
            'data' => $destination
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        // Ambil detail destinasi berdasarkan kolom slug
        $destination = Destination::where('slug', $slug)->firstOrFail();
        return response()->json([
            'success' => true,
            'message' => 'Detail destinasi berhasil diambil',
            'data' => $destination
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Mengubah data destinasi (Hanya Admin)
        $destination = Destination::findOrFail($id);
        $validatorUpdateDestination = $request->validate([
            'name' => ['sometimes','required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]); // sometimes : digunakan untuk mengirimkan data yang ingin di ubah saja

        if (isset($validatorUpdateDestination['name'])) {
            $validatorUpdateDestination['slug'] = $this->generateUniqueSlug($validatorUpdateDestination['name'], $destination->id);
        }

        $destination->update($validatorUpdateDestination);
        return response()->json([
            'success' => true,
            'message' => 'Destinasi berhasil diperbarui',
            'data' => $destination->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $destination = Destination::findOrFail($id);
        $destination->delete();
        return response()->json([
            'success' => true,
            'message' => 'Destinasi berhasil dihapus dari daftar'
        ]);
    }


    /**
     * Generate slug otomatis
     */
    private function generateUniqueSlug(string $name, ?int $ignoreid = null) : string {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while(
            Destination::where('slug', $slug)
                ->when($ignoreid, fn ($query) => $query->where('id', '!=', $ignoreid))
                ->exists()
        ) {
            $slug = $baseSlug . "-" . $counter;
            $counter++;
        }

        return $slug;
    }
}
