jQuery(document).ready(function($) {

    $("#rs-plugin-settings-show-advanced").on("click", function(e){
        e.preventDefault();

        $(".rs-plugin-settings-advanced-container").toggle();
    });
});