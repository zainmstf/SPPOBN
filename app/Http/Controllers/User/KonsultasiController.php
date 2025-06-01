<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Konsultasi;
use App\Models\Fakta;
use App\Models\Solusi;
use App\Services\ForwardChainingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class KonsultasiController extends Controller
{
    public function index()
    {
        $konsultasi = Konsultasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('konsultasi.index', compact('konsultasi'));
    }

    public function create()
    {
        return view('konsultasi.create');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Buat konsultasi baru
            $konsultasi = Konsultasi::create([
                'user_id' => Auth::id(),
                'status' => 'sedang_berjalan',
                'sesi' => '1'
            ]);

            // Mulai forward chaining
            $chainingService = new ForwardChainingService($konsultasi);
            $result = $chainingService->getPertanyaanSelanjutnya();

            DB::commit();

            if ($result['status'] === 'continue') {
                return redirect()->route('konsultasi.question', $konsultasi->id);
            } else {
                return redirect()->route('konsultasi.result', $konsultasi->id);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function question($id)
    {
        $konsultasi = Konsultasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($konsultasi->status === 'selesai') {
            return redirect()->route('konsultasi.result', $id);
        }

        $chainingService = new ForwardChainingService($konsultasi);
        $result = $chainingService->getPertanyaanSelanjutnya();

        if ($result['status'] === 'completed') {
            return redirect()->route('konsultasi.result', $id);
        }

        return view('konsultasi.question', [
            'konsultasi' => $konsultasi,
            'fakta' => $result['fakta'],
            'pertanyaan' => $result['pertanyaan'],
            'progress' => $result['progress']
        ]);
    }

    public function answer(Request $request, $id)
    {
        $request->validate([
            'fakta_kode' => 'required|string',
            'jawaban' => 'required|in:ya,tidak'
        ]);

        try {
            DB::beginTransaction();

            $konsultasi = Konsultasi::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $chainingService = new ForwardChainingService($konsultasi);
            $chainingService->saveAnswer($request->fakta_kode, $request->jawaban);

            // Cek pertanyaan selanjutnya
            $result = $chainingService->getPertanyaanSelanjutnya();

            DB::commit();

            if ($result['status'] === 'completed') {
                return redirect()->route('konsultasi.result', $id);
            } else {
                return redirect()->route('konsultasi.question', $id);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function result($id)
    {
        $konsultasi = Konsultasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['detailKonsultasi.fakta', 'inferensiLog'])
            ->firstOrFail();

        if ($konsultasi->status !== 'selesai') {
            return redirect()->route('konsultasi.question', $id);
        }

        // Ambil solusi berdasarkan hasil konsultasi
        $solusiKodes = $konsultasi->hasil_konsultasi ?? [];
        $solusi = Solusi::whereIn('kode', $solusiKodes)->get();

        // Ambil fakta antara yang terbentuk
        $faktaAntara = $konsultasi->inferensiLog()
            ->where('fakta_terbentuk', 'like', 'FA%')
            ->with('aturan')
            ->get();

        // Ambil jawaban user
        $jawabanUser = $konsultasi->detailKonsultasi()
            ->with('fakta')
            ->get()
            ->groupBy('jawaban');

        return view('konsultasi.result', compact('konsultasi', 'solusi', 'faktaAntara', 'jawabanUser'));
    }

    public function show($id)
    {
        $konsultasi = Konsultasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['detailKonsultasi.fakta', 'inferensiLog.aturan'])
            ->firstOrFail();

        return view('konsultasi.show', compact('konsultasi'));
    }

    public function restart($id)
    {
        $konsultasi = Konsultasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Hapus detail konsultasi dan log inferensi
            $konsultasi->detailKonsultasi()->delete();
            $konsultasi->inferensiLog()->delete();

            // Reset status konsultasi
            $konsultasi->update([
                'status' => 'sedang_berjalan',
                'pertanyaan_terakhir' => null,
                'hasil_konsultasi' => null,
                'completed_at' => null
            ]);

            DB::commit();

            return redirect()->route('konsultasi.question', $id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function exportPdf($id)
    {
        $konsultasi = Konsultasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['detailKonsultasi.fakta', 'inferensiLog', 'user'])
            ->firstOrFail();

        if ($konsultasi->status !== 'selesai') {
            return back()->withErrors(['error' => 'Konsultasi belum selesai']);
        }

        $solusiKodes = $konsultasi->hasil_konsultasi ?? [];
        $solusi = Solusi::whereIn('kode', $solusiKodes)->get();

        $pdf = PDF::loadView('konsultasi.pdf', compact('konsultasi', 'solusi'));

        return $pdf->download('hasil-konsultasi-' . $konsultasi->id . '.pdf');
    }

    public function getProgress($id)
    {
        $konsultasi = Konsultasi::findOrFail($id);
        // $chainingService = new ForwardChainingService($konsultasi);

        $totalFacts = Fakta::askable()->count();
        $answeredFacts = count($konsultasi->getFaktaTerjawab());
        $progress = $totalFacts > 0 ? round(($answeredFacts / $totalFacts) * 100, 2) : 0;

        return response()->json([
            'progress' => $progress,
            'answered' => $answeredFacts,
            'total' => $totalFacts
        ]);
    }

}