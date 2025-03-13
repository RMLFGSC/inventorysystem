

<!--script for table-->
<script>
    $(document).ready(function() {
    $('#dataTable').DataTable({
        "pagingType": "full_numbers",
        "lengthMenu": [
            [10, 20, 30, 40, -1],
            [10, 20, 30, 40, "All"]
        ],
        "order": [], // 🚀 This disables automatic sorting
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search here yot...",
        }
    });
});
</script>


