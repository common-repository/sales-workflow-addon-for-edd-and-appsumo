<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$nonce_action = "appsumo_registration_nonce_action";
$nonce_name = "appsumo_registration_nonce";
?>
<div class="appsumo-container">
  <div class="appsumo-container-desc">
    <div id="appsumo_registration_form_div" class="appsumo-w-50 appsumo-left-form">
      <div class="appsumo-desc">
        <a href="#!" class="appsumo-logo"><img src="<?php echo esc_attr( plugins_url('../img/logo.png', __FILE__) ); ?>" alt="logo"></a>
        <h1 class="appsumo-heading">Create your Account</h1>
        <p class="appsumo-sub-heading">Create  your account to redeem you appsumo code</p>
        <form action="<?php echo esc_attr( appsumo_get_landing_page_url() ); ?>" method="post">
					<?php wp_nonce_field( $nonce_action, $nonce_name ); ?>
          <?php appsumo_show_error_messages(); ?>
        	<div class="appsumo-double-form">
        		<p>
              <label for="reg_fname">First name: </label>
              <input type="text" name="fname" id="reg_fname" value="<?php echo esc_attr( $fname ); ?>" />
            </p>
            <p>
              <label for="reg_lname">Last name: </label>
              <input type="text" name="lname" id="reg_lname" value="<?php echo esc_attr( $lname ); ?>" />
            </p>
        	</div>
          <p>
            <label for="reg_email">Email: </label>
            <input type="text" name="email" id="reg_email" value="<?php echo esc_attr( $email ); ?>" />
          </p>
          <p>
            <label for="reg_pass">Set password: </label>
            <input type="password" name="pass" id="reg_pass" />
            <span class="appsumo-pshow-hide"></span>
          </p>
          <p class="appsumo-code-area">
            <label for="reg_appsumo_code">Appsumo Code: </label>
            <input type="text" name="appsumo_code" id="reg_appsumo_code" value="<?php echo esc_attr( $appsumo_code ); ?>" />
          </p>
          <input type="hidden" name="appsumo_action" value="registration" />
          <p>
            <input name="submit" type="submit" class="appsumo-submit" value="Create account & Redeem code" />
          </p>
        </form>
        <p class="appsumo-log-condition">Already have an account? <a href="<?php echo esc_attr( wp_login_url( home_url( 'appsumo' ) ) ); ?>" class="appsumo_btn_switch_to_login">Login</a></p>
        <p class="appsumo-log-condition appsumo-log-condition-2">Log in using the same email you used when purchasing our product. If you didn't receive the password or don't remember it  <a href="<?php echo esc_attr( wp_lostpassword_url() ); ?>">reset the password</a>. Still having problems? Contact our Support Team</p>
      </div>
    </div>
    <div class="appsumo-right-graph appsumo-w-50">
      <div class="appsumo-right-graph-desc">
        <img src="<?php echo esc_attr( plugins_url('../img/image.png', __FILE__) ); ?>" alt="login">
      </div>
    </div>
  </div>
</div>