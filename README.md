# WebFiori OAuth2 Library

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-blue" alt="PHP Version">
  <img src="https://img.shields.io/packagist/v/webfiori/oauth" alt="Latest Version">
  <img src="https://img.shields.io/packagist/dt/webfiori/oauth" alt="Total Downloads">
  <img src="https://img.shields.io/github/license/WebFiori/oauth" alt="License">
</p>

<p align="center">
  <a href="https://github.com/WebFiori/oauth/actions">
    <img src="https://github.com/WebFiori/oauth/actions/workflows/php84.yaml/badge.svg?branch=main">
  </a>
  <a href="https://codecov.io/gh/WebFiori/oauth">
    <img src="https://codecov.io/gh/WebFiori/oauth/branch/main/graph/badge.svg" />
  </a>
  <a href="https://sonarcloud.io/dashboard?id=WebFiori_oauth">
      <img src="https://sonarcloud.io/api/project_badges/measure?project=WebFiori_oauth&metric=alert_status" />
  </a>
</p>

A modern, secure, and easy-to-use OAuth2 client library for PHP. Simplify OAuth2 authentication flows with support for multiple providers, token management, and comprehensive security features.

## ✨ Features

- 🔐 **OAuth2 Authorization Code Flow** - Complete implementation 
- 🏢 **Multiple Providers** - Built-in support for Microsoft
- 🔄 **Token Management** - Automatic token refresh and secure storage
- 🔧 **Extensible** - Easy to add custom OAuth2 providers

## 📋 Table of Contents

- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Core Concepts](#-core-concepts)
- [OAuth2 Flow](#-oauth2-flow)
- [Providers](#-providers)
- [Token Management](#-token-management)
- [Security Features](#-security-features)
- [Configuration](#-configuration)
- [Advanced Usage](#-advanced-usage)
- [API Reference](#-api-reference)
- [Examples](#-examples)
- [Contributing](#-contributing)

## 🚀 Installation

Install via Composer:

```bash
composer require webfiori/oauth
```

Or add to your `composer.json`:

```json
{
    "require": {
        "webfiori/oauth": "^1.0"
    }
}
```

### Requirements

- PHP 8.1 or higher
- cURL extension

## ⚡ Quick Start

### Basic OAuth2 Flow

```php
<?php
require_once 'vendor/autoload.php';

use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;

// Configure your OAuth2 provider
$provider = new MicrosoftProvider(
    'your-client-id',
    'your-client-secret',
    'https://yourapp.com/callback'
);

// Set up token storage
$storage = new FileTokenStorage('/path/to/token/storage');

// Create OAuth2 client
$client = new OAuth2Client($provider, $storage);

// Step 1: Redirect user to authorization URL
if (!isset($_GET['code'])) {
    $authUrl = $client->getAuthorizationUrl(['openid', 'profile', 'email']);
    header('Location: ' . $authUrl);
    exit;
}

// Step 2: Exchange authorization code for tokens
try {
    $tokens = $client->exchangeCodeForToken($_GET['code'], $_GET['state'] ?? null);
    echo "Access token received: " . $tokens['access_token'];
    
    // Tokens are automatically stored for future use
    $storedTokens = $client->getStoredTokens();
    
} catch (Exception $e) {
    echo "OAuth error: " . $e->getMessage();
}
```

### Microsoft Azure Integration

```php
use WebFiori\OAuth\Providers\MicrosoftProvider;

// Configure for Microsoft Azure AD
$provider = new MicrosoftProvider(
    'your-azure-app-id',
    'your-azure-app-secret',
    'https://yourapp.com/auth/callback',
    'your-tenant-id' // Optional: specify tenant
);

$client = new OAuth2Client($provider, $storage);

// Request specific Microsoft Graph scopes
$authUrl = $client->getAuthorizationUrl([
    'openid',
    'profile', 
    'email',
    'https://graph.microsoft.com/User.Read',
    'https://graph.microsoft.com/Mail.Read'
]);

// After token exchange, use tokens to call Microsoft Graph API
$tokens = $client->getStoredTokens();
if ($tokens && !$client->isTokenExpired()) {
    $graphData = callMicrosoftGraph($tokens['access_token']);
}
```

## 🧠 Core Concepts

### OAuth2Client - The Main Interface

The `OAuth2Client` class orchestrates the entire OAuth2 flow:

```php
use WebFiori\OAuth\OAuth2Client;

$client = new OAuth2Client($provider, $storage);

// Generate authorization URL
$authUrl = $client->getAuthorizationUrl($scopes, $additionalParams);

// Exchange code for tokens
$tokens = $client->exchangeCodeForToken($code, $state);

// Refresh expired tokens
$newTokens = $client->refreshToken($refreshToken);

// Check token status
$isExpired = $client->isTokenExpired();
$storedTokens = $client->getStoredTokens();
```

### Providers - OAuth2 Service Configuration

Providers encapsulate OAuth2 service-specific configuration:

```php
use WebFiori\OAuth\Providers\MicrosoftProvider;

$provider = new MicrosoftProvider(
    $clientId,
    $clientSecret,
    $redirectUri,
    $tenantId // Optional
);

// Access provider information
echo $provider->getAuthorizationUrl();  // https://login.microsoftonline.com/...
echo $provider->getTokenUrl();          // https://login.microsoftonline.com/.../oauth2/v2.0/token
echo $provider->getClientId();
```

### Token Storage - Secure Token Persistence

Store tokens securely between requests:

```php
use WebFiori\OAuth\Storage\FileTokenStorage;

// File-based storage
$storage = new FileTokenStorage('/secure/path/tokens');

// Store tokens
$storage->store('access_token', [
    'access_token' => 'token_value',
    'refresh_token' => 'refresh_value',
    'expires_at' => time() + 3600
]);

// Retrieve tokens
$tokens = $storage->retrieve('access_token');

// Check if tokens exist
if ($storage->has('access_token')) {
    // Use stored tokens
}

// Clear tokens
$storage->delete('access_token');
```

## 🔐 OAuth2 Flow

### Authorization Code Flow with PKCE

```php
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\AuthorizationRequest;

// Step 1: Generate authorization URL with state and PKCE
$authRequest = new AuthorizationRequest($provider);
$authUrl = $authRequest->buildUrl([
    'openid', 'profile', 'email'
], [
    'prompt' => 'consent',
    'access_type' => 'offline'
]);

// Redirect user to authorization server
header('Location: ' . $authUrl);
exit;

// Step 2: Handle callback (after user authorization)
if (isset($_GET['code'])) {
    // Validate state parameter (CSRF protection)
    $expectedState = $_SESSION['oauth_state'] ?? '';
    $receivedState = $_GET['state'] ?? '';
    
    if (!hash_equals($expectedState, $receivedState)) {
        throw new Exception('Invalid state parameter');
    }
    
    // Exchange authorization code for tokens
    $tokenRequest = new TokenRequest($provider);
    $tokens = $tokenRequest->exchangeCode($_GET['code'], $receivedState);
    
    // Store tokens securely
    $storage->store('user_tokens', $tokens);
}
```

### Token Refresh Flow

```php
// Check if token needs refresh
$tokens = $storage->retrieve('user_tokens');

if ($tokens && isset($tokens['expires_at'])) {
    if (time() >= $tokens['expires_at']) {
        // Token expired, refresh it
        if (isset($tokens['refresh_token'])) {
            $tokenRequest = new TokenRequest($provider);
            $newTokens = $tokenRequest->refresh($tokens['refresh_token']);
            
            // Update stored tokens
            $storage->store('user_tokens', $newTokens);
            $tokens = $newTokens;
        } else {
            // No refresh token, need to re-authorize
            $authUrl = $client->getAuthorizationUrl($scopes);
            header('Location: ' . $authUrl);
            exit;
        }
    }
}
```

## 🏢 Providers

### Microsoft Provider

```php
use WebFiori\OAuth\Providers\MicrosoftProvider;

// Personal Microsoft accounts
$provider = new MicrosoftProvider(
    'client-id',
    'client-secret',
    'https://yourapp.com/callback',
    'common' // Allows both personal and work accounts
);

// Specific Azure AD tenant
$provider = new MicrosoftProvider(
    'client-id',
    'client-secret', 
    'https://yourapp.com/callback',
    'your-tenant-id'
);

// Work or school accounts only
$provider = new MicrosoftProvider(
    'client-id',
    'client-secret',
    'https://yourapp.com/callback',
    'organizations'
);
```

### Custom Provider

```php
use WebFiori\OAuth\Providers\AbstractProvider;

class CustomProvider extends AbstractProvider {
    public function getAuthorizationUrl(): string {
        return 'https://auth.example.com/oauth/authorize';
    }
    
    public function getTokenUrl(): string {
        return 'https://auth.example.com/oauth/token';
    }
    
    public function getScopes(): array {
        return ['read', 'write', 'admin'];
    }
}

// Use custom provider
$provider = new CustomProvider('client-id', 'client-secret', 'callback-url');
$client = new OAuth2Client($provider, $storage);
```

## 🔄 Token Management

### Automatic Token Management

```php
use WebFiori\OAuth\TokenManager;

$tokenManager = new TokenManager($storage);

// Store tokens with automatic expiration calculation
$tokenManager->storeTokens([
    'access_token' => 'token_value',
    'refresh_token' => 'refresh_value',
    'expires_in' => 3600 // Automatically calculates expires_at
]);

// Get valid tokens (automatically refreshes if needed)
$validTokens = $tokenManager->getValidTokens($provider);

// Check token status
if ($tokenManager->hasValidTokens()) {
    // Use tokens for API calls
    $accessToken = $tokenManager->getAccessToken();
} else {
    // Redirect to authorization
    $authUrl = $client->getAuthorizationUrl($scopes);
    header('Location: ' . $authUrl);
}
```

### Token Validation

```php
use WebFiori\OAuth\Exceptions\InvalidTokenException;

try {
    // Validate token format and expiration
    $tokenManager->validateToken($tokens);
    
    // Use validated tokens
    $apiResponse = makeApiCall($tokens['access_token']);
    
} catch (InvalidTokenException $e) {
    // Token is invalid or expired
    echo "Token error: " . $e->getMessage();
    
    // Attempt refresh or re-authorization
    if (isset($tokens['refresh_token'])) {
        $newTokens = $client->refreshToken($tokens['refresh_token']);
    } else {
        // Redirect to authorization
        header('Location: ' . $client->getAuthorizationUrl($scopes));
    }
}
```


## 📚 API Reference

### Core Classes

#### OAuth2Client

The main client for OAuth2 operations.

**Constructor:**
```php
public function __construct(Provider $provider, ?TokenStorage $storage = null)
```

**Key Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `getAuthorizationUrl()` | `array $scopes = [], array $params = []` | `string` | Generates authorization URL |
| `exchangeCodeForToken()` | `string $code, ?string $state = null` | `array` | Exchanges code for tokens |
| `refreshToken()` | `string $refreshToken` | `array` | Refreshes access token |
| `getStoredTokens()` | - | `?array` | Gets stored tokens |
| `isTokenExpired()` | `?array $tokens = null` | `bool` | Checks if token is expired |
| `hasValidTokens()` | - | `bool` | Checks if valid tokens exist |

#### Provider Classes

**MicrosoftProvider:**
```php
public function __construct(
    string $clientId,
    string $clientSecret, 
    string $redirectUri,
    string $tenantId = 'common'
)
```

**AbstractProvider Methods:**

| Method | Return | Description |
|--------|--------|-------------|
| `getAuthorizationUrl()` | `string` | OAuth2 authorization endpoint |
| `getTokenUrl()` | `string` | OAuth2 token endpoint |
| `getClientId()` | `string` | OAuth2 client ID |
| `getClientSecret()` | `string` | OAuth2 client secret |
| `getRedirectUri()` | `string` | OAuth2 redirect URI |

#### TokenStorage Interface

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `store()` | `string $key, array $tokens` | `bool` | Stores tokens |
| `retrieve()` | `string $key` | `?array` | Retrieves tokens |
| `has()` | `string $key` | `bool` | Checks if tokens exist |
| `delete()` | `string $key` | `bool` | Deletes tokens |

### Exceptions

| Exception | Description |
|-----------|-------------|
| `OAuth2Exception` | General OAuth2 errors |
| `InvalidTokenException` | Token validation errors |

## 📄 License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 📞 Support

- 🐛 [Issue Tracker](https://github.com/WebFiori/oauth/issues)
