jQuery.noConflict();
jQuery(document).ready(function($){
	
	jQuery(".parp_bg_color").wpColorPicker();
	
	
	$(document).on('click', "a.cvonvert-reviews", (function (e) {
        e.preventDefault();

        var data = {
            'action': 'convert_reviews'
        };


        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(Areview_Ajax.ajax_url, data, function (response) {
           
		   alert(response);
		   /*  $("span.converted-items").remove();
            $("div.convert-reviews").append('<div class="converted-items"><span class="converted-items">' + response.value + '</span></div>');

            clicked_item.unblock(); */
        });
    }))
	
	
		
});

jQuery(document).on('click','#parp_reset_value',parp_reset_all);
	
	function parp_reset_all(){

		jQuery('#parp_plugin_enable').prop('checked', true);
		jQuery('#parp_sh_ttl').prop('checked', true);
		jQuery('#parp_spv_enable').prop('checked', true);
		jQuery('#parp_upload_enable').prop('checked', true);
		
	
	}
