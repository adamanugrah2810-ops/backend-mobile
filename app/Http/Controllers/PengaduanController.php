<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PengaduanController extends Controller
{
    /**
     * =========================
     * MASYARAKAT - KIRIM PENGADUAN
     * =========================
     */
    public function store(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'judul'      => 'required|string|max:255',
            'deskripsi'  => 'required|string',
            'kategori'   => 'required|string',
            'provinsi'   => 'required|string',
            'kota'       => 'required|string', // ✅ TAMBAHAN
            'kecamatan'  => 'required|string',
            'kelurahan'  => 'required|string',
            'foto'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Pastikan folder upload ada
        $uploadPath = public_path('uploads/pengaduan');
        if (! File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Upload foto
        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = time().'_'.$request->foto->getClientOriginalName();
            $request->foto->move($uploadPath, $fotoName);
        }

        $pengaduan = Pengaduan::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori'  => $request->kategori,
            'provinsi'  => $request->provinsi,
            'kota'      => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'foto'      => $fotoName,
            'status'    => 'diajukan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dikirim',
            'data' => $pengaduan,
        ], 201);
    }

    /**
     * =========================
     * MASYARAKAT - PENGADUAN SAYA
     * =========================
     */
    public function myPengaduan()
    {
        $data = Pengaduan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * =========================
     * ADMIN - SEMUA PENGADUAN
     * =========================
     */
    public function index()
    {
        if (! Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak',
            ], 403);
        }

        $data = Pengaduan::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * =========================
     * ADMIN - UPDATE STATUS
     * =========================
     */
    public function update(Request $request, $id)
    {
        if (! Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:diajukan,diproses,selesai,ditolak',
            'tanggapan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->status = $request->status;
        $pengaduan->tanggapan = $request->tanggapan;
        $pengaduan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil diperbarui',
            'data' => $pengaduan,
        ]);
    }

    /**
     * =========================
     * ADMIN - HAPUS
     * =========================
     */
    public function destroy($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        // Admin boleh hapus semua
        if (Auth::user()->role === 'admin') {
            $pengaduan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengaduan berhasil dihapus oleh admin',
            ]);
        }

        // User hanya boleh hapus miliknya sendiri
        if ($pengaduan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak menghapus pengaduan ini',
            ], 403);
        }

        // Optional: batasi status
        if ($pengaduan->status !== 'diajukan') {
            return response()->json([
                'success' => false,
                'message' => 'Pengaduan tidak dapat dihapus setelah diproses',
            ], 403);
        }

        $pengaduan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dihapus',
        ]);
    }
}
