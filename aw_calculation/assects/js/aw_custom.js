$(document).ready(function() {
    $(function(){
        var progressbar = $("#progressbar"),
            bar         = progressbar.find('.uk-progress-bar'),
            settings    = {

            action: '/', // upload url

            allow : '*.(jpg|jpeg|gif|png)', // allow only images

            loadstart: function() {
                bar.css("width", "0%").text("0%");
                progressbar.removeClass("uk-hidden");
            },

            progress: function(percent) {
                percent = Math.ceil(percent);saveProject
                bar.css("width", percent+"%").text(percent+"%");
            },

            allcomplete: function(response) {

                bar.css("width", "100%").text("100%");

                setTimeout(function(){
                    progressbar.addClass("uk-hidden");
                }, 250);

                alert("Upload Completed");
            }
        };

        var select = UIkit.uploadSelect($("#upload-select"), settings),
            drop   = UIkit.uploadDrop($("#upload-drop"), settings);

        $('.add_project_section').hide();
        $('.add_location_section').hide();

    });

    
    /*== global variables starts  ==*/
    var formStatus = false;

    /*== global variables ends ==*/

    /*function to hide divs*/
    $.fn.getSiteUrl = function ( room_n, type, response, element ){
        var url = $('#url').attr('value');
        return url;
    };

    $.fn.checkFromStatus = function ( ){

        if( formStatus  ){
            var choice = confirm('Please save changes!');

            if( choice ){

                 $('.message').css('text-align','left').html('Click on save button to save changes.<i class="uk-icon-remove remove-meassage" style="float:right"></i><br/>').fadeIn(1000);
     
                
                return false;

            }else{

                 $('.message').css('text-align','left').html('').fadeOut(1000);
     

                return true;

            }
        }

        return true;
    };

    /* close button functionality. */
    $(document).delegate('.close-btn', 'click', function(event){
        event.preventDefault();
        $(this).parent().parent().fadeOut(1000).remove();
        return false;
    });

     /* close button functionality. */
    $(document).delegate('.add-area-btn', 'click', function(event){
        event.preventDefault();
        console.log('adding area...');

        /*change form status*/
        formStatus = true;

        var element = $(this).parent().parent();
        var url     = $.fn.getSiteUrl();

        var selectedOption = $(this).parent().parent().find('.form-wrapper').find('.form-wrapper-internal-collapse').eq(0).find('.room_types').val();
        
        /*show loader*/
        $('.aw-loader').removeClass('uk-hidden');

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_inner_main_section", selectedoption:selectedOption},
            success: function(response) {
                // console.log(element);
                // form-wrapper-inner
                
                element.find('.form-wrapper-inner').append(response);
                /*remove Loader*/
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
    });

    /* close button functionality. */
    $(document).delegate('.area-close', 'click', function(event){
        event.preventDefault();
        $(this).parent().parent().fadeOut(1000);
        $(this).parent().parent().remove();
        return false;
    });

    /* close button functionality. */
    $(document).delegate('.aw-toggle-btn', 'click', function(event){
        event.preventDefault();
        var icon        = $(this).find('i');
        var sectionID   = $(this).parent().parent('.main-section').find('.form-wrapper').find('.form-wrapper-inner').attr('id');
        var message     = '<div class="uk-alert uk-alert-warning">Room <strong>'+sectionID+'</strong> Collapsed.</div>';

        if(icon.hasClass('uk-icon-chevron-down')){
            icon.parent().attr('title','Expand');
            icon.removeClass('uk-icon-chevron-down');
            icon.addClass('uk-icon-chevron-right');
            $(this).parent().parent('.main-section').find('.form-wrapper').append(message);
        }else{
            icon.parent().attr('title','Collapse');
            icon.removeClass('uk-icon-chevron-right');
            icon.addClass('uk-icon-chevron-down');
            $(this).parent().parent('.main-section').find('.form-wrapper').find('.uk-alert-warning').remove();
            console.log('remove class');
        }
        return false;
    });

    /* close button functionality. */
    $(document).delegate('.aw-area-toggle-btn', 'click', function(event){
        event.preventDefault();
        var icon  = $(this).find('i');
        var sectionID = $(this).parent().parent().find(".inner-main-section").attr("id");
        var message     = '<div class="uk-alert uk-alert-warning">Area <strong>'+sectionID+'</strong> Collapsed.</div>';
        console.log(message);
        if(icon.hasClass('uk-icon-chevron-down')){
            icon.parent().attr('title','Expand');
            icon.removeClass('uk-icon-chevron-down');
            icon.addClass('uk-icon-chevron-left');
            console.log('add class');
            $(this).parent().parent().append(message);
        }else{
            icon.parent().attr('title','Collapse');
            icon.removeClass('uk-icon-chevron-left');
            icon.addClass('uk-icon-chevron-down');
            console.log('remove class');
            $(this).parent().parent().find('.uk-alert-warning').remove();
        }
        return false;
    });

    /*code for custom Ajax section */
    $(document).delegate('.add_room', 'click', function(event){
        event.preventDefault();
        
        /*change form status*/
        formStatus = true;

        var url  = $.fn.getSiteUrl();

        /*show loader*/
        $('.aw-loader').removeClass('uk-hidden');

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_main_section"},
            success: function(response) {
                // console.log(response);
                $('#main-section-form').append(response);
                /*remove Loader*/
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
    });

     /*code for custom Ajax section */
    $(document).delegate('#save_project', 'click', function(event){
        event.preventDefault();

        var validate        = 'no';

        var projectID       = '';

        var projectName     = '';
        var projectLocation = '';
        var projectsave     = '';
        var roomID          = [];
        var areaID          = [];
        var sectionID       = [];

        var projectcreator  = [];
        var section_fields  = [];
        var projectcreator1 = [];

        var url  = $.fn.getSiteUrl();

        var form            = $('#main-section-form');
        var formElements    = form[0];

        projectName     = $('#projectname').val();
        projectLocation = $('#P_locationid').val();
        projectsave = $('#saveproject').val();

       


        $.each(formElements, function (i) {
          element             = formElements[i];


            elementName  = element.name;
            elementValue = element.value;

             if( elementName == 'projectDivID' ) {
                projectID = elementValue;
             }

             if( elementValue == 'Select Room' ) {
                alert('please select room');

                validate = 'yes';

                return false;
                
             }

             if( elementValue == 'Select Area' ) {
                alert('please select area');

                  validate = 'yes';

                return false;
             }
               
                             
            
                 roomID     =  element.getAttribute('data-room');
                 areaID     =  element.getAttribute('data-area');
                 sectionID  =  element.getAttribute('data-section');
                var name    = elementName;

                var itemObject = {
                    name            : elementName,
                    value           : elementValue,
                    projectID       : projectID,
                    projectName     : projectName,
                    projectLocation : projectLocation,                    
                    roomID          : roomID,
                    areaID          : areaID,
                    sectionID       : sectionID,


                };
                projectcreator1.push(itemObject);

            return;
        });

        if(validate == 'no'){
             $('.aw-loader').removeClass('uk-hidden');

            jQuery.ajax({
                type: "post",
                url: url,
                data: {action: "aw_save_project", projectcreator:projectcreator1,projectsave:projectsave,status:'project'},
                success: function(response) {

                    $('.aw-project li').find('.currentProject').attr('href',response);     
                    
                    //$('.message').html('Project Save Successfully.').fadeIn(1000).fadeOut(2000);
                    $('.aw-loader').addClass('uk-hidden');
                    if(projectsave == 'update'){
                        $('.message').html('Project Update Successfully.').fadeIn(1000).fadeOut(2000);

                    }
                    else{
                       $('.message').css('text-align','left').html('Project Save Successfully.<i class="uk-icon-remove remove-meassage" style="float:right"></i><br/> <span>New projects will show in navigation when the page is refreshed.</span>').fadeIn(1000);
     
                    }
                

                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: {action: "aw_project_navigation"},
                    success: function(response) {

                    $('.left-sidebar').html(response);
                    
                  }
                 });

                formStatus = false;

                }
            });
        }


        return false;
    });


    /*code for custom Ajax to Save As project */
    $(document).delegate('#save_as_project', 'click', function(event){
        event.preventDefault();

        var status = $.fn.savelocation(event);

        if(status == false){

            return false;

        }

        var validate        = 'no';

        var projectID       = '';

        var projectName     = '';
        var projectLocation = '';
        var projectsave     = '';
        var roomID          = [];
        var areaID          = [];
        var sectionID       = [];

        var projectcreator  = [];
        var section_fields  = [];
        var projectcreator1 = [];

        var url  = $.fn.getSiteUrl();

        var form            = $('#main-section-form');
        var formElements    = form[0];

        // var copyprojectname = $('#copyprojectname').val();

        // if(copyprojectname == ''){

        //     $('.projectnameerror').text('please enter new project name');

        //     validate  = 'yes';

        // }

        projectName     = $('#projectname').val();
        projectLocation = $('#P_locationid').val();
        projectsave = 'saveas';

        $.each(formElements, function (i) {
          element             = formElements[i];


            elementName  = element.name;
            elementValue = element.value;

            if( elementName == 'projectDivID' ) {
                projectID = elementValue;
            }

            if( elementValue == 'Select Room' ) {
                alert('please select room');

                validate = 'yes';

                return false;
                
            }

            if( elementValue == 'Select Area' ) {
                alert('please select area');

                  validate = 'yes';

                return false;
            }
               
                roomID     =  element.getAttribute('data-room');
                areaID     =  element.getAttribute('data-area');
                sectionID  =  element.getAttribute('data-section');
                var name    = elementName;

                var itemObject = {
                    name            : elementName,
                    value           : elementValue,
                    projectID       : projectID,
                    projectName     : projectName,
                    projectLocation : projectLocation,                    
                    roomID          : roomID,
                    areaID          : areaID,
                    sectionID       : sectionID,


                };
                projectcreator1.push(itemObject);

            return;
        });

        if(validate == 'no'){

            jQuery.ajax({
                type: "post",
                url: url,
                data: {action: "aw_save_project", projectcreator:projectcreator1,projectsave:projectsave,status:'project'},
                success: function(response) {

                //$(".uk-close").trigger("click"); 

                var modal = UIkit.modal("#save_as");
                modal.hide();

                $("#copyprojectname").val('');     

                $('.aw-project li').find('.currentProject').attr('href',response);     
                
                //$('.message').html('Project Save Successfully.').fadeIn(1000).fadeOut(2000);
                $('.message').css('text-align','left').html('Project Save Successfully.<i class="uk-icon-remove remove-meassage" style="float:right"></i><br/> <span>New projects will show in navigation when the page is refreshed.</span>').fadeIn(1000);

                 }
            });
        }

        return false;
    });

    /*code for custom Ajax to save template section */
    $(document).delegate('#save_as_template', 'click', function(event){
        event.preventDefault();

        var validate        = 'no';

        var projectID       = '';

        var projectName     = '';
        var projectLocation = '';
        var projectsave     = '';
        var roomID          = [];
        var areaID          = [];
        var sectionID       = [];

        var projectcreator  = [];
        var section_fields  = [];
        var projectcreator1 = [];

        var url  = $.fn.getSiteUrl();

        var form            = $('#main-section-form');
        var formElements    = form[0];

        var templatename = $('#templatename').val();

        if(templatename == ''){

                $('.templateerror').text('please enter template name');

                validate  = 'yes';

        }

        projectName     = templatename;
        projectLocation = $('#P_locationid').val();
        projectsave = $('#saveproject').val();


        $.each(formElements, function (i) {
          element             = formElements[i];


            elementName  = element.name;
            elementValue = element.value;

                 if( elementName == 'projectDivID' ) {
                    projectID = elementValue;
                 }

                 if( elementValue == 'Select Room' ) {
                    alert('please select room');

                    validate = 'yes';

                    return false;
                    
                 }

                 if( elementValue == 'Select Area' ) {
                    alert('please select area');

                      validate = 'yes';

                    return false;
                 }
                   
                 roomID     =  element.getAttribute('data-room');
                 areaID     =  element.getAttribute('data-area');
                 sectionID  =  element.getAttribute('data-section');
                var name    = elementName;

                var itemObject = {
                    name            : elementName,
                    value           : elementValue,
                    projectID       : projectID,
                    projectName     : projectName,
                    projectLocation : projectLocation,                    
                    roomID          : roomID,
                    areaID          : areaID,
                    sectionID       : sectionID,


                };
                projectcreator1.push(itemObject);

            return;
        });

        if(validate == 'no'){

            jQuery.ajax({
                type: "post",
                url: url,
                data: {action: "aw_save_project", projectcreator:projectcreator1,projectsave:projectsave,status:'template'},
                success: function(response) {

                //$(".uk-close").trigger("click"); 

                var modal = UIkit.modal("#save_template");
                modal.hide();

                $("#templatename").val('');   

                $('.aw-project li').find('.currentProject').attr('href',response);     

               // $('.message').html('Template Save Successfully.</div>').fadeIn(1000);
                $('.message').css('text-align','left').html('Template Save Successfully.<i class="uk-icon-remove remove-meassage" style="float:right"></i><br/> <span>New Template will show in navigation when the page is refreshed.</span>').fadeIn(1000);

            }
            });
        }

      

        return false;
    });

    $(document).delegate('.remove-meassage', 'click', function(event){
        event.preventDefault();

        $(this).parent().fadeOut(1000);

        return false;

    });


    /*code for custom Ajax section */
    $(document).delegate('.add_section', 'click', function(event){
        event.preventDefault();

        /*change form status*/
        formStatus = true;

        var url  = $.fn.getSiteUrl();

        // var element = $(this).parent().parent().parent().parent();
        var element = $(this).parent().parent().parent().parent().parent('.inner-main-section');

        /*show loader*/
        $('.aw-loader').removeClass('uk-hidden');
        var  selectionVal = 'Floor';

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_area_section",area_type: selectionVal},
            success: function(response) {
                // console.log(response);
                // inner-main-section
                element.append(response);
                /*remove Loader*/
                $('.aw-loader').addClass('uk-hidden');
            }
        });

        console.log('add Section...!');

        return false;
    });


    $(document).delegate('.room_types', 'change', function(event){
        event.preventDefault();

         /*change form status*/
        formStatus = true;

        var mainSelection   = $(this).parent().parent().parent();
        var selectValue     =   $(this).val();

        mainSelection.find('.area-select-section').find('.selected_area').remove();

        /*show loader*/
        $('.aw-loader').removeClass('uk-hidden');

        if( selectValue == 'Select Room Type' ){
            return false;
        }

        var url = $.fn.getSiteUrl();
        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_area_select",itemtype:selectValue},
            success: function(response) {
                mainSelection.find('.area-select-section').append(response);
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
    });

    $(document).delegate('.selected_area', 'change', function(event){
        event.preventDefault();

         /*change form status*/
        formStatus = true;

        var mainSelection = $(this).parent().parent().parent().parent();
        var selectionVal  = $(this).val();
        var url           = $.fn.getSiteUrl();

        var roomID        = $(this).parent().parent().parent().parent().parent().parent().parent().parent().find('.aw-toolbar').find('input').val();
        var areaID        = $(this).parent().parent().parent().parent().parent().find('h3').find('input').val();


        $('.aw-loader').removeClass('uk-hidden');

        mainSelection.find('.area-wrapper').remove();

        if( selectionVal == 'Select Area' ){
            return false;
        }

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_area_section", area_type: selectionVal,roomID:roomID, areaID:areaID},
            success: function(response) {
                mainSelection.append(response);
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
    });

    $(document).delegate('.section', 'change', function(event){
        event.preventDefault();

         /*change form status*/
        formStatus = true;

        $('.aw-loader').removeClass('uk-hidden');

        var mainSelection = $(this).parent().parent().parent().parent();
        var selectionVal  = $(this).val();
        var url           = $.fn.getSiteUrl();
        var roomID        = $(this).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var areaID        = $(this).parent().parent().parent().parent().parent().parent().find('.inner-collapse-bar').find('input').val();

        if( selectionVal == 'Select Section' ){
            return false;
        }

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_floor_section", area_type: selectionVal,roomID:roomID,areaID:areaID},
            success: function(response) {
                mainSelection.append(response);
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
    });

    $(document).delegate('.remove_section', 'click', function(event){
        event.preventDefault();
        $(this).parent().parent().parent().parent().remove();
        // $(this).parent().parent().prev().prev().find(".section").val("Select Section");
    });


    $(document).delegate('.add_project', 'click', function(event){
        event.preventDefault();
        $('.welcome_page_section').hide();
        $('.close-btn').parent().parent().fadeOut(1000).remove();
        $("#main-section-form").children("div[class=main-section]:last").fadeOut();
        $('.add_location_section').hide();
        $('.add_project_section').show();
        return false;
    });

    $(document).delegate('.brand', 'change', function(event){
        event.preventDefault();

        $('.aw-loader').removeClass('uk-hidden');

        var mainSelection = $(this).parent().parent().parent().parent();
        var selectionVal  = $(this).val();
        var url           = $.fn.getSiteUrl();
        var roomID        = $(this).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var areaID        = $(this).parent().parent().parent().parent().parent().parent().find('.inner-collapse-bar').find('input').val();
        var element       = $(this);

        if( selectionVal == 'Select Section' ){
            return false;
        }

       jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_brand_section", area_type: selectionVal,roomID:roomID,areaID:areaID},
            dataType: "json",
            success: function(response) {
                
                //mainSelection.append(response);
                element.parent().parent().parent().find('.serries').html('');
                element.parent().parent().parent().find('.color').html('');
                element.parent().parent().parent().find('.serries').append($("<option></option>").text('Select Grout Series'));
                element.parent().parent().parent().find('.color').append($("<option></option>").text('Select Grout Color'));
                $.each(response.series, function (i) {
                    $.each(response.series[i], function (key, val) {
                        element.parent().parent().parent().find('.serries').append($("<option></option>").attr("val",key).text(val));
                    });
                }); 

                $.each(response.color, function (i) {
                    $.each(response.color[i], function (key, val) {
                    
                element.parent().parent().parent().find('.color').append($("<option></option>").attr("val",key).text(val));
                 
                });
                }); 

                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
        
    });


    $(document).delegate('.lay', 'change', function(event){
        event.preventDefault();

        $('.aw-loader').removeClass('uk-hidden');

        var mainSelection = $(this).parent().parent().parent().parent();
        var selectionVal  = $(this).val();
        var url           = $.fn.getSiteUrl();
        var roomID        = $(this).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var areaID        = $(this).parent().parent().parent().parent().parent().parent().find('.inner-collapse-bar').find('input').val();

        var element       = $(this);

        if( selectionVal == 'Select Section' ){
            return false;
        }

       jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_pattern_section", area_type: selectionVal,roomID:roomID,areaID:areaID},
            dataType: "json",
            success: function(response) {
               
                element.parent().parent().parent().find('.direction').html('');
                element.parent().parent().parent().find('.direction').append($("<option></option>").text('Select Lay Direction'));
                $.each(response, function (i) {
                   
                var val = response[i].post_name;
                element.parent().parent().parent().find('.direction').append($("<option></option>").attr("val",val).text(val));
                 
               
                }); 
                 
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
        
    });


     $(document).delegate('.pattern', 'change', function(event){
        event.preventDefault();

        $('.aw-loader').removeClass('uk-hidden');

        var mainSelection = $(this).parent().parent().parent().parent();
        var selectionVal  = $(this).val();
        var url           = $.fn.getSiteUrl();
        var roomID        = $(this).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var areaID        = $(this).parent().parent().parent().parent().parent().parent().find('.inner-collapse-bar').find('input').val();
        var element       = $(this);

        if( selectionVal == 'Select Section' ){
            return false;
        }

       jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_transitions_section", area_type: selectionVal,roomID:roomID,areaID:areaID},
            dataType: "json",
            success: function(response) {
            
                element.parent().parent().parent().find('.transitions_type').html('');
                element.parent().parent().parent().find('.transitions_type').append($("<option></option>").text('Select Transition Types'));
                $.each(response, function (i) {
                   
                var val = response[i].post_name;
                element.parent().parent().parent().find('.transitions_type').append($("<option></option>").attr("val",val).text(val));
                
                }); 
                 
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
        
    });


     $(document).delegate('.Underlayment', 'change', function(event){
        event.preventDefault();

        $('.aw-loader').removeClass('uk-hidden');

        var mainSelection = $(this).parent().parent().parent().parent();
        var selectionVal  = $(this).val();
        var url           = $.fn.getSiteUrl();
        var roomID        = $(this).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var areaID        = $(this).parent().parent().parent().parent().parent().parent().find('.inner-collapse-bar').find('input').val();
        var element       = $(this);
        if( selectionVal == 'Select Section' ){
            return false;
        }

       jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_thinset_section", area_type: selectionVal,roomID:roomID,areaID:areaID},
            dataType: "json",
            success: function(response) {
             
                element.parent().parent().parent().find('.thinset').html('');
                element.parent().parent().parent().find('.thinset').append($("<option></option>").text('Select Thinset For underlayment'));
                $.each(response, function (i) {
                   
                var val = response[i].post_name;
                element.parent().parent().parent().find('.thinset').append($("<option></option>").attr("val",val).text(val));
               
                });                 
                $('.aw-loader').addClass('uk-hidden');
            }
        });
        return false;
        
    }); 

    $(document).delegate('.aw-project li', 'click', function(event){
        event.preventDefault();

        var checkStatus = $(this).find('a').text();

        var projectID = $(this).find('a').attr("href");
        
        var url           = $.fn.getSiteUrl();

        if(checkStatus == 'Delete'){

            
            $.fn.deleteProject(url,projectID); 

            return false;

        }
        if(checkStatus == 'Archive'){

            
            $.fn.saveArchiveProject(url,projectID); 

            return false;

        }

        $('.aw-project li').find('a').removeClass('currentProject');
        $(this).find('a').addClass('currentProject');
        
       
        
        var project_name = $(this).find('a:first').text();
        var project_loc = $(this).parent().prev().text();
        //console.log(project_loc);
        
        if(projectID == '#'){
            return false;
        } else {

                  
        }


        /*function to check if from changed. */
        var checkForm = $.fn.checkFromStatus();

        console.log(checkForm);

        if(checkForm){

            $('.aw-loader').removeClass('uk-hidden');
            $('.add_project_section').hide();
            $('.close-btn').parent().parent().fadeOut(1000).remove();
            $('.welcome_page_section').show();
            $("#main-section-form").children("div[class=main-section]:last").fadeOut(); 


            jQuery.ajax({
                type: "post",
                url: url,
                data: {action: "aw_project_section", projectid: projectID},
                
                success: function(response) {
                    //console.log(project_loc);

                    if(project_loc != ''){
                         $("#project-loc").text(project_loc + ' -');
                    }
                    else{

                        $("#project-loc").text('');
                    }
                   
                    formStatus = false;

                    $("#project-name").text(project_name);
                    $('.welcome_page_section').hide();
                    $('.add_project_section').show();
                     $('#main-section-form').append(response);

                    var pdivid = $('#projecteditid').val();
                    var plocation = $('#projecteditlocation').val();
                    var pname = $('#projecteditname').val();

                    
                    $('#projectDivID').val(pdivid);
                    $('#P_locationid').val(plocation);
                    $('#projectname').val(pname);

                    $('#saveproject').val('update');

                    $('.aw-loader').addClass('uk-hidden');
                }
            });

        }
        return false;

    });


    $(document).delegate('.add_location', 'click', function(event){
        event.preventDefault();
        $('.welcome_page_section').hide();
        $('.close-btn').parent().parent().fadeOut(1000).remove();
        $("#main-section-form").children("div[class=main-section]:last").fadeOut();

        $('.add_project_section').hide();
        $('.add_location_section').show();
        $('.save_aw_project').attr('id','save_location');
        $("#main-location-form input").prop("disabled", false);
        $('#main-location-form').trigger("reset");
        $('.save_aw_project').attr('id','save_location');
        $('.locerror').html("");
        
    });


    $(document).delegate('#save_location', 'click', function(event){
        event.preventDefault();
        
        $.fn.savelocation(event);
        
    });

    $(document).delegate('.save_as', 'click', function(event){
        event.preventDefault();


        $('.save_aw_project').attr('id','save_as_project');
       
        
    });

    /*------------save location popup -------------*/

    $.fn.savelocation = function (event){
        var lot,subdivision,address,projectName;
        var url  = $.fn.getSiteUrl();

        var status = 'yes';

        var locSaveID =  $('.save_aw_project').attr('id');
        
       //console.log('abs');
        var projectDIVID = 'aw_project_' + event.timeStamp;
       
        projectName = $('#projectName').val();

        var selectionVal  = $('#chooselocation').val();
        var selectionText  = $('#chooselocation option:selected').text();
        var  locname = '';
        
        if(selectionVal == '#'){


        lot         = $('#locLOT').val().trim();
        subdivision = $('#locSudDivision').val().trim();
        address     = $('#locAddress').val().trim();
        

        if(address == ''){

          
        if((lot == '') || (subdivision == '')) {

            $('.locerror').html('Please enter lot and subdivivsion or address.');
              
          return false;
        }

            locname =   lot + ' ' + subdivision;
        }

        else{

            locname =   address;
        
        }
        
        if(projectName == ''){

            $('.locerror').html('Please enter project name.');
              
          return false;

        }

        var locationObject = {
                    lot     : lot,
                    subdivision    : subdivision,
                    address: address,
                    
        };

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_save_location", locationcreator:locationObject},
            success: function(response) {

            $('#P_locationid').val(response);
            $('#projectname').val(projectName);

            $("#project-loc").text(locname + ' -');
            $("#project-name").text(projectName);

            $('.message').html('Location Save Successfully.').fadeIn(1000).fadeOut(2000);
            var modal = UIkit.modal("#add_location");
            modal.hide();
           
            }
            
        });

        status = 'no';

        }

        else{

            if(projectName == ''){

            $('.locerror').html('Please enter project name.');
              
          return false;

        }

        $('#P_locationid').val(selectionVal);
        $('#projectname').val(projectName);
        $("#project-loc").text(selectionText + ' -');
        $("#project-name").text(projectName);

        var modal = UIkit.modal("#add_location");
        modal.hide();

        status = 'no';
                   
        }

        if(status == 'no'){



        if(locSaveID == 'save_location'){


        $('#projectDivID').val(projectDIVID);
        $('.welcome_page_section').hide();
        $('.close-btn').parent().parent().fadeOut(1000).remove();
        $("#main-section-form").children("div[class=main-section]:last").fadeOut();
        // $('.add_location_section').hide();
        $('.add_project_section').show();

        }
        
        $("#main-location-form input").prop("disabled", false);
        $('#main-location-form').trigger("reset");
        $('.locerror').html("");
        $('html').removeClass('uk-modal-page');

        return true;

        }

        else{

            return false;

        }

        return false;
    };



    $(document).delegate('#chooselocation', 'change', function(event){
        event.preventDefault();
        var selectionVal  = $(this).val();
        var url           = $.fn.getSiteUrl();
        $("#main-location-form input.newloc").prop("disabled", true);
    });


    $(document).delegate('.aw_project', 'click', function(event){
        event.preventDefault();

        var el = jQuery(this).parent().parent().parent().parent().find('a').eq(0).text();

        if( el == 'Templates'){
            $('.custom-tabs-nav').addClass('uk-hidden');
            $('.save_aw_project').attr('id','save_template_as_project');
            var modal = UIkit.modal("#add_location");
            modal.show();
        }
        else{
            $('.custom-tabs-nav').removeClass('uk-hidden');
            $('.save_aw_project').attr('id','save_location');
        }

       


       
    });


    /*code for custom Ajax to Save template As project */
    $(document).delegate('#save_template_as_project', 'click', function(event){
        event.preventDefault();

        var status = $.fn.savelocation(event);

        if(status == false){

            return false;

        }

        var validate        = 'no';

        var projectID       = '';

        var projectName     = '';
        var projectLocation = '';
        var projectsave     = '';
        var roomID          = [];
        var areaID          = [];
        var sectionID       = [];

        var projectcreator  = [];
        var section_fields  = [];
        var projectcreator1 = [];

        var url  = $.fn.getSiteUrl();

        var form            = $('#main-section-form');
        var formElements    = form[0];

        // var copyprojectname = $('#copyprojectname').val();

        // if(copyprojectname == ''){

        //     $('.projectnameerror').text('please enter new project name');

        //     validate  = 'yes';

        // }

        projectName     = $('#projectname').val();
        projectLocation = $('#P_locationid').val();
        projectsave = 'saveas';

        $.each(formElements, function (i) {
          element             = formElements[i];


            elementName  = element.name;
            elementValue = element.value;

            if( elementName == 'projectDivID' ) {
                projectID = elementValue;
            }

            if( elementValue == 'Select Room' ) {
                alert('please select room');

                validate = 'yes';

                return false;
                
            }

            if( elementValue == 'Select Area' ) {
                alert('please select area');

                  validate = 'yes';

                return false;
            }
               
                roomID     =  element.getAttribute('data-room');
                areaID     =  element.getAttribute('data-area');
                sectionID  =  element.getAttribute('data-section');
                var name    = elementName;

                var itemObject = {
                    name            : elementName,
                    value           : elementValue,
                    projectID       : projectID,
                    projectName     : projectName,
                    projectLocation : projectLocation,                    
                    roomID          : roomID,
                    areaID          : areaID,
                    sectionID       : sectionID,


                };
                projectcreator1.push(itemObject);

            return;
        });

        if(validate == 'no'){

                $.fn.saveTemplateAsProject(url,projectcreator1,projectsave);
        }

        return false;
    });


/*-------------ajax function for template save as project -------*/
    $.fn.saveTemplateAsProject = function(url,projectcreator1,projectsave){
        var projectID = '';   
         jQuery.ajax({
                async: false,
                type: "post",
                url: url,
                data: {action: "aw_save_project", projectcreator:projectcreator1,projectsave:projectsave,status:'project'},
                success: function(response) {

                //$(".uk-close").trigger("click"); 

                var modal = UIkit.modal("#save_as");
                modal.hide();

                $("#copyprojectname").val('');     

                $('.aw-project li').find('.currentProject').attr('href',response); 

                projectID = response;    
                
                //$('.message').html('Project Save Successfully.').fadeIn(1000).fadeOut(2000);
                $('.message').css('text-align','left').html('Project Save Successfully.<i class="uk-icon-remove remove-meassage" style="float:right"></i><br/> <span>New projects will show in navigation when the page is refreshed.</span>').fadeIn(1000);

            }
        });

      
        $.fn.getLeftSideBar(url);

        jQuery.ajax({
            type: "post",
            url: url,
            data: {action: "aw_project_section", projectid: projectID},
            
            success: function(response) {
                //console.log(project_loc);
                $('.custom-tabs-nav').removeClass('uk-hidden');
                $('.welcome_page_section').hide();
                $('.add_project_section').show();
                
                $('#main-section-form').append(response);

                var pdivid = $('#projecteditid').val();
                var plocation = $('#projecteditlocation').val();
                var pname = $('#projecteditname').val();

                
                $('#projectDivID').val(pdivid);
                $('#P_locationid').val(plocation);
                $('#projectname').val(pname);

                $('#saveproject').val('update');

                $('.aw-loader').addClass('uk-hidden');

            }
        });

    };

    $.fn.deleteProject = function(url,projectID){
        

        if(confirm("Are you sure to delete this.")){

            jQuery.ajax({
                async: false,
                type: "post",
                url: url,
                data: {action: "aw_delete_project", projectid: projectID},
                
                success: function(response) {
                    console.log(response);

                    $('.aw-loader').addClass('uk-hidden');
                }
            });

            $.fn.getLeftSideBar(url);


            }
    
    };

    $.fn.saveArchiveProject = function(url,projectID){
        

        jQuery.ajax({
            async: false,
            type: "post",
            url: url,
            data: {action: "aw_save_archive_project", projectid: projectID},
            
            success: function(response) {
                console.log(response);

                $('.aw-loader').addClass('uk-hidden');
            }
        });

        $.fn.getLeftSideBar(url);


    
    };

    $.fn.getLeftSideBar = function(url){

        jQuery.ajax({
                async: false,
                type: "post",
                url: url,
                data: {action: "aw_project_navigation"},
                success: function(response) {

                $('.left-sidebar').html(response);
                
              }
        });
                
    };

});
