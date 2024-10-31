<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$nonce_action = "appsumo_redeem_nonce_action";
$nonce_name = "appsumo_redeem_nonce";
?>
<div class="appsumo-container">
  <div class="appsumo-container-desc">
    <div id="appsumo_redeem_form_div" class="appsumo-w-50 appsumo-left-form">
    	<div class="appsumo-desc">
        	<a href="#!" class="appsumo-logo"><img src="<?php echo esc_attr( plugins_url('../img/logo.png', __FILE__) ); ?>" alt="logo"></a>
        	<h1 class="appsumo-heading">Enter your APPSUMO code</h1>
        	<p class="appsumo-sub-heading">You are already logged in, please enter your appsumo code:</p>
          <form action="<?php echo esc_attr( appsumo_get_landing_page_url() ); ?>" method="post">
            <?php wp_nonce_field( $nonce_action, $nonce_name ); ?>
            <?php appsumo_show_error_messages(); ?>
            <p>
              <label for="reg_appsumo_code">Appsumo Code: </label>
              <input type="text" name="appsumo_code" id="reg_appsumo_code" value="<?php echo esc_attr( $code ); ?>" />
            </p>
            <p>
              <input type="hidden" name="appsumo_action" value="redeem" />
              <input name="submit" type="submit" class="appsumo-submit" value="Redeem code" />
            </p>
          </form>
          <p class="appsumo-log-condition">Already logged in 

            <?php 
            // Get the current user object
            $current_user = wp_get_current_user();

            // Check if the user is logged in
            if ($current_user->ID != 0) {
                // Get the email address
                $user_email = $current_user->user_email;

                // Output or use the email address as needed
                echo '(as <b>' . esc_html( $user_email ) . '</b>) ';
            }
            
            ?> <a href="<?php echo esc_attr( wp_logout_url( home_url( 'appsumo' ) ) ); ?>" class="appsumo_btn_switch_to_login">Logout?</a></p>
      </div>
    </div>
    <div class="appsumo-right-graph appsumo-w-50">
    	<div class="appsumo-right-graph-desc">
      		<img src="<?php echo esc_attr( plugins_url('../img/image.png', __FILE__) ); ?>" alt="login">
      	</div>
    </div>
  </div>
</div>