jQuery(document).ready(function($) {
    /* Google Analytics */
    function rs_set_ga_tracker(tracker){
        window.rs_ga_client_id = tracker.get('clientId');
        window.rs_ga_tracking_id = tracker.get('trackingId');
    };
    function rs_ga_loaded_cb(){
        if (rs_ga_retry_count >= 10){
            return;
        }
        if (typeof window.ga !== "undefined"){
            ga(rs_set_ga_tracker);
            return;
        }
        rs_ga_retry_count += 1;
        window.setTimeout(rs_ga_loaded_cb, 500);
    };
    var rs_ga_retry_count = 0;
    window.setTimeout(rs_ga_loaded_cb, 100);

    /* Bind event to Register Now to append the client ID if it exists. */
    $(".rs-register-link a, .rs-show-register-link a").click(function(e){
        if (window.rs_ga_client_id && window.rs_ga_tracking_id){
            e.preventDefault();
            location.href = $(this).prop("href") + "&ga-client-id=" + encodeURI(window.rs_ga_tracking_id)
                + encodeURI('~') + encodeURI(window.rs_ga_client_id);
        }
    });
});