<?php
/**
 * Adds Thesis controls and embellishments to the WordPress administration panel.
 *
 * @package Thesis-Admin
 */

function thesis_admin_setup() {
	add_action('admin_menu', 'thesis_add_options_pages');
	add_action('admin_post_thesis_options', 'thesis_save_options');
	add_action('admin_post_thesis_design_options', 'thesis_save_design_options');
	add_action('admin_post_thesis_upgrade', 'thesis_upgrade');
	add_action('admin_menu', 'thesis_add_meta_boxes');
	add_action('init', 'thesis_options_head');
}

function thesis_add_options_pages() {
	add_theme_page(__('Thesis Options', 'thesis'), __('Thesis Options', 'thesis'), 'edit_themes', 'thesis-options', 'thesis_options_admin');
	add_theme_page(__('Design Options', 'thesis'), __('Design Options', 'thesis'), 'edit_themes', 'thesis-design-options', 'thesis_design_options_admin');
}

function thesis_options_admin() {
	include (THESIS_ADMIN . '/thesis_options.php');
}

function thesis_design_options_admin() {
	include (THESIS_ADMIN . '/design_options.php');
}

function thesis_add_meta_boxes() {
	$meta_boxes = thesis_meta_boxes();
	
	foreach ($meta_boxes as $meta_box) {
		add_meta_box($meta_box['id'], $meta_box['title'], $meta_box['function'], 'post', 'normal', 'high');
		add_meta_box($meta_box['id'], $meta_box['title'], $meta_box['function'], 'page', 'normal', 'high');
	}
	
	add_action('save_post', 'thesis_save_meta');
}

function thesis_options_head() {
	wp_enqueue_style('thesis-options-stylesheet', THESIS_CSS_FOLDER . '/options.css');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('thesis-admin-js', THESIS_SCRIPTS_FOLDER . '/thesis.js');
}

/*---:[ random admin file functions that will probably have a new home at some point as the theme grows ]:---*/

function thesis_is_css_writable() {
	if (!is_writable(THESIS_LAYOUT_CSS)) {
		echo '<div class="warning">' . "\n";
		echo '	<p><strong>' . __('Attention!', 'thesis') . '</strong> ' . __('Your <code>thesis/layout.css</code> file is not writable by the server, and in order to work the full extent of its magic, Thesis needs to be able to write to this file. All you have to do is set your <code>layout.css</code> file permissions to 666, and you\'ll be good to go. After setting your file permissions, you should head to the <a href="' . get_bloginfo('wpurl') . '/wp-admin/themes.php?page=thesis-design-options">Design Options</a> page and hit the save button.', 'thesis') . '</p>' . "\n";
		echo '</div>' . "\n";
	}
}

function thesis_massage_code($code) {
 	echo htmlentities(stripslashes($code), ENT_COMPAT);
}

function thesis_save_button_text($display = false) {
	global $thesis;
	$save_button_text = ($thesis['save_button_text']) ? strip_tags(stripslashes($thesis['save_button_text'])) : __('Big Ass Save Button', 'thesis');
	
	if ($display)
		echo $save_button_text;
	else
		return $save_button_text;
}