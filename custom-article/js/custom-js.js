jQuery(document).ready(function(){
	
    jQuery('#custom-editor-link').click(function(){
        jQuery("#custom-editor-div").css("background","#951C55");
        jQuery("#close_butn").css("display","inline-block");
    });

    jQuery('#custom-editor-link2').click(function(){
        jQuery("#custom-editor-div").css("background","#951C55");
        jQuery("#close_butn").css("background","inline-block");
    });
    
    if (jQuery('body').hasClass('category') || jQuery('body').hasClass('home')){
        jQuery("#menu-footer_links span:contains('Skriv din egen artikkel')").click(function(){
            jQuery("#custom-editor-div").css("background","#951C55");
            jQuery("#close_butn").css("display","inline-block");
            jQuery("#postbox").css("display","inline-block");
            jQuery("#edit_delete").css("display","inline-block");
            jQuery(".logout_btn").css("margin-top","0px");
        });
    }

    var abc=jQuery(document).find("title").text();
    if(abc=="Registrer deg"){
        jQuery("#logout_butn").css("display","none");
    }else if(abc=="Login"){
        jQuery(".logout_btn").css("margin-top","0px");
    }

    if (jQuery('body').hasClass('home')){
        jQuery(".home-cat").show();
        jQuery(".cat-cat").hide();
    }else{
        jQuery(".home-cat").hide();
        jQuery(".cat-cat").show();
    }

});
    
function reg_msg_div(){
	jQuery("#register_msg_div").css("display", "block"); 
}

function theFunction () {
    jQuery("#postbox").css("display","block");
    jQuery("#edit_delete").css("display","inline-block");
    jQuery(".logout_btn").css("margin-top","0px");
    return true;
}

function theFunction2 () {
    jQuery("#postbox").css("display","block");
    jQuery("#edit_delete").css("display","inline-block");
    jQuery(".logout_btn").css("margin-top","0px");
    jQuery("#close_butn").css("display","inline-block");
    return true;
}

function login_sett(){
	jQuery(".login_text").css("display", "inline-block");
    jQuery(".register_text").css("display", "inline-block");
    jQuery("#logout_butn").css("display", "none");
}

function log_out(){
   jQuery("#logout_butn").css("display", "inline-block");
   jQuery(".login_text").css("display", "none");
   jQuery(".register_text").css("display", "none");
}

function close_custom_editor(){
	jQuery("#postbox").css("display","none");
    jQuery("#close_butn").css("display","none");
	jQuery("#custom-editor-div").css("background","#000");
    jQuery("#edit_delete").css("display","none");
    jQuery(".logout_btn").css("margin-top","-16px");
}