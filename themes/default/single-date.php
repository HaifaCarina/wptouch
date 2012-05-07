<script type="text/javascript">
function hideshow(which){
    which.style.display="block";
}
</script>
<?php include('functions_date.php'); ?>


<?php
    global $wplogger;
    $wplogger->log('single date.php is called');
?>
<?php global $is_ajax; 
    $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']); 
    if (!$is_ajax) get_header(); ?>
<?php $wptouch_settings = bnc_wptouch_get_settings(); 

    //$wplogger->log(the_post());
?>

<div class="content single" id="content<?php //echo md5($_SERVER['REQUEST_URI']); ?>">
    
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php
                global $current_user;
                $wplogger->log('current user:'.$current_user->ID);
                ?>
                    
			<div class="post">
                            <?php $wplogger->log(get_the_time('M jS, Y @ h:i a')); ?>
                           
                            
                            <a class="sh2" href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( "Permanent Link to ", "wptouch" ); ?><?php if (function_exists('the_title_attribute')) the_title_attribute(); else the_title(); ?>"><?php the_title(); ?></a>
			        <div class="single-post-meta-top"><?php echo get_the_time('M jS, Y @ h:i a') ?> &rsaquo; <?php the_author() ?><br />
                                    
		<!-- Let's check for DISQUS... we need to skip to a different div if it's installed and active -->		
		<?php if ( 'open' == $post->comment_status && bnc_can_show_comments() ) : ?>
			<?php if (function_exists('dsq_comments_template')) { ?>
		 		<a href="#dsq-add-new-comment">&darr; <?php _e( "Skip to comments", "wptouch" ); ?></a>
			<?php } elseif (function_exists('id_comments_template')) { ?>
				<a href="#idc-container-parent">&darr; <?php _e( "Skip to comments", "wptouch" ); ?></a>
			<?php } elseif (isset($post->comment_count) && $post->comment_count == 0) { ?>
				<a href="#respond">&darr; <?php _e( "Leave a comment", "wptouch" ); ?></a>
			<?php } elseif (isset($post->comment_count) && $post->comment_count > 0) { ?>
				<a href="#com-head">&darr; <?php _e( "Skip to comments", "wptouch" ); ?></a>
			<?php } ?>
		<?php endif; ?>
		</div>
	</div>

		<div class="clearer"></div>
			<?php wptouch_include_adsense(); ?>
                        
         <div class="post" id="post-<?php the_ID(); ?>">
         	<div id="singlentry" class="<?php echo $wptouch_settings['style-text-justify']; ?>">
                    
            	<?php the_content(); ?>		
                <?php 
                if($current_user->ID != 0) {
                    $votedata = wtf_the_date_votes( NULL, false );
                    echo $votedata->voteoutput; 
                } else {
                    ?>
                    You must be <a id="loginopenlink" href="javascript:hideshow(document.getElementById('wptouch-login'))">logged in </a> to vote.
                    <?php
                }
                ?>
                    
                    <?php $wplogger->log($votedata); ?>
        
                    
                        
			</div>  
			
<!-- Categories and Tags post footer -->        

			<div class="single-post-meta-bottom">
					<?php /*wp_link_pages( 'before=<div class="post-page-nav">' . __( "Article Pages", "wptouch-pro" ) . ':&after=</div>&next_or_number=number&pagelink=page %&previouspagelink=&raquo;&nextpagelink=&laquo;' ); ?>          
			    <?php _e( "Categories", "wptouch" ); ?>: <?php if (the_category(', ')) the_category(); ?>
			    <?php if (function_exists('get_the_tags')) the_tags('<br />' . __( 'Tags', 'wptouch' ) . ': ', ', ', ''); */?>  
		    <?php 
                        _e( "Topics: ", "wptouch" );
                        $counter = 0;
                        foreach((get_the_category()) as $category) { 
                            echo '<a href="'.get_category_link($category->term_id ).'">'.$category->cat_name.'</a>';
                            if ($counter != (count(get_the_category())-1)){
                                echo ", ";
                            }
                            $counter++;
                            
                        } 
                    ?> </div>   

		<ul id="post-options">
		<?php $prevPost = get_previous_post(); if ($prevPost) { ?>
			<li><a href="<?php $prevPost = get_previous_post(false); $prevURL = get_permalink($prevPost->ID); echo $prevURL; ?>" id="oprev"></a></li>
		<?php } ?>
		<li><a href="mailto:?subject=<?php
bloginfo('name'); ?>- <?php the_title_attribute();?>&body=<?php _e( "Check out this post:", "wptouch" ); ?>%20<?php the_permalink() ?>" onclick="return confirm('<?php _e( "Mail a link to this post?", "wptouch" ); ?>');" id="omail"></a></li>
		<?php wptouch_twitter_link(); ?>
		<?php wptouch_facebook_link(); ?>
                <li><a href="javascript:return false;" id="obook"></a></li>
                <li style="width:100px;"><?php wpv_voting_display_vote($post->ID); ?></li>
		
		<?php $nextPost = get_next_post(); if ($nextPost) { ?>
			<li><a href="<?php $nextPost = get_next_post(false); $nextURL = get_permalink($nextPost->ID); echo $nextURL; ?>" id="onext"></a></li>
		<?php } ?>
		</ul>
    </div>

  	<div id="bookmark-box" style="display:none">
		<ul>
			<li><a  href="http://del.icio.us/post?url=<?php echo get_permalink()
?>&title=<?php the_title(); ?>" target="_blank"><img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/bookmarks/delicious.jpg" alt="" /> <?php _e( "Del.icio.us", "wptouch" ); ?></a></li>
			<li><a href="http://digg.com/submit?phase=2&url=<?php echo get_permalink()
?>&title=<?php the_title(); ?>" target="_blank"><img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/bookmarks/digg.jpg" alt="" /> <?php _e( "Digg", "wptouch" ); ?></a></li>
			<li><a href="http://technorati.com/faves?add=<?php the_permalink() ?>" target="_blank"><img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/bookmarks/technorati.jpg" alt="" /> <?php _e( "Technorati", "wptouch" ); ?></a></li>
			<li><a href="http://ma.gnolia.com/bookmarklet/add?url=<?php echo get_permalink() ?>&title=<?php the_title(); ?>" target="_blank"><img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/bookmarks/magnolia.jpg" alt="" /> <?php _e( "Magnolia", "wptouch" ); ?></a></li>
			<li><a href="http://www.newsvine.com/_wine/save?popoff=0&u=<?php echo get_permalink() ?>&h=<?php the_title(); ?>" target="_blank"><img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/bookmarks/newsvine.jpg" target="_blank"> <?php _e( "Newsvine", "wptouch" ); ?></a></li>
			<li class="noborder"><a href="http://reddit.com/submit?url=<?php echo get_permalink() ?>&title=<?php the_title(); ?>" target="_blank"><img src="<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/bookmarks/reddit.jpg" alt="" /> <?php _e( "Reddit", "wptouch" ); ?></a></li>
		</ul>
	</div>

<!-- Let's rock the comments -->
<?php if ( bnc_can_show_comments() ) : ?>
	<?php comments_template(); ?>
<script type="text/javascript">
jQuery(document).ready( function() {
// Ajaxify '#commentform'
var formoptions = { 
	beforeSubmit: function() {$wpt("#loading").fadeIn(400);},
	success:  function() {
		$wpt("#commentform").hide();
		$wpt("#loading").fadeOut(400);
		$wpt("#refresher").fadeIn(400);
		}, // end success 
	error:  function() {
		$wpt('#errors').show();
		$wpt("#loading").fadeOut(400);
		} //end error
	} 	//end options
$wpt('#commentform').ajaxForm(formoptions);
}); //End onReady
</script>
<?php endif; ?>
	<?php endwhile; else : ?>

<!-- Dynamic test for what page this is. A little redundant, but so what? -->

	<div class="result-text-footer">
		<?php wptouch_core_else_text(); ?>
	</div>

	<?php endif; ?>
</div>
	
	<!-- Do the footer things -->
	
<?php global $is_ajax; if (!$is_ajax) 
get_footer();