import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-dt/css/jquery.dataTables.css';

$(document).ready( function () {
    $('table').each(function () {
        $(this).DataTable({
            "pageLength": $(this).data('page-length'),
            "processing": true,
            "serverSide": true,
            "ajax": "/",
            "columns": $(this).data('columns')
        });
    });
} );
