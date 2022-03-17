$(".userinfo").click(function () {
    var userId = $(this).data("id");
    var urlDest = $(this).data("route");
    var newTitle = $(this).data("title");
    // AJAX request
    $.ajax({
        url: urlDest,
        type: "post",
        data: {
            userId: userId,
        },
        success: function (response) {
            // Add response in Modal body
            $(".modal-body").html(response);
            $(".modal-title").html(newTitle);
            // Display Modal
            $("#empModal").modal("show");
        },
        error: function () {
            $(".modal-body").html("Une erreur est survenue.");
            $(".modal-title").html("Erreur");
            // Display Modal
            $("#empModal").modal("show");
        }
    });
});

$(".close").click(function () {
    $("#empModal").modal("hide");
});

