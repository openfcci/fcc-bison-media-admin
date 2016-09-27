<?php
/*
Plugin name: FCC Bison Media: Photo Approval & Live Video Scheduler
Plugin URI: http://www.forumcomm.com/digital-network/
Description: Plugin for approving or rejecting Bison Media photo feed images and scheduling live video event streams.
Author: FCC Interactive (Ryan Veitch)
Author URI: http://www.forumcomm.com/digital-network/
Version: 1.2.1-16.09.01
Text Domain: bison-media-admin
*/

/*************************** Plugin Registration  *****************************
*******************************************************************************
* Plugin loading, registration and admin dashboard menu functions.
*/

  /**
   * Flush our rewrite rules on deactivation.
   */
  function fcc_deactivation() {
  	flush_rewrite_rules();
  }
  register_deactivation_hook( __FILE__, 'fcc_deactivation' );

  /**
   * Load "includes" files.
   */
  function load_bisonmedia_includes() {
    if ( function_exists('current_user_can') && current_user_can('manage_options') ) {
      require_once( plugin_dir_path( __FILE__ ) . 'includes/bisonmedia-custom-metaboxes.php' );
      require_once( plugin_dir_path( __FILE__ ) . 'includes/admin-photo-approval.php' );
      //require_once( plugin_dir_path( __FILE__ ) . 'includes/admin-bison_media_menu.php' ); //UNUSED MENU
    }
  }
  add_action( 'init', 'load_bisonmedia_includes' );

  function replace_photofeed_admin_menu_icon() {
    ?>
    <style type="text/css">
    #adminmenu #toplevel_page_bison-media-photo-admin div.wp-menu-image img {
      display: none;
    }
    #adminmenu #toplevel_page_bison-media-photo-admin div.wp-menu-image:before {
      content: '\f306';
    }
    </style>
    <?php
  }
  add_action( 'admin_head', 'replace_photofeed_admin_menu_icon' );

/*************************** Register Custom Post Type  ***********************
*******************************************************************************
* Registers the "BisonLive—Schedule Video" custom post type and menu.
*/

  add_action( 'init', 'bisonmedia_register_my_cpts' );
  function bisonmedia_register_my_cpts() {
  	$labels = array(
  		"name" => "Live Videos",
  		"singular_name" => "Live Video",
  		"menu_name" => "BisonLive—Schedule Video",
  		"all_items" => "Scheduled Live Videos",
  		"add_new" => "Add New",
  		"add_new_item" => "Add New Live Video Event",
  		"edit" => "Edit",
  		"edit_item" => "Edit Video",
  		"new_item" => "New Video",
  		"view" => "View",
  		"view_item" => "View Video",
  		"search_items" => "Search Video",
  		"not_found" => "No Videos Found",
  		"not_found_in_trash" => "No Videos found in Trash",
  		"parent" => "Parent Video",
  		);
  if ( function_exists('current_user_can') && current_user_can('manage_options') ) {
  	$args = array(
  		"labels" => $labels,
  		"description" => "Live Video Custom Post Type Test",
  		"public" => true,
  		"show_ui" => true,
  		"has_archive" => true,
  		"show_in_menu" => true,  //DO SHOW IN ADMIN
  		"exclude_from_search" => true,
  		"capability_type" => "post",
  		"map_meta_cap" => true,
  		"hierarchical" => false,
  		"rewrite" => array( "slug" => "live_video", "with_front" => true ),
  		"query_var" => true,
  		"menu_icon" => "dashicons-video-alt2",
  		"supports" => array( "title", "revisions"  ),
  	);
  } else {
      $args = array(
        "labels" => $labels,
        "description" => "Live Video Custom Post Type Test",
        "public" => true,
        "show_ui" => true,
        "has_archive" => true,
        "show_in_menu" => false, //DO NOT SHOW IN ADMIN
        "exclude_from_search" => true,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => array( "slug" => "live_video", "with_front" => true ),
        "query_var" => true,
        "menu_icon" => "dashicons-video-alt2",
        "supports" => array( "title", "revisions"  ),
      );
    }// endelse
  	register_post_type( "live_video", $args );
  } // End of bisonmedia_register_my_cpts()


  /**
   * Register Custom Columns
   */
  function add_new_live_video_columns($live_video_columns) {
      $new_columns['cb'] = '<input type="checkbox" />';
      $new_columns['title'] = _x('Event Title', 'column name');
      $new_columns['eventtime'] = __('Event Time');
      $new_columns['id'] = __('Post ID');
      $new_columns['scheduled'] = __('Scheduled');
      $new_columns['date'] = _x('Published', 'column name');

      return $new_columns;
  }
  add_filter('manage_edit-live_video_columns', 'add_new_live_video_columns');

  /**
   * Custom Columns Content
   */
  function manage_live_video_columns($column_name, $id) {
      global $wpdb;
      switch ($column_name) {
      case 'id':
          echo $id;
              break;
      case 'eventtime':
          $event_start_time_value = get_post_meta($id, 'start-time', true);
          $event_str_start_time = strtotime($event_start_time_value);
          $event_start_time = gmdate('m-d-Y g:i a', $event_str_start_time);
          if (!empty($event_start_time_value)) {
            echo $event_start_time;
          }
          else {
            echo 'N/A';
          }
          break;
      case 'scheduled':
          if (get_post_meta($id, 'pn_sent', true)) {
            echo '<div style="padding-left: 20px;"><span class="dashicons dashicons-yes" style="color:green"></span></div>';
          } else {
            echo '<div style="padding-left: 20px;"><span class="dashicons dashicons-no" style="color:red"></span></div>';
          }
          break;
      default:
          break;
      } // end switch
  }
  add_action('manage_live_video_posts_custom_column', 'manage_live_video_columns', 10, 2);

  /**
   * Make Column Sortable
   */
   add_filter( 'manage_edit-live_video_sortable_columns', 'make_sortable_live_video_column' );
   function make_sortable_live_video_column( $columns ) {
     $columns['eventtime'] = 'eventtime';
     $columns['id'] = 'id';
     return $columns;
   }

  /**
   * Teach WP How to Sort My Columns
   */
  function live_video_columns_orderby( $query ) {
      if( ! is_admin() )
          return;
      $orderby = $query->get('orderby');
      if( 'eventtime' == $orderby ) {
          $query->set('meta_key','start-time');
          $query->set('orderby','meta_value');
      }
  }
  add_action( 'pre_get_posts', 'live_video_columns_orderby' );

/*************************** PARSE SDK ****************************************
*******************************************************************************
* Include and initialize the Parse PHP SDK autoloader.
*/

/* WP-Options */
// $app_id = get_option( 'pn_app_id' );
// $rest_key = get_option( 'pn_app_masterkey' );
// $master_key = get_option( 'pn_app_restkey' );

/* WP-Options Backendless */
// $app_id = get_option( 'backendless_app_id' );
// $rest_key = get_option( 'backendless_app_masterkey' );
// $master_key = get_option( 'backendless_app_restkey' );
// $version = get_option('backendless_app_version');

$app_id = 'app-key';
$rest_key = 'api-rest-key';
// $master_key = get_option( 'backendless_app_restkey' );
$version = 'v1';

/* Load the Parse PHP SDK */
require( 'includes/parse-php-sdk-master/autoload.php' );

/* Load Backendless PHP SDK */
require( 'includes/backendless/autoload.php' );

/* Add class "use" declarations */
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParsePush;
use Parse\ParseInstallation;

use backendless\Backendless;

/**
 * Init parse: app_id, master_key, rest_key
 */
// ParseClient::initialize( $app_id, $master_key, $rest_key );

Backendless::initApp($app_id, $rest_key, $version);
/*************************** PARSE PN-CLASSES ********************************
******************************************************************************
* Include and initialize the Parse PHP SDK autoloader.
*/

function fcc_pn_schedule( $post_id ) {

	/* If this isn't a 'live_video' post, do nothing. */
	if ( 'live_video' != get_post_type() ) {
	    return;
	}

	/* If this is just a revision or autosave, do nothing. */
	if ( wp_is_post_revision( $post_id ) or wp_is_post_autosave( $post_id ) ) {
		return;
	}

	/* If this post has already been scheduled, do nothing. */
	if ( get_post_meta( $post_id, 'pn_sent', true ) ) {
		return;
	}

	/* If the post status action is 'publish, continue with the PN Schedule' */
	if ( 'publish' == get_post_status( $post_id ) ) {

		if ( get_post_meta( $post_id, 'start-time', true ) ) {

			/* Declare Variables */
			$parse_query = ParseInstallation::query();
			$alert = get_post_meta( $post_id, 'alert', true ); //Push Notification Text
			$android_title = get_the_title( $post_id ); //Push Notification Title (Android Only)
			$start_time = get_post_meta( $post_id, 'start-time', true );
				$pstart_time = strtotime( $start_time ) - 300; // Subtract 5 minutes from start time to sent 5 mins earlier.
			  $push_time = date( 'Y-m-d\TH:i', $pstart_time );

			/* Send Push */
			ParsePush::send( array(
				'push_time' => new DateTime( $push_time ),
				'where' => $parse_query,
				'expiry' => 86400,
				'data' => array(
					'alert' => $alert,
					'title' => $android_title,
					'post_id' => $post_id,
				)
			));

			/* Add 'pn_sent' post meta to mark the post as scheduled and prevent duplicate push sends */
			add_post_meta( $post_id, 'pn_sent', true, true ) or update_post_meta( $post_id, 'pn_sent', true, true );
		} else {
			return; // Do nothing, no start time was included
		}
	}
}
add_action( 'wp_insert_post', 'fcc_pn_schedule' );


/*--------------------------------------------------------------
# PARSE API FUNCTIONS
---------------------------------------------------------------*/

/**
 * Push Notification Send: iOS
 * Location: WP-Dashboard, 'Parse Push Notifications' Screen
 */
function parse_push_notifications_send( $message ) {
	$query_ios = ParseInstallation::query();
	$query_ios->equalTo( 'deviceType', 'ios' );
	ParsePush::send(array(
		'where' => $query_ios,
		'data' => array(
			'alert' => $message,
		),
	));
}

/**
 * Push Notification Send: To Everyone
 * Location: WP-Dashboard, 'Parse Push Notifications' Screen
 */
function parse_push_notifications_send_to_all( $message ) {
	ParsePush::send(array(
		'where' => '{}',
		'expiry' => 86400, // expire in 24 hrs
		'data' => array(
			'alert' => $message,
		),
	));
}

/**
* Approve Photo
*/
if ( isset( $_POST ['parse_approve_push_btn'] ) ) {
	parse_approval_send( $_POST['ob_id'] );
}

function parse_approval_send( $message ) {
	$approve_photos = new ParseObject( 'Photos', $message );
	$approve_photos->set( 'approved', true );
	$approve_photos->save();
}

  /**
   * Unapprove Photo
   */
   if ( isset ( $_POST ['parse_unapprove_push_btn'] ) ) {
     parse_unapprove_send( $_POST['ob_id'] );
   }
  function parse_unapprove_send( $message ) {
   $approve_photos = new ParseObject( "Photos", "$message" );
   $approve_photos->set( "approved", false );
   $approve_photos->save();
  }

  /**
   * Delete Photo
   */
   if ( isset ( $_POST ['parse_delete_push_btn'] ) ) {
     parse_del_send( $_POST['ob_id'] );
   }

  function parse_del_send( $message ) {
   // Set and Find Object
   $parsePhotos = new ParseObject('Photos');
   $parsePhotosQuery = new ParseQuery('Photos');
   $parsePhotosQuery->EqualTo('objectId', "$message");
   $results = $parsePhotosQuery->find();
   // Delete Object
   $object = $results[0];
   $object->destroy();
  }

/*--------------------------------------------------------------
 # JSON FEED
 --------------------------------------------------------------*/

/**
	* Add 'video' JSON Feed
	*
	* @since 1.15.08.11
	* @version 1.16.09.01
	*/
function fcc_bison_media_do_json_feed() {
	add_feed( 'video', 'add_bison_media_video_feed' );
	add_feed( 'liveblog', 'add_bison_media_blog_feed' );
}
add_action( 'init', 'fcc_bison_media_do_json_feed' );


function add_bison_media_video_feed() {
	load_template( plugin_dir_path( __FILE__ ) . 'template/feed-video.php' );
}

function add_bison_media_blog_feed() {
	load_template( plugin_dir_path( __FILE__ ) . 'template/feed-liveblog.php' );
}

 /*************************** PARSE-PN Admin Dashboard Menu ********************
 *******************************************************************************
 * Admin dashboard menu functions.
 */


   /**
    * Register Settings Page
    */
     add_action('admin_menu', 'parse_push_notifications_admin_pages');
     function parse_push_notifications_admin_pages() {
       if ( function_exists('current_user_can') && current_user_can('manage_options') ) { //Show Push Notification Tester ONLY to main SuperAdmin
       	add_options_page(
          'Parse API Settings',
          'Parse API Settings',
          'manage_options',
          'fcc_bison_push_settings',
          'fcc_bison_push_settings'
        );
        add_action('admin_init', 'wp_parse_pn_admin_init');
      }
     }

   /**
    * Register API Keys
    */
     function wp_parse_pn_admin_init() {
         register_setting('wp-parse-pn-settings-group', 'pn_app_id');
         register_setting('wp-parse-pn-settings-group', 'pn_app_masterkey');
         register_setting('wp-parse-pn-settings-group', 'pn_app_restkey');
     }

 /*************************** PARSE-PN Dashboard Page **************************
 *******************************************************************************
 * Create admin dashboard page.
 */

   function fcc_bison_push_settings() { //function parse_push_notifications_options_page() {

      // echo'<div class="wrap"><div class="card"><div class="inside">';
     // 	parse_push_notifications_create_form();
     // 	echo '</div></div></div>';

     ?>
     <!-- <div class="wrap">
     <div class="card">
       <div class="inside">
   	<form action="options.php" method="post">
   		<?php settings_fields('wp-parse-pn-settings-group'); ?>

   		<h3>Parse API App Settings</h3>

   		<table class="form-table">
   			<tr valign="top">
   				<th style="width:125px" scope="row">Application ID: </th>
   				<td><input type="text" name="pn_app_id" value="<?php echo get_option('pn_app_id'); ?>" size="50"></td>
   			</tr>
        <tr valign="top">
   				<th style="width:125px" scope="row">REST API Key: </th>
   				<td><input type="text" name="pn_app_restkey" value="<?php echo get_option('pn_app_restkey'); ?>" size="50"></td>
   			</tr>
   			<tr valign="top">
   				<th style="width:125px" scope="row">Master Key: </th>
   				<td><input type="text" name="pn_app_masterkey" value="<?php echo get_option('pn_app_masterkey'); ?>" size="50"></td>
   			</tr>
   		</table>

   		<?php submit_button(); ?>

   	</form>
     </div>
     </div>
     </div> -->
     <?php

		 echo'<div class="wrap"><div class="card"><div class="inside">';
			parse_push_notifications_create_form();
			echo '</div></div></div>';

		?>
		<div class="wrap">
		<div class="card">
			<div class="inside">
		<form action="options.php" method="post">
			<?php settings_fields('wp-parse-pn-settings-group'); ?>

			<h3>Parse API App Settings</h3>

			<table class="form-table">
				<tr valign="top">
					<th style="width:125px" scope="row">Application ID: </th>
					<td><input type="text" name="pn_app_id" value="<?php echo get_option('pn_app_id'); ?>" size="50"></td>
				</tr>
			 <tr valign="top">
					<th style="width:125px" scope="row">REST API Key: </th>
					<td><input type="text" name="pn_app_restkey" value="<?php echo get_option('pn_app_restkey'); ?>" size="50"></td>
				</tr>
				<tr valign="top">
					<th style="width:125px" scope="row">Master Key: </th>
					<td><input type="text" name="pn_app_masterkey" value="<?php echo get_option('pn_app_masterkey'); ?>" size="50"></td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form>
		</div>
		</div>
		</div>
		<?php

   }

   function parse_push_notifications_create_form(){

   	if (isset($_POST['parse_push_notifications_push_btn']))
   	{
   	   if ( function_exists('current_user_can') &&
   			!current_user_can('manage_options') )
   				die ( _e('Hacker?', 'parse_push_notifications') );
   		if (function_exists ('check_admin_referer') )
   			check_admin_referer('parse_push_notifications_form');
           parse_push_notifications_send_to_all($_POST['pn_text']);
   	}

     ?>
   		<div id="pn_form">
         <h2>Send a Parse Push Notification</h2>
         <form id="push_form" name="parse_push_notifications" method="post" action=" <?php $_SERVER['PHP_SELF'] ?> ?page=parse_push_notifications&amp;updated=true">
     <?php
       if (function_exists ('wp_nonce_field') )
       wp_nonce_field('parse_push_notifications_form');
     ?>
           <div>
             <p><input type="text" name="pn_text" placeholder="Enter push notification text here" size="70" maxlength="255" /></p>
           </div>
           <div>
             <input type="submit" id="push_button" class="button-primary" name="parse_push_notifications_push_btn" value="Send to all iOS devices"/>
           </div>
         </form>
   		</div>
   		<?php
   }

?>
