$(function() {
    $(".btn-expand-sidebar").on("click", function(){
        if($(".btn-expand-sidebar").hasClass("collapsed")) {
            $(".main-content").removeClass("d-none");
        } else {
            $(".main-content").addClass("d-none");
        }
    });
});
