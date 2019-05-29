<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$user = false;
if( is_user_logged_in() ) {
	$user = wp_get_current_user();
}

?>

<script type="text/javascript">
	!function(){function t(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,localStorage.getItem("rayToken")?t.src="https://app.raychat.io/scripts/js/"+o+"?rid="+localStorage.getItem("rayToken")+"&href="+window.location.href:t.src="https://app.raychat.io/scripts/js/"+o;var e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(t,e)}var e=document,a=window,o="<?php echo $widget_id; ?>";"complete"==e.readyState?t():a.attachEvent?a.attachEvent("onload",t):a.addEventListener("load",t,!1)}();
	<?php if( $user ):?>
	window.addEventListener('raychat_ready', function (ets) {
		window.Raychat.setUser({
		   email 		: '<?php echo $user->user_email;?>',
		   name 		: '<?php echo $user->display_name;?>',
		   about 		: 'Comming soon...',
		   phone 		: '',
		   avatar 	 	: '<?php echo get_avatar_url( $user->ID );?>',
		   updateOnce 	: true
		});
	});
	<?php endif;?>
</script>
