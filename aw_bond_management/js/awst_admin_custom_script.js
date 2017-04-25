/* admin javascript */
jQuery( document ).ready(function() {

   /* jQuery(".trade_btn").click(function(){
        var   auction_id        = jQuery(this).attr("data-auction");
        var   bid_price         = jQuery(this).attr("data-price");
        var   bon_id            = jQuery(this).attr("data-bond");
        var   bid_user          = jQuery(this).attr("data-user");
      
        jQuery.ajax({
            type: "POST",
            data : {action:"trade_bid",id:auction_id,bid_price:bid_price,bon_id:bon_id,bid_user:bid_user},
            url: "/wp-admin/admin-ajax.php",
            success: function(result){
                console.log(result);
                var res = result;
                if (res == "success") {
                    console.log(success)
                }
            }
        });
    });*/

    jQuery('#save_setting_btn').on('click', function(){

        jQuery(this).attr('disabled',true);
        var selected_value = []; // initialize empty array
        jQuery(".element:checked").each(function(){
            selected_value.push(jQuery(this).val());
        });

        jQuery.ajax({
            type:"POST",
            url: "admin-ajax.php",
            data: {action:'awst_settings_ajax', data: selected_value},
            success:function(data){
                console.log(data);
                jQuery('#awSuccess').fadeIn(2000).fadeOut(2000);

                jQuery('#save_setting_btn').delay(2000).removeAttr('disabled');
            }
        });
    });
    return false;
});