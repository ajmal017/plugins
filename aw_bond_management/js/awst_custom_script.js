/* forntend javascript */
jQuery( document ).ready(function() {

    /* function to handle like button click events */
    jQuery('body').on('click', '.awst_like_btn > i', function(){
        var post_id   = jQuery(this).attr("data-post-id");
        var post_like = jQuery(this).attr("data-post-like");

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_like',post_id:post_id, post_like:post_like},
            success:function(data){
                response = JSON.parse(data);

                if(response.status == 'success'){

                    $message = response.status;
                    $likcount = response.likecount;

                    if( jQuery('#awst_like_btn_'+post_id+' > i').hasClass('fa-thumbs-o-up')){
                        jQuery('#awst_like_btn_'+post_id+' > i').removeClass('fa-thumbs-o-up').addClass('fa-thumbs-up');
                    }else{
                        jQuery('#awst_like_btn_'+post_id+' > i').removeClass('fa-thumbs-up').addClass('fa-thumbs-o-up');
                    }
                    jQuery('.total_likes_'+post_id).text(response.likecount);
                }else{
                    /* error code block */
                    $message = response.message;
                    jQuery('#awMessageBlock').text($message).fadeIn(2000).fadeOut(2000);
                }
            }
        });
    });

    /* function to handle rating button click events */
    jQuery('.awst_rate_btn > i').on('click', function(){

        var post_id  = jQuery(this).attr("data-post-id");
        var rate_val = jQuery(this).attr("data-rate-id");

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_rating', post_id: post_id, rate_val: rate_val },
            success:function(data){

                response = JSON.parse(data);
                if(response.status == 'success'){

                    jQuery('.awst_rate_btn > i').removeClass('fa-star').addClass('fa-star-o');
                    for (i = 1; i <= rate_val; i++) {
                        var itemID = "#star"+i+" > i";
                        jQuery(itemID).removeClass('fa-star-o').addClass('fa-star');
                    }
                    var rating = response.rating;
                    jQuery('#average_rating').text(rating);
                }else{
                    $message = response.message;
                    jQuery('#awMessageBlock').text($message).fadeIn(2000).fadeOut(2000);
                }
            }
        });
    });

    /* function to handle review button click events */
    jQuery('.awst_rate_btn_review').on('click', function(){

        var post_id  = jQuery(this).attr("data-post-id");
        var review  = jQuery('#review_'+post_id).val();

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_review', post_id: post_id, review:review },
            success:function(data){
                response = JSON.parse(data);
                console.log(data);

                /*var message = '';
                message += '<li>';
                message +=     '<div class="review-content">';
                message +=        '<span style="float: left"><i class="fa fa-comment" aria-hidden="true"></i>'+response.review+'</span>';
                message +=        '<span style="float: right;"> <a href="#" class="awst_review_edit" data-item-id="'+response.review_id+'">Edit</a> &nbsp;|&nbsp; <a href="#" style="color: #FF0000" class="awst_review_delete" data-item-id="'+response.review_id+'">Delete</a></span>';
                message +=        '<div class="clear"></div>';
                message +=    '</div>';
                message +=    '<div class="review-detail">';
                message +=        '<span class="review-author"><i class="fa fa-user" aria-hidden="true"></i>'+response.user+'</span>';
                message +=        '<span class="review-date"><i class="fa fa-calendar" aria-hidden="true"></i>'+response.review_date+'</span>';
                message +=    '</div>';
                message += '</li>';*/

                var message = '';
                message += '<li>';
                message +=     '<div class="review-content">';
                message +=        '<span class="review_content_'+response.review_id+'" style="float: left"><i class="fa fa-comment" aria-hidden="true"></i>'+response.review+'</span>';
                message +=        '<span style="float: right;"> <a href="#" style="display: none" class="awst_review_edit" data-item-id="'+response.review_id+'">Save Changes</a> <a href="#" class="awst_review_edit_show" data-item-id="'+response.review_id+'">Edit</a> &nbsp;|&nbsp; <a href="#" style="color: #FF0000" class="awst_review_delete" data-item-id="'+response.review_id+'">Delete</a></span>';
                message +=        '<div class="clear"></div>';
                message +=        '<p><span class="edit_container" style="display: none"><textarea class="edit_review_box_'+response.review_id+'">'+response.review+'</textarea></span></p>';
                message +=    '</div>';
                message +=    '<div class="review-detail">';
                message +=        '<span class="review-author"><i class="fa fa-user" aria-hidden="true"></i>'+response.user+'</span>';
                message +=        '<span class="review-date"><i class="fa fa-calendar" aria-hidden="true"></i>'+response.review_date+'</span>';
                message +=    '</div>';
                message += '</li>';

                if(response.status == 'success'){
                    jQuery("#review-list ul").prepend(jQuery(message).hide().fadeIn(2000));
                    jQuery('#review_'+post_id).val("");
                }else if(response.status == 'error'){
                    jQuery('#review_'+post_id).val("");
                    alert(response.message);
                }

            }
        });
    });


    /*functionlity to delete reviews */
    jQuery('body').on('click', '.awst_review_delete', function(){
        var item = jQuery(this).attr('data-item-id');
        var element = jQuery(this);

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_review_delete', post_id: item},
            success:function(data){
                response = JSON.parse(data);
                if(response.status == 'success'){
                    jQuery(element).parent().parent().parent().fadeOut(500);
                }
            }
        });
        return false;
    });

    /*functionlity to reviews reviews */
    jQuery('body').on('click', '.awst_review_edit', function(){

        var element     = jQuery(this);
        var item        = jQuery(this).attr('data-item-id');
        var containerID = '.edit_review_box_'+item;
        var content     = jQuery(containerID).val();

        var review_content_item =  '.review_content_'+item;


        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_review_edit', post_id: item, post_content: content},
            success:function(data){
                jQuery('.awst_review_edit').fadeOut(1000);
                jQuery('.edit_container').fadeOut(1000);
                response = JSON.parse(data);
                var htmlText = '<i aria-hidden="true" class="fa fa-comment"></i>'+content;
                jQuery(review_content_item).html(htmlText);

                console.log(review_content_item);
                console.log(htmlText);


            }
        });
        return false;
    });


    jQuery('body').on('click', '.awst_review_edit_show', function(){
        jQuery('.awst_review_edit').fadeIn(1000);
        jQuery('.edit_container').fadeIn(1000);
        return false;
    });

//function dataUpload(){
/*jQuery('#form').submit(function (e) {


e.preventDefault();

var csv = jQuery("#csv_file").val(); 

//var formData = new FormData(jQuery(this)); 
var formData =  new FormData("form")[0];

//formData.append('file', jQuery('input[type=file]')[0].files[0]);

//formData.append('action','csvUpload');

console.log( formData );

 jQuery.ajax({
    type:"POST",
    url: "/wp-admin/admin-ajax.php",
    data: formData,
    contentType: false,
    processData: false,
    beforeSend:function(){
        jQuery('#loading-image').show();
    },
    success:function(data){
       alert(data);
    },
      complete: function(){
        jQuery('#loading-image').hide();
      }
});


 });*/

    // BWIC upload section datepickers logic 

    jQuery('#datetimepicker6').datetimepicker();
    jQuery('#datetimepicker7').datetimepicker({
        useCurrent: false //Important! See issue #1075
    });
    jQuery("#datetimepicker6").on("dp.change", function (e) {
        jQuery('#datetimepicker7').data("DateTimePicker").minDate(e.date);
    });
    jQuery("#datetimepicker7").on("dp.change", function (e) {
        jQuery('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
    });

    /* send mail to winner and other user */
    jQuery("#select_winner").click(function(){
        jQuery.ajax({
            type: "POST",
            data : {action:"send_mail_to_users",id:auction_id,bid_price:bid_price,bon_id:bon_id},
            url: "/wp-admin/admin-ajax.php",
            success: function(result){
                var res = result;
                if (res == "success") {
                }
            }
        });
   });

   jQuery(".create_bid").click(function(){ 
      
      jQuery(".place_bid").show();
      jQuery(".bid_success_msg").css("display","none");
      
      var bond_name = jQuery(this).attr("data-bon_title"); 
      jQuery(".modal-title").text(bond_name);

      var data_auc_id = jQuery(this).attr("data-auc_id"); 
      jQuery("#auct_id").val(data_auc_id);
      
      var data_bon_id = jQuery(this).attr("data-bon_id"); 
      jQuery("#bon_id").val(data_bon_id);

      var data_bon_ticker = jQuery(this).attr("data-bon_ticker"); 
      jQuery(".modal-extra").find(".modal_ticker").text(data_bon_ticker);

      var data_bon_orig = jQuery(this).attr("data-bon_orig"); 
      jQuery(".modal-extra").find(".modal_orig").text("ORIG SIZE (MM):"+data_bon_orig);
      
      var data_bon_dnt = jQuery(this).attr("data-bon_dnt"); 
      jQuery(".modal-extra").find(".modal_dnt").text("BWIC TIME:"+data_bon_dnt);

      var data_bid_price = jQuery(this).attr("data-bid_price");
      if (data_bid_price != "") {
        jQuery("#bid_price").val(data_bid_price);
        jQuery("#bid_btn").val("Edit Bid");
      }else{
        jQuery("#bid_btn").val("Bid");
      }

      //jQuery(this).text("Edit Bid")
      //jQuery(this).parent().parent().addClass("rem");
    });

    jQuery(".bid_btn").click(function(){
        var   auction_id        = jQuery("#auct_id").val();
        var   bid_price         = jQuery("#bid_price").val();
        var   bon_id            = jQuery("#bon_id").val();
        var   col_id            = 'bon_'+bon_id;
      
        jQuery("#post_"+auction_id).find("#accordion-"+auction_id).find(".all_detail_"+bon_id).find("a").addClass(col_id);
  
        jQuery("."+col_id).attr("data-bid_price",bid_price);
        
        jQuery("."+col_id).text("Edit Bid");

        jQuery('.loader-img').show();
        jQuery.ajax({
            type: "POST",
            data : {action:"place_bid",id:auction_id,bid_price:bid_price,bon_id:bon_id},
            url: "/wp-admin/admin-ajax.php",
            success: function(result){
                console.log(result);
                var res = result;
                if (res == "success") {
                    jQuery("#bid_price").val("");
                    jQuery(".place_bid").hide();
                    jQuery(".bid_success_msg").css("display","inline");
                    jQuery('.loader-img').hide();
                }
            }
        });
    });

});

