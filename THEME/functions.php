<?php 
if (!function_exists('add_action')) { header('Status: 403 Forbidden'); header('HTTP/1.1 403 Forbidden'); exit("This page can't be loaded outside of WordPress!"); }
define('_V_TITLE', '_V Theme');
define('_V_HOMEPAGE', get_option("home"));
define('_V_TEMPLATE_DIR', get_bloginfo('template_directory'));
define('_V_TEMPLATE_DIR_SCRIPTS', _V_TEMPLATE_DIR.'/scripts');
define('_V_TEMPLATE_DIR_IMAGES', _V_TEMPLATE_DIR.'/images');
if(!class_exists('_V')):
class _V {

	/**
	 *	@function	french_enabled
	 *	@params 	none
	 *	@return 	bool - <true> if language toggle enabled, <false> otherwise
	 */
	function french_enabled() { global $q_config; if ($q_config['language']) { return true; } return false; }

	/**
	 *	@function	translate
	 *	@params 	<e:string> contains the english content
	 *				<f:string> contains the french content
	 *	@return 	string - <f:string> if language toggle is set to <french>, <e:string> otherwise
	 */
	function translate($e=NULL,$f=NULL) { global $q_config; $t = $e; if ($q_config['language'] == "fr") { $t = $f; } return $t; }

	/**
	 *	@function	clean_post
	 *	@params 	<p:string> contains the content
	 *	@return 	string - <p:string> having been run through <func:strip_tags>, <func:stripslashes>, and <func:trim>
	 */
	function clean_post($p) { $p = strip_tags($p); $p = stripslashes($p); $p = trim($p); return $p; }

	/** ?
	 *	@function	verify_null
	 *	@params 	<post_id:int> is the post id
	 *				<n:string> is the name
	 *				<v:string> is the value
	 *	@return 	void
	 */
	function verify_null($post_id, $n, $v) { if('' == trim($v) || '0' == trim($v)) { delete_post_meta($post_id, $n); } else { update_post_meta($post_id, $n, $v); } }

	/**
	 *	@function	main_excerpt
	 *	@params 	<content:string> contains the content
	 *	@return 	string - <content:string> having been run through <func:str_replace> to replace "..." with "[...]"
	 */
	function main_excerpt($content) { $content = str_replace("[...]", "...", $content); return $content; }
	/**
	 *	@function	mail_headers
	 *	@params 	<send_from_name:string> contains a name
	 *				<send_from_email:string> contains a email
	 *	@return 	string - <headers:string> which contains a valid mail header
	 */
	function mail_headers($send_from_name, $send_from_email) {
		$headers = "From: <$send_from_email>"."\r\n";
		$headers .= "Reply-To: <$send_from_email>"."\r\n";
		$headers .= "Return-path: $send_from_email\n";
		$headers .= "X-Mailer:PHP".phpversion()."\r\n";
		$headers .= "Precedence: list\nList-Id: ".@get_option('blogname')."\r\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=\"".@get_bloginfo('charset')."\""."\r\n";
		return $headers;
	}

	/**
	 *	@function	get_email_addresses
	 *	@params 	<ns:string> contains either name or comma separated list of names
	 *				<es:string> contains either email or comma separated list of emails
	 *	@return 	string - <headers:string> which contains a valid mail header
	 */
	function get_email_addresses($ns,$es) {
		$n = explode(",",$ns); $e = explode(",",$es);
		if (count($e) > 1) {
			$temp["to"] = trim(addslashes($name[0]))."<".trim($e[0]).">";
			for ($x = 1; $x < count($e); $x++) { $temp["cc"] .= trim(addslashes($n[$x]))."<".trim($e[$x]).">,"; }
			$temp["cc"] = substr($temp["cc"],0,-1);
			$temp["cc"] = "Cc: " . $temp["cc"] . "\n";
		}
		else { $temp["to"] = trim($e[0]); $temp["cc"] = NULL; }
		return $temp;
	}

	/**
	 *	@function	get_email_addresses
	 *	@params 	<es:string> contains either email or comma separated list of emails
	 *	@return 	string - <headers:string> which contains a valid mail header
	 */
	function verify_email_addresses($es) {
		$temp = true;
		$e = explode(",",$es);
		if (count($e) > 1) { for ($x = 0; $x < count($e); $x++) { if (!@is_email($e[$x])) $temp = false;	} }
		else { if (!@is_email($e[0])) $temp = false; }
		return $temp;
	}
	/**
	 *	@function	remove_menu_items
	 *	@params 	void
	 *	@return 	void
	 */	
	function remove_menu_items() {
		global $menu;
		$current_user = wp_get_current_user();
		$restricted = array(__('Links'), __('Comments'), __('Tools'));
		if ("crgweb@parl.gc.ca"!=$current_user->user_email) { $restricted = array(__('Links'), __('Comments'), __('Tools')); }
		if (("crgweb@parl.gc.ca"!=$current_user->user_email) || (1!=$current_user->ID)) { __('Plugins'); }
		end ($menu);
		while (prev($menu)){
			$value = explode(' ',$menu[key($menu)][0]);
			if(in_array($value[0] != NULL?$value[0]:"" , $restricted)) { unset($menu[key($menu)]); }
		}
	}
	/**
	 *	@function	reconfigure_dashboard
	 *	@params 	void
	 *	@return 	void
	 */	
	function reconfigure_dashboard() {
		global $wp_meta_boxes;
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	}
	/**
	 *	@function	remove_submenus
	 *	@params 	void
	 *	@return 	void
	 */	
	function remove_submenus() { global $submenu; unset($submenu['index.php'][10]); /*Removes 'Updates'*/ unset($submenu['edit.php'][16]); /*Removes 'Tags'*/ }

	/**
	 *	@function	delete_submenu_items
	 *	@params 	void
	 *	@return 	void
	 */	
	function delete_submenu_items() {remove_submenu_page('themes.php', 'theme-editor.php');remove_submenu_page('plugins.php', 'plugin-editor.php');}
	/**
	 *	@function	admin_print_scripts
	 *	@params 	void
	 *	@return 	void
	 */	
	function admin_print_scripts() { wp_deregister_script('autosave'); wp_enqueue_script('media-upload'); wp_enqueue_script('thickbox'); }
	/**
	 *	@function	admin_print_styles
	 *	@params 	void
	 *	@return 	void
	 */
	function admin_print_styles() { wp_enqueue_style('thickbox'); }
	/**
	 *	@function	admin_print_styles
	 *	@params 	void
	 *	@return 	void
	 */
	function admin_head() {  
		?>
		<style type="text/css">
		/*
		 * Inner container to allow for even spacing of forms and settings. 
		 */
		.inner_container:before, .inner_container:after { content: '.'; display: block; overflow: hidden; visibility: hidden; font-size: 0; line-height: 0; width: 0; height: 0; }
		.inner_container:after { clear: both; }
		.inner_container { zoom: 1; width: 102%; margin-left: -1%; }

		[class*="col_"] { display: inline; float: left; margin-right: 1%; margin-left: 1%; }
		.col_1of4 { width: 23%; }
		.col_2of4 { width: 48%; }
		.col_3of4 { width: 73%; }
		.col_1of2 { width: 48%; }
		.col_1of3 { width: 31.33333333333333%; }
		.col_2of3 { width: 64.6666666666666%; }
		.col_1of1 { width: 98%; }
		.col_1of5 { width: 18%; }
		.col_2of5 { width: 38%; }
		.col_3of5 { width: 58%; }
		.col_4of5 { width: 78%; }

		/*
		 *	Classes set to extend wordpresses default
		 */

		.button-secondary {  }

		.wp-core-ui .button-secondary {
			background: #dd1a2e;
			background: -moz-linear-gradient(top, #dd1a2e 0%, #a01315 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#dd1a2e), color-stop(100%,#a01315));
			background: -webkit-linear-gradient(top, #dd1a2e 0%,#a01315 100%);
			background: -o-linear-gradient(top, #dd1a2e 0%,#a01315 100%);
			background: -ms-linear-gradient(top, #dd1a2e 0%,#a01315 100%);
			background: linear-gradient(to bottom, #dd1a2e 0%,#a01315 100%);
			border-color: #A01315;
			border-bottom-color: #A01315;
			-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
			box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
			color: #FFF;
			text-decoration: none;
			text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
		}

		.wp-core-ui .button-secondary.hover, .wp-core-ui .button-secondary:hover, .wp-core-ui .button-secondary.focus, .wp-core-ui .button-secondary:focus {
			background: #dd1a2e;
			background: -moz-linear-gradient(top, #dd1a2e 0%, #891010 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#dd1a2e), color-stop(100%,#891010));
			background: -webkit-linear-gradient(top, #dd1a2e 0%,#891010 100%);
			background: -o-linear-gradient(top, #dd1a2e 0%,#891010 100%);
			background: -ms-linear-gradient(top, #dd1a2e 0%,#891010 100%);
			background: linear-gradient(to bottom, #dd1a2e 0%,#891010 100%);
			border-color: #891010;
			-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
			box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
			color: #FFF;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3); 
		}


		</style>		
		<script type="text/javascript">
		 	$_=jQuery.noConflict();
			$_(document).ready(function() {

			});
		</script>
		<?php
	}
	/**
	 *	@function	widgets_and_sidebar
	 *	@params 	void
	 *	@return 	void
	 */
	function widgets_and_sidebar(){
		unregister_widget('WP_Widget_Calendar');
		unregister_widget('WP_Widget_Search');
		unregister_widget('WP_Widget_Recent_Comments');
		unregister_widget('WP_Widget_Categories');
		unregister_widget('WP_Widget_Links');
		unregister_widget('WP_Widget_Meta');
		unregister_widget('WP_Widget_Pages');
		unregister_widget('WP_Widget_Recent_Posts');
		unregister_widget('WP_Widget_RSS');
		unregister_widget('WP_Widget_Tag_Cloud');
		unregister_widget('WP_Widget_Archives');
		unregister_widget('WP_Widget_Text');
		if (function_exists('register_sidebar')) {
			register_sidebar(array(
				'name'=>'General Sidebar',
				'id'=>'sidebar-1',
				'description' => __('This is the general sidebar', '_V'),
				'before_widget' => '',
				'after_widget' => '',
				'before_title' => '',
				'after_title' => ''
			));
		}
	}
	function print_scripts() { @$this->jquery(); }
	/**
	 *	@function	jquery
	 *	@params 	void
	 *	@return 	void
	 */
	function jquery() { if (!is_admin()) { wp_enqueue_script('jquery'); } }
	/**
	 *	@function	footer
	 *	@params 	void
	 *	@return 	void
	 */
	function footer() {
		?>
		<script type="text/javascript">
		 	$js_=jQuery.noConflict();
			$js_(document).ready(function() {

			});
		</script>
		<?php
	}
	/**
	 *	@function	initialize
	 *	@params 	void
	 *	@return 	void
	 */
	function initialize() { @$this->register_my_menu(); }
	/**
	 *	@function	register_my_menu
	 *	@params 	void
	 *	@return 	void
	 */
	function register_my_menu() { register_nav_menu('nav-menu', __('Navigation Menu')); }
	/**
	 *	@function	admin_scripts
	 *	@params 	void
	 *	@return 	void
	 */
	function admin_scripts() {
		?>	
		<style type="text/css">

		</style>
		<script type="text/javascript">
		 	$js_=jQuery.noConflict();
			$js_(document).ready(function() {

			});
		</script>
		<?php
	}

	/*
 	 *	FRONTEND FUNCTIONS
	 */

	/**
	 *	@function	get_menu
	 *	@params 	void
	 *	@return 	<menu:string>
	 */
	function get_menu() {
		$menu = '';
		$args = array('theme_location' => 'nav-menu','menu' => NULL,'container' => false,'container_class' => false,'container_id' => false,'menu_class' => NULL,'menu_id' => NULL,'echo' => false,'fallback_cb' => 'wp_page_menu','before' => NULL,'after' => NULL,'link_before' => NULL,'link_after' => NULL,'items_wrap' => '<ul>%3$s</ul>','depth' => 0,'walker' => NULL);
		$menu .= wp_nav_menu($args);
		return $menu;
	}

} 
$_V = new _V();
global $_V;
else : exit("Class '_V' already exists"); endif;
if (isset($_V)) {
	if (is_admin()) {
		/* ADMIN ACTIONS */
		add_action("admin_head", array($_V, "admin_scripts"),7);
		add_action('admin_print_styles', array(&$_V, 'admin_print_styles'));
		add_action('admin_print_scripts', array(&$_V, 'admin_print_scripts'));
	}
	/* GLOBAL ACTIONS */
	add_action('init',array(&$_V,'initialize'));
	add_action('widgets_init', array(&$_V, 'widgets_and_sidebar'));
	add_action('wp_print_scripts', array(&$_V, 'print_scripts'));
	add_action('wp_dashboard_setup', array(&$_V, 'reconfigure_dashboard'));
}
require_once('functions-widgets.php');
?>