<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar laporan
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $reports = Report::with('user', 'program')->where('user_id', $user->id)->get();
        return response()->json($reports, Response::HTTP_OK);
    }

    /**
     * Menampilkan laporan spesifik berdasarkan ID
     */
    public function show($id)
    {
        $report = Report::with('user', 'program')->find($id);

        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Menyimpan laporan baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'jumlah_penerima' => 'required|numeric|gt:0',
            'tanggal_penyaluran' => 'required|date',
            'bukti_penyaluran' => 'nullable|max:2048',
            'catatan_tambahan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        //  menyimpan file bukti penyaluran
        $filePath = null;
        if ($request->hasFile('bukti_penyaluran')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('bukti_penyaluran')->getRealPath())->getSecurePath();
            $filePath = $uploadedFileUrl;
        }
        $report = new Report();
        $report->user_id = auth()->user()->id; // Menyimpan ID pengguna yang membuat laporan
        $report->program_id = $request->program_id;
        $report->provinsi = $request->provinsi;
        $report->kabupaten = $request->kabupaten;
        $report->kecamatan = $request->kecamatan;
        $report->jumlah_penerima = $request->jumlah_penerima;
        $report->tanggal_penyaluran = $request->tanggal_penyaluran;
        $report->bukti_penyaluran = $filePath;
        $report->catatan_tambahan = $request->catatan_tambahan;
        $report->status = 'pending'; // Status default adalah 'pending'
        $report->save();

        return response()->json(['message' => 'Laporan berhasil dibuat', 'data' => $report], Response::HTTP_CREATED);
    }

    /**
     * Mengupdate laporan
     */
    public function update(Request $request, $id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        // Cek apakah laporan sudah diverifikasi
        if ($report->status != 'pending') {
            return response()->json(['message' => 'Laporan sudah diverifikasi dan tidak dapat diubah'], Response::HTTP_BAD_REQUEST);
        }

        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'jumlah_penerima' => 'required|numeric|gt:0',
            'tanggal_penyaluran' => 'required|date',
            'bukti_penyaluran' => 'nullable|max:2048',
            'catatan_tambahan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        // Update file bukti penyaluran jika ada
        if ($request->hasFile('bukti_penyaluran')) {
            if ($report->bukti_penyaluran) {
                // Optionally, you can delete the old image from Cloudinary
                $publicId = pathinfo($report->bukti_penyaluran, PATHINFO_FILENAME);
                Cloudinary::destroy($publicId);
            }

            $uploadedFileUrl = Cloudinary::upload($request->file('bukti_penyaluran')->getRealPath())->getSecurePath();
            $report->bukti_penyaluran = $uploadedFileUrl;
        }

        $report->program_id = $request->program_id;
        $report->provinsi = $request->provinsi;
        $report->kabupaten = $request->kabupaten;
        $report->kecamatan = $request->kecamatan;
        $report->jumlah_penerima = $request->jumlah_penerima;
        $report->tanggal_penyaluran = $request->tanggal_penyaluran;
        $report->catatan_tambahan = $request->catatan_tambahan;
        $report->save();

        return response()->json(['message' => 'Laporan berhasil diperbarui', 'data' => $report], Response::HTTP_OK);
    }

    /**
     * Menghapus laporan
     */
    public function destroy($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        // Cek apakah laporan sudah diverifikasi
        if ($report->status != 'pending') {
            return response()->json(['message' => 'Laporan sudah diverifikasi dan tidak dapat dihapus'], Response::HTTP_BAD_REQUEST);
        }

        // hapus gambar di cloudinary
        if ($report->bukti_penyaluran) {
            $publicId = pathinfo($report->bukti_penyaluran, PATHINFO_FILENAME);
            Cloudinary::destroy($publicId);
        }

        $report->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus'], Response::HTTP_OK);
    }

    public function verifyIndex()
    {
        $reports = Report::with('user', 'program')->get();
        return response()->json($reports, Response::HTTP_OK);
    }

    /**
     * Menyetujui laporan
     */
    public function approveReport($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        if ($report->status != 'pending') {
            return response()->json(['message' => 'Laporan sudah diverifikasi'], Response::HTTP_BAD_REQUEST);
        }

        $report->status = 'disetujui';
        $report->save();

        return response()->json(['message' => 'Laporan disetujui'], Response::HTTP_OK);
    }

    /**
     * Menolak laporan dengan alasan
     */
    public function rejectReport(Request $request, $id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        if ($report->status != 'pending') {
            return response()->json(['message' => 'Laporan sudah diverifikasi'], Response::HTTP_BAD_REQUEST);
        }

        $validator = Validator::make($request->all(), [
            'alasan_penolakan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $report->status = 'ditolak';
        $report->alasan_penolakan = $request->alasan_penolakan;
        $report->save();

        return response()->json(['message' => 'Laporan ditolak', 'data' => $report], Response::HTTP_OK);
    }
}
