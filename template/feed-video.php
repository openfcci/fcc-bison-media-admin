<?php
/**
 * Bison Meda Live Video Feed
 * JSON Feed Template for displaying JSON Posts feed.
 *
 * @since 1.15.08.11
 * @version 1.16.09.01
 */

$callback = trim( esc_html( get_query_var( 'callback' ) ) );
$charset  = get_option( 'charset' );

# Begin the Loop
wp_reset_query();
$args = array(
	'post_type'     => 'live_video',
	'meta_key'      => 'start-time',
	'orderby'       => 'meta_value',
	'order'         => ASC,
	'meta_query'    => array(
		'relation'    => 'AND',
		array(
			'key'       => 'pn_sent',
			'value'     => '1',
			'compare'   => '=',
		),
		array(
			'key'       => 'expired',
			'value'     => '1',
			'compare'   => 'NOT EXISTS',
		),
	),
);
$the_query = new WP_Query( $args );

if ( $the_query->have_posts()  ) {

	$json = array();

	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$id = (int) $post->ID;

		# DELARE VARIABLES
		$post_title = wp_specialchars( get_the_title( $id ) );
		$alert = get_post_meta( $id, 'alert', true ); //Push Notification Text
		$expired = get_post_meta( $id, 'expired', true ); //Push Notification Text
		# Start Time
		$start_time_value = get_post_meta( $id, 'start-time', true );
		$str_start_time = strtotime( $start_time_value );
		$start_time = gmdate( 'Y-m-d H:i:s', $str_start_time );
		# End Time
		$end_time_value = get_post_meta( $id, 'end-time', true );
		$str_end_time = strtotime( $end_time_value );
		$end_time = gmdate( 'Y-m-d H:i:s', $str_end_time );
		# Current Time
		$current_time = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
		$str_current_time = strtotime( $current_time );
		# Event Duration
		$event_duration = human_time_diff( $str_start_time, $str_end_time );

		# Event Status
		if ( $str_start_time < $str_current_time and $str_end_time > $str_current_time ) {
			$time_compare = 'Event is in progress.';
		} elseif ( $str_start_time > $str_current_time and $str_end_time > $str_current_time ) {
			$time_compare = 'Event is upcoming.';
		} elseif ( $str_current_time > $str_start_time and $str_current_time > $str_end_time ) {
			$time_compare = 'Event is over.';
			add_post_meta( $id, 'expired', true, true ) or update_post_meta( $id, 'expired', true, true );
		} else {
			$time_compare = 'Error comparing times.';
		}

		# Is Live?
		if ( $str_start_time < $str_current_time and $str_end_time > $str_current_time ) {
			$islive = 1;
		} else {
			$islive = 0;
		}

		### JSON ARRAY ###
		$single = array(
			'id' 							=> $id,
			'event' 					=> $post_title,
			'alert' 					=> $alert,
			'start-time'			=> $start_time,
			'end-time'				=> $end_time,
			'current-time'		=> $current_time,
			'event-duration'	=> $event_duration,
			'event-status' 		=> $time_compare,
			'expired' 				=> $expired,
			'live'   					=> $islive,
			);

		# Add single posts to array
		$json[] = $single;

	} // END WHILE

	# Restore original Post Data
	wp_reset_postdata();
	wp_reset_query();

	# Encode array to JSON
	$json = json_encode( $json );

	# Create the feed
	nocache_headers();
	if ( ! empty( $callback ) ) {
		header( "Content-Type: application/x-javascript; charset={$charset}" );
		echo "{$callback}({$json});";
	} else {
		header( "Content-Type: application/json; charset={$charset}" );
		echo $json;
	}
} else {
	# No Upcoming Events
	$no_events = array(
		'event' 					=> 'There are no upcoming live videos scheduled.',
		'event-status' 		=> 0,
		'start-time'			=> "1985-10-26 01:21:22",
		'live'   					=> 1, // TODO temp fix until app is updated, then change back to 0
		);
	$json = json_encode( array( $no_events ) );
	nocache_headers();
	if ( ! empty( $callback ) ) {
		header( "Content-Type: application/x-javascript; charset={$charset}" );
		echo "{$callback}({$json});";
	} else {
		header( "Content-Type: application/json; charset={$charset}" );
		echo $json;
	}
}
