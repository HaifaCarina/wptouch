<?php

function shareAndVote ($link) {
    $share_and_vote_url = esc_url( home_url() )."/share-and-vote/";
    
    $args = array('taxonomy' => 'wtf_message_type','id' => 'wtf_type', 'name' => 'wtf_type');
    $categories = get_categories($args);
    $query = new WP_Query ($args);
   
    
    
    // handle submission of a story
    if ( isset($_POST['message']) && wp_verify_nonce( $_POST['_wpnonce'], 'wtf-date-or-not' ) ) :

            $message = trim ( wp_filter_nohtml_kses( $_POST['message'] ) );
            if ( !empty($message) && strlen( $message ) <= 1000 ) :

                    $post_author = ( isset( $_POST['anonpost'] ) && $_POST['anonpost'] == 1 ) ? get_user_by( 'login', 'Anonymous' ) : wp_get_current_user();		

                    $title = $post_author->display_name . ' Date or Non-Date? | ' . wp_html_excerpt( $message, 20 ) . '...';		

                    wp_insert_post(array(
                            'post_type' => 'date',
                            'post_status' => 'publish',
                            'post_title' => $title,
                            'post_content' => $message,
                            'post_author' => $post_author->ID
                    ));

                    wp_redirect($_SERVER['HTTP_REFERER']);

            elseif ( strlen($message) > 1000 ) :

                    $msg_error = "Your description was too long; let's keep the stories short (under 5000 characters).";

            endif;



    endif;
    
    if ( isset($_POST['message']) && is_user_logged_in() && wp_verify_nonce( $_POST['_wpnonce'], 'wtf-wtf' ) ) {
	
	$message = trim ( wp_filter_nohtml_kses( $_POST['message'] ) );
	
	if ( !empty($message) && strlen($message) <= 1000 ) :
		
		$tag = intval($_POST['wtf_type']);
		$term = get_term( $tag, 'wtf_message_type' );
		
		$post_author = ( isset( $_POST['anonpost'] ) && $_POST['anonpost'] == 1 ) ? get_user_by( 'login', 'Anonymous' ) : wp_get_current_user();
		
		$title = $post_author->display_name . ': WTF?! Moment | ' . wp_html_excerpt( $message, 25 ) . '...';
		
		if ( is_null($term) || is_wp_error($term) ) wp_die('No hacking allowed. :-)');
		
		$newpost = wp_insert_post(array(
			'post_type' => 'wtf',
			'post_status' => 'publish',
			'post_title' => $title,
			'post_content' => $message,
			'post_author' => $post_author->ID
		));
		
		if ($newpost) { wp_set_object_terms( $newpost, array($tag), 'wtf_message_type' ); }
		
		wp_redirect( $_SERVER['HTTP_REFERER'] . '#post-' . $newpost );
		
	elseif ( strlen($message) > 1000 ) :
		
		$GLOBALS['msg_error'] = "Your story was too long; we're looking for short stories! Please keep it under 500 characters.";
			
	endif;

}


    
    if($link=='share-date'){
            shareDate();  
           // exit;
    } else if($link=='share-wtf'){
            shareWtf();  
           // exit;
    }  else {
        if($link) {
        
        if($link=='my-posts'){
            global $current_user;
            //get_currentuserinfo();
            //$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $result = query_posts(
                    array(
              //              'paged' => $paged,
                            'author' => $current_user->ID,
                            //'post_type' => array('post','guest-blogs','ask-jess','wtf','date','columns','watch-and-listen'),
                            'post_type' => array('wtf','date'),
                            "posts_per_page" => 25
                    )
            );
            
        } else {
            $result = query_posts(array(
                "post_type" => array($link) ,
                "posts_per_page" => 25
                                    //"column_category" => "love-poems-2"
                    ));
        }
        
    } else {
        $result = query_posts(array(
                        "post_type" => array("wtf","date"),
                        "posts_per_page" => 25
        ));
    }
    }
    
    $share_and_vote_url = esc_url( home_url() )."/share-and-vote/";
    
    if($link!="share-date" && $link!="share-wtf"):
?>
<div class="content post" > 
                    <a class="h2" >SHARE AND VOTE</a>
                    
                    <form action=<?php echo $share_and_vote_url; ?> name="share-and-vote" method="get">
		
                            <div style="" class="mainentry left-justified">
                            <a class="read-more" name="link" href=<?php echo esc_url( home_url() )."/share-and-vote/"; ?>>All</a>
                            <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=date"; ?>>Date/Non-Date</a>
                            <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=wtf"; ?>>WTF! Moments</a>
                            <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=my-posts"; ?>>My Posts</a>
                            <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=share-date"; ?>>Post a New Date/Non-Date/Not-A-Date</a>
                            <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=share-wtf"; ?>>Share Your WTF?! Moment</a>
                           
			</div>	
                     </form>

		</div>

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

	<a class="h2" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<div class="post-author">
		<?php if ($wptouch_settings['post-cal-thumb'] != 'calendar-icons') { ?><span class="lead"><?php _e("Written on", "wptouch"); ?></span> <?php echo get_the_time('d.m.Y') ?> <?php _e("at", "wptouch"); ?> <?php echo get_the_time('G:i') ?><br /><?php if (!bnc_show_author()) { echo '<br />';} ?><?php } ?>
			<!--<?php /*if (bnc_show_author()) { ?><span class="lead"><?php _e("By", "wptouch"); ?></span> <?php the_author(); ?><br /><?php } */?>
			-->
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

<!-- #End post -->

<?php else : ?>

	<div class="result-text-footer">
		<?php wptouch_core_else_text(); ?>
	</div>

 <?php endif; 
 endif; ?>



<?php } ?>

<?php 
function shareDate(){
    
    ?>
    <div class="content post" > 
        <a class="h2" >SHARE AND VOTE</a>

        <form action=<?php echo $share_and_vote_url; ?> name="share-and-vote" method="get">

                <div style="" class="mainentry left-justified">
                <a class="read-more" name="link" href=<?php echo esc_url( home_url() )."/share-and-vote/"; ?>>All</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=date"; ?>>Date/Non-Date</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=wtf"; ?>>WTF! Moments</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=my-posts"; ?>>My Posts</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=share-date"; ?>>Post a New Date/Non-Date/Not-A-Date</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=share-wtf"; ?>>Share Your WTF?! Moment</a>

            </div>	
         </form>
        
        

    </div>

    <div class="content post" > 
        <form style="margin: 0 auto; " id="date-form" method="post" action="<?php echo site_url() . $_SERVER['REQUEST_URI'] ; ?>#respond">
                <p><textarea id="message" rows="8" cols="42" name="message"><?php if ( isset($_POST['message']) ) echo esc_html( $_POST['message'] ); ?></textarea></p>
                <p>You have <span id="metacount">1000</span> characters remaining.</p>
                <p><input type="checkbox" name="anonpost" value="1" /> Post my story anonymously</p>
                <p class="form-submit">
                        <?php wp_nonce_field( 'wtf-date-or-not' ); ?>
                        <input type="submit" value="Share Story" id="submit" name="submit" />
                </p>
        </form>
    </div>

<?php
    
}

function shareWtf(){
    
    ?>
    <div class="content post" > 
        <a class="h2" >SHARE AND VOTE</a>

        <form action=<?php echo $share_and_vote_url; ?> name="share-and-vote" method="get">

                <div style="" class="mainentry left-justified">
                <a class="read-more" name="link" href=<?php echo esc_url( home_url() )."/share-and-vote/"; ?>>All</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=date"; ?>>Date/Non-Date</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=wtf"; ?>>WTF! Moments</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=my-posts"; ?>>My Posts</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=share-date"; ?>>Post a New Date/Non-Date/Not-A-Date</a>
                <a class="read-more" name="link" href=<?php echo $share_and_vote_url."?link=share-wtf"; ?>>Share Your WTF?! Moment</a>

            </div>	
         </form>
        
        

    </div>


<div class="content post">
	<!--<div class="intro wtfintro">-->
		<h1>Share Your WTF?! Moment</h1>
		<?php 
                
    
                 wtf_wtf_form(); 
                
                ?>
	<!--</div>-->
</div>

<?php
    
}

  
function wtf_wtf_form() {
    
    
  
	echo '<p>Have a story, text message, Facebook wall post, voicemail, email or face-to-face interaction that made you say "WTF?!" Share it here!</p>';
	
	if ( !is_user_logged_in() ) : 
	?>

	<form id="wtf-form" method="post" action="<?php echo site_url() . $_SERVER['REQUEST_URI'] ; ?>">
		<p>
			<label for="message">What Happened?</label>
			<?php wp_dropdown_categories( array('taxonomy'=>'wtf_message_type', 'hide_empty' => false, 'id' => 'wtf_type', 'name' => 'wtf_type') ); ?>
		</p>
		<p>
			<label for="message">Post Here</label>
			<textarea id="message" rows="8" cols="42" name="message"></textarea>
		</p>
		<p>You have <span id="metacount">1000</span> characters remaining.</p>
		<p>
			<label for="anonpost"><input type="checkbox" name="anonpost" id="anonpost" value="1" /> Post anonymously</label>
		</p>
		<!--<p class="form-submit">
			<?php wp_nonce_field( 'wtf-wtf' ); ?>
			<input type="submit" value="Share" id="submit" name="submit" />
		</p>-->
	</form>
	<script type="text/javascript">jQuery('#message').NobleCount('#metacount', { max_chars: 1000, on_negative: 'error', block_negative: true });</script>
	<?php
	echo '
		<p class="must-log-in"><a href="#" onclick="document.getElementById(\'user_login\').focus(); return false;">Login</a> or ' . strtolower( wp_register('','',false) ) . ' to share your WTF moment.</p>
	';
	else :
	
 	if ( isset($GLOBALS['msg_error']) && $GLOBALS['msg_error'] ) { echo '<p class="error">'.$GLOBALS['msg_error'].'</p>'; }
?>
	<form id="wtf-form" method="post" action="<?php echo site_url() . $_SERVER['REQUEST_URI'] ; ?>">
		<p>
			<label for="message">What Happened?</label>
                        
			<?php wp_dropdown_categories( array('taxonomy'=>'wtf_message_type', 'hide_empty' => false, 'id' => 'wtf_type', 'name' => 'wtf_type') ); ?>
		</p>
		<p>
			<label for="message">Post Here</label>
			<textarea id="message" rows="8" cols="45" name="message"></textarea>
		</p>
		<p>You have <span id="metacount">1000</span> characters remaining.</p>
		<p>
			<label for="anonpost"><input type="checkbox" name="anonpost" id="anonpost" value="1" /> Post anonymously</label>
		</p>
		<p class="form-submit">
			<?php wp_nonce_field( 'wtf-wtf' ); ?>
			<input type="submit" value="Share" id="submit" name="submit" />
		</p>
	</form>
	<script type="text/javascript">jQuery('#message').NobleCount('#metacount', { max_chars: 1000, on_negative: 'error', block_negative: true });</script>
<?php
	endif; 
}
?>