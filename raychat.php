<?php

/**
 * Plugin Name: raychat
 * Author: raychat
 * Author URI: www.raychat.io
 * Plugin URI: www.raychat.io
 * Description: افزونه وردپرس گفتگوی آنلاین رایچت | با مشتریانتون صحبت کنید ، پشتیبانی کنید و از فروش خود لذت ببرید :)
 * Version: 1.0.4
 *
 * Text Domain:   raychat
 * Domain Path:   /
 */
if (!defined('ABSPATH')) {
    die("go away!");
}

load_plugin_textdomain('raychat', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)));

$lang = get_bloginfo("language");
$raychat_addr = 'https://www.raychat.io';


define("RAYCHAT_LANG", substr($lang, 0, 2));

define("RAYCHAT_WIDGET_URL", "raychat.io");
define("RAYCHAT_URL", $raychat_addr);
define("RAYCHAT_INTEGRATION_URL", RAYCHAT_URL . "/integration");
define("RAYCHAT_PLUGIN_URL", plugin_dir_url(__FILE__));
define("RAYCHAT_IMG_URL", plugin_dir_url(__FILE__) . "/img/");
// //register hooks for plugin
register_activation_hook(__FILE__, 'raychatInstall');
register_deactivation_hook(__FILE__, 'raychatDelete');

//add plugin to options menu
function catalog_admin_menu_raychat() {
    load_plugin_textdomain('raychat', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)));
    add_menu_page(__('رایچت', 'raychat'), __('رایچت', 'raychat'), 8, basename(__FILE__), 'raychatPreferences', RAYCHAT_IMG_URL . "raychat.png");
}

add_action('admin_menu', 'catalog_admin_menu_raychat');

function raychat_options_validate($args) {
    return $args;
}

/*
 * Register the settings
 */
add_action('admin_init', 'raychat_register_settings');

function raychat_register_settings() {
    register_setting('raychat_token', 'raychat_token', 'raychat_options_validate');
    register_setting('raychat_widget_id', 'raychat_widget_id', 'raychat_options_validate');
}

add_action('admin_post_wp_save_token', 'wp_save_token_function_raychat');
add_action('admin_post_nopriv_wp_save_token', 'wp_save_token_function_raychat');

add_action('wp_footer', 'raychatAppend', 100000);

function raychatInstall() {
    return raychat::getInstance()->install();
}

function raychatDelete() {
    return raychat::getInstance()->delete();
}

function raychatAppend() {
    echo raychat::getInstance()->append(raychat::getInstance()->getId());
}

function raychatPreferences() {
    if (isset($_POST["widget_id"])) {
        raychat::getInstance()->save();
    }

    load_plugin_textdomain('raychat', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)));

    wp_register_style('raychat_style', plugins_url('raychat.css', __FILE__));
    wp_enqueue_style('raychat_style');
    echo raychat::getInstance()->render();
}

function wp_save_token_function_raychat() {
    $tokenError = null;
    if (trim(esc_html($_POST['submit'])) !== '') {
        $token = esc_html($_POST['token-id']);
        if ($token !== '') {
            if (preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/", $token)) {
                if (get_option('raychat_widget_id') !== false) {
                    update_option('raychat_widget_id', $token);
                } else {
                    add_option('raychat_widget_id', $token, null, 'no');
                }
                $raychat = raychat::getInstance();
                $raychat->install();
            } else {
                $tokenError = "توکن نامعتبر است.";
            }
        } else {
            $tokenError = "توکن نمی تواند خالی باشد.";
        }
        set_transient('error_token_uuid', $tokenError);
    }
    wp_redirect($_SERVER['HTTP_REFERER']);
//	wp_redirect(admin_url('admin.php?page=raychat.php'));
    exit();
}

class raychat {

    protected static $instance, $db, $table, $lang;

    private function __construct() {
        $this->token = get_option('raychat_token');
        $this->widget_id = get_option('raychat_widget_id');
    }

    private function __clone() {
        
    }

    private function __wakeup() {
        
    }

    private $widget_id = '';
    private $token = '';

    public static function getInstance() {

        if (is_null(self::$instance)) {
            self::$instance = new raychat();
        }
        self::$lang = "en";
        if (isset($_GET["lang"])) {
            switch ($_GET["lang"]) {
                case 'ru':
                    self::$lang = "ru";
                    break;
                default:
                    self::$lang = "en";
                    break;
            }
        }

        return self::$instance;
    }

    public function setID($id) {
        $this->widget_id = $id;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * Install
     */
    public function install() {
        $file = dirname(__FILE__) . '/id';
        if (file_exists($file)) {
            $uuid = file_get_contents($file);
            if(get_option('raychat_widget_id') !== false){
                 update_option('raychat_widget_id', $uuid);
            }else{
                add_option('raychat_widget_id', $uuid, null, 'no');
            }
            unlink($file);
            $this->widget_id = $uuid;
            $this->save();
        } else {
            if (!$this->widget_id) {
                if(($out = get_option('raychat_widget_id')) !== false){
                    $this->widget_id = $out;
                }
            }
            $this->save();
        }
    }

    public function catchPost() {
        if (isset($_GET['mode']) && $_GET['mode'] == 'reset') {
            $this->widget_id = '';
            $this->token = '';
            $this->save();
        }
        if (isset($_POST['widget_id'])) {
            $this->widget_id = $_POST['widget_id'];
            $this->save();
        } elseif (isset($_POST['email']) && isset($_POST['userPassword'])) {
            $query = $_POST;
            $query['siteUrl'] = get_site_url();
            $query['partnerId'] = "wordpress";
            $authToken = md5(time() . get_site_url());
            $query['authToken'] = $authToken;
            if (!$query['agent_id']) {
                $query['agent_id'] = 0;
            }
            $query['lang'] = RAYCHAT_LANG;
            $content = http_build_query($query);

            if (ini_get('allow_url_fopen')) {
                $useCurl = false;
            } elseif (!extension_loaded('curl')) {
                if (!dl('curl.so')) {
                    $useCurl = false;
                } else {
                    $useCurl = true;
                }
            } else {
                $useCurl = true;
            }
           
            try {
                $path = RAYCHAT_INTEGRATION_URL . "/install";
                if (!extension_loaded('openssl')) {
                    $path = str_replace('https:', 'http:', $path);
                }
                if ($useCurl) {
                    if ($curl = curl_init()) {
                        $responce = wp_remote_get($path, array(
                            'CURLOPT_RETURNTRANSFER' => true,
                            'CURLOPT_POST' => true,
                            'CURLOPT_POSTFIELDS' => $content
                        ));
                        // curl_setopt( $curl, CURLOPT_URL, $path );
                        // curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                        // curl_setopt( $curl, CURLOPT_POST, true );
                        // curl_setopt( $curl, CURLOPT_POSTFIELDS, $content );
                        // $responce = curl_exec( $curl );
                        // curl_close( $curl );
                    }
                } else {
                    $responce = file_get_contents($path, false, stream_context_create(array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => 'Content-Type: application/x-www-form-urlencoded',
                            'content' => $content
                        )
                            )));
                }
                if ($responce) {
                    if (strstr($responce, 'Error')) {
                        return array("error" => $responce);
                    } else {
                        $this->widget_id = $responce;
                        $this->token = $authToken;
                        $this->save();

                        return true;
                    }
                }
            } catch (Exception $e) {
                _e("Connection error", 'raychat');
            }
        }
    }

    /**
     * delete plugin
     */
    public function delete() {
        if(get_option('raychat_widget_id') !== false){
            delete_option('raychat_widget_id');
        }
    }

    public function getId() {
        return $this->widget_id;
    }

    /**
     * render admin page
     */
    public function render() {
        $result = $this->catchPost();
        $error = '';
        $widget_id = $this->widget_id;
        if (is_array($result) && isset($result['error'])) {
            $error = $result['error'];
        }

        if (ini_get('allow_url_fopen')) {
            $requirementsOk = true;
        } elseif (!extension_loaded('curl')) {
            if (!dl('curl.so')) {
                $requirementsOk = false;
            } else {
                $requirementsOk = true;
            }
        } else {
            $requirementsOk = true;
        }

        if ($requirementsOk) {
            require_once "templates/page.php";
        } else {
            require_once "templates/error.php";
        }
    }

    public function append($widget_id = false) {
        if ($widget_id) {
            require_once "templates/script.php";
        }
    }

    public function save() {
        update_option('raychat_widget_id', $this->widget_id);
        update_option('raychat_token', $this->token);
    }

}
