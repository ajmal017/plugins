	function DrawCaptcha(){
				  /*  var a = Math.ceil(Math.random() * 9)+ '';
					var b = Math.ceil(Math.random() * 9)+ '';       
					var c = Math.ceil(Math.random() * 9)+ '';  
					var d = Math.ceil(Math.random() * 9)+ '';  
					var e = Math.ceil(Math.random() * 9)+ '';  
					var f = Math.ceil(Math.random() * 9)+ '';  
					var g = Math.ceil(Math.random() * 9)+ '';  
					var code = a + ' ' + b + ' ' + ' ' + c + ' ' + d + ' ' + e + ' '+ f + ' ' + g;
					document.getElementById("txtCaptcha").value = code
				*/	
					var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
					var string_length = 5;
					var randomstring = '';
					for (var i=0; i<string_length; i++) {
						var rnum = Math.floor(Math.random() * chars.length);
						randomstring += chars.substring(rnum,rnum+1)+' ';
					}
					document.getElementById("txtCaptcha").innerHTML = randomstring;

				}	 
				 $(document).ready(
					function(){
					//alert("aaaa");
					var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
					var string_length = 5;
					var randomstring = '';
					for (var i=0; i<string_length; i++) {
						var rnum = Math.floor(Math.random() * chars.length);
						randomstring += chars.substring(rnum,rnum+1)+' ';
					}
					document.getElementById("txtCaptcha").innerHTML = randomstring;
				});