$( document ).ready(function() {
    $(".spoiler p").click(function() {
        $(this).next(".content").toggle(200);
        $(this).toggleClass("active");
    });
});