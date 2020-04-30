$(document).ready(function () {
    $(".tap-search-body").hide();
    $(".tap-search").click(function () {
        $(".tap-search-body").slideToggle("slow");
    });

});
$(function () {
    $(".datepicker").datepicker({
        dateFormat: 'dd-mm-yy',
    });
});