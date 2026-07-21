<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<!-- asset plugin datatables -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.bootstrap5.js"></script>

<!-- load ckeditor cdn 
 <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script> ini berbayar jadi gabisa-->


<script>
CKEDITOR.replace('alamat', {
    filebrowserBrowseUrl: 'assets/ckfinder/ckfinder.html',
    filebrowserUploadUrl: 'assets/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
    height: '400px'
});
</script>

<script>
CKEDITOR.replace('alamat');
</script>

<script>
new DataTable('#example');
</script>

</body>

</html>