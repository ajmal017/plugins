$(function(){
	var btnUpload=$('#upload');
	var status=$('#status');
	var site_url = document.getElementById("siteurl").value;
	var imagedir = document.getElementById("imagedir").value;
	// alert(site_url);
	// alert(blogname[1]);
	blog_language= $("#language").val();
	//alert(blog_language);		 
	if(blog_language == "en_US"){
		fileexterror = "Only JPG, PNG or GIF files are allowed.";
        imageuploading = "Uploading...";
		imageuploaded = "Uploaded.";
		invalidimagename = "Invalid image name.....";
	}else if(blog_language == "sv_SE"){
		fileexterror = "Endast JPG, &auml;r PNG eller GIF-filer till&aring;ts.";	
		imageuploading = "Uppladdning...";
		imageuploaded = "Uppladdad.";
		invalidimagename = "Ogiltigt bildnamn.";
	}else if(blog_language == "nb_NO"){
		fileexterror = "Bare JPG, PNG or GIF filer kan benyttes.";
		imageuploading = "opplasting...";	
		imageuploaded = "lastet opp.";
		invalidimagename = "Ulovlig filnamvn.";
	}else{
		fileexterror = "Only JPG, PNG or GIF files are allowed.";
        imageuploading = "Uploading...";
		imageuploaded = "Uploaded.";
		invalidimagename = "Invalid image name.....";
	}
	new AjaxUpload(btnUpload, {
		action: site_url+'/wp-content/plugins/CustomPost/upload-file.php',
		name: 'uploadfile',
		onSubmit: function(file, ext){
		   if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				 // extension is not allowed 
				 //status.text(fileexterror);
				 document.getElementById("status").innerHTML = fileexterror;
				 return false;
			  } 
			  $('#image_type').val('image/'+ext+'');
			  document.getElementById("imgprgbar").style.display="block";
		},
		onComplete: function(file, response){
			document.getElementById("imgprgbar").style.display="none";
			//On completion clear the status
			var res = response.split("--"); 
			//alert(res[0]);
			status.text('');
			//Add uploaded file to list
			//blogid = res[1];
			imageprefix = res[1];
			//alert(imageprefix+file);
			file1 = imageprefix+file;
			if(res[0]==="success"){				
				//$('<li></li>').appendTo('#files').html('<img src="http://wordpress.scanmine.com/'+blogs+'/wp-content/plugins/CustomPost/uploads/'+file+'" alt="" /><br />'+file).addClass('success');
				//$('#status').text(imageuploaded);
				$("#show_image").hide();
				$('#featured_image').val(site_url+'/wp-content/'+imagedir+'/'+file1+'');
				$('#featuredImg').attr('src',site_url+'/wp-content/'+imagedir+'/'+file1+'');
				$('#img_name').val(file1);
				$('#image_type').val(res[2]);
				
			}else if(res[0]==="Error"){
				$('#status').text(invalidimagename);
				$('<li></li>').appendTo('#files').text(file1).addClass('error');
			}else{
				$('<li></li>').appendTo('#files').text(file1).addClass('error');
			}
		}
	});
});

function edit_rec(){
			var post_id= document.getElementById("postid").value;
			var site_url = document.getElementById("siteurl").value;
			//alert(site_url);
			window.location.href = site_url+"/custom-post/?post_id="+post_id;
}
function sendid(postid){
	document.getElementById("postid").value=postid;
}

function deletepost(){
	condel = document.getElementById("confirmdel").value;
	selectdel = document.getElementById("selectdel").value;
	//alert(condel);
	var url  = $('#url').val();
	var myRadio = $('input[name=post_check]');
	var checkedValue = myRadio.filter(':checked').val();
	// alert(checkedValue);	
	if(!parseInt(checkedValue)){
		alert(selectdel);
		return false;
	} 
	if(confirm(condel)==true){		
		var site_url = document.getElementById("siteurl").value;
		//alert(site_url);
		if(trim11(checkedValue) == "undefined"){
			alert('123');
		} else{
			var xmlhttp;
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
				xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					 window.location.href = site_url+"/custom-post/";
				}
			}
			xmlhttp.open("GET",url+"/CustomPost/ajax_request.php?id="+trim11(checkedValue)+"&action=delete",true);
			xmlhttp.send();
		}
	}
}
function trim11 (str) {
	str = str.replace(/^\s+/, '');
	for (var i = str.length - 1; i >= 0; i--) {
		if (/\S/.test(str.charAt(i))) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return str;
}	


function validate_custum_form(){ 	
	// alert(document.getElementById("edit_form").value);
	if(document.getElementById("cat_list").value==""){
		document.getElementById("show_cat").style.display = "block";
		return false;
	}else{
		document.getElementById("show_cat").style.display = "none";
	}
	var posttitle = document.getElementById("title1").value;
	if(posttitle.replace(/^\s+|\s+$/g,'')==""){
			document.getElementById("show_title").style.display = "block";
		return false;
	}else{
		document.getElementById("show_title").style.display = "none";
	}
	if(document.getElementById("blocks1").checked==false && document.getElementById("blocks2").checked==false && document.getElementById("blocks3").checked==false){
	 	document.getElementById("show_position").style.display = "block";
		return false;
	}else{
		document.getElementById("show_position").style.display = "none";
	}
	if(document.getElementById("blocks1").checked==true){
		var fullPath = document.getElementById('featuredImg').src;
		var filename = fullPath.replace(/^.*[\\\/]/, '')
		if(filename=="img-4.png"){
			document.getElementById("show_image").style.display = "block";
			return false;
		}else{
			document.getElementById("show_image").style.display = "none";
		}
	 }else{
		document.getElementById("show_image").style.display = "none";
	 }
	// alert(filename);
	
	captchadiv = document.getElementById("txtCaptcha");
	//alert(captchadiv.innerHTML);
	//Validation at add time
	var editpostid = document.getElementById("edit_form").value;	
	if(editpostid==""){		 
		 	if(document.getElementById("captcha-form").value==""){
					document.getElementById("show_captcha").style.display = "block";
					document.getElementById("show_captcha_valid").style.display = "none";
					return false;
			}else{
					document.getElementById("show_captcha").style.display = "none";
					var str1 = removeSpaces(captchadiv.innerHTML); 
					str2 = document.getElementById("captcha-form").value;
					//alert(str1+"============"+str2);
					if(str1.toLowerCase()!=str2.toLowerCase()){
						document.getElementById("show_captcha_valid").style.display = "block";
						return false;
					}else{
						document.getElementById("show_captcha_valid").style.display = "none";
					}
			} 
			 
	}
	var editorContent = tinyMCE.activeEditor.getContent();
	 if(editorContent==""){
		document.getElementById("show_title").style.display = "none";
		document.getElementById("show_description").style.display = "block";	 
		return false;
	}
	 
}

function removeSpaces(string){
			return string.split(' ').join('');
}  

function close_popup(){
	document.getElementById('popup_box').style.display = "none";
}

function close_popup_error(){
	document.getElementById('popup_box_error').style.display = "none";
}

function close_popup_error_post(){
	var site_url = document.getElementById("siteurl").value;
	//alert(site_url);
	window.location.href = site_url+"/custom-post/";
}