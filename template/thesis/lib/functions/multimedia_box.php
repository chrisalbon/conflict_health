<?php

/**
 * function thesis_show_multimedia_box()
 *
 * Determine if Thesis should show the multimedia box.
 *
 * @since 1.3.2
 */
function thesis_show_multimedia_box() {
	global $thesis_design;

	if ($thesis_design['multimedia_box']['status'] || $_GET['template']) // Here, $_GET['template'] forces this to show on the theme preview
		return true;
	else {
		if (is_single() || is_page() || (get_option('show_on_front') == 'page' && is_home())) {
			if (get_option('show_on_front') == 'page' && is_home())
				$id = get_option('page_for_posts');
			else {
				global $post;
				$id = $post->ID;
			}
			
			$image = get_post_meta($id, 'thesis_image', true);
			$video = get_post_meta($id, 'thesis_video', true);
			$custom_code = get_post_meta($id, 'thesis_custom_code', true);
			$custom_hook = get_post_meta($id, 'thesis_custom_hook', true);

			$deprecated_image = get_post_meta($id, thesis_get_custom_field_key('image'), true);
			$deprecated_video = get_post_meta($id, thesis_get_custom_field_key('video'), true);
			$deprecated_custom = get_post_meta($id, thesis_get_custom_field_key('html'), true);
			
			if ($image || $video || $custom_code || $custom_hook || $deprecated_image || $deprecated_video || $deprecated_custom) {
				return true;
			}
			else {
				return false;
			}
		}
		else
			return false;
	}
}

/**
 * function thesis_multimedia_box()
 *
 * If thesis_show_multimedia_box() returned true, generate our multimedia
 * box and determine what exactly we're going to display within it.
 *
 * @since 1.0.2
 */
function thesis_multimedia_box() {
	global $thesis_design;
	
	if ($thesis_design['multimedia_box']['status'])
		$box_type = $thesis_design['multimedia_box']['status'];
	elseif ($_GET['template'])
		$box_type = 'image';

	$open_box = '			<div id="multimedia_box">' . "\n";
	$close_box = '			</div>' . "\n";

	if (is_single() || is_page() || (get_option('show_on_front') == 'page' && is_home())) {
		if (get_option('show_on_front') == 'page' && is_home())
			$id = get_option('page_for_posts');
		else {
			global $post;
			$id = $post->ID;
		}
		
		$image = get_post_meta($id, 'thesis_image', true);
		$video = get_post_meta($id, 'thesis_video', true);
		$custom_code = get_post_meta($id, 'thesis_custom_code', true);
		$custom_hook = get_post_meta($id, 'thesis_custom_hook', true);

		$deprecated_image = get_post_meta($id, thesis_get_custom_field_key('image'), true);
		$deprecated_video = get_post_meta($id, thesis_get_custom_field_key('video'), true);
		$deprecated_custom = get_post_meta($id, thesis_get_custom_field_key('html'), true);
		
		if ($box_type || $image || $video || $custom_code || $custom_hook || $deprecated_image || $deprecated_video || $deprecated_custom || $_GET['template']) {
			echo $open_box;

			if ($image)
				thesis_image_box($image);
			elseif ($video)
				thesis_video_box($video);
			elseif ($custom_code)
				thesis_custom_box($custom_code);
			elseif ($custom_hook)
				thesis_custom_box('php');
			elseif ($deprecated_image)
				thesis_image_box($deprecated_image);
			elseif ($deprecated_video)
				thesis_video_box($deprecated_video);
			elseif ($deprecated_custom)
				thesis_custom_box($deprecated_custom);
			elseif ($box_type)
				thesis_default_box($box_type);
			elseif ($_GET['template'])
				thesis_image_box();
				
			echo $close_box;
			
			thesis_hook_after_multimedia_box();
		}
	}
	elseif ($box_type) {
		echo $open_box;
		thesis_default_box($box_type);
		echo $close_box;

		thesis_hook_after_multimedia_box();
	}
}

/**
 * function thesis_default_box()
 *
 * If the user isn't overriding the multimedia box on a per-post basis,
 * we fallback to whatever we have set in the Thesis options.
 *
 * @since 1.0.2
 */
function thesis_default_box($box_type = 'image') {
	if ($box_type == 'image')
		thesis_image_box();
	elseif ($box_type == 'video')
		thesis_video_box();
	elseif ($box_type == 'custom')
		thesis_custom_box();
}

/**
 * function thesis_image_box()
 *
 * Markup and display an image box if the user has chosen either a static image
 * or the image rotator for the multimedia box.
 *
 * @since 1.0.2
 */ 
function thesis_image_box($image_url = false) {
?>
				<div id="image_box">
<?php
	if ($image_url) {
		global $thesis;

		// If the server cannot handle files specified via URL,
		// break our URL into a (hopefully) local path.
		if (!$thesis['image']['fopen']) {
			$image_path = explode($_SERVER['SERVER_NAME'], $image_url);
			$image_path = $_SERVER['DOCUMENT_ROOT'] . $image_path[1];
		}
		else
			$image_path = $image_url;

		if (getimagesize($image_path)) {
			$image_info = getimagesize($image_path);
			$image['src'] = $image_url;
			$image['width'] = $image_info[0];
			$image['height'] = $image_info[1];
			
			if (thesis_get_image_size_class($image['width'], $image['height']))
				$image['class'] = 'class="' . thesis_get_image_size_class($image['width'], $image['height']) . '" ';
			
			echo '<img ' . $image['class'] . 'src="' . $image['src'] . '" alt="Custom image" />' . "\n";
		}
	}
	else
		thesis_image_rotator();
?>
				</div>
<?php
}

/**
 * function thesis_image_rotator()
 *
 * Choose, markup, then output a random image from our rotator directory
 *
 * @since 1.0.2
 */
function thesis_image_rotator() {
	global $thesis_design;
	$rotator_dir = opendir(THESIS_ROTATOR);

	while (($file = readdir($rotator_dir)) !== false) {
		$haystack = strtolower($file);
		if (strpos($haystack, '.jpg') || strpos($haystack, '.jpeg') || strpos($haystack, '.png') || strpos($haystack, '.gif'))  {
			$images[$file]['url'] = THESIS_ROTATOR_FOLDER . '/' . $file;
			$image_path = THESIS_ROTATOR . '/' . $file;
			$image_size = getimagesize($image_path);
			$images[$file]['width'] = $image_size[0];
			$images[$file]['height'] = $image_size[1];
			
			if (thesis_get_image_size_class($image_size[0], $image_size[1]))
				$images[$file]['class'] = 'class="' . thesis_get_image_size_class($image_size[0], $image_size[1]) . '" ';
		}
	}
	
	if ($images)
		$random_image = array_rand($images);

	$images[$random_image]['alt'] = ($thesis_design['multimedia_box']['alt_tags'][$random_image]) ? stripslashes($thesis_design['multimedia_box']['alt_tags'][$random_image]) : $random_image;
	$img = '<img ' . $images[$random_image]['class'] . 'src="' . $images[$random_image]['url'] . '" alt="' . $images[$random_image]['alt'] . '" />';

	if ($thesis_design['multimedia_box']['link_urls'][$random_image] != '') {
		$link_before = '<a href="' . $thesis_design['multimedia_box']['link_urls'][$random_image] . '">';
		$link_after = '</a>' . "\n";
	}
	else
		$img .= "\n";
	
	echo $link_before . $img . $link_after;
}

/**
 * function thesis_video_box()
 *
 * Output the containers and video box if the user has selected
 * to display video in the multimedia box.
 *
 * @since 1.0.2
 */
function thesis_video_box($video_code = false) {
	global $thesis_design;

	if ($video_code || $thesis_design['multimedia_box']['video']) {
?>
				<div id="video_box">
<?php
		if ($video_code)
			echo '<p>' . stripslashes($video_code) . '</p>' . "\n";
		elseif ($thesis_design['multimedia_box']['video'])
			echo '<p>' . stripslashes($thesis_design['multimedia_box']['video']) . '</p>' . "\n";

?>
				</div>
<?php
	}
}

/**
 * function thesis_custom_box()
 *
 * Output custom HTML for the multimedia box or execute the
 * actions tied to thesis_hook_multimedia_box, if any.
 *
 * @since 1.0.2
 */
function thesis_custom_box($custom_code = false) {
	global $thesis_design;
	echo '				<div id="custom_box">' . "\n";
	
	if ($custom_code || $thesis_design['multimedia_box']['code']) {
		if ($custom_code == 'php')
			thesis_hook_multimedia_box();
		elseif ($custom_code)
			echo stripslashes($custom_code) . "\n";
		else
			echo stripslashes($thesis_design['multimedia_box']['code']) . "\n";
	}
	else
		thesis_hook_multimedia_box();

	echo '				</div>' . "\n";
}

/**
 * function thesis_get_image_size_class()
 *
 * Given the height and width of an image, apply an appropriate class
 * for CSS-based resizing of the image to fit into the available space.
 *
 * @since 1.0.2
 */
function thesis_get_image_size_class($width, $height) {
	if ($width && $height) {
		$ratio = $width / $height;
		if (1.3125 < $ratio && $ratio < 1.3548)
			return 'four_by_three';
		elseif (0.7412 < $ratio && $ratio < 0.759)
			return 'three_by_four';
		elseif (1.4737 < $ratio && $ratio < 1.5273)
			return 'three_by_two';
		elseif (0.6587 < $ratio && $ratio < 0.6746)
			return 'two_by_three';
		elseif (1.2353 < $ratio && $ratio < 1.2651)
			return 'five_by_four';
		elseif (0.7925 < $ratio && $ratio < 0.8077)
			return 'four_by_five';
		elseif (1.7406 < $ratio && $ratio < 1.8166)
			return 'sixteen_by_nine';
		elseif (0.5558 < $ratio && $ratio < 0.5693)
			return 'nine_by_sixteen';
		elseif (1.981 < $ratio && $ratio < 2.019)
			return 'two_by_one';
		elseif (0.498 < $ratio && $ratio < 0.502)
			return 'one_by_two';
		elseif (0.995 < $ratio && $ratio < 1.005)
			return 'square';
	}
}