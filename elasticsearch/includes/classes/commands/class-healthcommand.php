<?php

namespace Automattic\VIP\Elasticsearch\Commands;

use \WP_CLI;
use \WP_CLI\Utils;

/**
 * Commands to view and manage the health of VIP Go Elasticsearch indexes
 *
 * @package Automattic\VIP\Elasticsearch
 */
class HealthCommand extends \WPCOM_VIP_CLI_Command {
	private const SUCCESS_ICON = "\u{2705}"; // unicode check mark
	private const FAILURE_ICON = "\u{274C}"; // unicode cross mark

	public function __construct() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		parent::__construct();
	}

	/**
	 * Validate DB and ES index counts for all objects
	 *
	 * ## OPTIONS
	 *
	 *
	 * ## EXAMPLES
	 *     wp vip-es health validate-counts
	 *
	 * @subcommand validate-counts
	 */
	public function validate_counts( $args, $assoc_args ) {
		$this->validate_posts_count( $args, $assoc_args );

		WP_CLI::line( '' );

		$this->validate_users_count( $args, $assoc_args );
	}

	/**
	 * ## OPTIONS
	 *
	 *
	 * ## EXAMPLES
	 *     wp vip-es health validate-users-count
	 *
	 * @subcommand validate-users-count
	 */
	public function validate_users_count( $args, $assoc_args ) {
		WP_CLI::line( sprintf( "Validating users count\n" ) );

		$users_results = \Automattic\VIP\Elasticsearch\Health::validate_index_users_count();
		$this->render_results( $users_results );
	}

	/**
	 * ## OPTIONS
	 *
	 *
	 * ## EXAMPLES
	 *     wp vip-es health validate-posts-count
	 *
	 * @subcommand validate-posts-count
	 */
	public function validate_posts_count( $args, $assoc_args ) {
		WP_CLI::line( "Validating posts count\n" );

		$posts_results = \Automattic\VIP\Elasticsearch\Health::validate_index_posts_count();
		$this->render_results( $posts_results );
	}

	/**
	 * Helper function to parse and render results of index verification functions
	 *
	 * @param array $results Array of results generated by index verification functions
	 */
	private function render_results( array $results ) {
		foreach( $results as $result ) {
			// If it's an error, print out a warning and go to the next iteration
			if ( array_key_exists( 'error', $result ) ) {
				WP_CLI::warning( 'Error while validating count: ' . $result[ 'error' ]);
				continue;
			}

			$message = ' inconsistencies found';  
			if ( $result[ 'diff' ] ) {
				$icon = self::FAILURE_ICON;
			} else {
				$icon = self::SUCCESS_ICON;
				$message = 'no' . $message;
			}

			$message = sprintf( '%s %s when counting entity: %s, type: %s (DB: %s, ES: %s, Diff: %s)', $icon, $message, $result[ 'entity' ], $result[ 'type' ], $result[ 'db_total' ], $result[ 'es_total' ], $result[ 'diff' ] );
			WP_CLI::line( $message );
		}
	}
}
