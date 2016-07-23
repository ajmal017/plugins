<?php

require("/var/www/html/wp-load.php");
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>';
echo '<script src="js/select2.min.js"></script>';
echo '<script src="js/js-script.js"></script>';
echo "<link rel='stylesheet' type='text/css' href='css/select2.min.css'>";
echo "<link rel='stylesheet' type='text/css' href='css/custom-style-css.css'>";

function blogName(){

	global $wpdb;

	$sql 	 	= "SELECT * FROM wp_aw_blog_sites";
	$results  	= $wpdb->get_results( $sql, OBJECT );
	$html 		= '';
	$html 	   .= "<select name='domain_name' class='domain_name' id='domain_name'>";
	$html  	   .= "<option value='0'>Select Blog</option>";
	foreach ($results as $value) {
		$name   = $value->site_name;
		$html  .= "<option value='".$name."'>".$name."</option>";
	}
	
	$html 	   .= "</select>";
	$html 	   .= "<span class='error_msg_blog_name'> Please Select Blog. </span>";
	
	$html 	   .= "<script>jQuery(document).ready(function(){jQuery('#domain_name').select2();});</script>";

	echo $html;

}

function domainTransfer(){
	
	if ($_GET['save']=="recordsaved") {
	?>
		<script type="text/javascript">
			saved_msg();
		</script>
	<?php
	}
	?>

	<!DOCTYPE html>
	<html>
	<head><title>Site Transfer</title></head>

	<body>
		<div class="blog_conf_title">Scanmine Blog Domain Configuration</div>
		<div class="saved_msg"><h3>Domain Configured Successfully!</h3></div>
		<div class="main_dom">
			<form method="POST" action="aw_saverecord.php" name="frm">
				<div class="field_row">
					<input type="text" name="domain_name_url" id="domain_name_url" class="domain_name" placeholder="Domain Name">
					<span class="error_msg_domain_name_url"> Please Enter Domain Name. </span>
				</div>
				<div class="field_row">
					<input type="text" name="dom_alias" id="dom_alias" class="domain_name" placeholder="Domain Alias">
					<span class="error_msg_domain_alias"> Please Enter Domain ALias. </span>
				</div>
				<div class="field_row">
					<?php blogName(); ?>
				</div>
				<div class="field_row">
					<input type="text" name="config_name" id="config_name" class="domain_name" placeholder="Config File Name">
					<span class="error_msg_config_name"> Please Enter Config File Name. </span>
				</div>
				<div class="field_row">
					<input type="text" name="config_file_path" id="config_file_path" class="domain_name" placeholder="Config File Path">
					<span class="error_msg_config_file_path"> Please Enter Config File Path. </span>
				</div>
				<div class="field_row">
					<input type="submit" name="transfer" class="domain_name trnsfr_btn" value="Setup Domain">
				</div>
			</form>
		</div>
	</body>
	</html>
<?php	
}
domainTransfer();

?>