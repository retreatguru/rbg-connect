jQuery(document).ready(function($) {

    // Initialize
    typeof window.ga !== "undefined" && (rs_initialize_ga_tracking());

    // Main
    function rs_initialize_ga_tracking() {
        // ga is a global from Google Analytics
        ga(function (tracker) {
            rs_set_ga_tracker(tracker);
            rs_register_link_handlers();

            // Setup tracking idea globally in case we need to reference it
            function rs_set_ga_tracker() {
                window.rs_ga_client_id = tracker.get('clientId');
                window.rs_ga_tracking_id = tracker.get('trackingId');
            }
        });
    }

    // Setup RG link handlers
    function rs_register_link_handlers() {
        $('.rs-register-link a, .rs-show-register-link a').click(function (e) {
            if (window.rs_ga_client_id && window.rs_ga_tracking_id && $('.rs-registration-external').length == 0) {
                e.preventDefault();
                location.href = $(this).prop('href') + '&ga-client-id=' + encodeURI(window.rs_ga_tracking_id)
                    + encodeURI('~') + encodeURI(window.rs_ga_client_id);
            }
        });
    }

});