/* RMU Custom js */
jQuery( document ).ready(function() {
    
    console.log( "rmu ready!" );
    jQuery('#rmu_submit').on('click', function(){

    	var blogN =	jQuery('#directories').val();
    	console.log(blogN);

    	jQuery('#directories').val(null).trigger("change");

    	for ( index in blogN) {
		    console.log(blogN[index]);
		    getAjaxData( blogN[index] );
		}   	

    	return false;
    });

    /*functionality to close result window.*/
    jQuery('.aw_message_Section').on('click', '.rmu_close', function(){ 
    	jQuery(this).parent().remove(); 
    });

    function getAjaxData( blogN ){

    	// jQuery('.rmu_loader').fadeIn(50);
    	
    	var divID = blogN;
    	
    	var loaderDiv = '<div id="'+divID+'" class="rum_message_created"><div class="rmu_loader_css"><div/></div>';
    	jQuery('#rmu_update_message').append(loaderDiv).fadeIn(500);

		jQuery.ajax({
			type: 'POST',			
			data:{ blogname: blogN },
		    url: 'http://iris.scanmine.com/wp-content/plugins/rssMultiUpdater/classes/RssUpdateSingle.php',
		    success: function(result){
		        if( result ){
		        	jQuery('.rmu_loader').hide();
					var data    = JSON.parse( result );
					
					console.log( "Div ID "+ divID );
					DivHide = ".aw_message_Section #"+divID;
					console.log( DivHide );

					jQuery(DivHide).remove();
					var message = 	'<div class="rum_message_created"> <p class="rmu_close">X</p><p> <span>Blog Name:</span> '+blogN+'</p> <p>'+ data["created"]+'</p> <p>'+data["updated"]+'</p> </div>';
					jQuery('#rmu_update_message').append(message).fadeIn(2000);		        	
		        }
		    }
		});
    }

});