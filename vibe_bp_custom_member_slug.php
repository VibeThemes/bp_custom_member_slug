<?php
/**
 * Plugin Name: Vibe BP Custom Member Slug
 * Plugin URI:  https://vibethemes.com/
 * Description: Allow buddypress users to customise their profile slugs
 * Author:      VibeThemes,ava
 * Author URI:  https://vibethemes.com/
 * Version:     1.0
 * Text Domain: vibe-bp-cms
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
*/

//if ( ! defined( 'ABSPATH' ) ) exit;

class Vibe_BP_Custom_Member_Slug{

    public static $instance;
    
    public static function init(){

      	if ( is_null( self::$instance ) )
          self::$instance = new Vibe_BP_Custom_Member_Slug();

      	return self::$instance;
    }

    private function __construct(){
    	add_action('bp_core_general_settings_before_submit',array($this,'add_custom_slug_field'));
    	add_action('bp_core_general_settings_after_save',array($this,'save'));
    }


    function add_custom_slug_field(){
    	$data = get_userdata(bp_displayed_user_id());
    	?>
    	<label for="email"><?php _ex('Account Slug','members profile settings','vibe-bp-cms'); ?></label>
    	<input type="text" name="user_nicename" id="user_nicename" value="<?php echo $data->user_nicename; ?>" class="settings-input">
    	<hr />
    	<?php
    }

    function save(){
    	$user_nicename = $_POST['user_nicename'];


    	$data = get_userdata(bp_displayed_user_id());
    	
    	$bp = buddypress();
    	
    	// Bail if no submit action.
		if ( ! isset( $_POST['submit'] ) )
			return;

		// Bail if not in settings.
		if ( ! bp_is_settings_component() || ! bp_is_current_action( 'general' ) )
			return;

    	//if(empty($bp->template_message) || $bp->template_message_type == 'success'){
	    	if(!empty($user_nicename) && $data->user_nicename != $user_nicename){
	 			$user_id = wp_update_user( array( 'ID' => bp_displayed_user_id(), 'user_nicename' => $user_nicename ) );
	 			
				if ( is_wp_error( $user_id ) ) {
					$bp->template_message .=$user_id->get_error_message();
					$bp->template_message_type = 'error';
				} else {
					
					$bp->displayed_user->domain = str_replace($data->user_nicename,$user_nicename,$bp->displayed_user->domain);
					
					// Success!
					bp_core_add_message(_x('Account Slug successfully changed','success message','vibe-bp-cms'),'success');
				}
	    	}
	    //}
    }
}

Vibe_BP_Custom_Member_Slug::init();