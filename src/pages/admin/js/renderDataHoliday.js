$(document).ready(function() {
    let currentPage = 0;
    let totalHolidays = 0;
    let searchQuery = '';
    const recordsPerPage = 5;

    function loadHolidays(page = 0, search = '') {
        $('#loading').removeClass('hidden');

        $.ajax({
            url: 'api/dayoff/get',
            type: 'GET',
            data: {
                start: page * recordsPerPage,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                $('#loading').addClass('hidden');

                if (response.status === 'unauthorized') {
                    window.location.href = 'unauthorized';
                    return;
                }

                totalHolidays = response.total || 0;
                currentPage = page;
                renderData(response.holidays);
                updatePaginationButtons();
            },
            error: function(xhr, status, error) {
                $('#loading').addClass('hidden');
                console.error('Error loading holidays:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
            }
        });
    }

    function renderData(holidays) {
        let holidayTableBody = $('#holiday-table-body');
        holidayTableBody.empty();

        if (holidays && holidays.length === 0 && currentPage > 0) {
            currentPage--;
            loadHolidays(currentPage, searchQuery);
            return;
        }

        if (!holidays || holidays.length === 0) {
            holidayTableBody.append(`
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
            `);
            return;
        }

        holidays.forEach(function(holiday, index) {
            const tanggalMulai = holiday.tanggal_mulai ?
                holiday.tanggal_mulai.split('-').reverse().join('-') : '-';
            const tanggalAkhir = holiday.tanggal_akhir ?
                holiday.tanggal_akhir.split('-').reverse().join('-') : '-';

            holidayTableBody.append(`
                <tr class="bg-gray-100">
                    <td class="px-6 py-2 text-center">${index + 1 + currentPage * recordsPerPage}</td>
                    <td class="px-6 py-2 text-center">${holiday.nama_libur || holiday.nama_hari_libur || '-'}</td>
                    <td class="px-6 py-2 text-center">${tanggalMulai}</td>
                    <td class="px-6 py-2 text-center">${tanggalAkhir}</td>
                    <td class="px-6 py-2 text-center">
                        <a href="dayoff/edit?id=${holiday.id}">
                            <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </a>
                        <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition ml-2" 
                                data-id="${holiday.id}" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function updatePaginationButtons() {
        const totalPages = Math.ceil(totalHolidays / recordsPerPage);
        const paginationContainer = $('#pagination-container');

        // Clear previous buttons and add new ones
        paginationContainer.find('.pagination-button').remove();

        // Create dynamic pagination buttons
        for (let i = 0; i < totalPages; i++) {
            const button = $(
                `<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${i + 1}</button>`
            );
            button.addClass(i === currentPage ? 'active-button' : 'inactive-button');
            button.insertBefore('#next-page'); // Insert before "Next" button
        }

        // Enable/Disable navigation buttons
        $('#first-page').prop('disabled', currentPage === 0);
        $('#prev-page').prop('disabled', currentPage === 0);
        $('#next-page').prop('disabled', currentPage >= totalPages - 1);
        $('#last-page').prop('disabled', currentPage >= totalPages - 1);
    }

    // Event handlers
    $(document).on('click', '.delete-button', function() {
        const id = $(this).data('id');
        deleteHoliday(id);
    });

    function deleteHoliday(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menghapus hari libur ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/dayoff/delete',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success');
                            loadHolidays(currentPage, searchQuery);
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus hari libur', 'error');
                    }
                });
            }
        });
    }

    // Pagination event handlers
    $('#first-page').on('click', function() {
        if (currentPage > 0) {
            loadHolidays(0, searchQuery);
        }
    });

    $('#prev-page').on('click', function() {
        if (currentPage > 0) {
            loadHolidays(--currentPage, searchQuery);
        }
    });

    $('#next-page').on('click', function() {
        if ((currentPage + 1) * recordsPerPage < totalHolidays) {
            loadHolidays(++currentPage, searchQuery);
        }
    });

    $('#last-page').on('click', function() {
        const totalPages = Math.ceil(totalHolidays / recordsPerPage);
        if (currentPage < totalPages - 1) {
            loadHolidays(totalPages - 1, searchQuery);
        }
    });

    $(document).on('click', '.pagination-button', function() {
        const page = parseInt($(this).data('page'));
        if (page !== currentPage) {
            loadHolidays(page, searchQuery);
        }
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        searchQuery = $(this).val();
        loadHolidays(0, searchQuery);
    });

    // Initial load
    loadHolidays(currentPage, searchQuery);
}); 