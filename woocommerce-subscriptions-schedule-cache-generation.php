<?php
/*
 * Plugin Name: WooCommerce Subscriptions - Schedule Cache Generation
 * Plugin URI: https://github.com/Prospress/woocommerce-subscriptions-schedule-cache-generation/
 * Description: Schedule the 'wcs_generate_related_order_cache' and 'wcs_generate_customer_subscription_cache' actions if they are not already scheduled.
 * Author: Prospress Inc.
 * Author URI: https://prospress.com/
 * License: GPLv3
 * Version: 1.0.0
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * GitHub Plugin URI: Prospress/woocommerce-subscriptions-schedule-cache-generation
 * GitHub Branch: master
 *
 * Copyright 2018 Prospress, Inc.  (email : freedoms@prospress.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		WooCommerce Subscriptions - Schedule Cache Generation
 * @author		Prospress Inc.
 * @since		1.0
 */

require_once( 'includes/class-pp-dependencies.php' );

if ( false === PP_Dependencies::is_woocommerce_active( '3.0' ) ) {
	PP_Dependencies::enqueue_admin_notice( 'WooCommerce Subscriptions - Schedule Cache Generation', 'WooCommerce', '3.0' );
	return;
}

if ( false === PP_Dependencies::is_subscriptions_active( '2.1' ) ) {
	PP_Dependencies::enqueue_admin_notice( 'WooCommerce Subscriptions - Schedule Cache Generation', 'WooCommerce Subscriptions', '2.1' );
	return;
}

/**
 * Whenever 'action_scheduler_run_queue' is run, make sure that the cache generation actions are 
 * scheduled, and if they aren't, schedule them.
 *
 * Using 'action_scheduler_run_queue' prevents clogging requests, as would be the case if using
 *  'admin_init' or worse, 'init'.
 */
function wcsscg_maybe_schedule_cache_generation_actions() {
	$cache_generation_actions = array(
		'wcs_generate_related_order_cache',
		'wcs_generate_customer_subscription_cache'
	);

	foreach ( $cache_generation_actions as $cache_generation_action ) {
		if ( false === wc_next_scheduled_action( $cache_generation_action ) ) {
			wc_schedule_single_action( gmdate( 'U' ) + 120, $cache_generation_action );
		}
	}
}
add_action( 'action_scheduler_run_queue', 'wcsscg_maybe_schedule_cache_generation_actions' );
