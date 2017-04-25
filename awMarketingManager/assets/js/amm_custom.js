jQuery(document).ready(function($) {
    console.log('working..')
    /*function to hide divs*/
    $.fn.getSiteUrl = function (){
        var url = $('#url').attr('value');
        return url;
    };


    $(document).delegate('.place-market-bid', 'click', function(event){
        event.preventDefault();
        var url         = $.fn.getSiteUrl(); 
        var element     =  $(this);
        var amount      = element.parent().find('.bid_amount').val().trim();

        var offerprice  = element.parent().find('.bid_amount').attr('data-offerprice').trim();



        if( Number(amount) <  Number(1)){

            var message = '<div class="msg alert alert-warning">Please enter bid amount greater then Zero.</div>';

            element.parent().prepend(message);
            element.parent().find('.msg').fadeOut(8000);

            return false;
        }


        var bondID = element.parent().parent().parent().parent().attr('id');

        var bidobject = {
                    bid_price  : amount,
                    id         : bondID,
                    offer_price: offerprice
                    }

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "amm_market_bid",object:bidobject },
            success: function(response) {

               var message = '<div class="msg alert alert-success">Bid Place Successfully.</div>';

               element.parent().prepend(message);
               element.parent().find('.msg').fadeOut(4000);
               console.log(response);

            }
        });
        return false;
    });


    $(document).delegate('.accept-offerprice', 'click', function(event){
        event.preventDefault();
        var url         = $.fn.getSiteUrl(); 
        var element     =  $(this);
        var row         =  $(this).parent().parent();
        $('#message-div').removeClass('hide');
        var offerprice  = element.attr('data-offerprice').trim();

        var bondID = element.attr('data-bondid');
        
        var bidobject = {
                    bid_price  : offerprice,
                    id         : bondID,
                    offer_price: offerprice,
                    acceptChecker: 'acceptChecker'
                    }

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "amm_market_bid",object:bidobject },
            success: function(response) {
            row.remove();

            $('#message-div').addClass('hide');
            var html = '<div id="messages" class="alert alert-success alert-dismissible" role="alert">';
                html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                html += '<div id="messages_content">Bid Place Successfully</div>';
                html += '</div>';

                $('.post_content').prepend(html);
            }
        });
        return false;
    });


    $(document).delegate('.amm_trade_btn', 'click', function(event){

        var   url         = $.fn.getSiteUrl(); 
        var   bid_price   =  $(this).attr("data-price");
        var   bon_id      =  $(this).attr("data-bond");
        var   bid_user    =  $(this).attr("data-user");
        console.log(bon_id);console.log(bid_price);console.log(bid_user);
        $.ajax({
            type: "POST",
            data : {action:"amm_trade_bid",bid_price:bid_price,bon_id:bon_id,bid_user:bid_user},
            url: url,
            success: function(result){
                
                var data    = $.parseJSON(result);
                console.log(data);
                // var bond_id = data.bon_id;
                // var user_id = data.bid_user;
                //accordion-154576
                alert('Bond Traded successfully.');
                $("#accordion-"+bon_id+" td div table tbody").find("tr td").find("button").remove();
                $("#accordion-"+bon_id+" td div table tbody").find("tr.user_"+bid_user).find("td").eq(1).html("<h5>Winner</h5>");
          }
        });
    });


});
