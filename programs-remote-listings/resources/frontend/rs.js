jQuery(document).ready(function($) {
	// toggle program info link
    $('.rs-program-message-link').click(function (e) {
        e.preventDefault();

        if ($('.rs-program-message-link span').text() == '+')
            $('.rs-program-message-link span').text('-')
        else
            $('.rs-program-message-link span').text('+')

        $('.rs-program-message').slideToggle();
    });
});