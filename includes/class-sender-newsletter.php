<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sender_Newsletter {
	const AJAX_ACTION = 'foundation_sender_subscribe';
	const NONCE_ACTION = 'foundation_sender_newsletter';
	const RATE_LIMIT_TTL = 30;

	public function hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'handle_subscribe' ) );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, array( $this, 'handle_subscribe' ) );
		add_action( 'wp_ajax_foundation_sender_nonce', array( $this, 'handle_nonce' ) );
		add_action( 'wp_ajax_nopriv_foundation_sender_nonce', array( $this, 'handle_nonce' ) );
	}

	/** Refresh a public form nonce without exposing account settings or subscribing. */
	public function handle_nonce() {
		nocache_headers();
		wp_send_json_success( array( 'nonce' => wp_create_nonce( self::NONCE_ACTION ) ) );
	}

	/**
	 * Return available Sender groups from the installed Sender plugin settings.
	 *
	 * @return array<string, string>
	 */
	public static function get_sender_groups() {
		$groups = get_option( 'sender_groups_data', array() );

		if ( ! is_array( $groups ) ) {
			return array();
		}

		$sanitized = array();
		foreach ( $groups as $group_id => $group_name ) {
			$group_id = sanitize_text_field( (string) $group_id );
			$group_name = sanitize_text_field( (string) $group_name );

			if ( '' !== $group_id && '' !== $group_name ) {
				$sanitized[ $group_id ] = $group_name;
			}
		}

		return $sanitized;
	}

	/**
	 * Resolve the default newsletter group ID.
	 *
	 * @return string
	 */
	public static function get_default_group_id() {
		$groups = self::get_sender_groups();
		$registration_list = self::get_group_option_value( 'sender_registration_list' );
		$customer_list     = self::get_group_option_value( 'sender_customers_list' );

		if ( '' !== $registration_list ) {
			return $registration_list;
		}

		foreach ( $groups as $group_id => $group_name ) {
			if ( 'newsletter' === strtolower( trim( $group_name ) ) ) {
				return $group_id;
			}
		}

		if ( '' !== $customer_list ) {
			return $customer_list;
		}

		return empty( $groups ) ? '' : (string) array_key_first( $groups );
	}

	/**
	 * Determine whether the Sender integration is ready.
	 *
	 * @return bool
	 */
	public static function is_ready() {
		return '' !== self::get_api_key() && '' !== self::get_default_group_id();
	}

	/**
	 * AJAX handler for the newsletter form.
	 */
	public function handle_subscribe() {
		if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'code'    => 'nonce_expired',
					'message' => __( 'Security check failed. Please refresh and try again.', 'foundation-elementor-plus' ),
				),
				403
			);
		}

		$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['first_name'] ) ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( (string) $_POST['email'] ) ) : '';
		$company    = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['company'] ) ) : '';
		$group_id   = self::resolve_group_id( isset( $_POST['group_id'] ) ? wp_unslash( (string) $_POST['group_id'] ) : '' );
		$interests  = isset( $_POST['interests'] ) ? (array) wp_unslash( $_POST['interests'] ) : array();

		if ( '' !== $company ) {
			wp_send_json_success(
				array(
					'message' => __( 'Thanks for signing up.', 'foundation-elementor-plus' ),
				)
			);
		}

		if ( '' === $email || ! is_email( $email ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please enter a valid email address.', 'foundation-elementor-plus' ),
				),
				422
			);
		}

		$api_key = self::get_api_key();

		if ( '' === $api_key || '' === $group_id ) {
			wp_send_json_error(
				array(
					'message' => __( 'The newsletter system is not configured yet.', 'foundation-elementor-plus' ),
				),
				500
			);
		}

		$rate_limit_key = 'foundation_sender_newsletter_' . md5( strtolower( $email ) . '|' . self::get_request_ip() );
		if ( false !== get_transient( $rate_limit_key ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please wait a moment before trying again.', 'foundation-elementor-plus' ),
				),
				429
			);
		}

		set_transient( $rate_limit_key, 1, self::RATE_LIMIT_TTL );

		$result = self::subscribe_contact( $email, $first_name, $group_id, $interests );

		if ( ! empty( $result['success'] ) ) {
			wp_send_json_success(
				array(
					'message' => ! empty( $result['message'] ) ? $result['message'] : __( 'Thanks for signing up. Keep an eye on your inbox.', 'foundation-elementor-plus' ),
				)
			);
		}

		wp_send_json_error(
			array(
				'message' => ! empty( $result['message'] ) ? $result['message'] : __( 'Something went wrong. Please try again.', 'foundation-elementor-plus' ),
			),
			! empty( $result['status'] ) ? (int) $result['status'] : 500
		);
	}

	/**
	 * Subscribe a contact using the installed Sender plugin API when possible.
	 *
	 * @param string $email Email address.
	 * @param string $first_name First name.
	 * @param string $group_id Sender group ID.
	 * @return array<string, mixed>
	 */
	private static function subscribe_contact( $email, $first_name, $group_id, array $interests = array() ) {
		$interest_group_ids = self::resolve_interest_group_ids( $interests );
		$api_result         = null;

		if ( empty( $interest_group_ids ) ) {
			$api_result = self::subscribe_via_sender_plugin( $email, $first_name, $group_id );
		}

		if ( is_array( $api_result ) ) {
			return $api_result;
		}

		return self::subscribe_via_http( $email, $first_name, $group_id, $interest_group_ids );
	}

	/**
	 * Attempt to subscribe using the Sender plugin's own PHP client.
	 *
	 * @param string $email Email address.
	 * @param string $first_name First name.
	 * @param string $group_id Sender group ID.
	 * @return array<string, mixed>|null
	 */
	private static function subscribe_via_sender_plugin( $email, $first_name, $group_id ) {
		if ( ! class_exists( '\Sender_API' ) || ! class_exists( '\WC_Geolocation' ) ) {
			return null;
		}

		try {
			$payload = array(
				'email'      => $email,
				'newsletter' => true,
				'list_id'    => $group_id,
			);

			if ( '' !== $first_name ) {
				$payload['firstname'] = $first_name;
			}

			$client   = new \Sender_API();
			$response = $client->senderTrackNotRegisteredUsers( $payload );

			if ( false === $response ) {
				return array(
					'success' => false,
					'message' => __( 'Could not connect to the newsletter system.', 'foundation-elementor-plus' ),
					'status'  => 502,
				);
			}

			return array(
				'success' => true,
				'message' => __( 'Thanks for signing up. Keep an eye on your inbox.', 'foundation-elementor-plus' ),
				'status'  => 200,
			);
		} catch ( \Throwable $exception ) {
			return null;
		}
	}

	/**
	 * Fallback subscription request that uses the stored Sender API key directly.
	 *
	 * @param string $email Email address.
	 * @param string $first_name First name.
	 * @param string $group_id Sender group ID.
	 * @return array<string, mixed>
	 */
	private static function subscribe_via_http( $email, $first_name, $group_id, array $interest_group_ids = array() ) {
		$groups = array_values( array_unique( array_filter( array_merge( array( $group_id ), $interest_group_ids ) ) ) );

		$body = array(
			'email'  => $email,
			'groups' => $groups,
		);

		if ( '' !== $first_name ) {
			$body['firstname'] = $first_name;
		}

		$response = wp_remote_post(
			'https://api.sender.net/v2/subscribers',
			array(
				'method'  => 'POST',
				'timeout' => 20,
				'headers' => array(
					'Authorization' => 'Bearer ' . self::get_api_key(),
					'Content-Type'  => 'application/json',
					'Accept'        => 'application/json',
				),
				'body'    => wp_json_encode( $body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => __( 'Could not connect to the newsletter system.', 'foundation-elementor-plus' ),
				'status'  => 502,
			);
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$raw_body    = wp_remote_retrieve_body( $response );
		$data        = json_decode( $raw_body, true );
		$message     = '';

		if ( is_array( $data ) && ! empty( $data['message'] ) && is_string( $data['message'] ) ) {
			$message = $data['message'];
		}

		if ( $status_code >= 200 && $status_code < 300 ) {
			return array(
				'success' => true,
				'message' => __( 'Thanks for signing up. Keep an eye on your inbox.', 'foundation-elementor-plus' ),
				'status'  => $status_code,
			);
		}

		if ( 409 === $status_code || false !== stripos( $message, 'already' ) ) {
			return array(
				'success' => true,
				'message' => __( 'You are already on the list. Keep an eye on your inbox.', 'foundation-elementor-plus' ),
				'status'  => 200,
			);
		}

		return array(
			'success' => false,
			'message' => '' !== $message ? $message : __( 'Something went wrong. Please try again.', 'foundation-elementor-plus' ),
			'status'  => $status_code > 0 ? $status_code : 500,
		);
	}

	/**
	 * Get the configured Sender API key.
	 *
	 * @return string
	 */
	private static function get_api_key() {
		$api_key = get_option( 'sender_api_key', '' );
		return is_scalar( $api_key ) ? trim( (string) $api_key ) : '';
	}

	/**
	 * Get a stored Sender list option as a normalized string.
	 *
	 * @param string $option_name Option name.
	 * @return string
	 */
	private static function get_group_option_value( $option_name ) {
		$value = get_option( $option_name, '' );
		return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	}

	/**
	 * Resolve the requested group ID, allowing saved widget values to survive stale group caches.
	 *
	 * @param string $requested_group Requested group ID.
	 * @return string
	 */
	private static function resolve_group_id( $requested_group ) {
		$requested_group = sanitize_text_field( (string) $requested_group );
		$groups          = self::get_sender_groups();

		if ( '' !== $requested_group ) {
			if ( empty( $groups ) || isset( $groups[ $requested_group ] ) ) {
				return $requested_group;
			}
		}

		return self::get_default_group_id();
	}

	/**
	 * Resolve selected interest labels to Sender group IDs when matching groups exist.
	 *
	 * @param array<int, string> $requested_interests Selected interest keys.
	 * @return array<int, string>
	 */
	private static function resolve_interest_group_ids( array $requested_interests ) {
		$labels = self::get_interest_option_labels();
		$groups = self::get_sender_groups();

		if ( empty( $labels ) || empty( $groups ) ) {
			return array();
		}

		$normalized_groups = array();
		foreach ( $groups as $group_id => $group_name ) {
			$normalized_groups[ self::normalize_interest_label( $group_name ) ] = (string) $group_id;
		}

		$resolved = array();
		foreach ( $requested_interests as $interest_key ) {
			$interest_key = sanitize_key( (string) $interest_key );
			if ( empty( $labels[ $interest_key ] ) ) {
				continue;
			}

			$normalized_label = self::normalize_interest_label( $labels[ $interest_key ] );
			if ( isset( $normalized_groups[ $normalized_label ] ) ) {
				$resolved[] = $normalized_groups[ $normalized_label ];
			}
		}

		return array_values( array_unique( $resolved ) );
	}

	/**
	 * Return the compact interest labels used by the footer widget.
	 *
	 * @return array<string, string>
	 */
	public static function get_interest_option_labels() {
		return array(
			'book_call'     => __( 'Book a call', 'foundation-elementor-plus' ),
			'request_email' => __( 'Request an email', 'foundation-elementor-plus' ),
			'work_together' => __( 'Let’s work together', 'foundation-elementor-plus' ),
		);
	}

	/**
	 * Normalize a label for loose matching against Sender group titles.
	 *
	 * @param string $label Raw label.
	 * @return string
	 */
	private static function normalize_interest_label( $label ) {
		$label = strtolower( sanitize_text_field( (string) $label ) );
		$label = preg_replace( '/[^a-z0-9]+/', ' ', $label );
		return trim( (string) $label );
	}

	/**
	 * Return a best-effort client IP for rate limiting.
	 *
	 * @return string
	 */
	private static function get_request_ip() {
		$keys = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'REMOTE_ADDR',
		);

		foreach ( $keys as $key ) {
			if ( empty( $_SERVER[ $key ] ) ) {
				continue;
			}

			$value = wp_unslash( (string) $_SERVER[ $key ] );
			if ( 'HTTP_X_FORWARDED_FOR' === $key ) {
				$parts = array_map( 'trim', explode( ',', $value ) );
				$value = $parts[0] ?? '';
			}

			$value = preg_replace( '/[^0-9a-fA-F:\\.]/', '', (string) $value );
			if ( ! empty( $value ) ) {
				return (string) $value;
			}
		}

		return 'unknown';
	}
}
