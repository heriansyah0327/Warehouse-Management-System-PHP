// =====================================================
// GUDANG - script.js
// Interaksi UI bersama untuk seluruh halaman
// =====================================================

document.addEventListener('DOMContentLoaded', function () {

    // --- Toggle sidebar (burger) ---
    var burgers = document.querySelectorAll('[data-toggle="sidebar"]');
    var appLayout = document.querySelector('.app-layout');
    burgers.forEach(function (btn) {
        btn.addEventListener('click', function () {
            appLayout.classList.toggle('sidebar-collapsed');
        });
    });

    // --- Submenu accordion (Menajemen Barang, Laporan) ---
    document.querySelectorAll('.nav-parent').forEach(function (parent) {
        parent.addEventListener('click', function () {
            var sub = parent.nextElementSibling;
            var isOpen = sub.classList.contains('open');

            // close all other submenus
            document.querySelectorAll('.nav-sub.open').forEach(function (s) {
                if (s !== sub) {
                    s.classList.remove('open');
                    s.previousElementSibling.classList.remove('open');
                }
            });

            sub.classList.toggle('open', !isOpen);
            parent.classList.toggle('open', !isOpen);
        });
    });

    // --- User dropdown on topbar ---
    var ddBtn = document.querySelector('.topbar-user-btn');
    var dd = document.querySelector('.topbar-dropdown');
    if (ddBtn && dd) {
        ddBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            dd.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!dd.contains(e.target)) {
                dd.classList.remove('open');
            }
        });
    }

    // --- Delete confirmation modal ---
    var modal = document.getElementById('deleteModal');
    if (modal) {
        var form = document.getElementById('deleteForm');
        document.querySelectorAll('[data-delete-url]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                form.setAttribute('action', btn.getAttribute('data-delete-url'));
                var label = btn.getAttribute('data-delete-label') || 'item ini';
                var msgEl = document.getElementById('deleteMessage');
                if (msgEl) {
                    msgEl.textContent = 'Apakah kamu yakin ingin menghapus ' + label + '? Tindakan ini tidak dapat dibatalkan.';
                }
                modal.classList.add('open');
            });
        });
        var cancelBtn = document.getElementById('deleteCancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                modal.classList.remove('open');
            });
        }
        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.classList.remove('open');
        });
    }

    // --- Toggle field/group berdasarkan pilihan select (mis. Jenis Barang: Senjata/Narko) ---
    // Pasang atribut data-toggle-group="NAMA" pada <select>, lalu data-toggle-target="NAMA"
    // + data-toggle-value="Senjata" pada elemen (biasanya .form-group) yang mau ditampilkan/disembunyikan.
    document.querySelectorAll('[data-toggle-group]').forEach(function (select) {
        var groupName = select.getAttribute('data-toggle-group');
        var targets = document.querySelectorAll('[data-toggle-target="' + groupName + '"]');

        function applyToggle() {
            var val = select.value;
            targets.forEach(function (el) {
                var show = el.getAttribute('data-toggle-value') === val;
                el.style.display = show ? '' : 'none';
                el.querySelectorAll('select, input, textarea').forEach(function (field) {
                    field.disabled = !show;
                });
            });
        }

        select.addEventListener('change', applyToggle);
        applyToggle(); // set kondisi awal saat halaman dimuat
    });

    // --- Searchable select (combobox) ---
    // Struktur yang diharapkan:
    // <div class="search-select" data-search-select>
    //   <input type="text" class="ss-input" data-ss-search>
    //   <input type="hidden" data-ss-value name="...">
    //   <div class="ss-dropdown" data-ss-dropdown>
    //      <div class="ss-option" data-value="1" data-search="teks pencarian">...</div>
    //      <div class="ss-empty">Tidak ditemukan</div>
    //   </div>
    // </div>
    document.querySelectorAll('[data-search-select]').forEach(function (wrap) {
        var input = wrap.querySelector('[data-ss-search]');
        var hidden = wrap.querySelector('[data-ss-value]');
        var dropdown = wrap.querySelector('[data-ss-dropdown]');
        if (!input || !hidden || !dropdown) return;
        var options = Array.prototype.slice.call(dropdown.querySelectorAll('.ss-option'));
        var emptyEl = dropdown.querySelector('.ss-empty');

        function openDropdown() {
            dropdown.classList.add('open');
        }
        function closeDropdown() {
            dropdown.classList.remove('open');
        }
        function filterOptions() {
            var term = input.value.toLowerCase().trim();
            var visibleCount = 0;
            options.forEach(function (opt) {
                var label = (opt.getAttribute('data-search') || opt.textContent).toLowerCase();
                var show = term === '' || label.indexOf(term) !== -1;
                opt.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            if (emptyEl) emptyEl.style.display = visibleCount === 0 ? '' : 'none';
        }

        input.addEventListener('input', function () {
            hidden.value = '';
            wrap.classList.remove('ss-selected');
            filterOptions();
            openDropdown();
        });
        input.addEventListener('focus', function () {
            filterOptions();
            openDropdown();
        });
        options.forEach(function (opt) {
            opt.addEventListener('click', function () {
                if (opt.classList.contains('ss-option-disabled')) return;
                hidden.value = opt.getAttribute('data-value');
                input.value = opt.getAttribute('data-label') || opt.textContent.trim();
                wrap.classList.add('ss-selected');
                closeDropdown();
            });
        });
        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) closeDropdown();
        });
    });

    // --- Simple client-side table search filter ---
    document.querySelectorAll('[data-table-search]').forEach(function (input) {
        input.addEventListener('input', function () {
            var term = input.value.toLowerCase();
            var table = document.querySelector(input.getAttribute('data-table-search'));
            if (!table) return;
            table.querySelectorAll('tbody tr').forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(term) !== -1 ? '' : 'none';
            });
        });
    });
});
