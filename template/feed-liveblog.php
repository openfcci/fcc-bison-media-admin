<?php
/**
 * Bison Meda Live Blog Feed
 * JSON Feed Template for displaying JSON Posts feed.
 *
 * @since 1.16.09.01
 * @version 1.16.09.01
 */

$callback = trim( esc_html( get_query_var( 'callback' ) ) );
$charset  = get_option( 'charset' );

// update_option( 'liveblog_feed_pageid', '86565'); // update the site page to parse the meta from
$scribblelive_event_id = get_post_meta( get_option( 'liveblog_feed_pageid' ), 'scribblelive_event_id', true );
$ustream = '';

$live_blog = array(
	'scribblelive' => $scribblelive_event_id,
	'ustream'			 => $ustream,
	);

if ( $live_blog ) {

	$json = json_encode( $live_blog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

	nocache_headers();
	if ( ! empty( $callback ) ) {
		header( "Content-Type: application/x-javascript; charset={$charset}" );
		echo "{$callback}({$json});";
	} else {
		header( "Content-Type: application/json; charset={$charset}" );
		echo $json;
	}
} else {
	status_header( '404' );
	wp_die( '404 Not Found' );
}
