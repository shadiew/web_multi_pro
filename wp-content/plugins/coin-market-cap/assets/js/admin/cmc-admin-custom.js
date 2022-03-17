jQuery(document).ready(function($) {
    var choose_affi = $('[name="choose_affiliate_type"]');
    var checked = $('[name="choose_affiliate_type"]:checked').val();
    var choose_predi = $('[name="cmc_single_settings_prediction"]');
    var choose_trading =$('[name="cmc_single_settings_trading_chart"]');
    var checked = $('[name="choose_affiliate_type"]:checked').val();
	var prediction = jQuery('[name="cmc_single_settings_prediction"]:checked').val();
	var trading = $('[name="cmc_single_settings_trading_chart"]:checked').val();
	if(prediction=='on'){
		console.log(prediction);
		$('.cmb-row.cmb-type-select.cmb2-id-cmc-single-settings-pred-up-down').show();
		$('.cmb-row.cmb-type-text.cmb2-id-cmc-single-settings-pred-perce.table-layout').show();
	}else{
		$('.cmb-row.cmb-type-select.cmb2-id-cmc-single-settings-pred-up-down').hide();
		$('.cmb-row.cmb-type-text.cmb2-id-cmc-single-settings-pred-perce.table-layout').hide();
	}
	choose_predi.on('change',function(){
		var prediction = jQuery('[name="cmc_single_settings_prediction"]:checked').val()
		if(prediction=='on'){
		$('.cmb-row.cmb-type-select.cmb2-id-cmc-single-settings-pred-up-down').show();
		$('.cmb-row.cmb-type-text.cmb2-id-cmc-single-settings-pred-perce.table-layout').show();
	}else{
		$('.cmb-row.cmb-type-select.cmb2-id-cmc-single-settings-pred-up-down').hide();
		$('.cmb-row.cmb-type-text.cmb2-id-cmc-single-settings-pred-perce.table-layout').hide();
	}
	});
    if(trading=='on'){
        $('.cmb-row.cmb-type-textarea-code.cmb2-id-cmc-single-settings-trading-chart-code').show();
    }else{
        $('.cmb-row.cmb-type-textarea-code.cmb2-id-cmc-single-settings-trading-chart-code').hide();
    }
    choose_trading.on('change',function(){
		var trading = $('[name="cmc_single_settings_trading_chart"]:checked').val();
		if(trading=='on'){
            $('.cmb-row.cmb-type-textarea-code.cmb2-id-cmc-single-settings-trading-chart-code').show();
        }else{
            $('.cmb-row.cmb-type-textarea-code.cmb2-id-cmc-single-settings-trading-chart-code').hide();
        }
	});
    if (checked == "changelly_aff_id") {
        jQuery('.cmb2-id-affiliate-id').show();
        jQuery('.cmb2-id-other-affiliate-link').hide();
    } else if (checked == "any_other_aff_id") {
        jQuery('.cmb2-id-other-affiliate-link').show();
        jQuery('.cmb2-id-affiliate-id').hide();
    }
    choose_affi.on('change', function () {
        if ($(this).val() == "changelly_aff_id") {
            jQuery('.cmb2-id-affiliate-id').show();
            jQuery('.cmb2-id-other-affiliate-link').hide();
        } else {
            jQuery('.cmb2-id-other-affiliate-link').show();
            jQuery('.cmb2-id-affiliate-id').hide();
        }
    });

    var donation_box_active = $('li a[href="admin.php?page=cdb-settings"]');
    if (donation_box_active.length > 0){
        $('#adminmenu .toplevel_page_cool-crypto-plugins ul li:nth-child(5)').hide();
    }
    else {
        $('#adminmenu .toplevel_page_cool-crypto-plugins ul li:nth-child(4)').hide();
    }
    

    if ($('body.post-type-cmc-description').length > 0) {
        $('[name="cmc_single_settings_des_coin_name"] option').each(function() {
            if ((cmc_description.already_created).includes($(this).val()) && $(this).attr('selected') != 'selected') {
                $(this).attr('disabled', 'disabled');
            }

        })
    }

    // create AJAX as a javascript function to invoke multiple time with unique arguments
    let fetchCoins = function(ajax_url, ajax_action = null, batch = 1, code = false) {
        if (ajax_url == null) return "Invalid AJAX action";

        let progressDiv = "#" + ajax_action + "_progress";
        return $.ajax({
            type: 'POST',
            url: ajax_url,
            data: { action: ajax_action, coin_batch: batch, verification: code },
            beforeSend: function() {
                $(progressDiv).show();
            },
            success: function(response) {
                if (batch == 2) {
                    $(progressDiv).text("Data updated successfully!");
                }
                return true;
            },
            error: function(error) {
                return false;
            }
        });
    }

    $('#btncmc-coins-update').on('click', function(evt) {
        evt.preventDefault();

        if ($(this).attr("disabled") != undefined) {
            return;
        }

        // disable button to prevent multiple clicks
        $(this).attr('disabled', 'disabled');
        let url = $(this).attr('data-url');
        let key = $(this).attr('data-key');
        let batch1 = fetchCoins(url, 'cmc_ajax_coins_update', 1, key),
            batch2 = batch1.then(function() { return fetchCoins(url, 'cmc_ajax_coins_update', 2, key) });
    });

    $('#btncmc-coins-meta-update').on('click', function(evt) {
        evt.preventDefault();

        if ($(this).attr("disabled") != undefined) {
            return;
        }

        // disable button to prevent multiple clicks
        $(this).attr('disabled', 'disabled');
        let url = $(this).attr('data-url');
        let key = $(this).attr('data-key');
        let batch1 = fetchCoins(url, 'cmc_ajax_coins_meta_update', 1, key),
            batch2 = batch1.then(function() { return fetchCoins(url, 'cmc_ajax_coins_meta_update', 2, key) });
    });

});