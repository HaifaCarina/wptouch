<style type="text/css">

#headerbar, #wptouch-login, #wptouch-search {

    background-color: #000000;
	background-repeat: no-repeat, repeat-x;
	background-image: url(<?php echo esc_url( home_url( '/wp-content/plugins/wptouch/themes/core/core-images/wtfbanner.png' ) ); ?> ),
					url(<?php echo esc_url( home_url( 'wp-content/plugins/wptouch/themes/core/core-images/wtfbanner-forrepeat.png' ) ); ?>);
	
	/*url('http://localhost/wordpress/wp-content/plugins/wptouch/themes/core/core-images/wtfbanner.png'), 
					url('http://localhost/wordpress/wp-content/plugins/wptouch/themes/core/core-images/wtfbanner-forrepeat.png') ; 
	/*#<?php echo bnc_get_header_background(); ?> url(<?php echo compat_get_plugin_url( 'wptouch' ); ?>/themes/core/core-images/head-fade-bk.png);
*/
}
#headerbar-title, #headerbar-title a {
	color: #<?php echo bnc_get_header_color(); ?>;
	
}
#wptouch-menu-inner a:hover {
	color: #<?php echo bnc_get_link_color(); ?>;
}
#catsmenu-inner a:hover {
	color: #<?php echo bnc_get_link_color(); ?>;
}
#drop-fade {
background: #<?php echo bnc_get_header_border_color(); ?>;
}
a, h3#com-head {
	color: #<?php echo bnc_get_link_color(); ?>;
}

a.h2, a.sh2, .page h2 {
font-family: '<?php echo bnc_get_h2_font(); ?>';
}

<?php wptouch_thumb_reflections(); ?>

<?php if (bnc_is_truncated_enabled()) { ?>
a.h2{
text-overflow: ellipsis;
white-space: nowrap;
overflow: hidden;
}
<?php } ?>

</style>