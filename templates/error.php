

<?php

if ( ! defined( 'ABSPATH' ) ) exit;

?>
<div class="wrap">
    <h1>
        <a href="https://raychat.io" target="_blank">
            <img src="<?php echo RAYCHAT_PLUGIN_URL; ?>img/<?php _e('logo.png','raychat');?>" />
        </a>
    </h1>
    <b style="color:red;"><?php echo $error; ?></b>
        <div class="gray_form">
			<?php _e('Unfortunately, your server configuration does not allow the plugin to connect to RaychatChat servers to create account. Please, go to <a target="_blank" href="https://admin.raychat.io/autoreg?lang=en">https://admin.raychat.io/autoreg?lang=en</a> and sign up. During the signup process you will be offered to download another Wordpress module that does not require to communicate over the network','raychat'); ?>
        </div>
</div>
