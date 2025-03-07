$(function () {
    "use strict";

    $(document).ready(function () {
        $("#example").DataTable();
    });

    $(document).ready(function () {
        var table = $("#example2").DataTable({
            buttons: ["excel", "pdf", "print"],
            lengthMenu: [[5, 10, 25, 50, 100, 200, 500, -1], [5, 10, 25, 50, 100, 200, 500, "All"]],
            dom: '<"d-flex justify-content-between"<"mb-3"B><"ml-auto"f>>rt<"d-flex justify-content-between"lp>',
        });

        table
            .buttons()
            .container()
            .appendTo("#example2_wrapper .col-md-6:eq(0)");
    });
});
