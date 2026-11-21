<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    public function index()
    {
        // Mengambil seluruh daftar destinasi diurutkan dari yang terbaru
        $destinations = Destination::query()->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar destinasi berhasil diambil',
            'data' => $destinations
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input data destinasi baru
        $validatorDestination = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
        ]);

        // Generate slug unik otomatis dari kolom name (contoh: "Kawah Ijen" -> "kawah-ijen")
        $validatorDestination['slug'] = $this->generateUniqueSlug($validatorDestination['name']);

        $destination = Destination::create($validatorDestination);

        return response()->json([
            'success' => true,
            'message' => 'Destinasi berhasil ditambahkan',
            'data' => $destination
        ], 201);
    }

    public function show(string $slug)
    {
        // Cari detail destinasi berdasarkan slug; lempar 404 jika tidak ditemukan
        $destination = Destination::where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Detail destinasi berhasil diambil',
            'data' => $destination
        ]);
    }

    public function update(Request $request, string $id)
    {
        // Cari destinasi berdasarkan ID; lempar 404 jika tidak ditemukan
        $destination = Destination::findOrFail($id);

        // 'sometimes' membuat validasi hanya berjalan jika field tersebut dikirim di request
        $validatorUpdateDestination = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        // Generate ulang slug hanya jika kolom 'name' ikut diubah
        if (isset($validatorUpdateDestination['name'])) {
            // Kirim $destination->id agar baris ini sendiri tidak dianggap bentrok saat cek slug
            $validatorUpdateDestination['slug'] = $this->generateUniqueSlug(
                $validatorUpdateDestination['name'],
                $destination->id
            );
        }

        // Simpan perubahan data yang sudah tervalidasi
        $destination->update($validatorUpdateDestination);

        return response()->json([
            'success' => true,
            'message' => 'Destinasi berhasil diperbarui',
            'data' => $destination->fresh()
        ]);
    }

    public function destroy(string $id)
    {
        $destination = Destination::findOrFail($id);
        $destination->delete();

        return response()->json([
            'success' => true,
            'message' => 'Destinasi berhasil dihapus dari daftar'
        ]);
    }

    // $ignoreid = null: diisi ID destinasi saat update agar ID tersebut dikecualikan dari pengecekan bentrok slug
    private function generateUniqueSlug(string $name, ?int $ignoreid = null): string
    {
        $baseSlug = Str::slug($name); // Ubah nama jadi format slug (cth: "Kawah Ijen" -> "kawah-ijen")
        $slug = $baseSlug; // Kandidat slug awal sebelum dicek ke database
        $counter = 1; // Angka awal akhiran jika slug bentrok (cth: "kawah-ijen-1", lalu naik jadi 2, 3, dst.)

        // Ulangi selama slug sudah dipakai data lain (selain $ignoreid jika sedang update)
        while (
            Destination::where('slug', $slug)
                ->when($ignoreid, fn ($query) => $query->where('id', '!=', $ignoreid))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
