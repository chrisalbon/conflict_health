<?php
/*
Plugin Name: WP-Archives
Plugin URI: http://blog.unijimpe.net/
Description: Display your archives with year/month list.
Version: 0.8
Author: Jim Penaloza Calixto
Author URI: http://blog.unijimpe.net
*/
function wparc_getarchives() {
	global $month, $wpdb, $wp_version;
	$now = current_time('mysql');
	
	if (version_compare($wp_version, '2.1', '<')) {
		$current_posts = "post_date < '$now'";
	} else {
		$current_posts = "post_type='post'";
	}
    $arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month, count(ID) as posts  FROM " . $wpdb->posts . " WHERE post_status='publish' AND $current_posts AND post_password='' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");
    
    if ($arcresults) {
        foreach ($arcresults as $arcresult) {
            $url = get_month_link($arcresult->year, $arcresult->month);
            $text = sprintf('%s %d', $month[zeroise($arcresult->month,2)], $arcresult->year);
			echo "<p>\n";
            echo "<strong>" . $text . "</strong>\n";
           
            $thismonth = zeroise($arcresult->month,2);
            $thisyear = $arcresult->year;        

            $arcresults2 = $wpdb->get_results("SELECT ID, post_date, post_title, comment_status FROM " . $wpdb-> posts . " WHERE post_date LIKE '$thisyear-$thismonth-%' AND $current_posts AND post_status='publish' AND post_password='' ORDER BY post_date DESC");

            if ($arcresults2) {
            	echo "<ul>\n";
                foreach ($arcresults2 as $arcresult2) {
					if ($arcresult2->post_date != '0000-00-00 00:00:00') {
                         $url = get_permalink($arcresult2->ID);
                         $arc_title = $arcresult2->post_title;

                         if ($arc_title) {
						 	$text = strip_tags($arc_title);
                         } else {
						 	$text = $arcresult2->ID;
						 }
                         $title_text = wp_specialchars($text, 1);
                         echo "<li>" . mysql2date('d', $arcresult2->post_date). ": <a href=\"" . $url . "\" title=\"" . $title_text . "\">" . wptexturize($text) . "</a></li>\n";
                     }
                }
                echo "</ul>\n";
            }
			echo "</p>\n";
        }
    }
}

function wparc_setarchive() {
	add_filter('the_content', 'wparc_findarchives');
}
function wparc_findarchives($post) {
	if (substr_count($post, '<!--wp_archives-->') > 0) {
		$archives = wparc_getarchives();
		$post = str_replace('<!--wp_archives-->', $archives, $post);
	}
	return $post;
}
function wparc_addheader() {
	echo "<!-- WP-Archives 0.8 by unijimpe -->\n";
}
add_action('init', 'wparc_setarchive');
add_action('wp_head', 'wparc_addheader');
?>