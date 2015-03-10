<?php

function thesis_nav_menu() {
	global $thesis;
	$home_text = thesis_home_link_text();
	$home_nofollow = ($thesis['nav']['home']['nofollow']) ? ' rel="nofollow"' : '';

	echo '<ul id="tabs">' . "\n";
	
	if ($thesis['nav']['home']['show'] || $_GET['template']) {
		if (is_front_page()) {
			$current_page = get_query_var('paged');
			$home_class = ($current_page <= 1) ? 'home-item current_page_item' : 'home-item';
		}
		else
			$home_class = (is_home() && is_front_page()) ? 'home-item current_page_item' : 'home-item';

		echo '<li class="' . $home_class . '"><a href="' . get_bloginfo('url') . '" title="' . $home_text . '"' . $home_nofollow . '>' . $home_text . '</a></li>' . "\n";
	}
	
	$item_num = 1;

	if ($thesis['nav']['pages']) {
		if (!$thesis['nav']['style']) {
			global $wp_query;
			foreach ($thesis['nav']['pages'] as $id => $nav_page) {
				if ($nav_page['show']) {
					$page = get_page($id);
					$page_item = 'page-item-' . $item_num;
					$class = (is_page($id) || ($wp_query->is_posts_page && get_option('show_on_front') == 'page' && get_option('page_for_posts') == $id) || (get_option('show_on_front') == 'page' && get_option('page_on_front') == $id)) ? $page_item . ' current_page_item' : $page_item;
					$link_text = ($thesis['nav']['pages'][$id]['text'] != '') ? $thesis['nav']['pages'][$id]['text'] : $page->post_title;
					echo '<li class="' . $class . '"><a href="' . get_page_link($id) . '" title="' . $page->post_title . '">' . $link_text . '</a></li>' . "\n";
					$item_num++;
				}
			}
		}
		else {
			foreach ($thesis['nav']['pages'] as $id => $nav_page) {
				if ($nav_page['show'])
					$nav_array[] = $id;
			}

			$nav_menu_pages = implode(',', $nav_array);
			wp_list_pages('title_li=&include=' . $nav_menu_pages);
		}
	}
	
	if ($thesis['nav']['categories'])
		wp_list_categories('title_li=&include=' . $thesis['nav']['categories']);
	
	if ($thesis['nav']['links']) {
		$nav_links = get_bookmarks('category=' . $thesis['nav']['links']);

		foreach ($nav_links as $nav_link) {
			if ($nav_link->link_description)
				$title = ' title="' . $nav_link->link_description . '"';
			if ($nav_link->link_rel)
				$rel = ' rel="' . $nav_link->link_rel . '"';
			if ($nav_link->link_target)
				$target = ' target="' . $nav_link->link_target . '"';
			
			echo '<li><a href="' . $nav_link->link_url . '"' . $title . $rel . $target . '>' . $nav_link->link_name . '</a></li>' . "\n";
		}
	}
	
	if ($thesis['nav']['feed']['show'] || $_GET['template']) {
		$feed_title = get_bloginfo('name') . ' RSS Feed';
		$feed_nofollow = ($thesis['nav']['feed']['nofollow']) ? ' rel="nofollow"' : '';

		echo '<li class="rss"><a href="' . thesis_feed_url() . '" title="' . $feed_title . '"' . $feed_nofollow . '>' . thesis_feed_link_text() . '</a></li>' . "\n";
	}
		
	echo '</ul>' . "\n";
}

function thesis_home_link_text() {
	global $thesis;
	$link_text = ($thesis['nav']['home']['text']) ? $thesis['nav']['home']['text'] : __('Home', 'thesis');
	return $link_text;
}

function thesis_feed_link_text() {
	global $thesis;
	$link_text = ($thesis['nav']['feed']['text']) ? $thesis['nav']['feed']['text'] : __('Subscribe', 'thesis');
	return $link_text;
}