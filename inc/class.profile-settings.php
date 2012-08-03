<?php
/**
 * Inform about Content - Profile Settings
 * @license GPLv2
 * @package Inform about Content
 * @subpackage Profile Settings
 */

class Iac_Profile_Settings {

	static private $classobj = NULL;
	// string for translation
	public $textdomain;

	/**
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @access public
	 * @since 0.0.2
	 * @return $classobj
	 */
	public function get_object() {

		if ( NULL === self :: $classobj ) {
			self :: $classobj = new self;
		}

		return self :: $classobj;
	}

	/**
	 * Construvtor, init on defined hooks of WP and include second class
	 *
	 * @access  public
	 * @since   0.0.2
	 * @uses    register_activation_hook, register_uninstall_hook, add_action
	 * @return  void
	 */
	public function __construct() {

		// textdomain from parent class
		$this -> textdomain = Inform_About_Content :: get_textdomain();

		register_uninstall_hook(  __FILE__, 	array( 'Iac_Profile_Settings', 'remove_author_meta_values' ) );

		add_action( 'show_user_profile', 		array( $this, 'add_custom_profile_fields' ) );
		add_action( 'edit_user_profile', 		array( $this, 'add_custom_profile_fields' ) );

		add_action( 'personal_options_update', 	array( $this, 'save_custom_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_custom_profile_fields' ) );
	}

	/**
	 * Return Textdomain string
	 *
	 * @access  public
	 * @since   0.0.2
	 * @return  string
	 */
	public function get_textdomain() {

		return $this -> textdomain;
	}

	/**
	 * Remove meta data from all users of the blog
	 *
	 * @access public
	 * @since  0.0.2
	 * @uses   delete_user_meta, get_users
	 * @return void
	 */
	public static function remove_author_meta_values() {
		global $blog_id;

		if ( isset( $blog_id ) && ! empty( $blog_id ) ) {
			$blogusers = get_users( array( 'blog_id' => $blog_id ) );
			foreach ( $blogusers as $user_object ) {
				delete_user_meta( $user_object -> ID, 'post_subscription' );
				delete_user_meta( $user_object -> ID, 'comment_subscription' );
			}
		}

	}

	/**
	 * Add cutom profile fields
	 *
	 * @access public
	 * @since  0.0.2
	 * @uses   _e, checked
	 * @param  array $user
	 * @return void
	 */
	public function add_custom_profile_fields( $user ) {

		$default_opt_in = apply_filters( 'iac_default_opt_in', FALSE );
		$default = $default_opt_in
			? '0'
			: '1';
		$inform_about_posts    = get_user_meta( $user->ID, 'post_subscription', TRUE );
		$inform_about_comments = get_user_meta( $user->ID, 'comment_subscription', TRUE );
		if ( '' === $inform_about_posts )
			$inform_about_posts = $default;
		if ( '' === $inform_about_comments )
			$inform_about_comments = $default;
	?>
		<h3><?php _e( 'Inform about Content?', $this -> get_textdomain() ); ?></h3>

		<table class="form-table">
			<tr id="post_subscription">
				<th>
					<label for="post_subscription_checkbox"><?php _e( 'Posts subscription', $this->get_textdomain() ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="post_subscription_checkbox" name="post_subscription" value="1"
					<?php checked( '1', $inform_about_posts ); ?> />
					<span class="description"><?php _e( 'Inform about new posts via e-mail, without your own posts.', $this->get_textdomain() ); ?></span>
				</td>
			</tr>
			<tr id="comment_subscription">
				<th>
					<label for="comment_subscription_checkbox"><?php _e( 'Comments subscription', $this->get_textdomain() ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="comment_subscription_checkbox" name="comment_subscription" value="1"
					<?php checked( '1', $inform_about_comments ); ?> />
					<span class="description"><?php _e( 'Inform about new comments via e-mail, without your own comments.', $this->get_textdomain() ); ?></span>
				</td>
			</tr>
		</table>
	<?php }

	/**
	 * Save meta data from custom profile fields only if the user changed something
	 *
	 * @access public
	 * @since  0.0.2
	 * @uses   current_user_can, update_user_meta
	 * @param  string $user_id
	 * @return void
	 */
	public function save_custom_profile_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) )
			return FALSE;

		$default_opt_in        = apply_filters( 'iac_default_opt_in', FALSE );
		$inform_about_posts    = get_user_meta( $user_id, 'post_subscription', TRUE );
		$inform_about_comments = get_user_meta( $user_id, 'comment_subscription', TRUE );

		if ( $default_opt_in ) {
			if ( ! isset( $_POST['post_subscription'] ) && '' === $inform_about_posts ) {
				#nothing to do, user didn't changed the default behaviour
				unset( $inform_about_posts );
			} elseif ( ! isset( $_POST[ 'post_subscription' ] ) ) {
				$inform_about_posts = '0';
			} else {
				$inform_about_posts = '1';
			}

			if ( ! isset( $_POST['comment_subscription'] ) && '' === $inform_about_comments ) {
				unset( $inform_about_comments );
			} elseif ( ! isset( $_POST[ 'comment_subscription' ] ) ) {
				$inform_about_comments = '0';
			} else {
				$inform_about_comments = '1';
			}
		} else {
			if ( isset( $_POST['post_subscription'] ) && '' === $inform_about_posts ) {
				unset( $inform_about_posts );
			} elseif ( isset( $_POST[ 'post_subscription' ] ) ) {
				$inform_about_posts = '1';
			} else {
				$inform_about_posts = '0';
			}

			if ( isset( $_POST['comment_subscription'] ) && '' === $inform_about_comments ) {
				unset( $inform_about_comments );
			} elseif ( isset( $_POST[ 'comment_subscription' ] ) ) {
				$inform_about_comments = '1';
			} else {
				$inform_about_comments = '0';
			}
		}

		if ( isset( $inform_about_posts ) )
			update_user_meta( $user_id, 'post_subscription', $inform_about_posts );

		if ( isset( $inform_about_comments ) )
			update_user_meta( $user_id, 'comment_subscription', $inform_about_comments );
	}

}
?>
