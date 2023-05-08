jQuery(document).ready(function($) {

    $('.cmc-major-update.notice .notice-dismiss').on('click', function(evt) {
        $.ajax({
            url: cmc_data.ajax_url,
            type: 'POST',
            data: { action: 'cmc_remove_major_update_notice' },
            success: function(res) {
                console.log(res);
            }
        });
    });

});