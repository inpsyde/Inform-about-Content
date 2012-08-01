<?php
/**
 * Plugin Name: Inform about Content
 * Plugin URI:  http://wordpress.org/extend/plugins/inform-about-content/
 * Text Domain: inform_about_content
 * Domain Path: /languages
 * Description: Informs all users of a blog about a new post and approved comments via email
 * Author:      Inpsyde GmbH
 * Version:     0.0.4
 * Licence:     GPLv3
 * Author URI:  http://inpsyde.com/
 */

/**
 * Informs all users of a blog about a new post and approved comments via email
 *
 * @author   fb
 * @since    0.0.1
 * @version  07/19/2012
 */
if ( ! class_exists( 'Inform_About_Content' ) ) {
	// add plugin to WP
	if ( function_exists( 'add_action' ) ) {
		add_action( 'plugins_loaded' , array( 'Inform_About_Content', 'get_object' ) );
	}

	class Inform_About_Content {

		/**
		 * set the default behaviour
		 * TRUE    all users have to opt-out of the notification
		 * FALSE   all users have to opt-in to the notification
		 *
		 * @var bool
		 */
		static protected $default_opt_in = FALSE;

		static private $classobj         = NULL;

		// hard values, alternative use the settings on user profile
		// bool for active mail for a new post
		public $inform_about_posts       = TRUE;

		// bool for active mail for a new comment
		public $inform_about_comments    = TRUE;

		// strings for mail
		public $mail_string_new_comment_to;
		public $mail_string_to;
		public $mail_string_by;
		public $mail_string_url;

		/**
		 * Handler for the action 'init'. Instantiates this class.
		 *
		 * @access public
		 * @since 0.0.1
		 * @return $classobj
		 */
		public function get_object() {

			if ( NULL === self :: $classobj ) {
				self :: $classobj = new self;
			}

			return self :: $classobj;
		}

		/**
		 * Constructor, init on defined hooks of WP and include second class
		 *
		 * @access  public
		 * @since   0.0.1
		 * @uses    add_action
		 * @return  void
		 */
		public function __construct() {

			# change the default behaviour from outside
			self::$default_opt_in = apply_filters( 'iac_default_opt_in', FALSE );

			// set srings for mail
			$this -> mail_string_new_comment_to	= __( 'new comment to', $this -> get_textdomain() );
			$this -> mail_string_to				= __( 'to:', $this -> get_textdomain() );
			$this -> mail_string_by				= __( 'by', $this -> get_textdomain() );
			$this -> mail_string_url			= __( 'URL', $this -> get_textdomain() );

			// include settings on profile
			require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc/class.profile-settings.php';
			$Iac_Profile_Settings = Iac_Profile_Settings :: get_object();

			add_action( 'admin_init', array( $this, 'localize_plugin' ) );

			if ( $this -> inform_about_posts )
				add_action( 'publish_post', array( $this, 'inform_about_posts' ) );
			if ( $this -> inform_about_comments )
				add_action( 'comment_post', array( $this, 'inform_about_comment' ) );
		}

		/**
		 * Return Textdomain string
		 *
		 * @access  public
		 * @since   0.0.2
		 * @return  string
		 */
		public function get_textdomain() {

			return 'inform_about_content';
		}

		/**
		 * localize_plugin function.
		 *
		 * @uses   load_plugin_textdomain, plugin_basename
		 * @access public
		 * @since  0.0.2
		 * @return void
		 */
		public function localize_plugin() {

			load_plugin_textdomain(
				$this -> get_textdomain(),
				FALSE,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		/**
		 * Get user-mails from all users of a blog
		 *
		 * @access  public
		 * @since  0.0.1
		 * @used   get_users
		 * @param  string $current_user email of user
		 * @param  string $key key of metavalues to user
		 * @return array string $users
		 */
		public function get_members( $current_user = NULL, $key = NULL ) {

			global $blog_id;

			$users = FALSE;
			if ( isset( $blog_id ) && ! empty( $blog_id ) ) {
				$blogusers = get_users( array( 'blog_id' => $blog_id ) );
				$users = array();
				foreach ( $blogusers as $user_object ) {
					$subscribe = get_the_author_meta( $key , $user_object -> ID );

					$not_current_user = TRUE;
					$subscriber = TRUE;
					// filter author mail
					if ( $current_user === $user_object -> user_email )
						$not_current_user = FALSE;
					// filter profile options
					if ( '0' === $subscribe )
						$subscriber = FALSE;
					if ( $not_current_user && $subscriber )
						$users[] .= $user_object -> user_email;
				}
				$users = implode( ', ', $users );
			}

			return $users;
		}

		/**
		 * Send mail, if publish a new post
		 *
		 * @access  public
		 * @sinde   0.0.1
		 * @used    get_post, get_userdata, get_author_name, get_option, wp_mail, get_permalink
		 * @param   string $post_id
		 * @return  string $post_id
		 */
		public function inform_about_posts( $post_id = FALSE ) {

			if ( $post_id ) {
				// get data from current post
				$post_data = get_post( $post_id );
				// get mail from author
				$user_mail = get_userdata( $post_data -> post_author );

				// email addresses
				$to = $this->get_members( $user_mail -> user_email, 'post_subscription' );
				// email subject
				$subject = get_option( 'blogname' ) . ': ' . $post_data -> post_title;
				// message content
				$message = $post_data -> post_content . ' ' . PHP_EOL .
					$this -> mail_string_by . ' ' .
					get_author_name( $post_data -> post_author ) . ' ' . PHP_EOL .
					$this -> mail_string_url . ': ' .
					get_permalink( $post_id );
				// create header data
				$headers = 'From: ' .
					get_author_name( $post_data -> post_author ) .
					' (' . get_option( 'blogname' ) . ')' .
					' <' . $user_mail -> user_email . '>' .
					PHP_EOL;
				// send mail
				wp_mail(
					$to,
					$subject,
					$message,
					$headers
				);
			}

			return $post_id;
		}

		/**
		 * Send mail, if approved a new comment
		 *
		 * @access  public
		 * @sinde   0.0.1
		 * @used    get_comment, get_post, get_userdata, get_author_name, get_option, wp_mail, get_permalink
		 * @param   string $comment_id
		 * @param   boolean $comment_status
		 * @return  string $comment_id
		 */
		public function inform_about_comment( $comment_id = FALSE, $comment_status = FALSE ) {

			if ( $comment_id ) {
				// get data from current comment
				$comment_data = get_comment( $comment_id );
				// if comment status is approved
				if ( '1' === $comment_data -> comment_approved || $comment_status ) {
					// get data from post to this comment
					$post_data = get_post( $comment_data -> comment_post_ID );
					// get mail from author
					$user_mail = get_userdata( $comment_data -> user_id );

					// email addresses
					$to = $this -> get_members( $user_mail->user_email, 'comment_subscription' );
					// email subject
					$subject = get_option( 'blogname' ) . ': ' . $post_data -> post_title;
					// message content
					$message = $comment_data -> comment_content . ' ' . PHP_EOL .
						$this -> mail_string_by . ' ' .
						get_author_name( $comment_data -> user_id ) . ' ' .
						$this -> mail_string_to . ' ' .
						$post_data -> post_title . ' ' . PHP_EOL .
						$this -> mail_string_url . ': ' .
						get_permalink( $post_data -> ID );
					// create header data
					$headers = 'From: ' .
						get_author_name( $comment_data -> user_id ) .
						' (' . get_option( 'blogname' ) . ')' .
						' <' . $user_mail -> user_email . '>' .
						PHP_EOL;
					// send mail
					wp_mail(
						$to,
						$subject, // email subject
						$message, // message content
						$headers // headers
					);
				}
			}

			return $comment_id;
		}

	} // end class Inform_About_Content

} // end if class exists
?>
