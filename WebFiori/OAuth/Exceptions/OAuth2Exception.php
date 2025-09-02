<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2025 Ibrahim BinAlshikh and Contributors 
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */
namespace WebFiori\OAuth\Exceptions;

use Exception;

/**
 * OAuth2 exception.
 * 
 * Base exception class for OAuth2-related errors. This exception is thrown
 * when OAuth2 operations fail, such as invalid tokens, network errors,
 * or provider-specific issues.
 * 
 * @example
 * ```php
 * try {
 *     $token = $client->exchangeCodeForToken($code);
 * } catch (OAuth2Exception $e) {
 *     error_log('OAuth2 error: ' . $e->getMessage());
 * }
 * ```
 */
class OAuth2Exception extends Exception {
}
