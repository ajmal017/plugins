( function( $ ) {
    $( function() {
      	
      	//** Validation for favicon. **//
      	$('form#aw_favicon_form').submit(function() {					
			
			var filename = $("#aw_favicon").val();

			if( filename == '') {
				alert('Please select favicon.');
				return false;
			}

			if ( !(/\.(png)$/i).test( filename )) {
			  alert('Please upload .png file.');
			  return false;
			}
			
			return true;
			
		});

		//**  Validation for logo. **//
		$('form#aw_logo_form').submit(function() {					
			
			var filename = $("#aw_logo").val();

			if( filename == '') {
				alert('Please select logo.');
				return false;
			}

			if ( !(/\.(png)$/i).test( filename )) {
			  alert('Please upload .png file.');
			  return false;
			}
						    
			return true;
			
		});

		//** Delete Favicon. **//
		$('#aw_delete_favicon').click(function() {
			var status = confirm("Are You Sure To Delete Favicon.");

			if( status == true ){				
				return true;
			}else{				
				return false;
			}
		});

		//** Delete Logo. **//
		$('#aw_delete_logo').click(function() {
			var status = confirm("Are You Sure To Delete Logo.");

			if( status == true ){				
				return true;
			}else{				
				return false;
			}
		});

		

    } );
} ( jQuery ) );