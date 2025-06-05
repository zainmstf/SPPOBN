export function setupUmpanBalikModal() {
    const umpanBalikModal = document.getElementById("umpanBalikModal");
    const konsultasiIDInput = document.getElementById("konsultasiIDInput");

    umpanBalikModal.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget;
        const konsultasiID = button.dataset.konsultasiId;

        konsultasiIDInput.value = konsultasiID;
    });

    const stars = document.querySelectorAll(".star-rating svg");
    const ratingInput = document.getElementById("ratingInput");

    stars.forEach((star) => {
        star.setAttribute("role", "button");
        star.setAttribute("tabindex", "0");
        star.addEventListener("click", function () {
            const ratingValue = parseInt(this.dataset.rating);
            ratingInput.value = ratingValue;

            stars.forEach((s) => {
                const starRating = parseInt(s.dataset.rating);
                if (starRating <= ratingValue) {
                    s.setAttribute("fill", "currentColor");
                } else {
                    s.setAttribute("fill", "none");
                }
            });
        });
    });
}
