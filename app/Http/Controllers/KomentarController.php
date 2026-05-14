<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class KomentarController extends Controller
{
    /**
     * Tampilkan daftar komentar/feedback pasien.
     * GET /admin/komentar
     */
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = (int) $request->input('per_page', 10);

        $query = Feedback::with('pasien')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('pasien', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%")
                       ->orWhere('no_rm', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at');

        $komentars = $query->paginate($perPage)->withQueryString();
        $avgRating = Feedback::avg('penilaian');

        return view('admin.komentar', compact('komentars', 'avgRating', 'search', 'perPage'));
    }

    /**
     * Hapus feedback.
     * DELETE /admin/komentar/{id}
     */
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()
            ->route('admin.komentar')
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
