$(function() {
    $(".btn-expand-sidebar").on("click", function(){
        if($(".btn-expand-sidebar").hasClass("collapsed")) {
            $(".main-content").removeClass("d-none");
        } else {
            $(".main-content").addClass("d-none");
        }
    });

    $("#btn-page-back").on("click", function(){
        if($("#tickets").hasClass('d-none')) {
            $("#pix").addClass("d-none");
            $("#tickets").removeClass("d-none");
        } else {
            $("#btn-close-modal-subscription").trigger("click");
        }
    });

    $(".btn-pay-with-pix").on("click", function(){
        $("#tickets").addClass("d-none");
        $("#pix").removeClass("d-none");
        $(".code-pix").hide();
        $("#code-pix-"+$(this).val()).show();
    });

    $(".code-pix-copy-paste").on("click", function(){
        navigator.clipboard.writeText($(this).val());
    });
});
