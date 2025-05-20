<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Konsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Menampilkan form umpan balik.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('user.feedback.index');
    }

    /**
     * Menyimpan umpan balik dari pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pesan' => 'required|string|max:500',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Feedback::create([
            'user_id' => 1,
            'pesan' => $request->input('pesan'),
            'rating' => $request->input('rating'),
        ]);

        return redirect()->route('feedback.index')->with('success', 'Terima kasih atas umpan balik Anda!');
    }

    /**
     * Menyimpan umpan balik setelah konsultasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Konsultasi  $konsultasi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeKonsultasiFeedback(Request $request, Konsultasi $konsultasi)
    {
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memberikan umpan balik pada konsultasi ini.');
        }

        $validator = Validator::make($request->all(), [
            'pesan' => 'required|string|max:500',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Feedback::create([
            'user_id' => Auth::id(),
            'konsultasi_id' => $konsultasi->id,
            'pesan' => $request->input('pesan'),
            'rating' => $request->input('rating'),
        ]);

        return back()->with('success', 'Terima kasih atas umpan balik Anda mengenai konsultasi ini!');
    }
}