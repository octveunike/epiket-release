document.addEventListener('DOMContentLoaded', function() {

    /* ===================== DROPDOWN TOGGLE ===================== */
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.dropdown.open').forEach(function(d) {
            if (!d.contains(e.target)) d.classList.remove('open');
        });
    });


    /* ===================== DATATABLES ===================== */
    document.querySelectorAll('.dt-table').forEach(function(dtTable) {
        if (typeof DataTable === 'undefined') return;
        new DataTable('#' + dtTable.id, {
            language: {
                search:       'Cari:',
                lengthMenu:   'Tampilkan _MENU_ data',
                info:         'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty:    'Menampilkan 0 data',
                emptyTable: 'Belum ada data tersedia',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords:  'Tidak ada data yang ditemukan',
                paginate: { next: '›', previous: '‹' },
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            pagingType: 'simple_numbers',
            columnDefs: [
                { orderable: false, className: 'dt-center', targets: 0  }, // kolom No
                { orderable: false, className: 'dt-center', targets: -1 }, // kolom Aksi
            ],
            order: [], // ikuti urutan asli dari server
        });
    });

    /* ===================== DELETE MODAL ===================== */
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    }


    document.querySelectorAll('.file-input-hidden').forEach(function(input) {
        input.addEventListener('change', function() {
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
        dtSearch.addEventListener('input', function() {
            clearTimeout(dtTimer);
            dtTimer = setTimeout(function() {
                dtSearch.closest('form').submit();
            }, 400);
        });
    }

});


/* ===================== HAMBURGER: TOGGLE SIDEBAR ===================== */
function toggleSidebar(e) {
    if (e) e.preventDefault();

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const main    = document.querySelector('.main');
    const header  = document.querySelector('.header-main');

    const isHidden = sidebar.classList.toggle('sidebar-hidden');

    if (isHidden) {
        main.style.marginLeft = '0';
        header.style.left     = '0';
        overlay.style.display = 'none';
    } else {
        main.style.marginLeft = 'var(--sidebar-width)';
        header.style.left     = 'var(--sidebar-width)';
        if (window.innerWidth <= 768) {
            overlay.style.display = 'block';
        }
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const main    = document.querySelector('.main');
    const header  = document.querySelector('.header-main');

    sidebar.classList.add('sidebar-hidden');
    main.style.marginLeft = '0';
    header.style.left     = '0';
    overlay.style.display = 'none';
}

function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('open');
}

function showDeleteModal(id) {
    const form  = document.getElementById('delete-form');
    const modal = document.getElementById('deleteModal');
    if (!form || !modal) return;
    const base = document.querySelector('.dt-table')?.dataset.destroyUrl
              || form.dataset.baseUrl
              || '';
    form.action = base + '/' + id;
    modal.classList.add('active');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) modal.classList.remove('active');
}