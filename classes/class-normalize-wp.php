<?php 
class normalizeWP {

	/**
	* Constructor
	*
	* @since  1.0
	*/
    public function __construct() {		
	   	# Scripts
		add_filter( 'wp_default_scripts', array( &$this, 'dequeue_jquery_migrate' ) ); // Remove jQuery Migrate script
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' ); 		
		add_filter( 'style_loader_src',  array( &$this, 't5_remove_version' )); // Remove version numbers from stylesheets
		add_filter( 'script_loader_src', array( &$this, 't5_remove_version' )); // Remove version numbers from scripts
		
		# Images
		add_filter( 'upload_mimes', array( &$this, 'cc_mime_types' ) ); // Allow SVG upload

		# Oembed
		add_filter('oembed_providers', array( &$this, 'twitter_oembed' ) ); // Add support for Twitter embed
		add_filter('oembed_dataparse', array( &$this, 'your_theme_embed_filter'), 90, 3 ); // Add box to Oembed video's and tweets for responsive support
		add_filter('oembed_result', array( &$this, 'modify_youtube_embed_url')); // Modify  YouTube URL

		# Users
		add_action('admin_init', array( &$this, 'edit_user_roles'));
		add_filter( 'user_contactmethods', array( &$this, 'user_contactmethods'), 10, 1); // Add & remove certain contact information fields from user profile

		# Plugins
		add_filter("gform_init_scripts_footer", array( &$this, "init_scripts")); // Load Gravity Forms scripts in footer

		# WP frontend
		add_action('init', array( &$this, 'remove_header_info')); // remove unnecessary header info
		add_filter('the_category', array( &$this, 'remove_category_rel_from_category_list')); // Remove invalid rel attribute
		add_filter('body_class', array( &$this, 'add_slug_to_body_class')); // Add page slug to body class
		add_filter('the_content_more_link', array( &$this, 'remove_more_jump_link')); // remove jump to content in more-link
		add_filter('excerpt_more', array( &$this, 'html5_blank_view_article')); // Add 'View Article' button instead of [...] for Excerpts

		# WP backend
		// add_action( 'admin_menu', array( &$this, 'adjust_the_wp_menu'), 999 ); // Remove items admin submenu
		add_filter( 'admin_footer_text', array( &$this, 'custom_admin_footer')); // Customize admin footer text
		add_filter( 'tiny_mce_before_init', array( &$this, 'fb_change_mce_options')); // Allow more HTML tags in the editor	
		add_action( 'wp_before_admin_bar_render', array( &$this, 'remove_admin_bar_items'), 0 ); // Remove items from admin menu
		// add_action( 'admin_menu', array( &$this, 'remove_admin_menu_items')); // Remove items in admin menu
		add_action( 'wp_dashboard_setup', array( &$this, 'my_custom_dashboard_widgets')); // Deactivate dashboard widgets

    }

	/* 	=============================================================================
	   	Scripts
	   	========================================================================== */

		/* 
		 * Remove jQuery Migrate script
		 */
		public function dequeue_jquery_migrate( &$scripts){
			if(!is_admin()){
				$scripts->remove( 'jquery');
				$scripts->add( 'jquery', false, array( 'jquery-core' ), '1.10.2' );
			}
		}

		/* 
		 * Remove version numbers from static resources
		 */
		public function t5_remove_version( $url ) {
		    return remove_query_arg( 'ver', $url );
		}


	/* 	=============================================================================
	   	Images
	   	========================================================================== */

		/*
		 * Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
		 */
		public function remove_thumbnail_dimensions( $html ){
		    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
		    return $html;
		}

		/* 
		 * Allow SVG upload
		 */
		public function cc_mime_types( $mimes ){
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		}


	/* 	=============================================================================
	   	Oembed settings / add-ons
	   	========================================================================== */  

		/*
		 * Add Twitter support
		 */
		public function twitter_oembed($a) {
		    $a['#http(s)?://(www\.)?twitter.com/.+?/status(es)?/.*#i'] = array( 'http://api.twitter.com/1/statuses/oembed.{format}', true);
		    return $a;
		}
				
		/*
		 * Add container to video's
		 */
		public function your_theme_embed_filter( $output, $data, $url ) {
			if ( $data->type == 'video' ) {
				$return = '<figure class="box box-video">'.$output.'</figure>';
				return $return;
			}
			if ( $data->provider_name == 'Twitter' ) {
				$return = '<figure class="box box-tweet">'.$output.'</figure>';
				return $return;		
				//echo print_r($data);
			}
		}

		/*
		 * Modify YouTube Embed URL	
		 */
		public function modify_youtube_embed_url($html) {
			$autoplay = '';
			
			if( get_field('video-popup') ) {
				$autoplay = '&autoplay=1';
			}
		    return str_replace("?feature=oembed", "?feature=oembed&". $autoplay ."vq=hd720&showinfo=0&autohide=1", $html);
		}


	/* 	=============================================================================
	   	Users, roles & capabilities
	   	========================================================================== */  

		public function edit_user_roles() {
			// Add certain admin roles to editor
			$_the_roles = new WP_Roles();

			$_the_roles->add_cap('editor','list_users');
			$_the_roles->add_cap('editor','edit_users');
			$_the_roles->add_cap('editor','create_users');
			$_the_roles->add_cap('editor','delete_users');
			$_the_roles->add_cap('editor','edit_theme_options');
			// $_the_roles->add_cap('editor','gform_full_access');
		}

		/*
		 * Add & remove certain contact information fields from user profile
		 */
		public function user_contactmethods($user_contactmethods){
			unset($user_contactmethods['yim']);
			unset($user_contactmethods['aim']);
			unset($user_contactmethods['jabber']);
			unset($user_contactmethods['website']);
			// unset($user_contactmethods['website']);
			// $user_contactmethods['phone'] = 'Telefoon';
			// $user_contactmethods['twitter'] = 'Twitter';
			// $user_contactmethods['facebook'] = 'Facebook';
			// $user_contactmethods['linkedin'] = 'Linkedin';
			// $user_contactmethods['user_title'] = 'Website Name';
			// $user_contactmethods['functie'] = 'Functie';
			// $user_contactmethods['gplus'] = 'Google Plus';
			return $user_contactmethods;
		}
		// Use this code to embed in template:
		// echo get_user_meta(1, 'twitter', true);



	/* 	=============================================================================
		Plugins
		===========================================================================*/

		/*
		 * Custom Styles plugin
		 */
		// include_once( rtrim( dirname( __FILE__ ), '/' ) . '/_/plugins/custom_styles.php' );


		public function init_scripts(){
		    return true;
		}


	/* 	=============================================================================
	   	WordPress Frontend
	   	========================================================================== */

		/*
		 * remove unnecessary header info
		 */
		public function remove_header_info() {
		    remove_action('wp_head', 'rsd_link');
		    remove_action('wp_head', 'wlwmanifest_link');
		    remove_action('wp_head', 'wp_generator');
		    remove_action('wp_head', 'start_post_rel_link');
		    remove_action('wp_head', 'index_rel_link');
		    remove_action('wp_head', 'adjacent_posts_rel_link');         // for WordPress <  3.0
		    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // for WordPress >= 3.0
		}

		/*
		 * Remove the <div> surrounding the dynamic navigation to cleanup markup 
		 */
		public function my_wp_nav_menu_args( $args = '' ) {
			$args['container'] = false;
			return $args;
		}

		/*
		 * Remove Injected classes, ID's and Page ID's from Navigation <li> items
		 */
		public function my_css_attributes_filter($var) {
			return is_array($var) ? array() : '';
		}

		/*
		 * Remove invalid rel attribute
		 */
		public function remove_category_rel_from_category_list($thelist){
		     return str_replace('rel="category tag"', 'rel="tag"', $thelist);
		}
		
		/*
		 * Add page slug to body class, love this - Credit: Starkers Wordpress Theme
		 */
		public function add_slug_to_body_class( $classes ) {
			global $post;
			if(is_singular() || is_page() ) {
				$classes[] = sanitize_html_class( $post->post_name );
			};

			return $classes;
		}

		/*
		 * remove jump to content in more-link
		 */
		public function remove_more_jump_link($link) { 
			$offset = strpos($link, '#more-');
			if ($offset) {
				$end = strpos($link, '"',$offset);
			}
			if ($end) {
				$link = substr_replace($link, '', $offset, $end-$offset);
			}
			return $link;
		}

		/*
		 * Custom View Article link to Post
		 */
		public function html5_blank_view_article($more)
		{
		    global $post;
		    return '';
		}
		

	/* 	=============================================================================
	   	WordPress UI
	   	========================================================================== */

		/*
		 * Remove items dashboard menu
		 */
		public function remove_menus () {
			global $menu;
			$restricted = array(
				// __('Dashboard'),
				// __('Posts'), 
				// __('Media'), 
				// __('Links'), 
				// __('Pages'), 
				// __('Appearance'), 
				// __('Tools'), 
				// __('Users'), 
				// __('Settings'),
				// __('Comments')
				// __('Plugins')
			);
			end ($menu);
			while (prev($menu)){
				$value = explode(' ',$menu[key($menu)][0]);
				if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
			}
		}

		/*
		 * Remove items admin submenu
		 */
		// public function adjust_the_wp_menu() {
		// 	$page = remove_submenu_page( 'themes.php', 'widgets.php' );
		// 	// $page[0] is the menu title
		// 	// $page[1] is the minimum level or capability required
		// 	// $page[2] is the URL to the item's file
		// }

		/*
		 * Customize admin footer text
		 */
		public function custom_admin_footer() {
		        echo '<strong>Probleempje?</strong> Bel Bram direct op <a href="tel:+31628265381">(+31) (0)6 28 26 53 81</a> of mail hem op <a href="mailto:contact@bramwillemse.nl">contact@bramwillemse.nl</a>';
		} 


	/* 	=============================================================================
	   	WordPress backend function tweaks
	   	========================================================================== */

		/*
		 * Allow more HTML tags in the editor
		 */
		public function fb_change_mce_options($initArray) {
			$ext = 'pre[id|name|class|style],iframe[align|longdesc| name|width|height|frameborder|scrolling|marginheight| marginwidth|src]';
		
			if ( isset( $initArray['extended_valid_elements'] ) ) {
				$initArray['extended_valid_elements'] .= ',' . $ext;
			} else {
				$initArray['extended_valid_elements'] = $ext;
			}
		
			return $initArray;
		}

		/*
		 * Remove items from admin menu
		 */
		public function remove_admin_bar_items() {
		        global $wp_admin_bar;
		       
		        $wp_admin_bar->remove_menu('wp-logo'); /* Remove WordPress Logo */
		        // $wp_admin_bar->remove_menu('comments'); /* Remove 'Add New > Posts' */
		        // $wp_admin_bar->remove_menu('new-post'); /* Remove 'Add New > Posts' */
		}

		/*
		 * Remove items in admin menu
		 */
		public function remove_admin_menu_items() {
			// Remove 'Comments'
			// remove_menu_page('edit-comments.php');

			// Remove submenu item: 'Appearance > Customize'
			remove_submenu_page( 'themes.php', 'themes.php' ); // hide the theme selection submenu
			remove_submenu_page( 'themes.php', 'customize.php' ); 
		    remove_submenu_page( 'themes.php', 'widgets.php' ); // hide the widgets submenu

		    // these are theme-specific. Can have other names or simply not exist in your current theme.
		    remove_submenu_page( 'themes.php', 'custom-header' );
		    remove_submenu_page( 'themes.php', 'custom-background' );


			// Conditional removals 
			if(!current_user_can('edit_themes')) { // Remove items for editors and below
				remove_menu_page('tools.php'); 
			}
			
		}

	   	/*
	   	 * Deactivate dashboard widgets
	   	 */
		public function my_custom_dashboard_widgets() {
			global $wp_meta_boxes;
			 //Right Now - Comments, Posts, Pages at a glance
			// unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
			//Recent Comments
			unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
			//Incoming Links
			// unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
			//Plugins - Popular, New and Recently updated Wordpress Plugins
			unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);

			//Wordpress Development Blog Feed
			unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
			//Other Wordpress News Feed
			unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
			//Quick Press Form
			unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
			//Recent Drafts List
			unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
		}

		/*
		 * remove extra CSS that 'Recent Comments' widget injects
		 */
		public function remove_recent_comments_style() {
		    global $wp_widget_factory;
		    remove_action('wp_head', array(
		        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
		        'recent_comments_style'
		    ));
		}
	
} 
?>