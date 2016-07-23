( function( $ ) {
    $( function() {
    	
    	$("#aw_sourceBlog").select2();

    	//** onChange Function.  **//
      	$("#aw_sourceBlog").on("change", function() {
			var sourcetemplatedir  	=  $("#aw_sourceBlog").val();
			var serverPath 			=	$("#serverPath").val();

			//** hide if any Message is displayed.**//
			$('#aw_Draft_Table_Message').text('');
	        $('#aw_Draft_Table_Message').fadeOut('5000');

			$("#aw_Draft_Table").text('');
			$("#aw_Draft_Table").addClass("aw_loading");

			var getDataUrl = serverPath+"functions/ajaxGetData.php?source="+sourcetemplatedir;

			if( sourcetemplatedir !== ''){
				//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
				$.ajax( getDataUrl )
				.done(function( data) { 
					//** Remove Loader Class**//
					$("#aw_Draft_Table").removeClass("aw_loading");
					
					//** Add Data Received From Ajax Request To Div.**//
					$("#aw_Draft_Table").html(data);
					
				})
				.fail(function() {
					
					//** Remove Loader Class**//
					$("#aw_Draft_Table").removeClass("aw_loading");
					
					$("#aw_Draft_Table").html('<div style="color: red">Network Error Occured. Please try Again Later.</div>');
					
				});
			}else{
				
				//** Remove Loader Class**//
				$("#aw_Draft_Table").removeClass("aw_loading");	

				//** Add Error Message If No Blog Selected**//
				$("#aw_Draft_Table").html('<div style="color: red; text-align: center; font-size: 18px;">Source Blog Not Selected</div>');
				
			}
			return false;
      	});

		//*** Code Block to Delete Single Post.**//
		$('#aw_Draft_Table').on('click', '.aw_delete_row', function (){
	        
	        //** Intialization of Variables. **//	      
	        var sourcetemplatedir  	=  $("#aw_sourceBlog").val();
			var serverPath 			=	$("#serverPath").val();


			//** Remove Errors if already Set. **//
	        $('#aw_Draft_Table_Message').text('');
	        $('#aw_Draft_Table_Message').fadeOut('5000');

	        var answer = confirm('Are you sure you want to delete this?');
			
			if (answer){

				//** get the ID of Post. **//
			 	var valueT = $(this).attr('data-val');

			 	//** Ajax function to delete a Post. **//
			 	var getDataUrl = serverPath+"functions/ajaxDeleteData.php?source="+sourcetemplatedir+"&id="+valueT;

				if( sourcetemplatedir !== ''){
					//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
					$.ajax( getDataUrl )
					.done(function( data) { 
						
						
				      	var ajaxData = $.parseJSON(data);
				      	
				      	if( ajaxData.status == 1 ){
				      		var hideID =	'#post-'+valueT;
							$(hideID).fadeOut('5000');				      		
				      	} 
				      	
				        $('#aw_Draft_Table_Message').html(ajaxData.message);
				        $('#aw_Draft_Table_Message').fadeIn('5000');
								
					})
					.fail(function() {
						
						$('#aw_Draft_Table_Message').html('<div style="color: red; text-align: center; font-size: 18px;">Network Error Occured. Please try Again Later.</div>');
				        $('#aw_Draft_Table_Message').fadeIn('5000');
						
					});
				}else{
					
					$('#aw_Draft_Table_Message').html('<div style="color: red; text-align: center; font-size: 18px;">No Source Blog Selected.</div>');
				    $('#aw_Draft_Table_Message').fadeIn('5000');
						
				}
			 	//** Ajax function Ends here.**//		        
			}
	        
	        return false;
	    });
		
		

		//** Code to publish Single post **//
		$('#aw_Draft_Table').on('click', '.aw_publish_row', function (){
			 
		});


		//** Select all property. **//
		$('#aw_Draft_Table').on('click', '#aw_select_all', function (){

			//** Remove Errors if already Set. **//
	        $('#aw_Draft_Table_Message').text('');
	        $('#aw_Draft_Table_Message').fadeOut('5000');
			
			var checkboxes = $('.aw_checkboxes');
		
			if( this.checked){
			 	checkboxes.attr("checked" , true);			
			} else {
			    checkboxes.attr ( "checked" , false );
			}
			return true;
		});


		//** Select all property. **//
		$('#aw_Draft_Table').on('click', '#aw_publish_all_button', function (){
			
			//** Remove Errors if already Set. **//
	        $('#aw_Draft_Table_Message').text('');
	        $('#aw_Draft_Table_Message').fadeOut('5000');

			var allVals = [];

			var checkboxes = $('.aw_checkboxes');

		    checkboxes.each(function() {
			    // allVals.push($(this).val());		       
		       	if( this.checked){			 		
			 		allVals.push($(this).val());
				}
		    });


		    
		    //** Intialization of Variables. **//	      
	        var sourcetemplatedir  	=  $("#aw_sourceBlog").val();
			var serverPath 			=  $("#serverPath").val();



	        var answer = confirm('Are you sure you want to Publish Selected Posts?');
			
			if (answer){

				//** check if any element is selected. **//
			    if(allVals.length > 0){
			    	// alert('Items Seleted');
			    }else{
			    	// alert('Items Not Seleted');
			    	$('#aw_Draft_Table_Message').html('<div style="color: Red; text-align: center; font-size: 18px;">No Post Selected. Please select Post to publish.</div>');
					$('#aw_Draft_Table_Message').fadeIn('5000');
			    	return false;
			    }		
			 
				//** get the ID of Post. **//
			 	// var valueT = JSON.stringify(allVals);
			 	var valueT = allVals.join(); 

			 	// alert(valueT);
			 	//** Ajax function to delete a Post. **//
			 	var getDataUrl = serverPath+"functions/ajaxPublishDataAll.php?source="+sourcetemplatedir+"&id="+valueT;

				if( sourcetemplatedir !== ''){
					
					//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
					$.ajax( getDataUrl )
					.done(function( data) { 
						
				      	var ajaxData = $.parseJSON(data);

				      	// console.log( ajaxData );
					   if( ajaxData.status == 1 ){
					      	for (i = 0; i < allVals.length; i++) {
							    var hideID =	'#post-'+allVals[i];
								$(hideID).fadeOut('5000');								
							}
						}	
										      	
				      
				      	//** show output Message. **//
				        $('#aw_Draft_Table_Message').html(ajaxData.message);
				        // $('#aw_Draft_Table_Message').html('<div style="color: green; text-align: center; font-size: 18px;">Posts published Successfully.</div>');
				        $('#aw_Draft_Table_Message').fadeIn('5000');
								
					})
					.fail(function() {

						
						//** show output Message. **//						
						$('#aw_Draft_Table_Message').html('<div style="color: red; text-align: center; font-size: 18px;">Network Error Occured. Please try Again Later.</div>');
				        $('#aw_Draft_Table_Message').fadeIn('5000');
						
					});
				}else{
					
				    //** show output Message. **//
					$('#aw_Draft_Table_Message').html('<div style="color: red; text-align: center; font-size: 18px;">Network Error Occured. Please try Again Later.</div>');
				    $('#aw_Draft_Table_Message').fadeIn('5000');
						
				}
			 	//** Ajax function Ends here.**//		        
			}
			return true;
		});

		$('#aw_Draft_Table').on('click', '#aw_delete_all_button', function (){
			
			//** Remove Errors if already Set. **//
	        $('#aw_Draft_Table_Message').text('');
	        $('#aw_Draft_Table_Message').fadeOut('5000');

			var allVals = [];

			var checkboxes = $('.aw_checkboxes');

		    checkboxes.each(function() {
			    // allVals.push($(this).val());		       
		       	if( this.checked){			 		
			 		allVals.push($(this).val());
				}
		    });

		    //** Intialization of Variables. **//	      
	        var sourcetemplatedir  	=  $("#aw_sourceBlog").val();
			var serverPath 			=  $("#serverPath").val();



	        var answer = confirm('Are you sure you want to Publish Selected Posts?');
			
			if (answer){

				//** check if any element is selected. **//
			    if(allVals.length > 0){
			    	// alert('Items Seleted');
			    }else{
			    	// alert('Items Not Seleted');
			    	$('#aw_Draft_Table_Message').html('<div style="color: Red; text-align: center; font-size: 18px;">No Post Selected. Please select Post to Delete.</div>');
					$('#aw_Draft_Table_Message').fadeIn('5000');
			    	return false;
			    }		
			 
				//** get the ID of Post. **//
			 	// var valueT = JSON.stringify(allVals);
			 	var valueT = allVals.join(); 

			 	// alert(valueT);
			 	//** Ajax function to delete a Post. **//
			 	var getDataUrl = serverPath+"functions/ajaxDeleteDataAll.php?source="+sourcetemplatedir+"&id="+valueT;

				if( sourcetemplatedir !== ''){
					
					//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
					$.ajax( getDataUrl )
					.done(function( data) { 
						
				      	var ajaxData = $.parseJSON(data);

				      	// console.log( ajaxData );
					   if( ajaxData.status == 1 ){
					      	for (i = 0; i < allVals.length; i++) {
							    var hideID =	'#post-'+allVals[i];
								$(hideID).fadeOut('5000');								
							}
						}	
										      	
				      
				      	//** show output Message. **//
				        $('#aw_Draft_Table_Message').html(ajaxData.message);
				        // $('#aw_Draft_Table_Message').html('<div style="color: green; text-align: center; font-size: 18px;">Posts published Successfully.</div>');
				        $('#aw_Draft_Table_Message').fadeIn('5000');
								
					})
					.fail(function() {

						
						//** show output Message. **//						
						$('#aw_Draft_Table_Message').html('<div style="color: red; text-align: center; font-size: 18px;">Network Error Occured. Please try Again Later.</div>');
				        $('#aw_Draft_Table_Message').fadeIn('5000');
						
					});
				}else{
					
				    //** show output Message. **//
					$('#aw_Draft_Table_Message').html('<div style="color: red; text-align: center; font-size: 18px;">Network Error Occured. Please try Again Later.</div>');
				    $('#aw_Draft_Table_Message').fadeIn('5000');
						
				}
			 	//** Ajax function Ends here.**//		        
			}
			return true;
		});
		
		

    } );
} ( jQuery ) );