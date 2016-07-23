( function( $ ) {
    $( function() {

    	//** Change Select Box to Searchable Select Box.**//
    	$("#widgetSource").select2();
		
		//** Change Select Box to Searchable Select Box.**//
    	//$("#widgetDestination").select2();
    	$("#widgetDestination").multipleSelect({
            filter: true,
            multiple: true
        });



    	//** Disable Submit in No Widgets.**//
		$("#copyWidgetSubmit").attr('disabled','disabled');

    	//** click Function.  **//
      	$('#aw_show_hide_tag').click(function() {
			$("#Aw_widgetList").fadeToggle('1000');
			return false;
		});		
		

		var elementRemoved = '';
      	
      	//** onChange Function.  **//
      	$("#widgetSource").on("change", function() {
			var sourcetemplatedir  	=  $("#widgetSource").val();
			var serverPath 			=	$("#serverPath").val();

			//** Rest Destination Blog. If Any Blog is already Selected. **//
			// $('#widgetDestination').select2().val('');
			// $("#widgetDestination").multipleSelect('refresh');
			$("#widgetDestination").multipleSelect('uncheckAll');

			//** Hide Any message if Already Posted Data.**//	
			$("#PostError").fadeOut('2000');
			

			//** Enable Submit button**//
			$("#copyWidgetSubmit").removeAttr('disabled');

			//** Remove Widget Error Message**//
			$("#widgetTitleMessage").text('');
			$("#widgetTitleMessage").removeClass('aw_errorMessage');

			//** Remove Source Select Error Message. **//
			$("#widgetSource").removeClass('aw_error');
			$("#widgetSourceMessage").text('');
			$("#widgetSourceMessage").removeClass('aw_errorMessage');

			//** Add Prevoiusly Removed Blog to Destination Blog(s) Selection List.**//
			if( elementRemoved !== '' ){
				var optionElement 	= "<option style='padding-bottom: 5px;' value='"+elementRemoved+"'>"+elementRemoved+"</option>";
				$('#widgetDestination').append(optionElement);
				
				//** Remove Option Name from Global Variable**//
				elementRemoved = '';
			} 

			//** Remove Source Blog Name Form Destination Blog(s) Selection List.**//
			var OptionToRemove 	= "#widgetDestination option[value='"+sourcetemplatedir+"']";
			
			//** Add Removed Option to Global Variable.**//
			elementRemoved 		= sourcetemplatedir; 
			$(OptionToRemove).remove();

			//** Remove form the Div**//
			$("#awOptionsCheck").text('');

			//** Add Gif Loader Class**//
			$("#awOptionsCheck").addClass("aw_loading");
			
			var getDataUrl = serverPath+"functions/ajaxGetWidgetData.php?source="+sourcetemplatedir;

			if( sourcetemplatedir !== ''){
				//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
				$.ajax( getDataUrl )
				.done(function( data) { 
					//** Remove Loader Class**//
					$("#awOptionsCheck").removeClass("aw_loading");

					//** Add Data Received From Ajax Request To Div.**//
					$("#awOptionsCheck").html(data);

					// if ("/No Widgets Defined for the site/i".test(dataString)){						
					if (data.indexOf("No Widgets Defined for the site") >= 0 ){						
						//** Disable Submit in No Widgets.**//    
						$("#copyWidgetSubmit").attr('disabled','disabled');	
					}
					
				})
				.fail(function() {
					
					//** Remove Loader Class**//
					$("#awOptionsCheck").removeClass("aw_loading");
					
					$("#awOptionsCheck").html('<div style="color: red">Network Error Occured. Please try Again Later.</div>');
					
					//** Disable Submit in No Widgets.**//
					$("#copyWidgetSubmit").attr('disabled','disabled');	
				});
			}else{
				
				//** Remove Loader Class**//
				$("#awOptionsCheck").removeClass("aw_loading");	

				//** Add Error Message If No Blog Selected**//
				$("#awOptionsCheck").html('<div style="color: red">Source Blog Not Selected</div>');
				
				//** Disable Submit in No Widgets.**//
				$("#copyWidgetSubmit").attr('disabled','disabled');	
			}
			return false;
		});


      	//** onchange function for **//
      	$("#widgetDestination").on("change", function() {


			$("#widgetDestination").removeClass('aw_error');
			$("#widgetDestinationMessage").text('');
			$("#widgetDestinationMessage").removeClass('aw_errorMessage');

      		var WidgetTitle 	= ''; 
			$( "#widgetDestination option:selected" ).each(function() {
				WidgetTitle += $( this ).text() + " ";
			});

			if (WidgetTitle == '') {
				$("#widgetDestination").addClass('aw_error');

				$("#widgetDestinationMessage").addClass('aw_errorMessage');
				$("#widgetDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};  

      	});

      	
      	//** Validation for Copy Widget Form. **//
      	$('form#aw_copywidget_form').submit(function() {			  		
			
			var flag = true;

			$("#widgetSource").removeClass('aw_error');
			$("#widgetTitle").removeClass('aw_error');
			$("#widgetDestination").removeClass('aw_error');

			$("#widgetSourceMessage").text('');
			$("#widgetSourceMessage").removeClass('aw_errorMessage');

			$("#widgetTitleMessage").text('');
			$("#widgetTitleMessage").removeClass('aw_errorMessage');

			$("#widgetDestinationMessage").text('');
			$("#widgetDestinationMessage").removeClass('aw_errorMessage');	

		

			var widgetSource 	= $("#widgetSource").val();
			if (widgetSource == '') {
				$("#widgetSource").addClass('aw_error');
				$("#widgetSourceMessage").addClass('aw_errorMessage');
				$("#widgetSourceMessage").text('Please select a Source Blog.');
				// alert('Please select a Source Template.');
				flag = false;
			};

			var WidgetTitle = $('input[name="widgetTitle[]"]:checked').length;
			if (!WidgetTitle) {
    			$("#widgetTitleMessage").addClass('aw_errorMessage');
				$("#widgetTitleMessage").text('No Widget(s) Selected.');
				
				flag = false;
			}  
			
			var WidgetTitle 	= ''; 
			$( "#widgetDestination option:selected" ).each(function() {

				WidgetTitle += $( this ).text() + " ";
			});

			if (WidgetTitle == '') {
				$("#widgetDestination").addClass('aw_error');

				$("#widgetDestinationMessage").addClass('aw_errorMessage');
				$("#widgetDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};


			
			if(flag == true ){
				return true;
			}else{
				return false;
			}
			
		});


    } );
} ( jQuery ) );

//** Function for Custom OPtion Form **//
( function( $ ) {
    $( function() {
    

    	//** Change Select Box to Searchable Select Box.**//
    	$("#customSource").select2();
		
		//** Change Select Box to Searchable Select Box.**//
    	// $("#customDestination").select2();

    	$("#customDestination").multipleSelect({
            filter: true,
            multiple: true
        });


	   	//** Disable Submit in No Widgets.**//
		$("#copyCustomSubmit").attr('disabled','disabled');

    	
		var elementRemoved = '';
      	
      	//** onChange Function.  **//
      	$("#customSource").on("change", function() {
			var sourcetemplatedir  	=  $("#customSource").val();
			var serverPath 			=	$("#serverPath").val();

			//** Rest Destination Blog. If Any Blog is already Selected. **//
			// $('#customDestination').select2().val("");
			$("#customDestination").multipleSelect('uncheckAll');

			//** Hide Any message if Already Posted Data.**//	
			$("#PostError").fadeOut('2000');
			

			//** Enable Submit button**//
			$("#copyCustomSubmit").removeAttr('disabled');

			//** Remove Widget Error Message**//
			$("#customTitleMessage").text('');
			$("#customTitleMessage").removeClass('aw_errorMessage');

			//** Remove Source Select Error Message. **//
			$("#customSource").removeClass('aw_error');
			$("#customSourceMessage").text('');
			$("#customSourceMessage").removeClass('aw_errorMessage');

			//** Add Prevoiusly Removed Blog to Destination Blog(s) Selection List.**//
			if( elementRemoved !== '' ){
				var optionElement 	= "<option style='padding-bottom: 5px;' value='"+elementRemoved+"'>"+elementRemoved+"</option>";
				$('#customDestination').append(optionElement);
				
				//** Remove Option Name from Global Variable**//
				elementRemoved = '';
			} 

			//** Remove Source Blog Name Form Destination Blog(s) Selection List.**//
			var OptionToRemove 	= "#customDestination option[value='"+sourcetemplatedir+"']";
			
			//** Add Removed Option to Global Variable.**//
			elementRemoved 		= sourcetemplatedir; 
			$(OptionToRemove).remove();

			//** Remove form the Div**//
			$("#awCustomOptionsCheck").text('');

			//** Add Gif Loader Class**//
			$("#awCustomOptionsCheck").addClass("aw_loading");
			
			var getDataUrl = serverPath+"functions/ajaxGetCustomData.php?source="+sourcetemplatedir;

			if( sourcetemplatedir !== ''){
				//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
				$.ajax( getDataUrl )
				.done(function( data) { 
					//** Remove Loader Class**//
					$("#awCustomOptionsCheck").removeClass("aw_loading");

					//** Add Data Received From Ajax Request To Div.**//
					$("#awCustomOptionsCheck").html(data);

					// if ("/No Widgets Defined for the site/i".test(dataString)){						
					if (data.indexOf("No Custom Options Defined for the site.") >= 0 ){						
						//** Disable Submit in No Widgets.**//    
						$("#copyCustomSubmit").attr('disabled','disabled');	
					}
					
				})
				.fail(function() {
					
					//** Remove Loader Class**//
					$("#awCustomOptionsCheck").removeClass("aw_loading");
					
					$("#awCustomOptionsCheck").html('<div style="color: red">Network Error Occured. Please try Again Later.</div>');
					
					//** Disable Submit in No Widgets.**//
					$("#copyCustomSubmit").attr('disabled','disabled');	
				});
			}else{
				
				//** Remove Loader Class**//
				$("#awCustomOptionsCheck").removeClass("aw_loading");	

				//** Add Error Message If No Blog Selected**//
				$("#awCustomOptionsCheck").html('<div style="color: red">Source Blog Not Selected</div>');
				
				//** Disable Submit in No Widgets.**//
				$("#copyCustomSubmit").attr('disabled','disabled');	
			}
			return false;
		});


      	//** onchange function for **//
      	$("#customDestination").on("change", function() {


			$("#customDestination").removeClass('aw_error');
			$("#customDestinationMessage").text('');
			$("#customDestinationMessage").removeClass('aw_errorMessage');

      		var WidgetTitle 	= ''; 
			$( "#customDestination option:selected" ).each(function() {
				WidgetTitle += $( this ).text() + " ";
			});

			if (WidgetTitle == '') {
				$("#customDestination").addClass('aw_error');

				$("#customDestinationMessage").addClass('aw_errorMessage');
				$("#customDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};  

      	});

      	
      	//** Validation for Copy Widget Form. **//
      	$('form#aw_copycustom_form').submit(function() {			  		
			
			var flag = true;

			$("#customSource").removeClass('aw_error');
			$("#customTitle").removeClass('aw_error');
			$("#customDestination").removeClass('aw_error');

			$("#customSourceMessage").text('');
			$("#customSourceMessage").removeClass('aw_errorMessage');

			$("#customTitleMessage").text('');
			$("#customTitleMessage").removeClass('aw_errorMessage');

			$("#customDestinationMessage").text('');
			$("#customDestinationMessage").removeClass('aw_errorMessage');	

		

			var customSource 	= $("#customSource").val();
			if (customSource == '') {
				$("#customSource").addClass('aw_error');
				$("#customSourceMessage").addClass('aw_errorMessage');
				$("#customSourceMessage").text('Please select a Source Blog.');
				// alert('Please select a Source Template.');
				flag = false;
			};

			var customTitle = $('input[name="customTitle[]"]:checked').length;
			if (!customTitle) {
    			$("#customTitleMessage").addClass('aw_errorMessage');
				$("#customTitleMessage").text('No custom option(s) Selected.');
				
				flag = false;
			}  
			
			var customTitle 	= ''; 
			$( "#customDestination option:selected" ).each(function() {

				customTitle += $( this ).text() + " ";
			});

			if (customTitle == '') {
				$("#customDestination").addClass('aw_error');

				$("#customDestinationMessage").addClass('aw_errorMessage');
				$("#customDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};


			
			if(flag == true ){
				return true;
			}else{
				return false;
			}
			
		});


    } );
} ( jQuery ) );



//** Function for Ads Form **//
( function( $ ) {
    $( function() {
    	
    	//** Change Select Box to Searchable Select Box.**//
    	$("#adsSource").select2();
		
		//** Change Select Box to Searchable Select Box.**//
    	// $("#adsDestination").select2();
    	$("#adsDestination").multipleSelect({
		    filter: true,
		    multiple: true
		});

    	//** Disable Submit in No Widgets.**//
		$("#copyAdsSubmit").attr('disabled','disabled');

    	
		var elementRemoved = '';
      	
      	//** onChange Function.  **//
      	$("#adsSource").on("change", function() {
			var sourcetemplatedir  	=  $("#adsSource").val();
			var serverPath 			=	$("#serverPath").val();

			//** Rest Destination Blog. If Any Blog is already Selected. **//
			// $('#adsDestination').select2().val('');
			$("#adsDestination").multipleSelect('uncheckAll');


			//** Hide Any message if Already Posted Data.**//	
			$("#PostError").fadeOut('2000');
			

			//** Enable Submit button**//
			$("#copyAdsSubmit").removeAttr('disabled');

			//** Remove Widget Error Message**//
			$("#adsTitleMessage").text('');
			$("#adsTitleMessage").removeClass('aw_errorMessage');

			//** Remove Source Select Error Message. **//
			$("#adsSource").removeClass('aw_error');
			$("#adsSourceMessage").text('');
			$("#adsSourceMessage").removeClass('aw_errorMessage');

			//** Add Prevoiusly Removed Blog to Destination Blog(s) Selection List.**//
			if( elementRemoved !== '' ){
				var optionElement 	= "<option style='padding-bottom: 5px;' value='"+elementRemoved+"'>"+elementRemoved+"</option>";
				$('#adsDestination').append(optionElement);
				
				//** Remove Option Name from Global Variable**//
				elementRemoved = '';
			} 

			//** Remove Source Blog Name Form Destination Blog(s) Selection List.**//
			var OptionToRemove 	= "#adsDestination option[value='"+sourcetemplatedir+"']";
			
			//** Add Removed Option to Global Variable.**//
			elementRemoved 		= sourcetemplatedir; 
			$(OptionToRemove).remove();

			//** Remove form the Div**//
			$("#awAdsOptionsCheck").text('');

			//** Add Gif Loader Class**//
			$("#awAdsOptionsCheck").addClass("aw_loading");
			
			var getDataUrl = serverPath+"functions/ajaxAdsData.php?source="+sourcetemplatedir;

			if( sourcetemplatedir !== ''){
				//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
				$.ajax( getDataUrl )
				.done(function( data) { 
					//** Remove Loader Class**//
					$("#awAdsOptionsCheck").removeClass("aw_loading");

					//** Add Data Received From Ajax Request To Div.**//
					$("#awAdsOptionsCheck").html(data);

					// if ("/No Widgets Defined for the site/i".test(dataString)){						
					if (data.indexOf("No Ads Options Defined for the site.") >= 0 ){						
						//** Disable Submit in No Widgets.**//    
						$("#copyAdsSubmit").attr('disabled','disabled');	
					}
					
				})
				.fail(function() {
					
					//** Remove Loader Class**//
					$("#awAdsOptionsCheck").removeClass("aw_loading");
					
					$("#awAdsOptionsCheck").html('<div style="color: red">Network Error Occured. Please try Again Later.</div>');
					
					//** Disable Submit in No Widgets.**//
					$("#copyAdsSubmit").attr('disabled','disabled');	
				});
			}else{
				
				//** Remove Loader Class**//
				$("#awAdsOptionsCheck").removeClass("aw_loading");	

				//** Add Error Message If No Blog Selected**//
				$("#awAdsOptionsCheck").html('<div style="color: red">Source Blog Not Selected</div>');
				
				//** Disable Submit in No Widgets.**//
				$("#copyAdsSubmit").attr('disabled','disabled');	
			}
			return false;
		});


      	//** onchange function for **//
      	$("#adsDestination").on("change", function() {


			$("#adsDestination").removeClass('aw_error');
			$("#adsDestinationMessage").text('');
			$("#adsDestinationMessage").removeClass('aw_errorMessage');

      		var adsTitle 	= ''; 
			$( "#adsDestination option:selected" ).each(function() {
				adsTitle += $( this ).text() + " ";
			});

			if (adsTitle == '') {
				$("#adsDestination").addClass('aw_error');

				$("#adsDestinationMessage").addClass('aw_errorMessage');
				$("#adsDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};  

      	});

      	
      	//** Validation for Copy Widget Form. **//
      	$('form#aw_copyads_form').submit(function() {			  		
			
			var flag = true;

			$("#adsSource").removeClass('aw_error');
			$("#adsTitle").removeClass('aw_error');
			$("#adsDestination").removeClass('aw_error');

			$("#adsSourceMessage").text('');
			$("#adsSourceMessage").removeClass('aw_errorMessage');

			$("#adsTitleMessage").text('');
			$("#adsTitleMessage").removeClass('aw_errorMessage');

			$("#adsDestinationMessage").text('');
			$("#adsDestinationMessage").removeClass('aw_errorMessage');	

		

			var adsSource 	= $("#adsSource").val();
			if (adsSource == '') {
				$("#adsSource").addClass('aw_error');
				$("#adsSourceMessage").addClass('aw_errorMessage');
				$("#adsSourceMessage").text('Please select a Source Blog.');
				// alert('Please select a Source Template.');
				flag = false;
			};

			var adsTitle = $('input[name="adsTitle[]"]:checked').length;
			if (!adsTitle) {
    			$("#adsTitleMessage").addClass('aw_errorMessage');
				$("#adsTitleMessage").text('No ads option(s) Selected.');
				
				flag = false;
			}  
			
			var adsTitle 	= ''; 
			$( "#adsDestination option:selected" ).each(function() {

				adsTitle += $( this ).text() + " ";
			});

			if (adsTitle == '') {
				$("#adsDestination").addClass('aw_error');

				$("#adsDestinationMessage").addClass('aw_errorMessage');
				$("#adsDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};


			
			if(flag == true ){
				return true;
			}else{
				return false;
			}
			
		});


    } );
} ( jQuery ) );


//** Function for Menus Form **//
( function( $ ) {
    $( function() {
		

    	//** Change Select Box to Searchable Select Box.**//
    	$("#menuSource").select2();
		
		//** Change Select Box to Searchable Select Box.**//
    	// $("#menuDestination").select2();
    	$("#menuDestination").multipleSelect({
		    filter: true,
		    multiple: true
		});


    	//** Disable Submit in No Widgets.**//
		$("#copyMenuSubmit").attr('disabled','disabled');

    	
		var elementRemoved = '';
      	
      	//** onChange Function.  **//
      	$("#menuSource").on("change", function() {
			var sourcetemplatedir  	=  $("#menuSource").val();
			var serverPath 			=	$("#serverPath").val();	

			//** Rest thre destination. **//
    		// $("#menuDestination").select2().val('');   
    		$("#menuDestination").multipleSelect('uncheckAll'); 		

			//** Hide Any message if Already Posted Data.**//	
			$("#PostError").fadeOut('2000');
			

			//** Enable Submit button**//
			$("#copyMenuSubmit").removeAttr('disabled');

			//** Remove Widget Error Message**//
			$("#menuTitleMessage").text('');
			$("#menuTitleMessage").removeClass('aw_errorMessage');

			//** Remove Source Select Error Message. **//
			$("#menuSource").removeClass('aw_error');
			$("#menuSourceMessage").text('');
			$("#menuSourceMessage").removeClass('aw_errorMessage');

			//** Add Prevoiusly Removed Blog to Destination Blog(s) Selection List.**//
			if( elementRemoved !== '' ){
				var optionElement 	= "<option style='padding-bottom: 5px;' value='"+elementRemoved+"'>"+elementRemoved+"</option>";
				$('#menuDestination').append(optionElement);
				
				//** Remove Option Name from Global Variable**//
				elementRemoved = '';
			} 

			//** Remove Source Blog Name Form Destination Blog(s) Selection List.**//
			var OptionToRemove 	= "#menuDestination option[value='"+sourcetemplatedir+"']";
			
			//** Add Removed Option to Global Variable.**//
			elementRemoved 		= sourcetemplatedir; 
			$(OptionToRemove).remove();

			//** Remove form the Div**//
			$("#awMenuOptionsCheck").text('');

			//** Add Gif Loader Class**//
			$("#awMenuOptionsCheck").addClass("aw_loading");
			
			var getDataUrl = serverPath+"functions/ajaxGetMenuData.php?source="+sourcetemplatedir;

			if( sourcetemplatedir !== ''){
				//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
				$.ajax( getDataUrl )
				.done(function( data) { 
					//** Remove Loader Class**//
					$("#awMenuOptionsCheck").removeClass("aw_loading");

					//** Add Data Received From Ajax Request To Div.**//
					$("#awMenuOptionsCheck").html(data);

					// if ("/No Widgets Defined for the site/i".test(dataString)){						
					if (data.indexOf("No  Menu(s) Defined for the site.") >= 0 ){						
						//** Disable Submit in No Widgets.**//    
						$("#copyMenuSubmit").attr('disabled','disabled');	
					}
					
				})
				.fail(function() {
					
					//** Remove Loader Class**//
					$("#awMenuOptionsCheck").removeClass("aw_loading");
					
					$("#awMenuOptionsCheck").html('<div style="color: red">Network Error Occured. Please try Again Later.</div>');
					
					//** Disable Submit in No Widgets.**//
					$("#copyMenuSubmit").attr('disabled','disabled');	
				});
			}else{
				
				//** Remove Loader Class**//
				$("#awMenuOptionsCheck").removeClass("aw_loading");	

				//** Add Error Message If No Blog Selected**//
				$("#awMenuOptionsCheck").html('<div style="color: red">Source Blog Not Selected</div>');
				
				//** Disable Submit in No Widgets.**//
				$("#copyMenuSubmit").attr('disabled','disabled');
			}
			return false;
		});


      	//** onchange function for **//
      	$("#menuDestination").on("change", function() {


			$("#menuDestination").removeClass('aw_error');
			$("#menuDestinationMessage").text('');
			$("#menuDestinationMessage").removeClass('aw_errorMessage');

      		var menuTitle 	= ''; 
			$( "#menuDestination option:selected" ).each(function() {
				menuTitle += $( this ).text() + " ";
			});

			if (menuTitle == '') {
				$("#menuDestination").addClass('aw_error');

				$("#menuDestinationMessage").addClass('aw_errorMessage');
				$("#menuDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};  

      	});

      	
      	//** Validation for Copy Widget Form. **//
      	$('form#aw_copymenus_form').submit(function() {			  		
			
			var flag = true;

			$("#menuSource").removeClass('aw_error');
			$("#menuTitle").removeClass('aw_error');
			$("#menuDestination").removeClass('aw_error');

			$("#menuSourceMessage").text('');
			$("#menuSourceMessage").removeClass('aw_errorMessage');

			$("#menuTitleMessage").text('');
			$("#menuTitleMessage").removeClass('aw_errorMessage');

			$("#menuDestinationMessage").text('');
			$("#menuDestinationMessage").removeClass('aw_errorMessage');	

		

			var menuSource 	= $("#menuSource").val();
			if (menuSource == '') {
				$("#menuSource").addClass('aw_error');
				$("#menuSourceMessage").addClass('aw_errorMessage');
				$("#menuSourceMessage").text('Please select a Source Blog.');
				// alert('Please select a Source Template.');
				flag = false;
			};

			var menuTitle = $('input[name="menuTitle[]"]:checked').length;
			if (!menuTitle) {
    			$("#menuTitleMessage").addClass('aw_errorMessage');
				$("#menuTitleMessage").text('No menu(s) Selected.');
				
				flag = false;
			}  
			
			var menuTitle 	= ''; 
			$( "#menuDestination option:selected" ).each(function() {

				menuTitle += $( this ).text() + " ";
			});

			if (menuTitle == '') {
				$("#menuDestination").addClass('aw_error');

				$("#menuDestinationMessage").addClass('aw_errorMessage');
				$("#menuDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};


			
			if(flag == true ){
				return true;
			}else{
				return false;
			}
			
		});


    } );
} ( jQuery ) );



//** Function for Page Form **//
( function( $ ) {
    $( function() {
		

    	//** Change Select Box to Searchable Select Box.**//
    	$("#pageSource").select2();
		
		//** Change Select Box to Searchable Select Box.**//
    	// $("#pageDestination").select2();
		$("#pageDestination").multipleSelect({
		    filter: true,
		    multiple: true
		});


    	//** Disable Submit in No Widgets.**//
		$("#copyPageSubmit").attr('disabled','disabled');

    	
		var elementRemoved = '';
      	
      	//** onChange Function.  **//
      	$("#pageSource").on("change", function() {
			var sourcetemplatedir  	=  $("#pageSource").val();
			var serverPath 			=	$("#serverPath").val();	

			//** Rest thre destination. **//
    		// $("#pageDestination").select2().val('');
    		$("#pageDestination").multipleSelect('uncheckAll');		

			//** Hide Any message if Already Posted Data.**//	
			$("#PostError").fadeOut('2000');
			

			//** Enable Submit button**//
			$("#copyPageSubmit").removeAttr('disabled');

			//** Remove Widget Error Message**//
			$("#pageTitleMessage").text('');
			$("#pageTitleMessage").removeClass('aw_errorMessage');

			//** Remove Source Select Error Message. **//
			$("#pageSource").removeClass('aw_error');
			$("#pageSourceMessage").text('');
			$("#pageSourceMessage").removeClass('aw_errorMessage');

			//** Add Prevoiusly Removed Blog to Destination Blog(s) Selection List.**//
			if( elementRemoved !== '' ){
				var optionElement 	= "<option style='padding-bottom: 5px;' value='"+elementRemoved+"'>"+elementRemoved+"</option>";
				$('#pageDestination').append(optionElement);
				
				//** Remove Option Name from Global Variable**//
				elementRemoved = '';
			} 

			//** Remove Source Blog Name Form Destination Blog(s) Selection List.**//
			var OptionToRemove 	= "#pageDestination option[value='"+sourcetemplatedir+"']";
			
			//** Add Removed Option to Global Variable.**//
			elementRemoved 		= sourcetemplatedir; 
			$(OptionToRemove).remove();

			//** Remove form the Div**//
			$("#awPageOptionsCheck").text('');

			//** Add Gif Loader Class**//
			$("#awPageOptionsCheck").addClass("aw_loading");
			
			var getDataUrl = serverPath+"functions/ajaxGetPageData.php?source="+sourcetemplatedir;

			if( sourcetemplatedir !== ''){
				//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
				$.ajax( getDataUrl )
				.done(function( data) { 
					//** Remove Loader Class**//
					$("#awPageOptionsCheck").removeClass("aw_loading");

					//** Add Data Received From Ajax Request To Div.**//
					$("#awPageOptionsCheck").html(data);

					// if ("/No Widgets Defined for the site/i".test(dataString)){						
					if (data.indexOf("No  Page(s) Defined for the site.") >= 0 ){						
						//** Disable Submit in No Widgets.**//    
						$("#copyPageSubmit").attr('disabled','disabled');	
					}
					
				})
				.fail(function() {
					
					//** Remove Loader Class**//
					$("#awPageOptionsCheck").removeClass("aw_loading");
					
					$("#awPageOptionsCheck").html('<div style="color: red">Network Error Occured. Please try Again Later.</div>');
					
					//** Disable Submit in No Widgets.**//
					$("#copyPageSubmit").attr('disabled','disabled');	
				});
			}else{
				
				//** Remove Loader Class**//
				$("#awPageOptionsCheck").removeClass("aw_loading");	

				//** Add Error Message If No Blog Selected**//
				$("#awPageOptionsCheck").html('<div style="color: red">Source Blog Not Selected</div>');
				
				//** Disable Submit in No Widgets.**//
				$("#copyPageSubmit").attr('disabled','disabled');
			}
			return false;
		});


      	//** onchange function for **//
      	$("#pageDestination").on("change", function() {


			$("#pageDestination").removeClass('aw_error');
			$("#pageDestinationMessage").text('');
			$("#pageDestinationMessage").removeClass('aw_errorMessage');

      		var pageTitle 	= ''; 
			$( "#pageDestination option:selected" ).each(function() {
				pageTitle += $( this ).text() + " ";
			});

			if (pageTitle == '') {
				$("#pageDestination").addClass('aw_error');

				$("#pageDestinationMessage").addClass('aw_errorMessage');
				$("#pageDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};  

      	});

      	
      	//** Validation for Copy Widget Form. **//
      	$('form#aw_copypages_form').submit(function() {			  		
			
			var flag = true;

			$("#pageSource").removeClass('aw_error');
			$("#pageTitle").removeClass('aw_error');
			$("#pageDestination").removeClass('aw_error');

			$("#pageSourceMessage").text('');
			$("#pageSourceMessage").removeClass('aw_errorMessage');

			$("#pageTitleMessage").text('');
			$("#pageTitleMessage").removeClass('aw_errorMessage');

			$("#pageDestinationMessage").text('');
			$("#pageDestinationMessage").removeClass('aw_errorMessage');	

		

			var pageSource 	= $("#pageSource").val();
			if (pageSource == '') {
				$("#pageSource").addClass('aw_error');
				$("#pageSourceMessage").addClass('aw_errorMessage');
				$("#pageSourceMessage").text('Please select a Source Blog.');
				// alert('Please select a Source Template.');
				flag = false;
			};

			var pageTitle = $('input[name="pageTitle[]"]:checked').length;
			if (!pageTitle) {
    			$("#pageTitleMessage").addClass('aw_errorMessage');
				$("#pageTitleMessage").text('No page(s) Selected.');
				
				flag = false;
			}  
			
			var pageTitle 	= ''; 
			$( "#pageDestination option:selected" ).each(function() {

				pageTitle += $( this ).text() + " ";
			});

			if (pageTitle == '') {
				$("#pageDestination").addClass('aw_error');

				$("#pageDestinationMessage").addClass('aw_errorMessage');
				$("#pageDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};


			
			if(flag == true ){
				return true;
			}else{
				return false;
			}
			
		});


    } );
} ( jQuery ) );


//** Code to copy BlogRolls**//
( function( $ ) {
    $( function() {

    	//** Change Select Box to Searchable Select Box.**//
    	$("#blogrollSource").select2();
		
		//** Change Select Box to Searchable Select Box.**//
    	//$("#blogrollDestination").select2();
    	$("#blogrollDestination").multipleSelect({
            filter: true,
            multiple: true
        });



    	//** Disable Submit in No Widgets.**//
		$("#copyBlogrollSubmit").attr('disabled','disabled');

    	//** click Function.  **//
      	$('#aw_show_hide_tag').click(function() {
			$("#Aw_widgetList").fadeToggle('1000');
			return false;
		});		
		

		var elementRemoved = '';
      	
      	//** onChange Function.  **//
      	$("#blogrollSource").on("change", function() {
			var sourcetemplatedir  	=  $("#blogrollSource").val();
			var serverPath 			=	$("#serverPath").val();

			//** Rest Destination Blog. If Any Blog is already Selected. **//
			// $('#blogrollDestination').select2().val('');
			// $("#blogrollDestination").multipleSelect('refresh');
			$("#blogrollDestination").multipleSelect('uncheckAll');

			//** Hide Any message if Already Posted Data.**//	
			$("#PostError").fadeOut('2000');
			

			//** Enable Submit button**//
			$("#copyBlogrollSubmit").removeAttr('disabled');

			//** Remove blogroll Error Message**//
			$("#blogrollTitleMessage").text('');
			$("#blogrollTitleMessage").removeClass('aw_errorMessage');

			//** Remove Source Select Error Message. **//
			$("#blogrollSource").removeClass('aw_error');
			$("#blogrollSourceMessage").text('');
			$("#blogrollSourceMessage").removeClass('aw_errorMessage');

			//** Add Prevoiusly Removed Blog to Destination Blog(s) Selection List.**//
			if( elementRemoved !== '' ){
				var optionElement 	= "<option style='padding-bottom: 5px;' value='"+elementRemoved+"'>"+elementRemoved+"</option>";
				$('#blogrollDestination').append(optionElement);
				
				//** Remove Option Name from Global Variable**//
				elementRemoved = '';
			} 

			//** Remove Source Blog Name Form Destination Blog(s) Selection List.**//
			var OptionToRemove 	= "#blogrollDestination option[value='"+sourcetemplatedir+"']";
			
			//** Add Removed Option to Global Variable.**//
			elementRemoved 		= sourcetemplatedir; 
			$(OptionToRemove).remove();

			//** Remove form the Div**//
			$("#awOptionsCheck").text('');

			//** Add Gif Loader Class**//
			$("#awOptionsCheck").addClass("aw_loading");
			
			var getDataUrl = serverPath+"functions/ajaxGetBlogroll.php?source="+sourcetemplatedir;

			if( sourcetemplatedir !== ''){
				//** On Change function calls ajax to fetch all the Widgets in source Template. **//	
				$.ajax( getDataUrl )
				.done(function( data) { 
					//** Remove Loader Class**//
					$("#awOptionsCheck").removeClass("aw_loading");

					//** Add Data Received From Ajax Request To Div.**//
					$("#awOptionsCheck").html(data);

					// if ("/No Widgets Defined for the site/i".test(dataString)){						
					if (data.indexOf("No BlogRoll Defined for the site.") >= 0 ){						
						//** Disable Submit in No Widgets.**//    
						$("#copyBlogrollSubmit").attr('disabled','disabled');	
					}
					
				})
				.fail(function() {
					
					//** Remove Loader Class**//
					$("#awOptionsCheck").removeClass("aw_loading");
					
					$("#awOptionsCheck").html('<div style="color: red">Network Error Occured. Please try Again Later.</div>');
					
					//** Disable Submit in No Widgets.**//
					$("#copyWidgetSubmit").attr('disabled','disabled');	
				});
			}else{
				
				//** Remove Loader Class**//
				$("#awOptionsCheck").removeClass("aw_loading");	

				//** Add Error Message If No Blog Selected**//
				$("#awOptionsCheck").html('<div style="color: red">Source Blog Not Selected</div>');
				
				//** Disable Submit in No Widgets.**//
				$("#copyWidgetSubmit").attr('disabled','disabled');	
			}
			return false;
		});


      	//** onchange function for **//
      	$("#blogrollDestination").on("change", function() {


			$("#blogrollDestination").removeClass('aw_error');
			$("#blogrollDestinationMessage").text('');
			$("#blogrollDestinationMessage").removeClass('aw_errorMessage');

      		var blogrollTitle 	= ''; 
			$( "#blogrollDestination option:selected" ).each(function() {
				blogrollTitle += $( this ).text() + " ";
			});

			if (blogrollTitle == '') {
				$("#blogrollDestination").addClass('aw_error');

				$("#blogrollDestinationMessage").addClass('aw_errorMessage');
				$("#blogrollDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};  

      	});

      	
      	//** Validation for Copy blogroll Form. **//
      	$('form#aw_copyblogroll_form').submit(function() {			  		
			
			var flag = true;

			$("#blogrollSource").removeClass('aw_error');
			$("#blogrollTitle").removeClass('aw_error');
			$("#blogrollDestination").removeClass('aw_error');

			$("#blogrollSourceMessage").text('');
			$("#blogrollSourceMessage").removeClass('aw_errorMessage');

			$("#blogrollTitleMessage").text('');
			$("#blogrollTitleMessage").removeClass('aw_errorMessage');

			$("#blogrollDestinationMessage").text('');
			$("#blogrollDestinationMessage").removeClass('aw_errorMessage');	

		

			var blogrollSource 	= $("#blogrollSource").val();
			if (blogrollSource == '') {
				$("#blogrollSource").addClass('aw_error');
				$("#blogrollSourceMessage").addClass('aw_errorMessage');
				$("#blogrollSourceMessage").text('Please select a Source Blog.');
				// alert('Please select a Source Template.');
				flag = false;
			};

			var blogrollTitle = $('input[name="blogrollTitle[]"]:checked').length;
			if (!blogrollTitle) {
    			$("#blogrollTitleMessage").addClass('aw_errorMessage');
				$("#blogrollTitleMessage").text('No blogroll(s) Selected.');
				
				flag = false;
			}  
			
			var blogrollTitle 	= ''; 
			$( "#blogrollDestination option:selected" ).each(function() {

				blogrollTitle += $( this ).text() + " ";
			});

			if (blogrollTitle == '') {
				$("#blogrollDestination").addClass('aw_error');

				$("#blogrollDestinationMessage").addClass('aw_errorMessage');
				$("#blogrollDestinationMessage").text('Please Select Destination Blog(s).');

				flag = false;
			};


			
			if(flag == true ){
				return true;
			}else{
				return false;
			}
			
		});


    } );
} ( jQuery ) );
