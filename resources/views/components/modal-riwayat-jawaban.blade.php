<div class="modal fade" id="riwayatJawabanModal" tabindex="-1" aria-labelledby="riwayatJawabanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="riwayatJawabanModalLabel">
                    <i class="fas fa-history"></i> Riwayat Jawaban Konsultasi
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                <table class="table table-striped" id="riwayatJawabanTable">
                    <thead class="text-center">
                        <tr class="text-center">
                            <th>Kode Pertanyaan</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban</th>
                            <th>Kategori</th>
                            <th>Subkategori</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div id="riwayatJawabanLoading" class="text-center" style="display: none;">Memuat...</div>
                <div id="riwayatJawabanError" class="text-center" style="display: none;">Gagal memuat riwayat jawaban.</div>
            </div>
        </div>
    </div>
</div>