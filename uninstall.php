<?php

/**
 * Prevent direct access to uninstall file
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Delete raychat widget ID
 */
delete_option( 'raychat_widget_id' );