<?php 
include( dirname(__FILE__) . '/../core/core-functions.php' );

register_post_type(
	'ask-jess',
	array(
		'labels' => array( 
			'name' => 'Ask Jess', 
			'add_new' => 'Add New (Blank)', 
			'add_new_item' => 'Add New Answer from Scratch', 
			'edit_item' => 'Answer an Ask Jess Inquiry', 
			'view_item' => 'View Answer' ),
		'description' => 'Answers to member questions!',
		'public' => true,
		'menu_icon'	=> (get_bloginfo('template_directory')).'/images/admin_ask.png',
		'capability_type' => 'page',
		'supports' => array('title','editor','thumbnail'),
		'menu_position' => 8
	)
);

register_post_type('wtf',array(
	'labels' => array( 'name' => 'WTF?!', 'add_new_item' => 'Add WTF?! Moment', 'edit_item' => 'Edit WTF?! Moment', 'view_item' => 'View WTF?! Moment' ),
	'description' => 'Women sharing their "what the fuck? did that seriously just hapen?!" moments.',
	'public' => true,
	'menu_icon'	=> (get_bloginfo('template_directory')).'/images/admin_wtf.png',
	'capability_type' => 'page',
	'supports' => array('title','editor','comments','author'),
	'menu_position' => 7
));

register_taxonomy('wtf_message_type',array('wtf'),array(
	'label' => 'Message Types',
	'hierarchical' => true,
	'rewrite' => true,
	'show_tagcloud' => false
));


register_post_type('date',array(
	'labels' => array( 'name' => 'Date or Not?', 'add_new_item' => 'Add New Date or Non-Date Story', 'edit_item' => 'Edit a Date or Non-Date Story', 'view_item' => 'View Date or Non-Date Story' ),
	'description' => 'Registered users share scenarios, and other members vote on whether it was a date, non-date, or not-a-date.',
	'public' => true,
	'rewrite' => array( 'slug' => 'date-or-non-date' ),
	'menu_icon'	=> (get_bloginfo('template_directory')).'/images/admin_date.png',
	'capability_type' => 'page',
	'supports' => array('title','editor','comments','author'),
	'menu_position' => 9
));


register_post_type('glossary',array(
	'labels' => array( 'name' => 'Glossary', 'add_new' => 'Add Definition', 'add_new_item' => 'Add New Glossary Definition', 'edit_item' => 'Edit Glossary Definition', 'view_item' => 'View Definition' ),
	'description' => 'The lingo of WTF.',
	'public' => true,
	'menu_icon'	=> (get_bloginfo('template_directory')).'/images/admin_glossary.png',
	'capability_type' => 'page',
	'supports' => array('title','editor'),
	'menu_position' => 20
));

register_post_type('guest-blogs',array(
		'label' => 'Guest Posts',
		'singular_label' => 'Guest Blog Post',
		'description' => 'Guest blog posts from the members.',
		'public' => true,
		'capability_type' => 'post',
		// 'capabilities' => array( 'edit_posts' => 'guest_blog' ),
		'supports' => array('title','editor','comments','revisions','author','excerpt','thumbnail'),
		'menu_position' => 6,
		'taxonomies' => array('category')
	));

/** Ardee Aram 2011 October 28 1902 Software. Add categories for the Gaggles **/

register_taxonomy('glossary-category', 'glossary' ,array(
	'label' => 'Glossary Category',
	'labels' => array(
		'singular_name' => 'Glossary Category',
		'add_new_item' => 'Add New Glossary Category',
		'search_items' => 'Search Glossary Category',
		'all_items' => 'All Glossary Category',
		'parent_item_colon' => 'Parent Glossary Category',
		'parent_item_colon' => 'Parent Glossary Category:',
		'edit_item' => 'Edit Glossary Category',
		'update_item' => 'Update Glossary Category'
		),
	'hierarchical' => true
));

//additional post types
register_post_type('columns',array(
	'labels' => array( 'name' => 'Columns', 'add_new' => 'Add a Column', 'add_new_item' => 'Add New Column', 'edit_item' => 'Edit Column', 'view_item' => 'View Column' ),
	'description' => 'The WTF Columns.',
	'public' => true,
	'capability_type' => 'post',
	'supports' => array('title','editor','comments','revisions','author','excerpt','thumbnail'),
	'menu_position' => 5,
	'taxonomies' => array('column_category', 'column_tags')
));

register_taxonomy('column_category',array('columns'),array(
	'label' => 'Column Categories',
	'hierarchical' => true,
	'rewrite' => true,
	'show_tagcloud' => false
));
register_taxonomy('column_tags',array('columns'),array(
	'label' => 'Column Tags',
	'hierarchical' => false,
	'rewrite' => true,
	'show_tagcloud' => true
));

//watch-and-listen refactored to plug-in.

register_taxonomy('wtf_media_categories',array('watch-and-listen'),array(
	'label' => 'Media Categories',
	'hierarchical' => true,
	'rewrite' => true,
	'show_tagcloud' => false
));
register_taxonomy('wtf_media_tags',array('watch-and-listen'),array(
	'label' => 'Media Tags',
	'hierarchical' => false,
	'rewrite' => true,
	'show_tagcloud' => false
));


//---------------- Custom Exclude Cats Function ----------------//

function wptouch_exclude_category( $query ) {
	$excluded = wptouch_excluded_cats();
	
	if ( $excluded ) {
		$cats = explode( ',', $excluded );
		$new_cats = array();
		
		foreach( $cats as $cat ) {
			$new_cats[] = trim( $cat );
		}
	
		$query->set( 'category__not_in', $new_cats );
	}
	
	return $query;
}

add_filter('pre_get_posts', 'wptouch_exclude_category');

//---------------- Custom Exclude Tags Function ----------------//

function wptouch_exclude_tags( $query ) {
	$excluded = wptouch_excluded_tags();
	
	if ( $excluded ) {
		$tags = explode( ',', $excluded );
		$new_tags = array();
		
		foreach( $tags as $tag ) {
			$new_tags[] = trim( $tag );
		}
	
		$query->set( 'tag__not_in', $new_tags );
	}
	
	return $query;
}

add_filter('pre_get_posts', 'wptouch_exclude_tags');

//---------------- Custom Excerpts Function ----------------//
function wptouch_trim_excerpt($text) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');
		$text = strip_shortcodes( $text );
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 30);
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words) > $excerpt_length) {
			array_pop($words);
			array_push($words, '...');
			$text = implode(' ', $words);
			$text = force_balance_tags( $text );
		}
	}
	return apply_filters('wptouch_trim_excerpt', $text, $raw_excerpt);
}


//---------------- Custom Time Since Function ----------------//

function wptouch_time_since($older_date, $newer_date = false)
	{
	// array of time period chunks
	$chunks = array(
//	array(60 * 60 * 24 * 365 , 'yr'),
	array(60 * 60 * 24 * 30, __('mo', 'wptouch') ),
	array(60 * 60 * 24 * 7, __('wk', 'wptouch') ),
	array(60 * 60 * 24, __('day', 'wptouch') ),
	array(60 * 60, __('hr', 'wptouch') ),
	array(60 , __('min', 'wptouch'), )
	);
	
	$newer_date = ($newer_date == false) ? (time()+(60*60*get_settings("gmt_offset"))) : $newer_date;
	
	// difference in seconds
	$since = $newer_date - $older_date;
	
	for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0)
			{
			break;
			}
		}

	// set output var
	$output = ($count == 1) ? '1 '.$name : "$count {$name}s";

	// step two: the second chunk
	if ($i + 1 < $j)
		{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
			{
			// add to output var
			$output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
	
	return $output;
	}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wptouch_trim_excerpt');