export function setupEdukasiModal() {
    document.addEventListener("DOMContentLoaded", function () {
        const linksToModal = document.querySelectorAll(
            '[data-bs-toggle="modal"][data-bs-target="#edukasiModal"]'
        );
        const modalTitle = document.getElementById("edukasiModalLabel");
        const modalBody = document.getElementById("edukasiModalBody");
        const modalHeader = document.getElementById("modalHeader");
        const modalContent = document.querySelector(
            "#edukasiModal .modal-content"
        );
        const edukasiModal = document.getElementById("edukasiModal");
        const modalDialog = document.querySelector(
            "#edukasiModal .modal-dialog"
        );

        linksToModal.forEach((thumbnail) => {
            thumbnail.addEventListener("click", function () {
                modalBody.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>`;

                const kontenId = this.getAttribute("data-konten-id");
                fetch(`edukasi/konten/${kontenId}`)
                    .then((response) => {
                        if (!response.ok)
                            throw new Error(
                                `HTTP error! status: ${response.status}`
                            );
                        return response.json();
                    })
                    .then((data) => {
                        modalHeader.style.display = "none";

                        if (data.jenis === "video") {
                            modalDialog.classList.remove("modal-xl");
                            modalDialog.classList.add("modal-lg");
                            const youtubeUrl = `${data.path}?autoplay=1`;
                            modalBody.innerHTML = `
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe id="modalVideoIframe" class="embed-responsive-item rounded-iframe-video" style="width:100%;" 
                                        src="${youtubeUrl}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                                    </iframe>
                                </div>`;
                            edukasiModal.classList.add("modal-video");
                        } else if (data.jenis === "artikel") {
                            modalDialog.classList.add("modal-xl");
                            modalDialog.classList.remove("modal-lg");
                            edukasiModal.classList.remove("modal-video");
                            modalTitle.textContent = data.judul;

                            const storagePath = modalBody.dataset.storagePath;
                            const placeholderImage =
                                modalBody.dataset.placeholderImage;

                            modalBody.innerHTML = `
                                ${
                                    data.path
                                        ? `<img src="${storagePath}/${data.path}" class="img-fluid mb-3" alt="${data.judul}" 
                                    " style="max-height: 600px; width: 100%; object-fit: cover;">`
                                        : ""
                                }
                                <h2 class="mb-2">${data.judul}</h2>
                                <div class="mb-2">
                                    <i class="bi bi-calendar"></i> ${new Date(
                                        data.created_at
                                    ).toLocaleDateString("id-ID", {
                                        weekday: "long",
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    })} ${new Date(
                                data.created_at
                            ).toLocaleTimeString("id-ID", {
                                hour: "2-digit",
                                minute: "2-digit",
                            })}
                                </div>
                                <div class="mb-2"><i class="bi bi-person-fill"></i> Penulis: ${
                                    data.penulis || ""
                                }</div>
                                <div class="mt-3 text-justify">${
                                    data.deskripsi || ""
                                }</div>`;
                        } else if (data.jenis === "infografis") {
                            modalDialog.classList.add("modal-xl");
                            modalDialog.classList.remove("modal-lg");
                            edukasiModal.classList.remove("modal-video");
                            modalTitle.textContent = data.judul;

                            const storagePath = modalBody.dataset.storagePath;
                            const placeholderImage =
                                modalBody.dataset.placeholderImage;

                            modalBody.innerHTML = `
                                <h2 class="mb-2">${data.judul}</h2>
                                <div class="mb-2">
                                    <i class="bi bi-calendar"></i> ${new Date(
                                        data.created_at
                                    ).toLocaleDateString("id-ID", {
                                        weekday: "long",
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    })} ${new Date(
                                data.created_at
                            ).toLocaleTimeString("id-ID", {
                                hour: "2-digit",
                                minute: "2-digit",
                            })}
                                </div>
                                <div class="mb-2"><i class="bi bi-person-fill"></i> Penulis: ${
                                    data.penulis || ""
                                }</div>
                                ${
                                    data.path
                                        ? `<img src="${storagePath}/${data.path}" class="img-fluid rounded" 
                                    alt="Infografis ${data.judul}"" style="width: 100%;">`
                                        : ""
                                }
                                <div class="mt-3">${
                                    data.deskripsi || ""
                                }</div>`;
                        } else {
                            modalBody.innerHTML = `<div>${
                                data.deskripsi || ""
                            }</div>`;
                        }
                    })
                    .catch((error) => {
                        console.error("Error fetching content:", error);
                        modalBody.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Konten tidak dapat dimuat. Silakan coba lagi nanti.
                            </div>`;
                    });
            });
        });

        edukasiModal.addEventListener("hidden.bs.modal", function () {
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`;
            modalTitle.textContent = "";

            const modalVideoIframe =
                document.getElementById("modalVideoIframe");
            if (modalVideoIframe) {
                const currentSrc = modalVideoIframe.src;
                modalVideoIframe.src = "";
                setTimeout(() => {
                    if (modalVideoIframe) modalVideoIframe.src = currentSrc;
                }, 100);
            }
        });
    });
}
