<div class="modal fade" id="umpanBalikModal" tabindex="-1" aria-labelledby="umpanBalikModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="umpanBalikModalLabel">Beri Umpan Balik Konsultasi Ini</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3 align-items-center w-lg-75 mx-auto"
                    action="{{ route('feedback.konsultasi', $konsultasi->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="konsultasiID" id="konsultasiIDInput" value="">
                    <div class="col-sm-12">
                        <div class="input-group-icon">
                            <div class="star-rating mb-2 d-flex align-items-center justify-content-center">
                                <svg class="star-icon" data-rating="1" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                <svg class="star-icon" data-rating="2" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                <svg class="star-icon" data-rating="3" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                <svg class="star-icon" data-rating="4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                <svg class="star-icon" data-rating="5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                <input type="hidden" name="rating" id="ratingInput" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="input-group-icon">
                            <textarea class="form-control form-little-squirrel-control" name="pesan" id="pesanInput" placeholder="Masukkan Pesan"
                                rows="6"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12 d-flex justify-content-between">
                        <button class="btn btn-primary orange-gradient-btn fs--1" type="submit">
                            Kirim Pesan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
