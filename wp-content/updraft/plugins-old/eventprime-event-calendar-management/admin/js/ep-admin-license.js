jQuery( function( $ ) {
   
    $(".ep-license-block").keyup(function(e) {
        var prefix = $(this).data('key');
        
        var license_key_length = $('#' + prefix + '_license_key' ).val();
        // let child_length = $('.'+ prefix +  ' .' + prefix + '-license-status-block').children.length;
        if( license_key_length.length === 32 && prefix != 'undefined' && prefix != '' ){
            $('#' + prefix + '_license_activate' ).show();
        }
    });

    $(".ep-license-block").keydown(function(e) {
        var prefix = $(this).data('key');    
       
        if( prefix != 'undefined' && prefix != '' ){
            $('#' + prefix + '_license_activate' ).hide();
        }
    });

    $( document ).on( 'click', '.ep_license_activate', function(e) {
        e.preventDefault();
        var prefix = $(this).attr('data-key');
        let key = $(this).attr('data-prefix');
        
        var license_key = $('#'+prefix + '_license_key').val();
        var ep_license_activate = $('#' + prefix + '_license_activate').val();
        
        $( '.'+ prefix +  ' .license-expire-date' ).html( '' );
            $( '.'+ prefix +  ' .' + prefix + '-license-status-block .ep_license_activate' ).addClass( 'disabled' );
    
        let data = { 
            'action': 'ep_eventprime_activate_license', 
            'nonce': ep_admin_license_settings.ep_license_nonce,
            'ep_license_activate' : ep_license_activate,
            'ep_license' : license_key,
            'ep_item_id' : key, 
            'ep_item_key': prefix
        };
        console.log(data);
        $.ajax({
            type: 'POST', 
            url :  get_ajax_url(),
            data: data,
            success: function( response ) {
                $( '.'+ prefix +  ' .' + prefix + '-license-status-block .ep_license_activate' ).removeClass( 'disabled' );
                if( response.data.license_data.success === true )
                {
                    show_toast( 'success', response.data.message );
                    // update license activate/deactivate button
                    if( response.data.license_status_block != '' && response.data.license_status_block != 'undefined' ){
                        if( prefix == 'ep_free' || prefix == 'ep_professional' || prefix == 'ep_essential' || prefix == 'ep_metabundle' || prefix == 'ep_metabundle_plus' || prefix == 'ep_premium_plus' ){
                            $('.ep_premium .ep_premium-license-status-block').html(response.data.license_status_block);
                        }else{
                            $('.'+ prefix +  ' .' + prefix + '-license-status-block').html(response.data.license_status_block);
                        }
                    }
                    // update license expiry date
                    if( prefix == 'ep_free' || prefix == 'ep_professional' || prefix == 'ep_essential' || prefix == 'ep_metabundle' || prefix == 'ep_metabundle_plus' || prefix == 'ep_premium_plus' ){
                        $('.ep_premium .license-expire-date').html(response.data.expire_date);
                    }else{
                        $('.' + prefix +  ' .license-expire-date').html(response.data.expire_date);
                    }
                }else{
                    show_toast( 'error', response.data.message );
                }
            
            }
        });
    });

    $( document ).on( 'click', '.ep_license_deactivate', function(e) {
        e.preventDefault();
        var prefix = $(this).attr('data-key');
        let key = $(this).attr('data-prefix');
        var license_key = $('#'+prefix + '_license_key').val();
        var ep_license_deactivate = $('#'+ prefix + '_license_deactivate').val();
        
         $( '.'+ prefix +  ' .license-expire-date' ).html( '' );
         $( '.'+ prefix +  ' .' + prefix + '-license-status-block .ep_license_deactivate' ).addClass( 'disabled' );
        
       
        var data = { 
            action: 'ep_eventprime_deactivate_license', 
            nonce: ep_admin_license_settings.ep_license_nonce,
            ep_license_deactivate : ep_license_deactivate,
            'ep_license' : license_key,
            'ep_item_id' : key, 
            'ep_item_key': prefix
        };
        $.ajax({
            type: 'POST', 
            url :  get_ajax_url(),
            data: data,
            success: function( response ) {
                $( '.'+ prefix +  ' .' + prefix + '-license-status-block .ep_license_deactivate' ).removeClass( 'disabled' );
                if( response.data.license_data.success === true )
                {
                    show_toast( 'success', response.data.message );
                    // update license activate/deactivate button
                    if( response.data.license_status_block != '' && response.data.license_status_block != 'undefined' ){
                        if( prefix == 'ep_free' || prefix == 'ep_professional' || prefix == 'ep_essential' || prefix == 'ep_metabundle' || prefix == 'ep_metabundle_plus' || prefix == 'ep_premium_plus' ){
                            $('.ep_premium  .ep_premium-license-status-block').html(response.data.license_status_block);
                        }else{
                            $('.'+ prefix +  ' .' + prefix + '-license-status-block').html(response.data.license_status_block);
                        }
                    }
                    // update license expiry date
                    // $('.'+prefix+ ' .license-expire-date').html(response.data.expire_date);
                    if( prefix == 'ep_free' || prefix == 'ep_professional' || prefix == 'ep_essential' || prefix == 'ep_metabundle' || prefix == 'ep_metabundle_plus' || prefix == 'ep_premium_plus' ){
                        $('.ep_premium .license-expire-date').html('');
                    }else{
                        $('.'+prefix+ ' .license-expire-date').html('');
                    }
                }else{
                    show_toast( 'error', response.data.message );
                    if( response.data.license_status_block != '' && response.data.license_status_block != 'undefined' ){
                        if( prefix == 'ep_free' || prefix == 'ep_professional' || prefix == 'ep_essential' || prefix == 'ep_metabundle' || prefix == 'ep_metabundle_plus' || prefix == 'ep_premium_plus' ){
                            $('.ep_premium .ep_premium-license-status-block').html(response.data.license_status_block);
                        }else{
                            $('.'+ prefix +  ' .' + prefix + '-license-status-block').html(response.data.license_status_block);
                        }
                    }
                    // if( response.data.expire_date != '' && response.data.expire_date != 'undefined' ){
                    //     // update license expiry date
                    //     $('.'+prefix+ ' .license-expire-date').html(response.data.expire_date);
                    // }
                    if( prefix == 'ep_free' || prefix == 'ep_professional' || prefix == 'ep_essential' || prefix == 'ep_metabundle' || prefix == 'ep_metabundle_plus' || prefix == 'ep_premium_plus' ){
                        $('.ep_premium .license-expire-date').html(''); 
                    }else{
                        $('.'+prefix+ ' .license-expire-date').html(''); 
                    }
                }
            }
        });
    });


});

function ep_on_change_bundle(value)
{
    jQuery('#ep_premium_license_key').attr('data-prefix',value);
    jQuery('.ep_premium-license-status-block button').attr('data-prefix',value);
}


jQuery(document).ready(function () {
    jQuery('.ep-tooltips').append("<span></span>");
    jQuery('.ep-tooltips:not([tooltip-position])').attr('tooltip-position', 'bottom');


    jQuery(".ep-tooltips").mouseenter(function () {
        jQuery(this).find('span').empty().append(jQuery(this).attr('tooltip'));
    });
});