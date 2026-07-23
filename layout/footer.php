<footer class="main-footer">
    <strong>Ibnu Ramadhan &copy; 13juli-13nov 2026 <a
            href="https://www.instagram.com/rmdhnibnu_?igsh=bHYxcWExa2ozcGVm">@rmdhnibnu_</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>BBPPMPV BMTI</b> 2026
    </div>
</footer>

<aside class="control-sidebar control-sidebar-dark">
</aside>
</div>
<script src="assets-template/plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<script src="assets-template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets-template/plugins/chart.js/Chart.min.js"></script>
<script src="assets-template/plugins/sparklines/sparkline.js"></script>
<script src="assets-template/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="assets-template/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<script src="assets-template/plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="assets-template/plugins/moment/moment.min.js"></script>
<script src="assets-template/plugins/daterangepicker/daterangepicker.js"></script>
<script src="assets-template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="assets-template/plugins/summernote/summernote-bs4.min.js"></script>
<script src="assets-template/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="assets-template/dist/js/adminlte.js"></script>
<!--<script src="assets-template/dist/js/demo.js"></script>-->

<script src="assets-template/dist/js/pages/dashboard.js"></script>

<script src="assets-template/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets-template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="assets-template/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets-template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="assets-template/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets-template/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="assets-template/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="assets-template/plugins/datatables-buttons/js/buttons.print.min.js"></script>

<script>
$(function() {
    $('#example2').DataTable();
});
</script>

<script>
$(document).ready(function() {
    $('#serverside').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "mahasiswa-serverside.php?action=table_data",
            dataType: "json",
            type: "POST"
        },
        columns: [{
                "data": "no",
                "className": "text-center",
                "width": "5%"
            },
            {
                "data": "nama"
            },
            {
                "data": "prodi"
            },
            {
                "data": "jk"
            },
            {
                "data": "telepon"
            },
            {
                "data": "aksi",
                "className": "text-center",
                "width": "25%"
            }
        ]
    });
});
</script>

</body>

</html>