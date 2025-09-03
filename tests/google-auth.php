<?php
require_once '../vendor/autoload.php';

use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\GoogleProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;

// Load config
$config = require '../config/local.php';

// Setup Google OAuth2 client
$provider = new GoogleProvider(
    $config['google']['client_id'],
    $config['google']['client_secret'],
    $config['google']['redirect_uri']
);

$storage = new FileTokenStorage();
$client = new OAuth2Client($provider, $storage);

// Check if code is provided
if (isset($_GET['code'])) {
    echo '<pre>';
    echo "Google Authorization Code: " . htmlspecialchars($_GET['code']) . "\n";
    if (isset($_GET['state'])) {
        echo "State: " . htmlspecialchars($_GET['state']) . "\n";
    }
    
    // Exchange code for tokens
    try {
        $tokens = $client->exchangeCodeForToken($_GET['code'], $_GET['state'] ?? null);
        
        echo "\nGoogle Tokens received:\n";
        echo "Access Token: " .$tokens['access_token'] . "\n";
        
        if (isset($tokens['refresh_token'])) {
            echo "Refresh Token: " .$tokens['refresh_token'] . "\n";
        }
        
        if (isset($tokens['expires_in'])) {
            echo "Expires In: " . $tokens['expires_in'] . " seconds\n";
        }
        
        // Test Google API call
        echo "\nTesting Google API call:\n";
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $tokens['access_token']
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $userData = json_decode($response, true);
            echo "Google User: " . $userData['email'] . " (" . ($userData['name'] ?? 'No name') . ")\n";
            echo "User ID: " . $userData['id'] . "\n";
        } else {
            echo "Google API call failed with HTTP " . $httpCode . "\n";
        }
        
    } catch (Exception $e) {
        echo "\nError exchanging code: " . $e->getMessage() . "\n";
    }
} else {
    // Redirect to authorization URL
    $authUrl = $client->getAuthorizationUrl([
        'openid', 'email', 'profile'
    ]);
    
    header('Location: ' . $authUrl);
    exit;
}
