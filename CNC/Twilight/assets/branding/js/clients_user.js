/**
 * DataTables Basic
 */

$(function () {
    'use strict';

    var dt_basic_table = $('.datatables-basic'), dt_date_table = $('.dt-date'),
        dt_complex_header_table = $('.dt-complex-header'), dt_row_grouping_table = $('.dt-row-grouping'),
        dt_multilingual_table = $('.dt-multilingual'), assetPath = '../../../app-assets/';

    // DataTable with buttons
    // --------------------------------------------------------------------

    var dt_basic = dt_basic_table.DataTable({
        ajax: {url: '/api/internal/servers.json', dataSrc: ""},
        columns: [{data: 'source'}, {data: 'ip'}, {data: 'country'}, {data: 'system'}, {data: 'status'}, {data: 'actions'}],
        columnDefs: [{
            // Avatar image/badge, Name and post
            targets: 0, responsivePriority: 4, render: function (data, type, full, meta) {
                const country = full['short'], source = full['source'];
                return '<i class="flag-icon flag-icon-' + country + '"></i> ' + source + ' ';
            }
        }, {
            // Avatar image/badge, Name and post
            targets: 1, responsivePriority: 4, render: function (data, type, full, meta) {
                return full['ip_masked'];
            }
        }, {
            // Label
            targets: -2, render: function (data, type, full, meta) {
                var $status_number = full['status'];
                var $status = {
                    "active": {title: 'Active', class: 'badge-light-success'},
                    "idle": {title: 'Idle', class: ' badge-light-warning'},
                    "offline": {title: 'Offline', class: ' badge-light-danger'}
                };
                if (typeof $status[$status_number] === 'undefined') {
                    return data;
                }
                return ('<span class="badge rounded-pill ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>');
            }
        }, {
            // Actions
            targets: -1, title: 'Actions', orderable: false, render: function (data, type, full, meta) {
                return ('<div class="d-inline-flex">' + '<a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">' + feather.icons['more-vertical'].toSvg({class: 'font-small-4'}) + '</a>' + '<div class="dropdown-menu dropdown-menu-end">' + '<a href="javascript:;" class="dropdown-item">' + feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Details</a>' + '<a href="javascript:;" class="dropdown-item">' + feather.icons['archive'].toSvg({class: 'font-small-4 me-50'}) + 'Archive</a>' + '<a href="javascript:;" class="dropdown-item delete-record">' + feather.icons['trash-2'].toSvg({class: 'font-small-4 me-50'}) + 'Delete</a>' + '</div>' + '</div>' + '<a href="javascript:;" class="item-edit">' + feather.icons['edit'].toSvg({class: 'font-small-4'}) + '</a>');
            }
        }],
        order: [[2, 'desc']],
        dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 7,
        lengthMenu: [7, 10, 25, 50, 75, 100],
        buttons: [{
            extend: 'collection',
            className: 'btn btn-outline-secondary dropdown-toggle me-2',
            text: feather.icons['share'].toSvg({class: 'font-small-4 me-50'}) + 'Export',
            buttons: [{
                extend: 'print',
                text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Print',
                className: 'dropdown-item',
                exportOptions: {columns: [3, 4, 5, 6, 7]}
            }, {
                extend: 'csv',
                text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                className: 'dropdown-item',
                exportOptions: {columns: [3, 4, 5, 6, 7]}
            }, {
                extend: 'excel',
                text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                className: 'dropdown-item',
                exportOptions: {columns: [3, 4, 5, 6, 7]}
            }, {
                extend: 'pdf',
                text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                className: 'dropdown-item',
                exportOptions: {columns: [3, 4, 5, 6, 7]}
            }, {
                extend: 'copy',
                text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
                className: 'dropdown-item',
                exportOptions: {columns: [3, 4, 5, 6, 7]}
            }],
            init: function (api, node, config) {
                $(node).removeClass('btn-secondary');
                $(node).parent().removeClass('btn-group');
                setTimeout(function () {
                    $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex');
                }, 50);
            }
        }]
    });
    $('div.head-label').html('<h3 class="mb-0">Clients</h3>');

});