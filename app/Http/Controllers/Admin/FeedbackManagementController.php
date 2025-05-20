<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackManagementController extends Controller
{
    /**
     * Menampilkan daftar semua umpan balik.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $feedbacks = Feedback::with('user', 'konsultasi')->get();
        return view('admin.feedback.index', compact('feedbacks'));
    }

    /**
     * Menampilkan detail dari satu umpan balik.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('user', 'konsultasi', 'konsultasi.detailKonsultasi.fakta');
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Menghapus umpan balik.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return redirect()->route('admin.feedback.index')->with('success', 'Umpan balik berhasil dihapus.');
    }
}