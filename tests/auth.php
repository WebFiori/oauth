<?php
require_once '../vendor/autoload.php';

use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;

// Load config
$config = require '../config/local.php';

// Setup OAuth2 client
$provider = new MicrosoftProvider(
    $config['microsoft']['client_id'],
    $config['microsoft']['client_secret'],
    $config['microsoft']['redirect_uri'],
    $config['microsoft']['tenant_id']
);

$storage = new FileTokenStorage();
$client = new OAuth2Client($provider, $storage);

// Check if code is provided
if (isset($_GET['code'])) {
    echo "<pre>Authorization Code: " . htmlspecialchars($_GET['code']) . "\n";
    if (isset($_GET['state'])) {
        echo "State: " . htmlspecialchars($_GET['state']) . "\n";
    }
    
    // Exchange code for tokens
    try {
        $tokens = $client->exchangeCodeForToken($_GET['code'], $_GET['state'] ?? null);
        
        echo "\nTokens received:\n";
        echo "Access Token: " . $tokens['access_token']. "\n";
        
        if (isset($tokens['refresh_token'])) {
            echo "Refresh Token: " .$tokens['refresh_token']. "\n";
        }
        
        if (isset($tokens['expires_in'])) {
            echo "Expires In: " . $tokens['expires_in'] . " seconds\n";
        }
        
    } catch (Exception $e) {
        echo "\nError exchanging code: " . $e->getMessage() . "\n";
    }
} else {
    // Redirect to authorization URL
    $authUrl = $client->getAuthorizationUrl([
        'openid', 'profile', 'offline_access'
    ], [
        'prompt' => 'consent'
    ]);
    
    header('Location: ' . $authUrl);
    exit;
}
