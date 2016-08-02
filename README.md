# Informer

_– A licky boom, boom down_

[![Video Informer on YouTube](https://i.ytimg.com/vi/kqCI6QGVHIk/mqdefault.jpg)](https://www.youtube.com/watch?v=kqCI6QGVHIk)

---

Informs all users of a blog about a new post and approved comments via email

## Description
Plugin which sends emails to us from WordPress, for comments and new posts, except our own comments and posts. You can disable the option in your profile. At default, all user are receiving an email except the author.

### Made by [Inpsyde](http://inpsyde.com) · We love WordPress
Have a look at the premium plugins in our [market](http://marketpress.com).


## Installation
### Requirements 
* WordPress version 3.0 and later (tested at 3.5.1 up to 4.5.2)
* PHP 5.3 but we _strongly recommend_ to use at least PHP version 5.6! (Support for 5.5 and lower will likely be dropped in the next mayor release)

### Installation
1. Unpack the download-package
1. Upload the folder and all folder and files includes this to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Optional: Change global settings on 'Settings' → 'Reading'
1. That's all

Alternatively you can install the plugin [directly via composer](https://packagist.org/packages/inpsyde/informer):

```
$ composer require inpsyde/informer
```

## Screenshots
1. Settings on profile page
![Screenshot of Settings on profile page](https://raw.github.com/inpsyde/Inform-about-Content/master/assets/screenshot-1.png)

2. Settings on Settings → Reading
![Screenshot of Settings on Settings → Reading](https://raw.github.com/inpsyde/Inform-about-Content/master/assets/screenshot-2.png)

## API
### Plugin settings
By default, the plugin sends a mail to all registered users of a blog on new posts or comments, except a user disables the functionality for itself (opt-out). As of version 0.0.5 you can change this behaviour to opt-in with the filter `iac_default_opt_in` :
```php
add_filter( 'iac_default_opt_in', '__return_true' );
```
Make sure, this code runs on the action ```plugins_loaded``` with a priority lower than 10 or earlier.

As of version 0.0.5 the plugin provides a settings section (Settings→Reading). The one new option allows you to send all emails with the Bcc-header to hide users email-addresses to all other recipients. This option is disabled by default. You have access to the default settings via the filter `iac_default_options`. An array is passed to this function with the key `send_by_bcc`. Change the value to '1' and return the array on your callback function.

### User settings handling
To change the users settings (inform about posts, inform about comments) use the action `iac_save_user_settings` like this:
```php
do_action(
	'iac_save_user_settings',
	$user_id,
	$inform_about_posts, # '1', '0' or NULL if the user didn't changed anything
	$inform_about_comments # '1', '0' or NULL if the user didn't changed anything
);
```
Getting the current user settings is also easy:
```php
$user_settings = apply_filters( 'iac_get_user_settings', array(), $user_id );
```

## Other Notes
### Bugs, technical hints or contribute
Please give us feedback, contribute and file technical bugs on [GitHub Repo](https://github.com/inpsyde/Inform-about-Content).

### Authors, Contributors
[Contributors Stats](https://github.com/inpsyde/Inform-about-Content/graphs/contributors)

### License
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog.

## Changelog
[CHANGELOG.md](https://github.com/inpsyde/Inform-about-Content/blob/master/CHANGELOG.md)
