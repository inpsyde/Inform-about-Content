<?php

/**
 * Send Post Media as attachment
 *
 * @package Inform About Content
 * @since 0.0.6
 */

class Iac_Attach_Media {

	/**
	 * instance
	 *
	 * @var Iac_Attach_Media
	 */
	private static $instance = NULL;

	/**
	 * get the instance
	 *
	 * @return Iac_Attach_Media
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

		add_filter( 'iac_post_attachments', array( $this, 'attach_media' ), 10, 3 );
	}

	/**
	 * apply reference headers
	 *
	 * @param array $attachments
	 * @param array $iac_options
	 * @param int $post_ID
	 * @return array
	 */
	public function attach_media( $attachments, $iac_options, $item_ID ) {

		if ( '1' !== $iac_options[ 'send_attachments' ] )
			return $attachments;


		return $attachments;
	}
}
