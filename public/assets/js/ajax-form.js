$(document).ready(function () {
    $('#contact-form').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        let submit_button  = $(this).find("button[type=submit]");
        submit_button.prop('disabled', true);
        submit_button.html('Loading...');

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#contact-form').hide();
                    $('#result').html(response.message).removeClass('text-gray-400').addClass('fs-4 text-green-500');
                } else {
                    $('#result').html(response.message).removeClass('text-gray-400').addClass('text-red-500');
                }
            },
            error: function () {
                submit_button.prop('disabled', false);
                submit_button.html('Subscribe <img src="/assets/images/btn-arrow.svg" alt="icon">');
                $('#result').html('An error occurred. Please try again.').removeClass('text-gray-400').addClass('text-red-500');
            }
        });
    });
});
