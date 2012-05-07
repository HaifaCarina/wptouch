
<?php global $wplogger;  
    $displayed = false;
    $current_url = "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
        
?>
<?php global $is_ajax; $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']); if (!$is_ajax) get_header(); ?>
<?php $wptouch_settings = bnc_wptouch_get_settings(); ?>


<div class="content" id="content<?php echo md5($_SERVER['REQUEST_URI']); ?>">
	
    	
	<?php 
	
	$wplogger->log("index.php is called");
        $wplogger->log("current_url:".$current_url);
        $wplogger->log("watch_and_listen_url:".$watch_and_listen_url);
        
        
        if (is_search()){
             $wplogger->log("searching");
        } else {
            //$wplogger->log("not searching");
            query_posts(array(
				"post_type" => array("columns","guest-blogs","ask-jess") ,
                                'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
		)); 
        }
	
        if (strpos($current_url, "ask-jess")== true){    
            /**
               Note: If ASK JESS page, display only posts from columns post type
            **/	
                $wplogger->log("ask-jess is called");
                $result = query_posts(array(
				"post_type" => "ask-jess", 
                                'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
		));     
                
	} else if (strpos($current_url, "columns")== true){ 
            /**
               Note: If COLUMNS page, display only posts from columns post type
            **/	
                $wplogger->log("columns is called");
                $result = query_posts(array(
				"post_type" => "columns",
                                'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
		));     
		
	} else if (strpos($current_url, "guest-blogs")== true){
            /**
               Note: If GUEST BLOGS page, display only posts from columns post type
            **/	
                $wplogger->log("guest-blogs is called");
                $result = query_posts(array(
				"post_type" => "guest-blogs" ,
                                'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
		));     
		
                
                
                
	} else if (strpos($current_url, "glossary")== true){
            /**
                Note: If GLOSSARY page, display only posts from columns post type
            **/
            $wplogger->log("glossary is called");
            $result = query_posts(array(
                    "post_type" => "glossary",
                    "posts_per_page" => 11,
                    'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
                
            ));
            
            glossaryMenu();
            
            
        } else if (strpos($current_url, "watch-and-listen")== true){

            $wplogger->log("watch-and-listen1 is called");
            $default_place = "";
            $media_category_slug = $_GET['place'] ? $_GET['place'] : $default_place;
            
            $result = query_posts(array(
                    "post_type" => "watch-and-listen",
                    "posts_per_page" => 5,
                    'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
            ));
            watchAndListenMenu();
      
           
	} else if (strpos($current_url, "/category/")== true){    
            /**
               Note: If after-the-fashion page, display only posts from after-the-fashion post type
            **/	
            $wplogger->log("category/after-the-fashion/ is called");
            
            $cat_array = explode("/",$current_url);
            $term = get_term_by('slug', $cat_array[4], 'category');
            $result = query_posts('cat='.$term->term_id.'&post_type=columns' );
                
	} 
        
        /*else if (strcasecmp($current_url,"http://wtfisupwithmylovelife.com/")==0){
            query_posts(array(
				"post_type" => array("columns","guest-blogs","ask-jess") ,
                                'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
		));     
        } */    
        
        
        if (strstr($current_url,"watch-and-listen") && $_GET['place']) {
            /**
                    Watch and Listen with PLACE
            **/
            $media_category_slug = $_GET['place'];
            
            $result = query_posts(array(
                    "post_type" => "watch-and-listen",
                    "wtf_media_categories" => $media_category_slug,
                    "posts_per_page" => 5,
                    'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
            ));
            $wplogger->log($result);
            $wplogger->log($media_category_slug);
            
            watchAndListenMenu();
            $wplogger->log("watch-and-listen2 is called");
        }
        
        if (strstr($current_url,"glossary-category") ) {
            $wplogger->log("glossary2 is called");
            glossaryMenu();
        }
        
        
        
            
        
        ?>
            
            
	<div class="result-text"><?php wptouch_core_body_result_text(); ?></div>
        
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
 <div class="post" id="post-<?php the_ID(); ?>">
        
			<?php if ( is_home() && is_sticky() && $wptouch_settings['post-cal-thumb'] == 'nothing-shown' ) echo '<div class="sticky-icon-none"></div>'; ?>
			<?php if ( is_home() && is_sticky() && $wptouch_settings['post-cal-thumb'] != 'nothing-shown' ) echo '<div class="sticky-icon"></div>'; ?>

 		<?php if (!function_exists('dsq_comments_template') && !function_exists('id_comments_template')) { ?>
				<?php if (wptouch_get_comment_count() && !is_archive() && !is_search() && bnc_can_show_comments() ) { ?>
					<div class="<?php if ($wptouch_settings['post-cal-thumb'] == 'nothing-shown') { echo 'nothing-shown ';} ?>comment-bubble<?php if ( wptouch_get_comment_count() > 99 ) echo '-big'; ?>">
						<?php comments_number('0','1','%'); ?>
					</div>
				<?php } ?>
		<?php } ?>
 	
 	<?php if (is_archive() || is_search()) { ?>
     
		<div class="archive-top"> 
			<div class="archive-top-right">
				<?php if (bnc_excerpt_enabled()) { ?>
				<script type="text/javascript">
					$wpt(document).ready(function(){
						$wpt("a#arrow-<?php the_ID(); ?>").bind( touchStartOrClick, function(e) {
							$wpt(this).toggleClass("post-arrow-down");
							$wpt('#entry-<?php the_ID(); ?>').wptouchFadeToggle(500);
						});	
					 });					
				</script>
					<a class="post-arrow" id="arrow-<?php the_ID(); ?>" href="javascript: return false;"></a>
				<?php } ?>
			</div> 
		 <div id="arc-top" class="archive-top-left month-<?php echo get_the_time('m') ?>">
			<?php echo get_the_time('M') ?> <?php echo get_the_time('j') ?>, <?php echo get_the_time('Y') ?>
		 </div>
		</div>
 	<?php } else { ?>	
				<?php if (bnc_excerpt_enabled()) { ?>
				<script type="text/javascript">
					$wpt(document).ready(function(){
						$wpt("a#arrow-<?php the_ID(); ?>").bind( touchStartOrClick, function(e) {
							$wpt(this).toggleClass("post-arrow-down");
							$wpt('#entry-<?php the_ID(); ?>').wptouchFadeToggle(500);
						});	
					 });					
				</script>
					<a class="post-arrow" id="arrow-<?php the_ID(); ?>" href="javascript: return false;"></a>
				<?php } ?>
				
				
				<?php 
					$version = bnc_get_wp_version();
					if ($version >= 2.9 && $wptouch_settings['post-cal-thumb'] != 'calendar-icons' && $wptouch_settings['post-cal-thumb'] != 'nothing-shown') { ?>
					<div class="wptouch-post-thumb-wrap">
						<div class="thumb-top-left"></div><div class="thumb-top-right"></div>
					<div class="wptouch-post-thumb">
						<?php 
						if (function_exists('p75GetThumbnail')) { 
						if ( p75HasThumbnail($post->ID) ) { ?>
						
						<img src="<?php echo p75GetThumbnail($post->ID); ?>" alt="post thumbnail" />
						
						<?php } else { ?>
						<?php
								$total = '24'; $file_type = '.jpg'; 
							
								// Change to the location of the folder containing the images 
								$image_folder = '' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core-images/thumbs/'; 
								$start = '1'; $random = mt_rand($start, $total); $image_name = $random . $file_type; 
							
							if ($wptouch_settings['post-cal-thumb'] == 'post-thumbnails-random') {
									echo "<img src=\"$image_folder/$image_name\" alt=\"$image_name\" />";
									} else {
									echo '<img src="' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core-images/thumbs/thumb-empty.jpg" alt="thumbnail" />';
								}
							?>						
						<?php } ?>
						
						<?php } elseif (get_post_custom_values('Thumbnail') == true || get_post_custom_values('thumbnail') == true) { ?>
						
						<img src="<?php $custom_fields = get_post_custom($post_ID); $my_custom_field = $custom_fields['Thumbnail']; foreach ( $my_custom_field as $key => $value ) echo "$value"; ?>" alt="custom-thumbnail" />
						 
						<?php } elseif (function_exists('the_post_thumbnail') && !function_exists('p75GetThumbnail')) { ?>
							
							<?php if (has_post_thumbnail()) { ?>
								<?php the_post_thumbnail(); ?>
							
							<?php } else { ?>				
							
								<?php
								$total = '24'; $file_type = '.jpg'; 
							
								// Change to the location of the folder containing the images 
								$image_folder = '' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core-images/thumbs/'; 
								$start = '1'; $random = mt_rand($start, $total); $image_name = $random . $file_type; 
							
							if ($wptouch_settings['post-cal-thumb'] == 'post-thumbnails-random') {
									echo "<img src=\"$image_folder/$image_name\" alt=\"$image_name\" />";
									} else {
									echo '<img src="' . compat_get_plugin_url( 'wptouch' ) . '/themes/core/core-images/thumbs/thumb-empty.jpg" alt="thumbnail" />';
								}
							?>
						<?php } } ?>
					</div>
						<div class="thumb-bottom-left"></div><div class="thumb-bottom-right"></div>
					</div>
				<?php }  elseif ($wptouch_settings['post-cal-thumb'] == 'calendar-icons') { ?>
					<div class="calendar">
						<div class="cal-month month-<?php echo get_the_time('m') ?>"><?php echo get_the_time('M') ?></div>
						<div class="cal-date"><?php echo get_the_time('j') ?></div>
					</div>				
				<?php }  elseif ($wptouch_settings['post-cal-thumb'] == 'nothing-shown') { }  else { ?>
					<div class="calendar">
						<div class="cal-month month-<?php echo get_the_time('m') ?>"><?php echo get_the_time('M') ?></div>
						<div class="cal-date"><?php echo get_the_time('j') ?></div>
					</div>	
				<?php } ?>

	<?php } ?>
 
	<a class="h2" href="<?php the_permalink(); ?>"><?php  the_title();?></a>
        
        	<div class="post-author">
		<?php if ($wptouch_settings['post-cal-thumb'] != 'calendar-icons') { ?><span class="lead"><?php _e("Written on", "wptouch"); ?></span> <?php echo get_the_time('d.m.Y') ?> <?php _e("at", "wptouch"); ?> <?php echo get_the_time('G:i') ?><br /><?php if (!bnc_show_author()) { echo '<br />';} ?><?php } ?>
			
                        <?php if (bnc_show_categories()) { echo('<span class="lead">' . __( 'Categories', 'wptouch' ) . ':</span> '); the_category(', '); echo('<br />'); } ?> 
			<?php if (bnc_show_tags() && get_the_tags()) { the_tags('<span class="lead">' . __( 'Tags', 'wptouch' ) . ':</span> ', ', ', ''); } ?>
		</div>	
			<div class="clearer"></div>	
            <div id="entry-<?php the_ID(); ?>" <?php  if (bnc_excerpt_enabled()) { ?>style="display:none"<?php } ?> class="mainentry <?php echo $wptouch_settings['style-text-justify']; ?>">
 				<?php the_excerpt(); ?>
 		    <a class="read-more" href="<?php the_permalink() ?>"><?php _e( "Read This Post", "wptouch" ); ?></a>
	        </div>  
      </div>

    <?php endwhile; ?>	

<?php if (!function_exists('dsq_comments_template') && !function_exists('id_comments_template')) { ?>

	<div id="call<?php echo md5($_SERVER['REQUEST_URI']); ?>" class="ajax-load-more">
		<div id="spinner<?php echo md5($_SERVER['REQUEST_URI']); ?>" class="spin"	 style="display:none"></div>
		<a class="ajax" href="javascript:return false;" onclick="$wpt('#spinner<?php echo md5($_SERVER['REQUEST_URI']); ?>').fadeIn(200); $wpt('#ajaxentries<?php echo md5($_SERVER['REQUEST_URI']); ?>').load('<?php echo get_next_posts_page_link(); ?>', {}, function(){ $wpt('#call<?php echo md5($_SERVER['REQUEST_URI']); ?>').fadeOut();});">
			<?php _e( "Load more entries...", "wptouch" ); ?>
		</a>
	</div>
	<div id="ajaxentries<?php echo md5($_SERVER['REQUEST_URI']); ?>"></div>
	
<?php } else { ?>
				<div class="main-navigation">
					<div class="alignleft">
						<?php previous_posts_link( __( 'Newer Entries', 'wptouch') ) ?>
					</div>
					<div class="alignright">
						<?php next_posts_link( __('Older Entries', 'wptouch')) ?>
					</div>
				</div>
<?php } ?>
</div><!-- #End post -->

<?php else : ?>

	<div class="result-text-footer">
		<?php wptouch_core_else_text(); ?>
	</div>

 <?php endif; ?>
<?php// } //ENDIF WatchAndListen URL ?>


<!-- Here we're establishing whether the page was loaded via Ajax or not, for dynamic purposes. If it's ajax, we're not bringing in footer.php -->
<?php global $is_ajax; if (!$is_ajax) get_footer(); ?>


<?php
function watchAndListenMenu () {
  
    global $wplogger;
    
    $args = array('taxonomy' => 'wtf_media_categories','post_type'=>'watch-and-listen');
    $categories = get_categories($args);
            
            ?>
            <div class="post" > 
                <a class="h2" >VIDEOS</a>
                <div style="" class="mainentry left-justified">
<?php 
            foreach ($categories as $cat) {
                $slug = $cat->slug;
                $name = $cat->name;
                $link = esc_url( home_url() )."/watch-and-listen/?place=".$slug;
               
                ?>
                <a class="read-more" href=<?php echo $link;?>>
                   <?php echo $name; ?>
                </a>
                
   <?php     } ?>
            
            	</div>		
            </div>
		<!--
		<div class="post" > 
			<a class="h2" >PHOTOS</a>
			
			<!--
			<a class="post-arrow post-arrow-up" href="#"></a>
			-- >
			<div style="" class="mainentry left-justified">
			<a class="read-more" >WTF?! Aspen!</a>
			<a class="read-more" >WTF?! Utah!</a>
			<a class="read-more" >WTF?! Atlanta!</a>
			</div>		
		</div> -->
<?php
    
}

function glossaryMenu () {
    
  
    global $wplogger;
    
    $args = array('taxonomy' => 'glossary-category','post_type'=>'glossary');
            $categories = get_categories($args);
            
            
            ?>
            <div class="post" > 
                <a class="h2" >WTF!? 101</a>
                <div style="" class="mainentry left-justified">
<?php 
            foreach ($categories as $cat) {
                $slug = $cat->slug;
                $name = $cat->name;
                $nicename = $cat->category_nicename;
                $link = esc_url( home_url() )."/glossary-category/".$nicename;
               
                ?>
                <a class="read-more" href=<?php echo $link;?>>
                   <?php echo $name; ?>
                </a>
                
   <?php     } ?>
            
            	</div>		
            </div>

<?php
    
}



?>