document.addEventListener('DOMContentLoaded', function () {

    /* ===================== DROPDOWN TOGGLE ===================== */
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.dropdown.open').forEach(function (d) {
            if (!d.contains(e.target)) d.classList.remove('open');
        });
    });


    /* ===================== DATATABLES ===================== */
    document.querySelectorAll('.dt-table').forEach(function (dtTable) {
        if (typeof DataTable === 'undefined') return;

        new DataTable('#' + dtTable.id, {
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 data',
                emptyTable: 'Belum ada data tersedia',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Tidak ada data yang ditemukan',
                paginate: { next: '›', previous: '‹' },
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            pagingType: 'simple_numbers',
            columnDefs: [
                { orderable: false, targets: 0, className: 'col-no' },
                { orderable: false, targets: -1, className: 'col-center' },
            ],
            order: [],
        });
    });


    /* ===================== FILE INPUT ===================== */
    document.querySelectorAll('.file-input-hidden').forEach(function (input) {
        input.addEventListener('change', function () {
            const filename = document.getElementById(this.id + 'Filename');
            if (!filename) return;
            if (this.files && this.files.length > 0) {
                filename.textContent = this.files[0].name;
                filename.classList.add('has-file');
            } else {
                filename.textContent = 'Tidak ada file dipilih';
                filename.classList.remove('has-file');
            }
        });
    });


    /* ===================== DATATABLE SEARCH (debounce auto-submit) ===================== */
    const dtSearch = document.querySelector('.dt-search-input');
    if (dtSearch) {
        let dtTimer;
        dtSearch.addEventListener('input', function () {
            clearTimeout(dtTimer);
            dtTimer = setTimeout(function () {
                dtSearch.closest('form').submit();
            }, 400);
        });
    }


    /* ===================== TIME PICKER (.timepick) ===================== */
    document.querySelectorAll('.timepick').forEach(function (root) {
        var trigger = root.querySelector('.timepick-trigger');
        var pop     = root.querySelector('.timepick-pop');
        var label   = root.querySelector('.timepick-label');
        var jamWrap = root.querySelector('.timepick-jam');
        var menWrap = root.querySelector('.timepick-menit');
        var input   = root.querySelector('.timepick-input');
        if (!trigger || !pop || !input) return;

        // Batas maksimum dari data-max (HH:MM); default tanpa batas (23:59)
        var max = (root.dataset.max || '23:59').split(':');
        var MAX_JAM = parseInt(max[0], 10), MAX_MEN = parseInt(max[1], 10);

        function pad(n) { return String(n).padStart(2, '0'); }
        function tooLate(h, m) { return h > MAX_JAM || (h === MAX_JAM && m > MAX_MEN); }

        // Nilai awal: value input → data-default → waktu sekarang (dibatasi maksimum)
        var jam, menit, init = input.value || root.dataset.default || '';
        if (/^\d{1,2}:\d{2}$/.test(init)) {
            jam = parseInt(init.split(':')[0], 10); menit = parseInt(init.split(':')[1], 10);
        } else {
            var now = new Date(); jam = now.getHours(); menit = now.getMinutes();
        }
        if (tooLate(jam, menit)) { jam = MAX_JAM; menit = MAX_MEN; }

        function render() {
            label.textContent = pad(jam) + ':' + pad(menit);
            input.value = pad(jam) + ':' + pad(menit);
            input.dispatchEvent(new Event('change', { bubbles: true })); // beri tahu datetimepick
            jamWrap.querySelectorAll('.timepick-cell').forEach(function (c) {
                c.classList.toggle('sel', +c.dataset.v === jam);
            });
            menWrap.querySelectorAll('.timepick-cell').forEach(function (c) {
                var mv = +c.dataset.v;
                c.disabled = tooLate(jam, mv);          // nonaktifkan menit yang melewati batas
                c.classList.toggle('sel', mv === menit);
            });
        }

        // Tombol jam 00–23 (di atas batas dinonaktifkan)
        for (var h = 0; h < 24; h++) {
            (function (h) {
                var b = document.createElement('button');
                b.type = 'button'; b.className = 'timepick-cell'; b.dataset.v = h; b.textContent = pad(h);
                if (h > MAX_JAM) b.disabled = true;
                b.addEventListener('click', function () {
                    jam = h;
                    if (tooLate(jam, menit)) menit = MAX_MEN;
                    render();
                });
                jamWrap.appendChild(b);
            })(h);
        }

        // Tombol menit 00–59
        for (var m = 0; m < 60; m++) {
            (function (m) {
                var b = document.createElement('button');
                b.type = 'button'; b.className = 'timepick-cell'; b.dataset.v = m; b.textContent = pad(m);
                b.addEventListener('click', function () {
                    if (tooLate(jam, m)) return;
                    menit = m; render(); closePop();   // memilih menit menutup picker
                });
                menWrap.appendChild(b);
            })(m);
        }

        function scrollToSel(w) {
            var sel = w.querySelector('.sel');
            if (sel) w.scrollTop = sel.offsetTop - (w.clientHeight / 2) + (sel.offsetHeight / 2);
        }
        function openPop() {
            pop.classList.add('show'); trigger.classList.add('open');
            scrollToSel(jamWrap); scrollToSel(menWrap); // pusatkan nilai terpilih di kolom
        }
        function closePop() { pop.classList.remove('show'); trigger.classList.remove('open'); }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            pop.classList.contains('show') ? closePop() : openPop();
        });
        document.addEventListener('click', function (e) { if (!root.contains(e.target)) closePop(); });

        render();
    });


    /* ===================== DATETIME PICKER (.datetimepick) ===================== */
    document.querySelectorAll('.datetimepick').forEach(function (wrap) {
        var dateEl = wrap.querySelector('.dtp-date');
        var timeEl = wrap.querySelector('.timepick-input'); // diisi oleh .timepick
        var out    = wrap.querySelector('.dtp-out');
        if (!dateEl || !timeEl || !out) return;

        function combine() {
            out.value = (dateEl.value && timeEl.value) ? (dateEl.value + 'T' + timeEl.value) : '';
        }
        combine(); // gabung nilai awal → field bernama asli (Y-m-dTH:i)
        dateEl.addEventListener('input', combine);
        dateEl.addEventListener('change', combine);
        timeEl.addEventListener('change', combine);
    });

});


/* ===================== HAMBURGER: TOGGLE SIDEBAR ===================== */
function toggleSidebar(e) {
    if (e) e.preventDefault();

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const main = document.querySelector('.main');
    const header = document.querySelector('.header-main');

    const isHidden = sidebar.classList.toggle('sidebar-hidden');

    if (isHidden) {
        main.style.marginLeft = '0';
        header.style.left = '0';
        overlay.style.display = 'none';
    } else {
        main.style.marginLeft = 'var(--sidebar-width)';
        header.style.left = 'var(--sidebar-width)';
        if (window.innerWidth <= 768) {
            overlay.style.display = 'block';
        }
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const main = document.querySelector('.main');
    const header = document.querySelector('.header-main');

    sidebar.classList.add('sidebar-hidden');
    main.style.marginLeft = '0';
    header.style.left = '0';
    overlay.style.display = 'none';
}

function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('open');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) modal.classList.remove('show');
}