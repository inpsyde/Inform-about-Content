<?php # -*- coding: utf-8 -*-

/**
 * Add a column to the user table
 */
class Iac_User_Table {

	/**
	 * @var array
	 */
	private $options = array();

	private $column_name = 'iac_subscriptions';

	/**
	 * @param array $options
	 */
	public function __construct( array $options = array() ) {

		$this->options = $options;
	}

	/**
	 * @wp-hook manage_users_columns (manage_{$screen->id}_columns)
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function table_header( array $columns = array() ) {

		$columns[ $this->column_name ] = __( 'Subscriptions', 'inform_about_content' );

		return $columns;
	}

	/**
	 * @wp-hook manage_users_custom_column
	 *
	 * @param string $content
	 * @param string $column
	 * @param int    $user_id
	 *
	 * @return string
	 */
	public function table_column( $content, $column, $user_id ) {

		if ( $this->column_name !== $column )
			return $content;

		// Unicode checkmark u2713: ✓
		$check = "\xe2\x9c\x93";
		// Unicode ballot X u2717: ✗
		$ballot_x = "\xe2\x9c\x97";

		$user_settings    = apply_filters( 'iac_get_user_settings', array(), $user_id );
		$subscribe_status = function( $type ) use ( $user_settings, $check, $ballot_x ) {
			$key = "inform_about_{$type}s";
			if ( ! isset( $user_settings[ $key ] ) ) {
				return $ballot_x;
			}

			return $user_settings[ $key ]
				? $check
				: $ballot_x;
		};

		$posts    = $subscribe_status( 'post' );
		$comments = $subscribe_status( 'comment' );
		$cell     =
			  __( 'Posts', 'inform_about_content' ) . ": $posts<br/>"
			. __( 'Comments', 'inform_about_content' ) . ": $comments";

		return $cell;
	}
}