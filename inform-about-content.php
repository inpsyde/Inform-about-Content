<?php
/**
 * Plugin Name: Informer
 * Plugin URI:  http://wordpress.org/extend/plugins/inform-about-content/
 * Text Domain: inform_about_content
 * Domain Path: /languages
 * Description: Informs all users of a blog about a new post and approved comments via email
 * Author:      Inpsyde GmbH
 * Version:     1.0.0-RC2
 * License:     GPLv3
 * Author URI:  http://inpsyde.com/
 */


if ( ! class_exists( 'Inform_About_Content' ) ) {
	require_once __DIR__ . '/inc/class-Inform_About_Content.php';
}

// set the default behaviour
add_filter( 'iac_default_opt_in', array( 'Inform_About_Content', 'default_opt_in' ) );

// some default filters
add_filter( 'iac_post_message', 'strip_tags' );
add_filter( 'iac_comment_message', 'strip_tags' );

add_filter( 'iac_post_message', array( 'Inform_About_Content', 'sender_to_message' ), 10, 3 );
add_filter( 'iac_comment_message', array( 'Inform_About_Content', 'sender_to_message' ), 10, 3 );

// since 0.0.6
add_filter( 'iac_post_message', 'strip_shortcodes' );
add_filter( 'iac_comment_message', 'strip_shortcodes' );

// load the plugin
add_action( 'plugins_loaded', array( 'Inform_About_Content', 'get_object' ) );
