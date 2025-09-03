<?php
require_once '../vendor/autoload.php';

use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\GitHubProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;

// Load config
$config = require '../config/local.php';

// Setup GitHub OAuth2 client
$provider = new GitHubProvider(
    $config['github']['client_id'],
    $config['github']['client_secret'],
    $config['github']['redirect_uri']
);

$storage = new FileTokenStorage();
$client = new OAuth2Client($provider, $storage);
echo '<pre>';
// Check if code is provided
if (isset($_GET['code'])) {
    echo "GitHub Authorization Code: " . htmlspecialchars($_GET['code']) . "\n";
    if (isset($_GET['state'])) {
        echo "State: " . htmlspecialchars($_GET['state']) . "\n";
    }
    
    // Exchange code for tokens
    try {
        $tokens = $client->exchangeCodeForToken($_GET['code'], $_GET['state'] ?? null);
        
        echo "\nGitHub Tokens received:\n";
        echo "Access Token: " . $tokens['access_token'] . "\n";
        
        if (isset($tokens['refresh_token'])) {
            echo "Refresh Token: " .$tokens['refresh_token'] . "\n";
        }
        
        if (isset($tokens['expires_in'])) {
            echo "Expires In: " . $tokens['expires_in'] . " seconds\n";
        }
        
        // Test GitHub API call
        echo "\nTesting GitHub API call:\n";
        $ch = curl_init('https://api.github.com/user');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $tokens['access_token'],
                'User-Agent: OAuth-Test-App'
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $userData = json_decode($response, true);
            echo "GitHub User: " . $userData['login'] . " (" . ($userData['name'] ?? 'No name') . ")\n";
            echo "Public Repos: " . $userData['public_repos'] . "\n";
        } else {
            echo "GitHub API call failed with HTTP " . $httpCode . "\n";
        }
        
    } catch (Exception $e) {
        echo "\nError exchanging code: " . $e->getMessage() . "\n";
    }
} else {
    // Redirect to authorization URL
    $authUrl = $client->getAuthorizationUrl([
        'user:email', 'read:user'
    ]);
    
    header('Location: ' . $authUrl);
    exit;
}
