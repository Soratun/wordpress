<?php
/**
 * Class for license
 */

defined( 'ABSPATH' ) || exit;

class EventPrime_License {
    // activate license
    public function ep_activate_license($license,$item_id,$prefix)
    {
        $return = array();
        $error_status = '';
        $ep_store_url = "https://theeventprime.com/";
        $home_url = home_url();
        // data to send in our API request
           $api_params = array(
               'edd_action' => 'activate_license',
               'license'    => $license,
               'item_id'    => $item_id,
               'url'        => $home_url
           );

           // Call the custom API.
           $response = wp_remote_post( $ep_store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
           
            // make sure the response came back okay
            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : esc_html__( 'An error occurred, please try again.' );
            } else {
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                $error_status = (isset($license_data->error))?$license_data->error:'';
                if ( false === $license_data->success ) {
                    if( isset( $license_data->error ) ){
                        switch( $license_data->error ) {
                            case 'expired' :
                                $message = sprintf(
                                    esc_html__( 'Your license key expired on %s.', 'profilegrid-user-profiles-groups-and-communities' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;
                            case 'revoked' :
                                $message = esc_html__( 'Your license key has been disabled.' , 'profilegrid-user-profiles-groups-and-communities' );
                                break;
                            case 'missing' :
                                $message = esc_html__( 'Your license key is invalid.' , 'profilegrid-user-profiles-groups-and-communities' );
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = esc_html__( 'Your license is not active for this URL.' , 'profilegrid-user-profiles-groups-and-communities' );
                                break;
                            case 'item_name_mismatch' :
                                $message = esc_html__( 'The key you have entered seems to be invalid. Please verify and try again.', 'profilegrid-user-profiles-groups-and-communities'  );
                                break;
                            case 'no_activations_left':
                                $message = esc_html__( 'Your license key has reached its activation limit.', 'profilegrid-user-profiles-groups-and-communities'  );
                                break;
                            default :
                                $message = esc_html__( 'The key you have entered seems to be invalid. Please verify and try again.', 'profilegrid-user-profiles-groups-and-communities'  );
                                break;
                        }
                    }
                }
            }

            // Check if anything passed on a message constituting a failure
            if ( ! empty( $message ) ) {
            }
            
            if( !empty( $license_data ) ){
                // $license_data->license will be either "valid" or "invalid"
                $license_status  = ( isset( $license_data->license ) && ! empty( $license_data->license ) && $license_data->license == 'valid' ) ? $license_data->license : '';
                $license_response  = ( isset( $license_data ) && ! empty( $license_data ) ) ? $license_data : '';
                update_option( $prefix.'_license_status', $license_status );
                update_option( $prefix.'_license_response', $license_response );
                update_option( $prefix.'_item_id', $item_id );
            }
            
            if( isset( $license_data->expires ) && ! empty( $license_data->expires ) ) {
                if( $license_data->expires == 'lifetime' ){
                    $expire_date = esc_html__( 'Your license key is activated for lifetime', 'profilegrid-user-profiles-groups-and-communities' );
                }else{
                    $expire_date = sprintf( esc_html__( 'Your license Key expires on %s.', 'profilegrid-user-profiles-groups-and-communities' ), gmdate( 'F d, Y', strtotime($license_data->expires) ) );
                }
            }else{
                $expire_date = '';
            }   
            
            ob_start(); ?>
                <?php if( isset( $license_data->license ) && $license_data->license == 'valid' ){ ?>
                    <button type="button" class="button action pg-my-2 pg_license_deactivate" data-prefix="<?php echo esc_attr( $item_id ); ?>" name="<?php echo esc_attr( $prefix); ?>_license_deactivate" id="<?php echo esc_attr( $prefix ); ?>_license_deactivate" value="<?php esc_html_e( 'Deactivate License', 'profilegrid-user-profiles-groups-and-communities' );?>"><?php esc_html_e( 'Deactivate License', 'profilegrid-user-profiles-groups-and-communities' );?></button>
                <?php }elseif( isset( $license_data->license ) && $license_data->license == 'invalid' ){ ?>
                    <button type="button" class="button action pg-my-2 pg_license_activate" data-prefix="<?php echo esc_attr( $item_id ); ?>" name="<?php echo esc_attr( $prefix ); ?>_license_activate" id="<?php echo esc_attr( $prefix ); ?>_license_activate" value="<?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?>"><?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?></button>
                <?php }else{ ?>
                    <button type="button" class="button action pg-my-2 pg_license_activate" data-prefix="<?php echo esc_attr($item_id); ?>" name="<?php echo esc_attr( $prefix ); ?>_license_activate" id="<?php echo esc_attr( $prefix ); ?>_license_activate" value="<?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?>"><?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?></button>
                <?php } ?>      
            <?php
            $license_status_block = ob_get_clean();

            if ( empty( $message ) || $license_data->license == 'valid' ) {
                if( isset( $license_data->license ) && $license_data->license == 'valid' ){
                    $message = esc_html__( 'Your License key is activated.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
                if( isset( $license_data->license ) && $license_data->license == 'invalid' ){
                    $message = esc_html__( 'Your license key is invalid.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
                if( isset( $license_data->license ) && $license_data->license == 'deactivated' ){
                    $message = esc_html__( 'Your License key is deactivated.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
                if( isset( $license_data->license ) && $license_data->license == 'failed' ){
                    $message = esc_html__( 'Your License key deactivation failed. Please try after some time.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
            }

            $return = array( 'license_data' => $license_data, 'license_status_block' => $license_status_block, 'expire_date' => $expire_date, 'message' => $message );
        
            return $return;
           
    }

      // deactivate license
    public function ep_deactivate_license($license,$item_id,$prefix)
    {
        $return = array();
        $error_status = '';
        $ep_store_url = "https://theeventprime.com/";
        $home_url = home_url();
        // data to send in our API request
           $api_params = array(
               'edd_action' => 'deactivate_license',
               'license'    => $license,
               'item_id'    => $item_id,
               'url'        => $home_url
           );
        
         // Call the custom API.
            $response = wp_remote_post( $ep_store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
            
            // make sure the response came back okay
            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : esc_html__( 'An error occurred, please try again.' );
            } else {
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                $error_status = (isset($license_data->error))?$license_data->error:'';
                if ( false === $license_data->success ) {
                    if( isset( $license_data->error ) ){
                        switch( $license_data->error ) {
                            case 'expired' :
                                $message = sprintf(
                                    esc_html__( 'Your license key expired on %s.' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;
                            case 'revoked' :
                                $message = esc_html__( 'Your license key has been disabled.', 'profilegrid-user-profiles-groups-and-communities'   );
                                break;
                            case 'missing' :
                                $message = esc_html__( 'Your license key is invalid.', 'profilegrid-user-profiles-groups-and-communities'   );
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = esc_html__( 'Your license is not active for this URL.', 'profilegrid-user-profiles-groups-and-communities'   );
                                break;
                            case 'item_name_mismatch' :
                                $message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'profilegrid-user-profiles-groups-and-communities'   ), $item_name );
                                break;
                            case 'no_activations_left':
                                $message = esc_html__( 'Your license key has reached its activation limit.', 'profilegrid-user-profiles-groups-and-communities'   );
                                break;
                            default :
                                $message = esc_html__( 'An error occurred, please try again.', 'profilegrid-user-profiles-groups-and-communities'   );
                                break;
                        }
                    }
                }
            }

            // Check if anything passed on a message constituting a failure
            if ( ! empty( $message ) ) {

            }  
            
            if( !empty( $license_data ) ){
                // $license_data->license will be either "valid" or "invalid"
                $license_status  = ( isset( $license_data->license ) && ! empty( $license_data->license ) && $license_data->license == 'valid' ) ? $license_data->license : '';
                $license_response  = ( isset( $license_data ) && ! empty( $license_data ) ) ? $license_data : '';
                update_option( $prefix.'_license_status', $license_status );
                update_option( $prefix.'_license_response', $license_response );
                update_option( $prefix.'_item_id', $item_id );
            }
            
            if( isset( $license_data->expires ) && ! empty( $license_data->expires ) ) {
                if( $license_data->expires == 'lifetime' ){
                    $expire_date = esc_html__( 'Your license key is activated for lifetime', 'profilegrid-user-profiles-groups-and-communities' );
                }else{
                    $expire_date = sprintf( esc_html__( 'Your License Key expires on %s.', 'profilegrid-user-profiles-groups-and-communities' ), gmdate('F d, Y', strtotime( $license_data->expires ) ) );
                }
            }else{
                $expire_date = '';
            }           
            
            ob_start(); ?>
                <?php if( isset( $license_data->license ) && $license_data->license == 'valid' ){ ?>
                    <button type="button" class="button action ep-my-2 pg_license_deactivate" data-prefix="<?php echo esc_attr( $item_id ); ?>" name="<?php echo esc_attr( $prefix ); ?>_license_deactivate" id="<?php echo esc_attr( $prefix ); ?>_license_deactivate" value="<?php esc_html_e( 'Deactivate License', 'profilegrid-user-profiles-groups-and-communities' );?>"><?php esc_html_e( 'Deactivate License', 'profilegrid-user-profiles-groups-and-communities' );?></button>
                <?php }elseif( isset( $license_data->license ) && $license_data->license == 'invalid' ){ ?>
                    <button type="button" class="button action ep-my-2 pg_license_activate" data-prefix="<?php echo esc_attr($item_id ); ?>" name="<?php echo esc_attr( $prefix ); ?>_license_activate" id="<?php echo esc_attr($prefix); ?>_license_activate" value="<?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?>"><?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?></button>
                <?php }elseif( isset( $license_data->license ) && $license_data->license == 'failed' ){ ?>
                    <button type="button" class="button action ep-my-2 pg_license_activate" data-prefix="<?php echo esc_attr( $item_id); ?>" name="<?php echo esc_attr($prefix); ?>_license_activate" id="<?php echo esc_attr( $prefix); ?>_license_activate" value="<?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?>"><?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?></button>
                <?php }else{ ?>
                    <button type="button" class="button action ep-my-2 pg_license_activate" data-prefix="<?php echo esc_attr($item_id); ?>" name="<?php echo esc_attr($prefix); ?>_license_activate" id="<?php echo esc_attr($prefix); ?>_license_activate" value="<?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?>"><?php esc_html_e( 'Activate License', 'profilegrid-user-profiles-groups-and-communities' );?></button>
                <?php } ?>    
            <?php
            $license_status_block = ob_get_clean();

            if ( empty( $message ) || $license_data->license == 'valid' ) {
                if( isset( $license_data->license ) && $license_data->license == 'valid' ){
                    $message = esc_html__( 'Your License key is activated.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
                if( isset( $license_data->license ) && $license_data->license == 'invalid' ){
                    $message = esc_html__( 'Your license key is invalid.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
                if( isset( $license_data->license ) && $license_data->license == 'deactivated' ){
                    $message = esc_html__( 'Your License key is deactivated.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
                if( isset( $license_data->license ) && $license_data->license == 'failed' ){
                    $message = esc_html__( 'Your License key deactivation failed. Please try after some time.', 'profilegrid-user-profiles-groups-and-communities'  );
                }
            }

            $return = array( 'license_data' => $license_data, 'license_status_block' => $license_status_block, 'expire_date' => $expire_date, 'message' => $message );
          

            return $return;
          
    }
    
     public function ep_get_activate_extensions() {
        $extensions = array(
            'Eventprime_Event_Import_Export'=>array(849,'Events Import Export'),
            'Eventprime_Woocommerce_Integration'=>array(526,'WooCommerce Integration'),
            'Eventprime_Elementor_Integration'=>array(22432,'Elementor Integration'),
            'Eventprime_Attendees_List'=>array(966,'Attendees List'),
            'Eventprime_Live_Seating'=>array(870,'Live Seating'),
            'Eventprime_Event_Invoices'=>array(867,'Invoices'),
            'Eventprime_Event_Coupons'=>array(846,'Coupon Code'),
            'Eventprime_Guest_Booking'=>array(864,'Guest Booking'),
            'Eventprime_Event_Sponsor'=>array(855,'Events Sponsors'),
            'Eventprime_Admin_Attendee_Booking'=>array(858,'Admin Attendee Booking'),
            'Eventprime_List_Widgets'=>array(852,'Events List Widgets'),
            'Eventprime_Event_Tickets'=>array(861,'Events Tickets'),
            'Eventprime_Advanced_Reports'=>array(21781,'Advanced Reports'),
            'Eventprime_Advanced_Checkout_Fields'=>array(22434,'Advanced Checkout Fields'),
            'Eventprime_Ratings_And_Reviews'=>array(25465,'Ratings and Reviews'),
            'Eventprime_Event_Feedback'=>array(22845,'User Feedback'),
            'Eventprime_RSVP'=>array(23282,'RSVP'),
            'Eventprime_Twilio_Text_Notification'=>array(882,'Twilio Text Notifications'),
            'Eventprime_Event_Mailpoet'=>array(873,'MailPoet Integration'),
            'Eventprime_Zoom_Meetings'=>array(888,'Zoom Integration'),
            'Eventprime_Zapier_Integration'=>array(885,'Zapier Integration'),
            'Eventprime_Mailchimp_Integration'=>array(22842,'Mailchimp Integration'),
            'Eventprime_Event_Stripe'=>array(879,'Stripe Payment'),
            'Eventprime_Offline'=>array(876,'Offline Payment'),
            'Eventprime_Woocommerce_Checkout_Integration'=>array(23284,'WooCommerce Checkout'),
            'Eventprime_Attendee_Event_Check_In'=>array(30503,'Attendee Event Check In'),
        );
        
        $activate = array();
                foreach ( $extensions as $key=>$value ) {
			if ( class_exists( $key ) ) {
                            $activate[$key] = $value;
			}
		}
		return $activate;
    }

    public function ep_get_premium_bundle_id($key='ep_free')
    {
        if(empty($key) || $key==null)
        {
            return '';
        }
        $premium = array('ep_free'=>23935,
            'ep_premium'=>19088,
            'ep_professional'=>23912,
            'ep_essential'=>23902,
            'ep_premium_plus'=>21789,
            'ep_metabundle'=>22462,
            'ep_metabundle_plus'=>21790
            );
        
        return $premium[$key];
        
    }
    
    public function ep_get_license_detail($key,$options)
    {
        
        $license_key =$key.'_license_key';
        $license_status = $key.'_license_status';
        $license_response = $key.'_license_response';
        $license_option_value = $key.'_license_option_value';
        $license_item_id = $key.'_item_id';
        $license = new stdClass();
        
        $license->license_key = get_option($license_key, (!empty($options->$license_key))?$options->$license_key:'');
        $license->license_status = get_option($license_status, (!empty($options->$license_status))?$options->$license_status:'');
        $license->license_response = get_option($license_response, (!empty($options->$license_response))?$options->$license_response:'');
        $license->license_option_value = get_option($license_option_value, (!empty($options->$license_option_value))?$options->$license_option_value:'');
        $license->item_id = get_option($license_item_id, (!empty($options->$license_item_id))?$options->$license_item_id:'');
        
        return $license;
        
    }
    
}