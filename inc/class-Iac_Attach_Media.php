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

		$size  = 0;
		$max_size = $iac_options[ 'attachment_max_size' ];
		$links = array();
		$files = array();
		$dir   = wp_upload_dir();
		$posts = get_children(
			array(
				'post_parent'    => $item_ID,
				'post_type'      => 'attachment',
				'post_status'    => array( 'publish', 'inherit' ),
				'posts_per_page' => -1
			)
		);

		$posts = apply_filters( 'iac_attach_media_posts', $posts, $item_ID, $iac_options );

		if ( empty( $posts ) )
			return $attachments;

		$unique_posts = array();
		foreach ( $posts as $p ) {
			# avoid attach one file twice
			if ( in_array( $p->ID, $unique_posts ) )
				continue;
			$meta = wp_get_attachment_metadata( $p->ID );
			$file = self::get_thumbnail_file( $meta );
			if ( ! $file )
				$file = $dir[ 'basedir' ] . DIRECTORY_SEPARATOR . $meta[ 'file' ];

			if ( ! file_exists( $file ) )
				continue;

			# limit the size of all attachments
			$size = filesize( $file );
			if ( $size < $max_size ) {
				$attachments[]  = $file;
				$unique_posts[] = $p->ID;
			}
		}

		return $attachments;
	}

	/**
	 * function to get the filesystem path to an image
	 *
	 * @param string $size
	 * @param array $meta (Return value of wp_get_attachment_metadata() )
	 * @return string|false
	 */
	public static function get_thumbnail_file( $meta, $size = 'medium' ) {

		if ( ! isset( $meta[ 'sizes' ][ $size ] ) ) {
			$file = FALSE;
		} else {
			$dir = wp_upload_dir();
			$file_parts = array(
				$dir[ 'basedir' ],
				dirname( $meta[ 'file' ] ),
				$meta[ 'sizes' ][ $size ][ 'file' ]
			);

			$file = implode( DIRECTORY_SEPARATOR, $file_parts );
		}

		return apply_filters( 'iac_attach_media_thumbnail_file', $file, $meta, $size );
	}
}
