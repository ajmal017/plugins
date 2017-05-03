jQuery(document).ready(function(){
	var items = [];	
	jQuery('#segment .segmentItem').each(function(){
		items.push(this);
		console.log(items);
		jQuery(this).remove();
		
	});
	jQuery('#course').on('change', function(event) {
		event.preventDefault();
		jQuery('#segment .segmentItem').remove();
		var label = jQuery(this).val();
		jQuery(items).each(function( ){
			label=label.replace(/\s+/g, '_');
			if( jQuery(this).hasClass(label) ){
				jQuery('#segment').append(this);
			}
		});

	});

	console.log(items);

	setMmiSearchValues();
	
		var teacherid 	=  jQuery("#teacher_id").val();
		var recordtype 	=  jQuery("#record_type").val();
		var mmidate 	=  jQuery("#mmi_date").val();
		/*var courseseg 	=  jQuery("#course").val();
		var segmentcrs 	=  jQuery("#segment").val();*/
		var recordid 	=  jQuery("#record_id").val();

		//if ( teacherid!='' || recordtype!='' || mmidate!='' || courseseg!='' || segmentcrs!='' || recordid!='' ) {
		if ( teacherid!='' || recordtype!='' || mmidate!='' || recordid!='' ) {
			 if (jQuery('.search').length > 0 ) { 
			  jQuery("#show_advance_form").html('<strong>Go to basic search</strong>');
			  jQuery('.advance_form').css('display','block');
			 }
		}else{
			if (jQuery('.search').length > 0 ) { 
			  jQuery("#show_advance_form").html('<strong>Go to advanced search</strong>');
			  jQuery('.advance_form').css('display','none');
			 }
		}
		/*if (jQuery('.search').length > 0 && textvalue=='') {
		  jQuery("#show_advance_form").html('<strong style="color:#618794; text-decoration:underline;">Return to basic search</strong>');
		  jQuery('.advance_form').css('display','block');
		}*/
		
	/* From Submit. */
	jQuery('#searchform').on('submit', function(){
		
		var course    = jQuery('#course').val();
		var segment   = jQuery("#segment").val();
		var record_id = jQuery("#record_id").val();	

		if( course !== '' ){
			if( segment == ''){
				jQuery('#segmentMessage').html('*In order to search by Program, you must also select a Segment or Module').fadeIn(2000).fadeOut(5000);
				return false;
			}			
		}

		var segmentText = jQuery("#segment option:selected").text();

		if( segmentText == 'LAOL16' ){

			console.log( segmentText )
			jQuery('#course').append('<option value="'+segmentText+'">'+segmentText+'</option>')
			jQuery('#course').val(segmentText);
			console.log( segmentText )
		}		
		// return false;

	/*	if( record_id !== '' ){
			var flag = true;

			if( segment == ''){
				jQuery('#segmentMessage').html('*In order to search by class Number, You must also select a Segment or Module').fadeIn(2000).fadeOut(5000);				
				flag = true;
			}
			
			if( course == ''){
				jQuery('#courseMessage').html('*In order to search by class Number, You must also select a Program').fadeIn(2000).fadeOut(5000);
				flag = true;
			}

			if( !flag ){
				return false;
			}
		}*/
		return true;
	});


	jQuery('#show_advance_form').click(function(event){ 
    	var text = jQuery("#show_advance_form").text();
    	
    	if( text == 'Go to advanced search'){
    		jQuery("#show_advance_form").html('<strong style="color:#4b6f7b; text-decoration:underline;">Go to basic search</strong>');
    		jQuery('#clear_advance_form').css('display','inline');
    		jQuery('.word-phrase span').text('To find a specific file:');
    		jQuery('.file-number span').text('Or:');
    		jQuery('.search-teacher span').text('To search by instructor:');
    		jQuery('.select-activity span').text('Or by what happening:');
    	}else{
    		jQuery("#show_advance_form").html('<strong style="color:#4b6f7b; text-decoration:underline;">Go to advanced search</strong>');
    		jQuery("#clear_advance_form").css('display', 'none');
    		jQuery("#clear_advance_form" ).trigger( "click" );
    		jQuery('.word-phrase span').text('If you are looking for a specific file in the MIM library:');
    		jQuery('.file-number span').text('');
    		jQuery('.search-teacher span').text('');
    		jQuery('.select-activity span').text('');
    	}

    	jQuery(".advance_form").slideToggle(100);
    	event.stopPropagation();
	});

	jQuery('#show_advance_form_mob').click(function(event){ 
    	var text = jQuery("#show_advance_form_mob").text();
    	
    	if( text == 'Go to advanced search'){
    		jQuery("#show_advance_form_mob").html('<strong style="color:#4b6f7b; text-decoration:underline;">Go to basic search</strong>');
    		jQuery('#clear_advance_form').css('display','inline');
    		jQuery('.word-phrase span').text('To find a specific file:');
    		jQuery('.file-number span').text('Or:');
    		jQuery('.search-teacher span').text('To search by instructor:');
    		jQuery('.select-activity span').text('Or by what happening:');
    	}else{
    		jQuery("#show_advance_form_mob").html('<strong style="color:#4b6f7b; text-decoration:underline;">Go to advanced search</strong>');
    		jQuery("#clear_advance_form").css('display', 'none');
    		jQuery("#clear_advance_form" ).trigger( "click" );
    		jQuery('.word-phrase span').text('If you are looking for a specific file in the MIM library:');
    		jQuery('.file-number span').text('');
    		jQuery('.search-teacher span').text('');
    		jQuery('.select-activity span').text('');
    	}

    	jQuery(".advance_form").slideToggle(100);
    	event.stopPropagation();
	});

	
	/* functionality to clear search form */
	jQuery("#clear_advance_form").click(function(){		

		jQuery("#teacher_id").val('');
		jQuery("#teacher_id").css('color', '#999999');

		jQuery("#record_type").val('');
		jQuery("#record_type").css('color', '#999999');


		jQuery("#mmi_date").val('');
		jQuery("#record_id").val('');
		
		jQuery("#course").val('');
		jQuery("#course").css('color', '#999999');

		jQuery("#segment").val('');
		jQuery('.segmentItem').hide();	
		jQuery("#segment").css('color', '#999999');

	});
	/* function clear form ends */


	jQuery('#course').on('change', function(){ 
		console.log( jQuery(this).val() );
		jQuery("#segment").val('');		
		
		var item = jQuery(this).val();
		if( item == '' ){
			jQuery('.segmentItem').hide();	
			jQuery('#course').css('color', '#999999');
			jQuery('#segment').css('color', '#999999');

		}else{
			jQuery(".segmentItem").hide();
			jQuery('#segment').css('color', '#999999');
			
			jQuery('#course').css('color', '#000');

			var itemNew  = item.replace(/ /g, '_');
			var itemClass = "."+itemNew;

			console.log(itemClass)
			jQuery(itemClass).show();
			
		} 
	});

	jQuery('#segment').on('change', function(){ 
		var item = jQuery(this).val();
		if( item == '' ){
			jQuery(this).css('color', '#999999');
		}else{
			jQuery(this).css('color', '#000');
		} 
	});


	jQuery('#teacher_id').on('change', function(){ 
		var item = jQuery(this).val();
		if( item == '' ){
			jQuery(this).css('color', '#999999');
		}else{
			jQuery(this).css('color', '#000');
		} 
	});

	jQuery('#record_type').on('change', function(){ 
		var item = jQuery(this).val();
		if( item == '' ){
			jQuery(this).css('color', '#999999');
		}else{
			jQuery(this).css('color', '#000');
		} 
	});

	/* Add date Range for date.  */
	// jQuery('#mmi_date').daterange();
	// jQuery('#mmi_date').dateRangePicker({format: 'dddd MMM Do, YYYY'});
	jQuery('#mmi_date').dateRangePicker({format: 'MM/d/YYYY', separator: '-'});


	/* Function to set the values of html controls */
	function setMmiSearchValues(){
		console.log('This is test Set MMI Search');

		var title  		=		jQuery('#pre_title').attr('value');
		var teacher_id  =		jQuery('#pre_teacher_id').attr('value');
		var record_type =		jQuery('#pre_record_type').attr('value');
		var mmi_date    =		jQuery('#pre_mmi_date').attr('value');
		var record_id   =		jQuery('#pre_record_id').attr('value');
		var course      =		jQuery('#pre_course').attr('value');
		var segment     =		jQuery('#pre_segment').attr('value');
		

		console.log(title)

		if(  title !== ''){
			jQuery("#title").val(title);			
		}

		if(  teacher_id !== ''){
			jQuery("#teacher_id").val(teacher_id);		
			jQuery("#teacher_id").css('color', '#000');
		}
			

		if(  record_type !== ''){
			jQuery("#record_type").val(record_type);
			jQuery("#record_type").css('color', '#000');
		}

		// jQuery("#mmi_date").val('');
		if(  course !== ''){
			jQuery("#course").val(course);
			jQuery("#course").css('color', '#000');
			jQuery("#course").trigger('change');
			console.log('Working... change')

			jQuery("#segment").val(segment);
			jQuery("#segment").css('color', '#000');
			
		}
		
		if(  segment !== ''){	
			jQuery("#segment").val(segment);
			jQuery("#segment").css('color', '#000');
		}

		// jQuery("show_advance_form").trigger('click');
		// jQuery(".advance_form").slideToggle();
	}
	/* Function to*/
	
	jQuery('body').on('click', '.delete_btn', function(){
	// jQuery(".delete_btn").click(function(){
		var element = jQuery(this);
		var del_id  = element.attr("id");		
		var info    = del_id;
		if(confirm("Are you sure you want to delete this?")){
			jQuery.ajax({
			   type: "POST",
			   url: "//mindinmotion-online.com/wp-content/plugins/mmi_search_filter/delete_mmishowdata.php",
			   data: ({id:info}),
			   success: function( Success ){
					var row_Id      = "body #data_"+del_id;
					var row_buttons = row_Id+" .button";

			   		jQuery(row_buttons).hide();
			   		jQuery(row_Id).css('background-color', 'red');
			   		jQuery( row_Id ).fadeOut(2000);
			   		
	   			}
	 		});
 
 		}
		return false;
	});

	jQuery.ajax({
    type: "POST",
    url: '//mindinmotion-online.com/wp-content/plugins/mmi_search_filter/getMMISavedSearchList.php',
    
    success: function(res) {
    	console.log(res);
    	var result = JSON.parse(res);
       var select = jQuery("#mmi_show_list"), options = '';
       /*select.empty();*/      

       for(var i=0;i<result.length; i++)
       {
        options += "<option value='"+result[i].url+"'>"+ result[i].name +"</option>";              
       }
       console.log(options);
       jQuery('#mmi_show_list').append(options);
    }
	});

	/* Check all checkbox on single click start */
	jQuery(document).on('click', '#check_all_files', function() { 
		var allChecked = jQuery(this);
      jQuery(".search_table input[type=checkbox]").each(function() {
        jQuery(this).prop("checked", allChecked.is(':checked'));
      });
		
	});

	jQuery('#mmi_modify_data').on('click', function(event){
		 jQuery('#searchform').slideToggle(100); 
		 event.stopPropagation();
	});
	/* Check all checkbox on single click end */

	/* Form submit on enter keypress start */
	jQuery('#title').live("keypress", function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
	     if (code == 13) {
	        e.preventDefault();
	        e.stopPropagation();
	        jQuery(this).closest('form').submit();
	     }
  	});
	/* Form submit on enter keypress End */
	
	jQuery(document).keypress(function(e) {
	   if(jQuery("body").hasClass("search")){
	      if(e.which == 13) {
	         window.location.replace("//mindinmotion-online.com/mmi-index");
	       }
	   }
	});


	
});
