
<?php
/**
 * called from functions.php, this script sets up and creates the Date or Non-Date? feature 
 */
/*
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

*/
/**************************/
/***  voting apparatus  ***/
/**************************/

/**
 * create voting table
 */
 
if ( !get_option('date_voting_setup') ) :

	global $wpdb;
	
	$table_name = $wpdb->prefix . 'wtf_datevotes';
	if( $wpdb->get_var("show tables like '$table_name'") != $table_name ) {
		
		$sql = "CREATE TABLE " . $table_name . " (
	  		postid bigint(20) NOT NULL,
	  		userid bigint(20) NOT NULL,
	  		vote tinytext NOT NULL,
	  		PRIMARY KEY id (postid, userid)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		add_option( 'date_voting_setup', true );
		
	}

endif;

/**
 * create voting choices
 */

if ( !get_option('date_voting_options') ) :

	add_option('date_voting_options', array( 'date', 'non-date', 'not-a-date' ));

endif;

function wtf_the_date_votes( $postid = NULL, $echo = true ) 
{
    
    global $wplogger;
    
	if ( is_null($postid) ) {
		global $post;
		if (!$postid = $post->ID) return false;
	} elseif ( !is_int($postid) ) {
		return false;
	}
	
	// go get results
	global $wpdb;
	$table_name = $wpdb->prefix . "wtf_datevotes";
	$votes_sql = "SELECT 
		(SELECT COUNT( vote ) FROM $table_name WHERE vote = 'date' AND postid = $postid) AS 'date', 
		(SELECT COUNT( vote ) FROM $table_name WHERE vote = 'non-date' AND postid = $postid) AS 'non-date',
		(SELECT COUNT( vote ) FROM $table_name WHERE vote = 'not-a-date' AND postid = $postid) AS 'not-a-date'
	";
        
    
	if ( is_user_logged_in() ) {
		global $user_ID;
		$votes_sql .= ", (SELECT vote FROM $table_name WHERE userid = $user_ID AND postid = $postid) AS 'my_vote'";
	}
	$votes = $wpdb->get_row($votes_sql,ARRAY_A);
	
        $wplogger->log($votes);
	// my vote
	if ( isset($votes['my_vote']) ) {
		$votes_my = $votes['my_vote'];
		unset($votes['my_vote']);
	} else { $votes_my = ''; }
	
	// total votes 
	$votes_total = array_sum($votes);
	
	//storage
	$datevotes = new stdClass();
	
	//winner
	arsort( $votes, SORT_NUMERIC );
	$datevotes->winner = ( current($votes) > next($votes) && reset($votes) ) ? key($votes) : 'tie';
	
	// output votes
	ob_start();
?>

    <form class="date-votes" method="post" id="datevotes-<?php echo $postid; ?>">
		<?php if ( is_user_logged_in() ) { ?><p><strong>Click a heart to vote!</strong></p><?php } ?>
               <table align="center">
                <tr>
                    <td align="center">
		<div class="vote-date vote-choice<?php if ( $votes_my == 'date' ) echo ' my-vote'; if ($datevotes->winner == 'date') echo ' winner'; ?>">
			
                        <input type="hidden" name="postid" value="<?php echo $postid; ?>" />
                        <input type="button" name="date" value="date" onclick="datevote(<?php echo $postid; ?>, 'date');" />
			<?php $vote = ($votes['date']) ? ($votes['date'] / $votes_total) * 100 : 0; ?>
                        <div class="bar"><span style="width: <?php echo round($vote); ?>%;"></span></div>
			<div class="numeric"><?php echo round($vote, 1); ?>%</div>
			<!--<div class="label">date</div>-->
		</div>
                    </td>
                    <td align="center">
		<div class="vote-non-date vote-choice<?php if ( $votes_my == 'non-date' ) echo ' my-vote'; if ($datevotes->winner == 'non-date') echo ' winner'; ?>">
			<input type="button" name="non-date" value="non-date" onclick="datevote(<?php echo $postid; ?>, 'non-date');" />
			<?php $vote = ($votes['non-date']) ? ($votes['non-date'] / $votes_total) * 100  : 0; ?>
                        <div class="bar"><span style="width: <?php echo round($vote); ?>%;"></span></div>
			<div class="numeric"><?php echo round($vote, 1); ?>%</div>
			<!--<div class="label">non-date</div>-->
		</div>
                    </td>
                    <td align="center">
		<div class="vote-not-a-date vote-choice<?php if ( $votes_my == 'not-a-date' ) echo ' my-vote'; if ($datevotes->winner == 'not-a-date') echo ' winner'; ?>">
			<input type="button" name="not-a-date" value="not-a-date" onclick="datevote(<?php echo $postid; ?>, 'not-a-date');" />
			<?php $vote = ($votes['not-a-date']) ? ($votes['not-a-date'] / $votes_total) * 100  : 0; ?>
			<div class="bar"><span style="width: <?php echo round($vote); ?>%;"></span></div>
			<div class="numeric"><?php echo round($vote, 1); ?>%</div>
			<!--<div class="label">not-a-date</div>-->
		</div>
                    </td>
               </tr>
               </table>
		
		<div class="vote-date-total">
		<!--Total Votes: <?php echo $votes_total; ?>-->
		</div>
	</form>
<?php
	$datevotes->voteoutput = ob_get_contents();
	ob_end_clean();
	
	if ( $echo ) echo $datevotes->voteoutput; 
	else return $datevotes;	
}

/**
 * queue scripts and setup ajax on appropriate pages
 */
 
//if ( is_page('date-or-non-date') || get_post_type() == 'date' ) :
add_action('wp_ajax_date_vote', 'date_vote_ajax_callback');
add_action('wp_head', 'date_vote_wp_head');

function date_vote_ajax_callback() {
	global $user_ID;
	if ( !is_user_logged_in() ) { exit('-1'); } 
	
	//validation / sanitization
	$postid = intval($_POST['post_id']);
	if ( get_post_type($postid) != 'date' || !in_array( $_POST['the_choice'], get_option('date_voting_options') ) ) { exit('Security error.'); }
	
	//execute query
	global $wpdb;
	$table_name = $wpdb->prefix . "wtf_datevotes";
	
	$updated_rows = $wpdb->update( $table_name, array( 'vote' => $_POST['the_choice'] ), array( 'postid' => $postid, 'userid' => $user_ID ) );
	if ( $updated_rows <= 0 ) {
		$wpdb->insert( $table_name, array( 'vote' => $_POST['the_choice'], 'postid' => $postid, 'userid' => $user_ID ) );
	}
	
	//reoutput for response
	wtf_the_date_votes( $postid );
	
	die();
}

function date_vote_wp_head() {
?>
<script type="text/javascript" >
function datevote(postid,choice) {
<?php if ( is_user_logged_in() ) { ?>	
console.log('datevote called');
jQuery.post( 
	'<?php echo admin_url('admin-ajax.php'); ?>', 
	{ action: 'date_vote', post_id: postid, the_choice: choice },
 	function(response) { 
 		if (response == '-1') return false; 
	 	jQuery('#datevotes-'+postid).replaceWith(response);
	 	var winner = 'winner-'+jQuery('#datevotes-'+postid+' .winner .label').html();
	 	if ( winner == 'winner-' ) winner = 'winner-tie';
	 	jQuery('#post-'+postid+' .votewrap').removeClass().addClass('votewrap '+winner); 
 	}
);
<?php } else { ?>
jQuery('#post-'+postid+' .loginerror').animate( {marginLeft: '20px' }, 250, 'swing', function(){ jQuery(this).animate({marginLeft: '0px'},250,'swing'); } );
<?php } ?>
}
</script>
<?php	
}

// endif;

?>