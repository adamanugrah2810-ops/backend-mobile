<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class PengaduanController extends Controller
{
    /**
     * =========================
     * MASYARAKAT - KIRIM PENGADUAN
     * =========================
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'judul'      => 'required|string|max:255',
            'deskripsi'  => 'required|string',
            'kategori'   => 'required|string',
            'provinsi'    => 'required|string',
            'kecamatan'  => 'required|string',
            'kelurahan'       => 'required|string',
            'foto'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Pastikan folder upload ada
        $uploadPath = public_path('uploads/pengaduan');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Upload foto
        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = time() . '_' . $request->foto->getClientOriginalName();
            $request->foto->move($uploadPath, $fotoName);
        }

        $pengaduan = Pengaduan::create([
            'user_id'   => Auth::id(),
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori'  => $request->kategori,
            'provinsi'   => $request->provinsi,
            'kecamatan' => $request->kecamatan,
            'kelueahan'      => $request->kelurahan,
            'foto'      => $fotoName,
            'status'    => 'diajukan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dikirim',
            'data'    => $pengaduan
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
            'data' => $data
        ]);
    }

    /**
     * =========================
     * ADMIN - SEMUA PENGADUAN
     * =========================
     */
    public function index()
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $data = Pengaduan::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * =========================
     * ADMIN - UPDATE STATUS
     * =========================
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status'    => 'required|in:diajukan,diproses,selesai,ditolak',
            'tanggapan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->status = $request->status;
        $pengaduan->tanggapan = $request->tanggapan;
        $pengaduan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil diperbarui',
            'data'    => $pengaduan
        ]);
    }

    /**
     * =========================
     * ADMIN - HAPUS
     * =========================
     */
    public function destroy($id)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dihapus'
        ]);
    }
}
