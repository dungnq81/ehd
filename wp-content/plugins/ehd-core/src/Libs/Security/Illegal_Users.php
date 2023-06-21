<?php

namespace EHD_Libs\Security;

class Illegal_Users {

	/**
	 * Array containing common usernames prone to attacks.
	 *
	 * @var array
	 */
	public array $common_usernames = [
		'administrator',
		'user',
		'user1',
		'admin',
		'admin1',
	];

	/**
	 * Add illegal usernames
	 *
	 * @param array $usernames Default illegal usernames.
	 *
	 * @return array            Default + custom illegal usernames.
	 */
	public function get_illegal_usernames( array $usernames = [] ): array {
		$illegal_usernames = apply_filters(
			'ehd_illegal_users',
			$usernames
		);

		return array_map(
			'strtolower',
			array_merge(
				$illegal_usernames,
				$this->common_usernames
			)
		);
	}

	/**
	 * Change the default admin username.
	 *
	 * @param array $new_username The new username provided by the user.
	 *
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function change_common_username( array $new_username ) {
		global $wpdb;

		return $wpdb->update( // phpcs:ignore
			$wpdb->users, // phpcs:ignore
			[ 'user_login' => $new_username['user_login'] ],
			[ 'ID' => $new_username['ID'] ]
		);
	}

	/**
	 * Check if common usernames exist in the database.
	 *
	 * @return array The array containing the common usernames.
	 */
	public function check_for_common_usernames(): array {
		// Get all users for validating usernames.
		$all_users = get_users(
			[
				'orderby' => 'user_login',
				'order'   => 'ASC',
				'fields'  => [
					'ID',
					'user_login',
				],
			]
		);

		// Get all admins.
		$admins = get_users(
			[
				'role'    => 'administrator',
				'orderby' => 'user_login',
				'order'   => 'ASC',
				'fields'  => [
					'ID',
					'user_login',
				],
			]
		);

		// Check for illegal usernames.
		foreach ( $admins as $key => $admin ) {
			// Remove the user if its username is not in the illegal list.
			if ( ! in_array( strtolower( $admin->user_login ), $this->get_illegal_usernames() ) ) {
				unset( $admins[ $key ] );
			}
		}

		// Build the response array.
		return [
			'all_users'     => $all_users,
			'admin_matches' => array_values( $admins ),
		];
	}

	/**
	 * Start the name change for common usernames.
	 *
	 * @param array $usernames The array containing the changed usernames.
	 *
	 * @return array $result Array containing the result for each username update.
	 */
	public function update_common_usernames( array $usernames ): array {
		// Bail if usernames array is empty.
		if ( empty( $usernames ) ) {
			return [];
		}

		// Loop the specified usernames.
		foreach ( $usernames as $key => $username ) {
			// Remove the successfully changes and return the failed only if any.
			if ( 1 === $this->change_common_username( $username ) ) {
				unset( $usernames[ $key ] );
			}
		}

		return $usernames;
	}
}
