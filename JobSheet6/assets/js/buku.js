async function muatDaftarBuku() {
    await muatDataTabel("../data/buku.json", ["judul", "pengarang", "kategori", "tahun", "stok"]);
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();
    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", function () {
            muatDaftarBuku();
        });
    }
});