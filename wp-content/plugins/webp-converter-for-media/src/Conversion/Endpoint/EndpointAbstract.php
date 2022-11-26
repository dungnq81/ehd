<?php

namespace WebpConverter\Conversion\Endpoint;

use WebpConverter\Service\NonceManager;

/**
 * Abstract class for class that supports image conversion method.
 */
abstract class EndpointAbstract implements EndpointInterface {

	/**
	 * {@inheritdoc}
	 */
	public static function get_url_lifetime(): int {
		return ( 24 * 60 * 60 );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_valid_request( string $request_nonce ): bool {
		return ( new NonceManager( $this->get_url_lifetime(), false ) )
			->verify_nonce(
				$request_nonce,
				sprintf( EndpointIntegration::ROUTE_NONCE_ACTION, $this->get_route_name() )
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_route_args(): array {
		return [];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_route_url(): string {
		return get_rest_url(
			null,
			sprintf(
				'%1$s/%2$s',
				EndpointIntegration::ROUTE_NAMESPACE,
				static::get_route_name()
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_route_nonce(): string {
		return ( new NonceManager( static::get_url_lifetime(), false ) )
			->generate_nonce( sprintf( EndpointIntegration::ROUTE_NONCE_ACTION, static::get_route_name() ) );
	}
}
