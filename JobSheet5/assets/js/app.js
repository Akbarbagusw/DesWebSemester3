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
    document.addEventListener("click", function (event) {
        const button = event.target.closest(".btn-hapus");

        if (!button) {
            return;
        }

        const row = button.closest("tr");
        const itemName = row?.cells[1]?.textContent.trim() || "data ini";

        if (row && window.confirm(`Hapus ${itemName}?`)) {
            const section = row.closest("section");
            row.remove();
            if (section) {
                updateTableCount(section);
            }
        }
    });
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
