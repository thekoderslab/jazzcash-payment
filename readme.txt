=== WooCommerce JazzCash Gateway Plugin ===
Contributors: AKSA, Jazz
Donate link: https://www.aksa-sds.com
Tags: woocommerce,payment gateway, woocommerce extension, JazzCash payment,payment, payment option, custom payment
Requires at least: 5.8.1
Tested up to: 5.6.0
Stable tag: 1.0.0
Requires PHP: 5.6
WC requires at least: 5.6.0
WC tested up to: 7.6.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Allows WooCommerce to accept payments using JazzCash gateway.

 == Upgrade Notice ==

This is an upgrade of WooCommerce JazzCash Gateway Plugin version 1.0

== Description ==

> **Support policy**
>
> * Should you need assistance, please open a support request in the **[Support section, above](https://wordpress.org/support/plugin/woocommerce-jazzcash-gateway)**, and we will look into it as soon as possible (usually within a couple of days).
> * If you need support urgently, or you require a customisation, you can avail of our paid support and consultancy services. To do so, please contact us [using our dedicated portal](https://www.jazzcash.com.pk/), specifying that you are using our WooCommerce JazzCash plugin. You will receive direct assistance from our team, who will troubleshoot your site and help you to make it work smoothly. We can also help you with installation, customisation and development of new features.

The plugin will add a new payment gateway called JazzCash, which will add support for the JazzCash payment gateway. Upon checkout, your customers will be redirected to a secure portal where they can complete their payment. Both standard and Quick Checkout modes are supported. - See more at: http://www.jazzcash.com.pk/digital-payments/online-payments/

= IMPORTANT =
**Make sure that you read and understand the plugin requirements and the FAQ before installing this plugin**. Almost all support requests we receive are related to missing requirements, and incomplete reading of the message that is displayed when such requirements are not met.

= Included localisations =
* English (GB)

= Requirements =
* A JazzCash Merchant account. The plugin was not tested with personal accounts and might not work correctly with them.
* WordPress 5.8.1
* PHP 7 or greater
* WooCommerce  5.6.0

= Current limitations =
* Plugin does not yet support pre-authorisation or subscriptions.

= Notes =
* This plugin is provided as a **free** alternative to the many commercial plugins that add the JazzCash payment gateway to WooCommerce, and it's not automatically covered by free support. See FAQ for more details.

== Installation ==

1. Extract the zip file and drop the contents in the ```wp-content/plugins/``` directory of your WordPress installation.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to ```WooCommerce > Settings > Payment Gateways > JazzCash``` to configure the plugin.

For more information about installation and management of plugins, please refer to [WordPress documentation](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

= Setup =
On the settings page, the following settings are required:

* **MercahntID**: this is the ID associated to your JazzCash merchant account.
* **Password**: this is the password of your JazzCash account.
* **ActionUrl**: this is the action url provided by JazzCash support.
* **ReturnUrl**: http://ip:port/wordpress_directory/?wc-api=jazzcashresponse (from woocommerce_api_jazzcashresponse add_action in constructor).
* **WSDLUrl**: http://ip:port/wordpress_directory/wp-content/plugins/jazzcash/wsdl/?wsdl

If you wish to get more details about JazzCash, please refer to [JazzCash website](https://www.jazzcash.com.pk/).

== Screenshots ==

1. Checkout Page Preview.
2. Payment Gateway Settings Page.

== Changelog ==

= 1.0 =
* Initial release.

== Upgrade Notice == 
* Initial release.


== Frequently Asked Questions == 

How do I add a FAQ in WooCommerce?

Integration Steps
There are two steps in integrating JazzCash Payment module as listed below:
1.Installation
2.Configuration
The first step in integration is installing the module. Next is to Activate it and then Configure it with the configuration data provided by the JazzCash.