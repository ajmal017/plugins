<?php
/**
 * Add Feed Site Administration Page
 *
 * @package WordPress
 * @subpackage Scanmine
 * @since 0.1
 */

require_once('configReader/ConfigReader.php');

$messages = array();
//$current_site = get_current_site();

if ( isset($_REQUEST['action']) && 'site-config-test' == $_REQUEST['action'] ) {

	check_admin_referer( 'site-config-test', '_wpnonce_site-config-test' );

	$blog = $_POST['blog'];
	$config_url = $blog['config-url'];

	if ( empty( $config_url ) )
		wp_die( __( 'Missing config URL.' ) );

	$messages[] = $config_url;

	$reader = new ConfigReader($config_url);
	$reader->parse();
	foreach ($reader->getErrors() as $error)
		$messages[] = "<b style='color:red'>$error</b>";

}

?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="site-config-test"><?php _e('Test a site configuration XML') ?></h2>
<?php
if ( ! empty( $messages ) ) {
	echo '<div id="message" class="updated">' . PHP_EOL;
	foreach ( $messages as $msg )
		if (is_array($msg)) {
			echo '  <ul>' . PHP_EOL;
			foreach ( $msg as $li )
				echo '    <li>' . $li . '</li>' . PHP_EOL;
			echo '  </ul>' . PHP_EOL;
		} else {
			echo '  <p>' . $msg . '</p>' . PHP_EOL;
		}
	echo '</div>' . PHP_EOL;
} ?>
<form method="post" action="<?php echo admin_url('admin.php?page=aw_blogger_config_test&amp;action=site-config-test'); ?>">
<?php
  wp_nonce_field( 'site-config-test', '_wpnonce_site-config-test' ) 
?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Config URL' ) ?></th>
			<td><input name="blog[config-url]" type="text" class="regular-text" title="<?php esc_attr_e( 'URL' ) ?>"/></td>
		</tr>
	</table>
	<?php submit_button( __('Test config'), 'primary', 'site-config-test' ); ?>
	</form>
</div>

<?php

if ( isset($reader) ) {

	echo "<p>address: ", var_export($reader->getProperty('address'), 1), "</p>\n";
	echo "<p>title: ", var_export($reader->getProperty('title'), 1), "</p>\n";
	echo "<p>description: ", var_export($reader->getProperty('description'), 1), "</p>\n";
	echo "<p>theme: ", var_export($reader->getProperty('theme'), 1), "</p>\n";
	echo "<p>owner: ", var_export($reader->getProperty('owner'), 1), "</p>\n";
	echo "<p>feeds: ";
	foreach ($reader->getProperty('feeds')->feed as $feed)
		echo var_export($feed, 1), " ";
	echo "</p>\n";

	$options = $reader->getOptions();
	foreach ($options->option as $option)
		echo "<p>", $option->name, ": ", $option->value, "</p>\n";

}
