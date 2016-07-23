jQuery(document).ready(function(){

	jQuery( "#site_name" ).change(function() {
		var site_name = jQuery("#site_name").val();
		var site_url = jQuery("#mainUrl").val();
		
		site_name = site_name.toLowerCase();
		/* jQuery.ajax({
				type: "POST",
				url: document.location.protocol+"//"+document.location.host+'/wp-content/themes/HotelBooking/check_siteslug.php',			
				data: {site_name:site_name,site_url:site_url},
				success: function(html){
					
				}
		}); */
		site_name = site_name.replace(/\s/g, "-");
		jQuery("#site_slug").val(site_name);
		jQuery("#site_url").val(site_name);
		jQuery("input[name='site_hurl']").val(site_url+"/"+site_name);
	});

	jQuery('.trnsfr_btn').click(function(){
    		var flag = true;
    		
    		var blogname = jQuery('#domain_name').val();
    		if (blogname == '0') {
    			jQuery('.error_msg_blog_name').show();
    			flag = false;
    		}else{  
    			jQuery('.error_msg_blog_name').hide();
    		}

    		var domain_name_url = jQuery('#domain_name_url').val();
    		if (domain_name_url == '') {
    			jQuery('.error_msg_domain_name_url').show();
    			flag = false;
    		}else{  
    			jQuery('.error_msg_domain_name_url').hide();
    		}

            var dom_alias = jQuery('#dom_alias').val();
            if (dom_alias == '') {
                jQuery('.error_msg_domain_alias').show();
                flag = false;
            }else{  
                jQuery('.error_msg_domain_alias').hide();
            }

            var config_name = jQuery('#config_name').val();
            if (config_name == '') {
                jQuery('.error_msg_config_name').show();
                flag = false;
            }else{  
                jQuery('.error_msg_config_name').hide();
            }

            var config_file_path = jQuery('#config_file_path').val();
            if (config_file_path == '') {
                jQuery('.error_msg_config_file_path').show();
                flag = false;
            }else{  
                jQuery('.error_msg_config_file_path').hide();
            }

    		
    		if(flag){
    			return true;
    		}else{
    			return false;	
    		}	

    });
	
});

function saved_msg(){
	jQuery(document).ready(function(){
    	jQuery(".saved_msg").fadeIn(5000).fadeOut(5000);
	});
}
