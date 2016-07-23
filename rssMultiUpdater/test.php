<?php
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';
//<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script> -->
echo  '<script type=\'text/javascript\'>
jQuery( document ).ready(function() {    
    console.log( \'rmu ready!\' );
    var blogN =	[\'backpainlatest\',\'astrologi\',\'golfnyhetene\',\'b2news-trump\',\'astrologinytt_test\',\'health\',\'watchportal\',\'pokernytt\',\'knowntreatments\'];
    for ( index in blogN) {
	    console.log(blogN[index]);
	    getAjaxData( blogN[index] );
	}

    function getAjaxData( blogN ){
    	jQuery.ajax({
			type: \'GET\',			
			data:{ blogname: blogN },
		    url: \'http://iris.scanmine.com/wp-content/plugins/rssMultiUpdater/classes/RssUpdateSingleGet.php\',
		    success: function(result){
		        if( result ){		        	
					var data    = JSON.parse( result );					
					console.log( data );				
		        }
		    }
		});
    }
});
</script>';
?>