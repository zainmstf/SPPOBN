<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakta;
use App\Models\Solusi;
use Illuminate\Http\Request;

class VisualisasiController extends Controller
{
    public function index()
    {
        $categories = [
            'risiko_osteoporosis' => 'Risiko Osteoporosis',
            'asupan_nutrisi' => 'Asupan Nutrisi',
            'preferensi_makanan' => 'Preferensi Makanan'
        ];

        return view('admin.visualisasi.index', compact('categories'));
    }

    public function show($kategori)
    {
        $questions = Fakta::where('kategori', $kategori)->where('is_askable', '1')->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Tidak ada pertanyaan untuk kategori ini');
        }

        $mermaidScript = $this->generateMermaidScript($questions);

        return view('admin.visualisasi.show', [
            'kategori' => $kategori,
            'mermaidScript' => $mermaidScript,
            'questions' => $questions
        ]);
    }

    private function generateMermaidScript($questions)
    {
        $script = "%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#f8f9fa'}}}%%\n";
        $script .= "flowchart TD\n";
        $processedNodes = [];
        $endNodes = [];

        // Generate all question nodes
        foreach ($questions as $question) {
            $nodeId = $this->sanitizeNodeId($question->kode);
            $processedNodes[] = $nodeId;

            $script .= sprintf(
                "    %s[\"%s\"]\n",
                $nodeId,
                $this->escapeMermaidText($question->pertanyaan)
            );

            // Check for end nodes (next_if_yes is null)
            if (is_null($question->next_if_yes)) {
                $endNodes[] = ['from' => $nodeId, 'answer' => 'Ya'];
            }

            // Check for end nodes (next_if_no is null)
            if (is_null($question->next_if_no)) {
                $endNodes[] = ['from' => $nodeId, 'answer' => 'Tidak'];
            }
        }

        // Generate all edges
        foreach ($questions as $question) {
            $nodeId = $this->sanitizeNodeId($question->kode);

            // Add 'Yes' edge
            if ($question->next_if_yes) {
                $nextYesId = $this->sanitizeNodeId($question->next_if_yes);
                $script .= "    {$nodeId} -->|Ya| {$nextYesId}\n";
            }

            // Add 'No' edge
            if ($question->next_if_no) {
                $nextNoId = $this->sanitizeNodeId($question->next_if_no);
                $script .= "    {$nodeId} -->|Tidak| {$nextNoId}\n";
            }
        }

        // Add end nodes if any
        if (!empty($endNodes)) {
            $script .= "    Selesai((Selesai))\n";
            $script .= "    style Selesai fill:#2ecc71,color:white,stroke:#27ae60\n";

            foreach ($endNodes as $end) {
                $script .= "    {$end['from']} -->|{$end['answer']}| Selesai\n";
            }
        }

        return $script;
    }

    private function sanitizeNodeId($kode)
    {
        return 'Q' . preg_replace('/[^a-zA-Z0-9]/', '', $kode);
    }

    private function escapeMermaidText($text)
    {
        $text = str_replace('"', '\\"', $text);
        $text = str_replace("'", "\\'", $text);
        $text = str_replace("\n", "<br>", $text);
        return $text;
    }
}