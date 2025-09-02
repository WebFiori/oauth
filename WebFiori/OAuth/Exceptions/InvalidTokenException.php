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

/**
 * Invalid token exception.
 * 
 * Thrown when an OAuth2 token is invalid, expired, or malformed.
 * This is a specialized exception for token-specific errors.
 * 
 * @example
 * ```php
 * try {
 *     $userInfo = $api->getUserInfo($accessToken);
 * } catch (InvalidTokenException $e) {
 *     // Token is invalid, need to refresh or re-authenticate
 *     $newToken = $client->refreshToken($refreshToken);
 * }
 * ```
 */
class InvalidTokenException extends OAuth2Exception {
}
