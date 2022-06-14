<?php
 /* Plugin Name: WooCommerce Extra Rate
 * Plugin URI: https://gist.github.com/BFTrick/b5e3afa6f4f83ba2e54a
 * Description: A plugin for add extra rate in woocommerce order.
 * Author: Komal Dudhat
 * Author URI: http://speakinginbytes.com/
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

class WC_settings_extra_rate {

    /* Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_extra_rate', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_extra_rate', __CLASS__ . '::update_settings' );
    }
    
    
    /* Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_extra_rate'] = __( 'Extra Rate', 'woocommerce-settings-extra-rate' );
        return $settings_tabs;
    }


    /* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }


    /* Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }


    /* Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Extra Rate', 'woocommerce-settings-extra-rate' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_extra_rate_section_title'
            ),
            'title' => array(
                'name' => __( 'Title', 'woocommerce-settings-extra-rate' ),
                'type' => 'text',
                'desc' => __( 'This is rate title', 'woocommerce-settings-extra-rate' ),
                'id'   => 'wc_settings_tab_rate_title'
            ),
            'rate_type' => array(
                'name' => __( 'Rate Type', 'woocommerce-settings-extra-rate' ),
                'type' => 'select',
                'options' => array("flat_rate" => "Flat Rate","per_rate" => "Percentage"),
                'desc' => __( 'This is rate Type', 'woocommerce-settings-extra-rate' ),
                'id'   => 'wc_settings_tab_rate_type'
            ),
            'rate' => array(
                'name' => __( 'Rate', 'woocommerce-settings-extra-rate' ),
                'type' => 'number',
                'desc' => __( 'This is rate amount in % (for Percentage rate type) or $ (for flat rate)', 'woocommerce-settings-extra-rate' ),
                'id'   => 'wc_settings_tab_rate_amount'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_extra_rate_section_end'
            )
        );

        return apply_filters( 'wc_settings_extra_rate_settings', $settings );
    }

}

WC_settings_extra_rate::init();

add_action('woocommerce_cart_calculate_fees', function() {
	if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}
	
    $rate_type = get_option( 'wc_settings_tab_rate_type' );
	$rate = get_option( 'wc_settings_tab_rate_amount' );
    $title = get_option( 'wc_settings_tab_rate_title' );
    if($rate_type == "per_rate"){
	    $percentage_fee = (WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total()) * $rate / 100;
	    WC()->cart->add_fee(__($title."(" . $rate . "%)", 'txtdomain'), $percentage_fee);
    }
    else
    {
        $percentage_fee =  $rate;
	    WC()->cart->add_fee(__($title."($" . $rate . ")", 'txtdomain'), $percentage_fee);
    }
});
?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>
$(document).ready(function() {
    $('#wc_settings_tab_rate_type').on('change', function() {
        var ratetype = $(this).children("option:selected").val();
        if(ratetype == "per_rate")
        {
            $("#wc_settings_tab_rate_amount").val(1);
            $("#wc_settings_tab_rate_amount").attr({
            "max" : 10,        
            "min" : 1          
            });
        }
        else
        {
            $("#wc_settings_tab_rate_amount").removeAttr("max");
            $("#wc_settings_tab_rate_amount").removeAttr("min");
        }
    });
});
</script>