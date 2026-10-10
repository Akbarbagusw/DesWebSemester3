function initNavToggle() {
    const toggle = document.querySelector(".nav-toggle-label");
    const nav = document.querySelector("header nav");

    if (!toggle || !nav) {
        return;
    }

    toggle.addEventListener("click", function () {
        const isOpen = nav.classList.toggle("nav-open");
        toggle.setAttribute("aria-expanded", String(isOpen));
    });

    nav.addEventListener("click", function (event) {
        if (event.target.closest("a") && window.matchMedia("(max-width: 899px)").matches) {
            nav.classList.remove("nav-open");
            toggle.setAttribute("aria-expanded", "false");
        }
    });
}

function updateTableCount(section) {
    const count = section.querySelector(".table-count");
    const rows = section.querySelectorAll("tbody tr");

    if (!count) {
        return;
    }

    const visibleRows = Array.from(rows).filter(function (row) {
        return !row.hidden;
    }).length;

    count.textContent = `Menampilkan ${visibleRows} dari ${rows.length} ${count.dataset.itemLabel}.`;
}

function initHapusConfirm() {
    document.addEventListener("submit", function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.classList.contains("form-hapus")) {
            return;
        }

        const row = form.closest("tr");
        const itemName = row?.cells[0]?.textContent.trim() || "data ini";

        if (!window.confirm(`Yakin ingin menghapus "${itemName}"?`)) {
            event.preventDefault();
        }
    });
}

async function muatDataTabel(url, kunciKolom, opsi = {}) {
    const tbodySelector = opsi.tbodySelector || ".table-responsive table tbody";
    const loadingId = opsi.loadingId || "loading-indicator";
    const delay = opsi.delay !== undefined ? opsi.delay : 3000;

    const tbody = document.querySelector(tbodySelector);
    const loading = document.getElementById(loadingId);
    if (!tbody || !loading) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, delay));

        const res = await fetch(url);
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }

        const dataList = await res.json();
        dataList.forEach(function (item) {
            const tr = document.createElement("tr");
            let cellHtml = "";
            kunciKolom.forEach(function (kunci) {
                const val = item[kunci] !== undefined && item[kunci] !== null ? item[kunci] : "";
                cellHtml += "<td>" + val + "</td>";
            });
            cellHtml +=
                "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";
            tr.innerHTML = cellHtml;
            tbody.appendChild(tr);
        });

        const section = tbody.closest("section");
        if (section) {
            const filterInput = section.querySelector(".table-filter");
            if (filterInput && filterInput.value.trim() !== "") {
                filterInput.dispatchEvent(new Event("input"));
            } else {
                updateTableCount(section);
            }
        }
    } catch (err) {
        const messageRow = document.createElement("tr");
        const messageCell = document.createElement("td");
        const table = tbody.closest("table");
        const thCount = table ? table.querySelectorAll("thead th").length : (kunciKolom.length + 1);
        messageCell.colSpan = thCount;
        messageCell.textContent = "Gagal memuat data: " + err.message;
        messageRow.appendChild(messageCell);
        tbody.appendChild(messageRow);
    } finally {
        loading.style.display = "none";
    }
}

function initTableFilter() {
    document.querySelectorAll(".table-filter").forEach(function (filter) {
        const section = filter.closest("section");
        const table = section?.querySelector("tbody");

        if (!table) {
            return;
        }

        updateTableCount(section);

        filter.addEventListener("input", function () {
            const query = filter.value.trim().toLocaleLowerCase("id");
            const columnIndex = Number(filter.dataset.filterColumn);

            table.querySelectorAll("tr").forEach(function (row) {
                const cell = Number.isInteger(columnIndex) ? row.cells[columnIndex] : null;
                const searchableText = cell ? cell.textContent : row.textContent;
                const matches = searchableText.toLocaleLowerCase("id").includes(query);
                row.hidden = !matches;
            });

            updateTableCount(section);
        });
    });
}

function initValidasiForm() {
    const forms = Array.from(document.querySelectorAll("form")).filter(function (form) {
        return form.querySelector("#judul, #no_anggota");
    });

    forms.forEach(function (form) {
        form.noValidate = true;
        const requiredFieldNames = Array.from(form.querySelectorAll("[required]"), function (field) {
            return field.name;
        });

        form.querySelectorAll("input, select, textarea").forEach(function (field) {
            field.addEventListener("input", function () {
                const error = field.nextElementSibling;
                if (error?.classList.contains("form-error")) {
                    error.remove();
                }
            });
        });

        form.addEventListener("submit", function (event) {
            form.querySelectorAll(".form-error").forEach(function (error) {
                error.remove();
            });

            let firstInvalidField = null;

            form.querySelectorAll("input, select, textarea").forEach(function (field) {
                let message = "";

                if (requiredFieldNames.includes(field.name) && !field.value.trim()) {
                    message = "Field ini wajib diisi.";
                } else if (field.validity.rangeUnderflow) {
                    message = `Nilai minimal adalah ${field.min}.`;
                } else if (field.validity.rangeOverflow) {
                    message = `Nilai maksimal adalah ${field.max}.`;
                } else if (field.validity.patternMismatch) {
                    message = "ISBN hanya boleh berisi angka dan tanda hubung.";
                } else if (field.validity.typeMismatch) {
                    message = "Masukkan format yang valid.";
                }

                if (message) {
                    const error = document.createElement("span");
                    error.className = "form-error";
                    error.setAttribute("role", "alert");
                    error.textContent = message;
                    field.insertAdjacentElement("afterend", error);
                    firstInvalidField ||= field;
                }
            });

            if (firstInvalidField) {
                event.preventDefault();
                firstInvalidField.focus();
            }
        });
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});
