async function muatDaftarAnggota() {
    await muatDataTabel("../data/anggota.json", ["no_anggota", "nama", "alamat", "no_hp"]);
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarAnggota();
    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", function () {
            muatDaftarAnggota();
        });
    }
});
