<?php

// Using hooks is absolutely the smartest, most bulletproof way to implement things like plugins,
// custom design elements, and ads. You can add your hook calls below, and they should take the 
// following form:
// add_action('thesis_hook_name', 'function_name');
// The function you name above will run at the location of the specified hook. The example
// hook below demonstrates how you can insert Thesis' default recent posts widget above
// the content in Sidebar 1:
// add_action('thesis_hook_before_sidebar_1', 'thesis_widget_recent_posts');

// Delete this line, including the dashes to the left, and add your hooks in its place.

/**
 * function custom_bookmark_links() - outputs an HTML list of bookmarking links
 * NOTE: This only works when called from inside the WordPress loop!
 * SECOND NOTE: This is really just a sample function to show you how to use custom functions!
 *
 * @since 1.0
 * @global object $post
*/

function custom_bookmark_links() {
	global $post;
?>
<ul class="bookmark_links">
	<li><a rel="nofollow" href="http://delicious.com/save?url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>" onclick="window.open('http://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>', 'delicious', 'toolbar=no,width=550,height=550'); return false;" title="Bookmark this post on del.icio.us">Bookmark this article on Delicious</a></li>
</ul>
<?php
}

/* Full Width Top Bar */

function full_width_topbar() { ?>
	<div id="top_area" class="full_width">
		<div class="page"> 
			<a href="http://conflicthealth.com/about/" title="Read about Conflict Health">About</a> : <a href="http://christopheralbon.com/" title="Christopher Albon">The Author</a> : <a href="http://conflicthealth.com/contact/" title="Conflict Health contact information">Contact</a> : <a href="http://conflicthealth.com/terms-of-use/" title="Conflict Health Terms Of Use">Terms Of Use</a>
		</div>
	</div>
<?php }
add_action('thesis_hook_before_content_area', 'full_width_topbar');

/* Full Width Nav */

function full_width_nav() { ?>
	<div id="nav_area" class="full_width">
		<div class="page">
			<?php thesis_nav_menu(); ?> 
		</div>
	</div>
<?php }
remove_action('thesis_hook_before_header', 'thesis_nav_menu');
add_action('thesis_hook_before_content_area', 'full_width_nav');

/* Adding Tag Cloud To Topics Page */
function my_tag_cloud() {
if (is_page('750')) wp_tag_cloud('smallest=10&largest=35&number=0');
}
add_action('thesis_hook_after_post', 'my_tag_cloud');

/* End of Post Author Description */
function seth_godin_stuff () {
if (is_single()) {
?>
<p class="after_post"><?php the_author_meta('description'); ?><p/>
<?php
}
}
add_action('thesis_hook_after_post', 'seth_godin_stuff', '1');

/* End of Post Pitch */
function seth_godin_subscribe () {
if (is_single()) {
?>
<p class="after_post_subscribe">Want more? Subscribe to Conflict Health through <a href="http://feeds.feedburner.com/ConflictHealth">RSS</a> or <a href="http://feedburner.google.com/fb/a/mailverify?uri=ConflictHealth&amp;loc=en_US">email</a>.<p/>
<?php
}
}
add_action('thesis_hook_after_post', 'seth_godin_subscribe', '1');

/* Customize Footer */
function tpreg_footer() {
echo '<p>Copyright 2012.</p> <p>The content available on Conflict Health is solely the opinion of its respective author(s) and does not represent the opinions of their respective employer(s).</p>
<p>Design By <a href="http://diythemes.com/">DIYThemes</a></p>';
}

remove_action('thesis_hook_footer', 'thesis_attribution');
add_action('thesis_hook_footer', 'tpreg_footer', '1');


/* Remove Comment Link 

remove_action('thesis_hook_after_post', 'thesis_comments_link'); */

/* End of Post Pitch 
function alert_box () {
if (is_home()) {
?>
<p class="note">Due to moving, the holidays, and a grant due, there will be little or no new posts on Conflict Health until January 6th.<p/>
<?php
}
}
add_action('thesis_hook_before_headline', 'alert_box', '1');*/