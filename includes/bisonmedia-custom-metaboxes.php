<?php
/*************************** Metabox Module ***********************************
*******************************************************************************
* Include and initialize the iG:Custom Metaboxes autoloader.
*/

define( 'IG_CUSTOM_METABOXES_ROOT', __DIR__ . '/ig-custom-metaboxes-master/' );
define( 'IG_CUSTOM_METABOXES_VERSION', '1.0' );

function ig_custom_metaboxes_loader() {
	/*
	 * Load autoloader
	 */
	require_once IG_CUSTOM_METABOXES_ROOT . 'autoload.php';
}

ig_custom_metaboxes_loader();

// Set Minimum Date/Time To Now
$min_date = date('Y-m-d\TH:i', current_time( 'timestamp', 0 ) );
// Set Max Date/Time Now + Plus Two Weeks
$mb_current_time = date( 'Y-m-d\TH:i', current_time( 'timestamp', 0 ) );
$mb_time_plus_two_weeks = strtotime( $mb_current_time ) + 1209600;
$max_start_date = date( 'Y-m-d\TH:i', $mb_time_plus_two_weeks );

$metabox = new iG\Metabox\Metabox( 'bison-pn-metabox' );

$metabox->set_title( 'Live Video & Push Notification Scheduler' )
		->set_context( 'normal' )
		->set_priority( 'default' )
		->set_css_class( 'pn-metabox' )
		->set_post_type( 'live_video' )
		->add_field(	//add a simple text input field
			iG\Metabox\Text_Field::create( 'alert', 'Event Description' )
								//->set_description( 'Android notifications use both Title and Description, iOS notifications will display Description only.' )
								->set_css_class( 'bison-pn-alert-msg' )
								->set_placeholder( 'Enter the push notification text here' )
								->set_size( 50 )
		)
		->add_field(	//add a HTML5 date time picker
			iG\Metabox\Date_Time_Field::create( 'start-time', 'Start Date/Time' )
								//->set_description( 'Push Notifications cannot be scheduled more than two weeks in advance.' )
								->set_css_class( 'live-start-time' )
								->set_min( $min_date )
								->set_max( $max_start_date )
		)
    ->add_field(	//add a HTML5 date time picker
			iG\Metabox\Date_Time_Field::create( 'end-time', 'End Date/Time' )
								->set_description( '
								<br>
								*Android notifications display both Title and Description. iOS notifications display Description only.<br>
								**Push Notifications cannot be scheduled more than two weeks in advance.<br>
								***Editing or deleteing an event that has already been scheduled <strong>will not update or delete</strong> the scheduled push notification on Parse.com. Changes to scheduled notifications must be made from the Parse.com Development Console.
								' )
								->set_css_class( 'live-end-time' )
								->set_min( $min_date )
		)
		->add();

		?>
