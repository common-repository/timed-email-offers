=== Timed Email Offers ===
Contributors: jkohlbach, RymeraWebCo, rymera01
Donate link:
Tags: woocommerce offers, woocommerce timed offers, woocommerce email offer, woocommerce timed email offers, timed email offers, sales offers, automatic offers, follow up offers
Requires at least: 3.4
Tested up to: 4.7.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Increase repeat orders with automatic follow-up offer emails in WooCommerce

== DESCRIPTION ==

**VISIT OUR WEBSITE:** [Advanced Coupons](https://advancedcouponsplugin.com/?utm_source=WordPressOrg&utm_medium=TEOPlugin&utm_campaign=PluginListing)

**FREE VERSION:**

Timed Email Offers gives WooCommerce store owners the ability to send automatic offer emails based on what their customers ordered last time. It's a super powerful way to get customers back to the store to order again and encourage a more consistent purchasing cycle.

1. Detect what your customer ordered last time
1. Automatically send a personalised offer at a time you decide
1. Automatically add products and coupons to the cart ready for them to checkout

Some features at a glance:

**CART CONTENTS DETECTION**

Advanced conditions that let you define exactly when a Timed Email Offer should be sent to a customer.

A few examples:

1. If they ordered "Product A" and it's been 2 weeks, send them an offer for "Product A Bulk Pack" along with a 20% off coupon.
1. If they ordered "Product B" and it's been 1 week, send them a 10% off coupon for their next order.
1. If they ordered both "Product C" and "Product A" in their last order, send them an offer for Product B with a 15% off coupon.

The Premium version extends the conditions you can use. Detect when items are purchased from a particular Product Category, whether the customer has a particular User Role (great for promoting to Wholesale or special customers), if the customer has ordered certain Products in the past, order subtotal amount threshold and more!

**AUTOMATICALLY ADDS PRODUCTS & COUPONS**

Define which products and/or coupons should be added to the cart when the customer accepts the Offer.

The items are automatically added to the shopping cart and they are presented with their complete order ready for purchase.

**MULTIPLE OFFERS**

Lets you have multiple timed offers going out so you can capture different ordering scenarios and make as many personalised offers as you like.

Your customers will love the insightful offers that are tailored to them based on what they ordered previously.

**WORKS OUT OF THE BOX**

No funny scripts or templates to setup. It just works straight out of the box with WooCommerce and will work with most themes.

We're also constantly testing new themes and plugin combinations to ensure maximum compatibility.

**PREMIUM ADD-ON**

Click here for information about the Timed Email Offers Premium add-on:
https://marketingsuiteplugin.com/product/timed-email-offers/

Some premium features at a glance:

1. Loads more Offer conditions to test (check for product categories in the previous order, whether they've ordered a certain product before, customer user role, order subtotal and loads more!)
1. New WooCommerce reports focused around Offer performance
1. Generate personalised coupon codes to be created on the fly
1. Add an expiry date to Offers
1. ... and much more!

We also have a whole bundle of marketing automation plugins available at:
https://marketingsuiteplugin.com

== Installation ==

1. Upload the `timed-email-offers/` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Setup your first popup Offer under WooCommerce->Timed Email Offers and follow the instructions
1. ... Profit!

== Frequently asked questions ==

We'll be publishing a list of frequently asked questions in our knowledge base soon.

== Screenshots ==

Plenty of amazing screenshots for this plugin and more over at:
https://marketingsuiteplugin.com/product/timed-email-offers/

== Changelog ==

= 1.2.2 =
* Improvement: Add compatibility with upcoming WooCommerce version 2.7.0
* Bug Fix: Notices on debug.log

= 1.2.1 =
* Feature: Additional hover help in settings
* Feature: Adjust offer timeout period setting
* Feature: Only show pending recipients initially
* Feature: Add offer order on quick edit screen
* Feature: Tidy up codebase
* Bug Fix: Make sure activation codebase is executed properly

= 1.2.0 =
* Feature: Improve internal data structure to handle large loads of offers and recipients.
* Feature: WooCommerce Product Bundles Integration (min required 5.0.1).
* Feature: WooCommerce Composite Products Integration (min required 3.7.1).
* Feature: Pixel tracking for offer emails.
* Feature: Add offers priority order.
* Feature: Add plugin's tour.
* Feature: Custom in plugin error logging.
* Improvement: Link up order number to actual order page on recipients table.
* Improvement: Clean up plugin custom resources on uninstallation and clean up plugin options is enabled.
* Improvement: Tidy up code base.
* Bug Fix: Console errors on offer cpt.
* Bug Fix: Can't add product rule with more than 0 quantity

= 1.1.1 = 
* Bug Fix: Logic error in checking product in order offer condition

= 1.1.0 =
* Feature: Global settings to only unschedule emails when offer is accepted AND an order is processed
* Improvement: Tidy up default email template content
* Improvement: Add settings link to plugin listings
* Improvement: Only invalidate offer links if offer converted an order
* Improvement: Replace all instances of .delegate() with .on() ( JQuery )
* Improvement: Improve and Tidy up codebase
* Bug Fix: WP Editor on email template not getting contents properly when switching between visual and text editor mode
* Bug Fix: Offer links not working properly when added on the wp editor without protocol and has extra trailing forward slash
* Bug Fix: PHP notice when opening recipient detail popup
* Bug Fix: Notice when saving other settings pages
* Bug Fix: Handle invalid or unavailable offers when links are accessed

= 1.0.0 =
* Initial version

== Upgrade notice ==

There is a new version of Timed Email Offers available.
