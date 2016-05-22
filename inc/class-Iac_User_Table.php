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
		$default_opt_in = $this->options[ 'default_opt_in' ];
		$subscribe_negotiation = function( $meta_value ) use ( $default_opt_in, $check, $ballot_x ) {
			if ( in_array( $meta_value, array( '0', '1' ), TRUE ) ) {
				// if the user has set explicit value, take this as state
				return $meta_value
					? $check
					: $ballot_x;
			}
			// meta value should be empty (not set yet) here, fall back to default:
			return $default_opt_in
				? $check
				: $ballot_x;
		};

		$posts = $subscribe_negotiation(
			get_user_meta( $user_id, 'post_subscription', TRUE )
		);
		$comments = $subscribe_negotiation(
			get_user_meta( $user_id, 'comment_subscription', TRUE )
		);

		$cell =
			  __( 'Posts', 'inform_about_content' ) . ": $posts<br/>"
			. __( 'Comments', 'inform_about_content' ) . ": $comments";

		return $cell;
	}
}