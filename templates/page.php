<?php

if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap">
    <h1>
        <a href="https://raychat.io" target="_blank">
            <img src="<?php echo RAYCHAT_IMG_URL ?>raychat-logo.svg"/>
        </a>
    </h1>
    <b style="color:red;"><?php echo $error; ?></b>

	<?php if ( ! $widget_id ) { ?>
		<?php if($error = get_transient('error_token_uuid')): ?>
            <div class="error">
                <p><?php echo $error ?></p>
            </div>
		<?php endif; ?>
        <div class="gray_form">
            <h3> تبریک میگوییم، شما برای نصب ابزارک رایچت در سایتتان نصف راه را پیموده اید :)</h3>
            <p>
                اکنون از پنل
                <a href="http://raychat.io/admin" target="_blank">مدیریت رایچت</a>

                از قسمت تنظیمات کانال
                توکن کانال مورد نظر را در کادر زیر وارد کنید.
            </p>

            <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" id="form-token">
                <input type="hidden" value="wp_save_token" name="action">
                <div>
                    <label for="raychat_id">توکن: </label>
                    <input type="text" class="" id="raychat_id" name="token-id"/><input type="submit" name="submit"
                                                                                        class="button button-primary"
                                                                                        value="ذخیره">
                </div>

                <br><br>
                <hr>
                <br><br>
                <p>
                    چنانچه تا کنون در رایچت عضو نشده اید میتوانید از طریق لینک زیر در رایچت عضو شوید و به صورت نامحدود
                    با کاربران وبسایتتون مکالمه کنید و فروش خود را چند برابر کنید
                    <br>
                    <br>
                    <a class="button button-primary" href="http://raychat.io/signup" target="_blank">عضویت رایگان</a>
                    <br><br>
                <hr>
                <p style="font-size: 12px">
                    رایچت، ابزار گفتگوی آنلاین |
                    <a href="http://raychat.io/" target="_blank">دمو</a>
                <p>
                </p>
            </form>

        </div>
	<?php } else {
		?>
        <div class="success">
			<?php _e( 'تبریک میگوییم ابزارک رایچت در سایت شما با موفقیت نصب شد. برای فعال سازی ابزارک فقط کافیست یک بار دیگر سایت خود را بارگذاری کنید.', 'raychat' ); ?>
        </div>
        <div class="gray_form">
            <h3>1. <?php _e( 'ورود به پنل اپراتوری', 'raychat' ); ?></h3>
            <a class="button button-primary" href="https://app.raychat.io"
               target="_blank"><?php _e( 'ورود به ناحیه کاربری', 'raychat' ); ?></a>
            <h3>2. <?php _e( 'شخصی سازی ابزارک یا مدیریت اپراتور ها از طریق پنل مدیریت', 'raychat' ); ?></h3>
            <p><?php _e( 'بعد از نصب و فعال سازی ابزارک برای هر چه بهتر مدیریت کردن اپراتور ها و شخصی سازی ابزارک میتوانید از طریق پنل مدیریت اقدام کنید', 'raychat' ); ?></p>
            <a class="button button-primary" href='https://raychat.io/login'
               target="_blank"><?php _e( 'ورود به پنل مدیریت', 'raychat' ); ?></a>
        </div>
	<?php } ?>
</div>
