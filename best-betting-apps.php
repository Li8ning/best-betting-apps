<?php
/**
 * Plugin Name:       Best Betting Apps
 * Plugin URI:        https://github.com/Li8ning/best-betting-apps
 * Description:       A plugin that works with a shortcode to display data from a JSON file.
 * Version:           1.0.0
 * Author:            Dharmrajsinh Jadeja
 * Author URI:        https://github.com/Li8ning/
 * Text Domain:       best-betting-apps
 * Domain Path:       /languages
 *
 * @package BestBettingApps
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue required Stylesheet.
 */
function enqueue_stylesheet() {

	wp_enqueue_style( 'best-betting-apps-css', plugins_url( 'assets/css/best-betting-apps.css', __FILE__ ), array(), '1.0.0' );
	wp_enqueue_style( 'font-awesome-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_stylesheet' );

// Register shortcode.
add_shortcode( 'best_betting_apps', 'display_apps_list_handler' );

/**
 * Shortcode handler function.
 *
 * @param string $atts Sort order.
 * @return html
 */
function display_apps_list_handler( $atts ) {

	$info = '';
	// Parse shortcode attributes.
	$args = shortcode_atts(
		array(
			'sorting' => 'a',
		),
		$atts
	);

	// Load JSON data from file.
	$json_file = plugins_url( 'data.json', __FILE__ );
	$response  = wp_remote_get( $json_file );

	// Check for successful request.
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
		// Error handling if the request fails.
		$json_data = '';
	} else {
		// Get the response body.
		$json_data = wp_remote_retrieve_body( $response );
	}

	// Decode JSON data.
	$data = json_decode( $json_data, true );

	// Check if JSON decoding was successful.
	if ( is_null( $data ) ) {
		// JSON decoding failed.
		// Handle the error or display an appropriate message.
		return 'Error: Failed to decode JSON data.';
	}

	// Check if the required 'toplists' key exists in the decoded data.
	if ( ! isset( $data['toplists'] ) || ! is_array( $data['toplists'] ) ) {
		// 'toplists' key is missing or not an array.
		// Handle the error or display an appropriate message.
		return 'Error: Invalid JSON data format.';
	}

	// Extract required array from json data.
	// Use '...' splat operator to expand 'toplist' array as separate arguments.
	// Use array_merge to merge the expanded array into one.
	$get_app_lists = array_merge( ...array_values( $data['toplists'] ) );

	// Check if the extracted array is not empty.
	if ( empty( $get_app_lists ) ) {
		// The extracted array is empty.
		// Handle the error or display an appropriate message.
		return 'Error: No data available.';
	}

	// Validate sorting argument.
	$allowed_sorting_values = array( 'a', '0', '1' );
	if ( ! in_array( $args['sorting'], $allowed_sorting_values, true ) ) {
		// Sorting value is not valid.
		// Handle the error or set a default value.
		$info            = 'Invalid Sorting argument found. Sorting by default.';
		$args['sorting'] = 'a';
	}

	// Sort data by position or natural sort order.
	if ( '0' === $args['sorting'] ) {
		// Sort list by ascending order on receiving '0' as argument.
		// Use usort to sort the array by custom defined function.
		// Use substract method to get the lowest position and return the array.
		usort(
			$get_app_lists,
			function( $a, $b ) {
				return $a['position'] - $b['position'];
			}
		);
	} elseif ( '1' === $args['sorting'] ) {
		// Sort list by descending order on receiving '1' as argument.
		// Use usort to sort the array by custom defined function.
		// Use substract method to get the lowest position and return the array.
		usort(
			$get_app_lists,
			function( $a, $b ) {
				return $b['position'] - $a['position'];
			}
		);
	} else {
		// Sort array by natural order by default or if argument is 'a'.
		// Use usort to sort the array by custom defined function.
		// Use strcmp to fetch the lower array alphatecially and return it to usort.
		usort(
			$get_app_lists,
			function( $a, $b ) {
				return strcmp( $a['info']['bonus'], $b['info']['bonus'] );
			}
		);
	}

	// Build HTML table.
	$html  = ! empty( $info ) ? '<span class="info-message">' . $info . '</span>' : '';
	$html .= '<table>';
	$html .= '<thead><tr>';
	$html .= '<th class="casino-head">Casino</th>
        <th class="bonus-head">Bonus</th>
        <th class="features-head">Features</th>
        <th class="play-head">Play</th>';
	$html .= '</tr></thead><tbody>';
	foreach ( $get_app_lists as $app ) {
		$html       .= '<tr>';
		$html       .= '<td><div class="wrap-data"><img src=' . esc_url( $app['logo'] ) . '/><a href="' . site_url( $app['brand_id'], 'https' ) . '" class="review-link">Review</a></div></td>';
		$html       .= '<td><div class="wrap-data bonus"><div class="star-rating">';
		$filled_star = $app['info']['rating'];
		while ( $filled_star >= 1 ) {
			$html .= '<span class="fa fa-star checked"></span>';
			$filled_star--;
		}
		$unfilled_star = 5 - $app['info']['rating'];
		while ( $unfilled_star >= 1 ) {
			$html .= '<span class="fa fa-star-o"></span>';
			$unfilled_star--;
		}
		$html .= '</div><span>' . $app['info']['bonus'] . '</span></div></td>';
		$html .= '<td><div class="wrap-data features"><ul class="text-left">';
		foreach ( $app['info']['features'] as $app_feature ) {
			$html .= '<li class="feature"><span class="fa fa-check-square"></span>' . $app_feature . '</li>';
		}
		$html .= '</ul></div></td>';
		$html .= '<td><div class="wrap-data play"><a href="' . esc_url( $app['play_url'] ) . '" class="play-btn">PLAY NOW</a><div class="terms-conditions">' . $app['terms_and_conditions'] . '</div></div></td>';
		$html .= '</tr>';
	}
	$html .= '</tbody></table>';

	// Return table HTML.
	return $html;
}
