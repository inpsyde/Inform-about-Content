<?php

/**
 * This feature append the Header-Fields 'Message-ID', 'References'
 * and 'In-Reply-To' to the outgoing mails to give mail clients the chance
 * to display them hierarchicaly
 *
 * @package Inform About Content
 * @since 0.0.6
 */

class Iac_Threaded_Mails {

	/**
	 * instance
	 *
	 * @var Iac_Threaded_Mails
	 */
	private static $instance = NULL;

	/**
	 * get the instance
	 *
	 * @return Iac_Threaded_Mails
	 */
	public static function get_instance() {

		if ( ! self::$instance instanceof self ) {
			$new = new self;
			$new->init();
			self::$instance = $new;
		}

		return self::$instance;
	}

	/**
	 * hook into the filters
	 *
	 * @return void
	 */
	protected function init() {

		add_filter( 'iac_comment_headers', array( $this, 'message_id_header' ), 10, 3 );
		add_filter( 'iac_comment_headers', array( $this, 'message_reference_headers' ), 10, 3 );
		add_filter( 'iac_post_headers',    array( $this, 'message_id_header' ), 10, 3 );
		add_filter( 'iac_post_headers',    array( $this, 'message_reference_headers' ), 10, 3 );
	}

	/**
	 * apply the ID header
	 *
	 * @param array $headers
	 * @param array $iac_options
	 * @param int $item_ID
	 * @return array
	 */
	public function message_id_header( $headers, $iac_options, $item_ID ) {

		$type = ( 'iac_comment_headers' == current_filter() )
			? 'comment'
			: 'post';

		$item = ( 'post' == $type )
			? get_post( $item_ID )
			: get_comment( $item_ID );

		$headers[ 'Message-ID' ] =  '<' . Iac_Mail_ID::generate_ID( $type, $item ) . '>';

		return $headers;
	}

	/**
	 * apply reference headers
	 *
	 * @param array $headers
	 * @param array $iac_options
	 * @param int $item_ID
	 * @return array
	 */
	public function message_reference_headers( $headers, $iac_options, $item_ID ) {

		$type = ( 'iac_comment_headers' == current_filter() )
			? 'comment'
			: 'post';
		$parent = NULL;

		switch ( $type ) {
			case 'post' :
				$item = get_post( $item_ID );
				if ( 0 != $item->post_parent )
					$parent = get_post( $post_parent );
				break;
			case 'comment' :
				$item = get_comment( $item_ID );
				if ( 0 != $item->comment_parent ) {
					$parent = get_comment( $item->comment_parent );
				} else {
					$parent = get_post( $item->comment_post_ID );
					$type   = 'post';
				}
				break;
		}

		if ( ! $parent )
			return $headers;

		$headers[ 'References' ]  = '<' . Iac_Mail_ID::generate_ID( $type, $parent ) . '>';
		$headers[ 'In-Reply-To' ] = '<' . Iac_Mail_ID::generate_ID( $type, $parent ) . '>';

		return $headers;
	}
}
