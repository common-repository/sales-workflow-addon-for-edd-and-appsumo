<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$nonce_action = "appsumo_login_nonce_action";
$nonce_name = "appsumo_login_nonce";
?>
<div id="appsumo_login_form_div" style="display: none">
    <form action="<?php echo esc_attr( appsumo_get_landing_page_url() ); ?>" method="post">
        <?php wp_nonce_field( $nonce_action, $nonce_name ); ?>
        <?php appsumo_show_error_messages(); ?>
        <p>
            <label for="login_email">Email: </label>
            <input type="text" name="email" id="login_email" />
        </p>
        <!-- Rest of your form elements -->
        <p>
            <input name="submit" type="submit" value="Sign In" />
        </p>
    </form>
    <a href="#" class="appsumo_btn_switch_to_registration"> New to <?php echo esc_html( get_bloginfo('name') ); ?>? Register for a new account.</a>
</div>
