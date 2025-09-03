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

An easy-to-use OAuth2 client library for PHP. Simplify OAuth2 authentication flows with support for multiple providers and token storage.

## ✨ Features

- 🔐 **OAuth2 Authorization Code Flow** - Complete implementation 
- 🏢 **Multiple Providers** - Built-in support for Microsoft, Google, and GitHub
- 🔄 **Token Management** - Secure token storage and retrieval
- 🔧 **Extensible** - Easy to add custom OAuth2 providers

## 📋 Table of Contents

- [Supported PHP Versions](#-supported-php-versions)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Providers](#-providers)
- [Token Storage](#-token-storage)
- [Multi-Provider Management](#-multi-provider-management)
- [API Reference](#-api-reference)
- [Examples](#-examples)

## Supported PHP Versions

|                                                                                        Build Status                                                                                         |
|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/oauth/actions/workflows/php81.yaml"><img src="https://github.com/WebFiori/oauth/actions/workflows/php81.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/oauth/actions/workflows/php82.yaml"><img src="https://github.com/WebFiori/oauth/actions/workflows/php82.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/oauth/actions/workflows/php83.yaml"><img src="https://github.com/WebFiori/oauth/actions/workflows/php83.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/oauth/actions/workflows/php84.yaml"><img src="https://github.com/WebFiori/oauth/actions/workflows/php84.yaml/badge.svg?branch=main"></a> |


## 🚀 Installation

Install via Composer:

```bash
composer require webfiori/oauth
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
$tokens = $client->exchangeCodeForToken($_GET['code']);
if ($tokens) {
    $graphData = callMicrosoftGraph($tokens['access_token']);
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

### Google Provider

```php
use WebFiori\OAuth\Providers\GoogleProvider;

$provider = new GoogleProvider(
    'google-client-id',
    'google-client-secret',
    'https://yourapp.com/callback'
);

$client = new OAuth2Client($provider, $storage);

// Request Google-specific scopes
$authUrl = $client->getAuthorizationUrl([
    'openid',
    'email',
    'profile',
    'https://www.googleapis.com/auth/drive.readonly'
]);
```

### GitHub Provider

```php
use WebFiori\OAuth\Providers\GitHubProvider;

$provider = new GitHubProvider(
    'github-client-id',
    'github-client-secret',
    'https://yourapp.com/callback'
);

$client = new OAuth2Client($provider, $storage);

// Request GitHub-specific scopes
$authUrl = $client->getAuthorizationUrl([
    'user:email',
    'read:user',
    'repo'
]);
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
    
    public function getDefaultScopes(): array {
        return ['read', 'write'];
    }
    
    public function getUserInfoUrl(): string {
        return 'https://api.example.com/user';
    }
}

// Use custom provider
$provider = new CustomProvider('client-id', 'client-secret', 'callback-url');
$client = new OAuth2Client($provider, $storage);
```

### OAuthManager - Multi-Provider Support

```php
use WebFiori\OAuth\OAuthManager;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;

// Create manager with shared storage
$storage = new FileTokenStorage('/secure/tokens');
$manager = new OAuthManager($storage);

// Register multiple providers
$manager->addProvider('microsoft', new MicrosoftProvider(
    'ms-client-id', 'ms-secret', 'https://app.com/callback'
));

$manager->addProvider('google', new GoogleProvider(
    'google-client-id', 'google-secret', 'https://app.com/callback'
));

// Get clients by provider name
$msClient = $manager->getClient('microsoft');
$googleClient = $manager->getClient('google');

// Check if provider exists
if ($manager->hasProvider('github')) {
    $githubClient = $manager->getClient('github');
}

// Get all registered providers
$providerNames = $manager->getProviderNames(); // ['microsoft', 'google']

// Remove a provider
$manager->removeProvider('google');
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

### OAuth2Client

The main client for OAuth2 operations.

**Constructor:**
```php
public function __construct(Provider $provider, ?TokenStorage $storage = null, ?callable $tokenRequestFactory = null)
```

**Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `getAuthorizationUrl()` | `array $scopes = []` | `string` | Generates authorization URL |
| `exchangeCodeForToken()` | `string $code, ?string $state = null` | `array` | Exchanges code for tokens |
| `refreshToken()` | `string $refreshToken` | `array` | Refreshes access token |

### Provider Classes

**MicrosoftProvider:**
```php
public function __construct(
    string $clientId,
    string $clientSecret, 
    string $redirectUri,
    string $tenantId = 'common'
)
```

**GoogleProvider:**
```php
public function __construct(
    string $clientId,
    string $clientSecret,
    string $redirectUri
)
```

**GitHubProvider:**
```php
public function __construct(
    string $clientId,
    string $clientSecret,
    string $redirectUri
)
```

**Provider Methods:**

| Method | Return | Description |
|--------|--------|-------------|
| `getAuthorizationUrl()` | `string` | OAuth2 authorization endpoint |
| `getTokenUrl()` | `string` | OAuth2 token endpoint |
| `getDefaultScopes()` | `array` | Default OAuth2 scopes |
| `getUserInfoUrl()` | `string` | User info API endpoint |
| `getClientId()` | `string` | OAuth2 client ID |
| `getClientSecret()` | `string` | OAuth2 client secret |
| `getRedirectUri()` | `string` | OAuth2 redirect URI |

### OAuthManager

Multi-provider OAuth2 manager.

**Constructor:**
```php
public function __construct(?TokenStorage $storage = null)
```

**Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `addProvider()` | `string $name, Provider $provider` | `OAuthManager` | Registers a provider |
| `getClient()` | `string $name` | `OAuth2Client` | Gets client for provider |
| `hasProvider()` | `string $name` | `bool` | Checks if provider exists |
| `getProviderNames()` | - | `array` | Gets all provider names |
| `removeProvider()` | `string $name` | `OAuthManager` | Removes a provider |
| `setStorage()` | `TokenStorage $storage` | `OAuthManager` | Sets token storage |

### TokenStorage Interface

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `store()` | `string $key, array $tokens` | `bool` | Stores tokens |
| `retrieve()` | `string $key` | `?array` | Retrieves tokens |
| `exists()` | `string $key` | `bool` | Checks if tokens exist |
| `delete()` | `string $key` | `bool` | Deletes tokens |

### TokenManager

Token management utility.

**Constructor:**
```php
public function __construct(TokenStorage $storage)
```

**Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `store()` | `string $key, array $token` | `void` | Stores token data |
| `retrieve()` | `string $key` | `?array` | Retrieves token data |
| `delete()` | `string $key` | `void` | Deletes token data |

### Exceptions

| Exception | Description |
|-----------|-------------|
| `OAuth2Exception` | General OAuth2 errors |
| `InvalidTokenException` | Token validation errors |

## 📄 Examples

### Complete Microsoft OAuth2 Flow

```php
<?php
require_once 'vendor/autoload.php';

use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;

session_start();

// Configuration
$provider = new MicrosoftProvider(
    'your-client-id',
    'your-client-secret',
    'https://yourapp.com/callback',
    'common'
);

$storage = new FileTokenStorage();
$client = new OAuth2Client($provider, $storage);

if (!isset($_GET['code'])) {
    // Step 1: Redirect to authorization
    $authUrl = $client->getAuthorizationUrl([
        'openid', 'profile', 'email', 'offline_access'
    ]);
    
    header('Location: ' . $authUrl);
    exit;
} else {
    // Step 2: Handle callback
    try {
        $tokens = $client->exchangeCodeForToken($_GET['code'], $_GET['state'] ?? null);
        
        // Use access token to call Microsoft Graph
        $ch = curl_init('https://graph.microsoft.com/v1.0/me');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $tokens['access_token'],
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $userData = json_decode($response, true);
            echo "Welcome, " . $userData['displayName'] . "!";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
```

### Token Refresh Example

```php
<?php
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\TokenManager;

$tokenManager = new TokenManager($storage);
$tokens = $tokenManager->retrieve('user_tokens');

if ($tokens && isset($tokens['expires_at']) && time() >= $tokens['expires_at']) {
    // Token expired, refresh it
    if (isset($tokens['refresh_token'])) {
        try {
            $newTokens = $client->refreshToken($tokens['refresh_token']);
            $tokenManager->store('user_tokens', $newTokens);
            echo "Token refreshed successfully";
        } catch (Exception $e) {
            echo "Failed to refresh token: " . $e->getMessage();
        }
    }
}
```

## 📄 License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 📞 Support

- 🐛 [Issue Tracker](https://github.com/WebFiori/oauth/issues)
