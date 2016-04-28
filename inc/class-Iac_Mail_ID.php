<?php

/**
 * Generates an ID for Comments an Posts
 *
 * @package Informer
 * @since 0.0.6
 */

class Iac_Mail_ID {

	/**
	 * generates the ID based on content type (comment or post)
	 *
	 * @param string $type
	 * @param object $item
	 */
	public static function generate_ID( $type, $item ) {

		$item = self::sanitize_item( $item );
		$type = in_array( $type, array( 'comment', 'post' ) )
			? $type
			: 'post';
		$blog_ID = get_current_blog_ID();
		$base = array(
			$type,
			$blog_ID,
			$item->ID,
			$item->date_gmt
		);
		$url_parts = parse_url( get_option( 'siteurl' ) );

		$ID = sha1( implode( ':', $base ) ) . '@' . $url_parts[ 'host' ];

		return $ID;
	}

	/**
	 * normalize comment/post data objects
	 *
	 * @param array|object $item
	 * @return stdClass
	 */
	protected static function sanitize_item( $item ) {

		if ( is_array( $item ) )
			$item = ( object )$item;

		if ( empty( $item->ID ) && isset( $item->comment_ID ) )
			$item->ID = $item->comment_ID;

		if ( isset( $item->post_date_gmt ) )
			$item->date_gmt = $item->post_date_gmt;

		if ( isset( $item->comment_date_gmt ) )
			$item->date_gmt = $item->comment_date_gmt;

		return $item;
	}

}
