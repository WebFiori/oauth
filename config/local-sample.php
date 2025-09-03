<?php
return [
    'microsoft' => [
        'client_id' => getenv('MS_CLIENT_ID') ?: 'your-client-id-here',
        'client_secret' => getenv('MS_CLIENT_SECRET') ?: 'your-client-secret-here',
        'redirect_uri' => getenv('MS_REDIRECT_URI') ?: 'http://localhost:8080/auth.php',
        'tenant_id' => getenv('MS_TENANT_ID') ?: 'common',
        'refresh_token' => getenv('MS_RFR_TOKEN') ?: 'your-refresh-token-here',
    ],
    'github' => [
        'client_id' => getenv('GH_CLIENT_ID') ?: 'your-github-client-id-here',
        'client_secret' => getenv('GH_CLIENT_SECRET') ?: 'your-github-client-secret-here',
        'redirect_uri' => getenv('GH_REDIRECT_URI') ?: 'http://localhost:8080/github-auth.php',
        'refresh_token' => getenv('GH_RFR_TOKEN') ?: 'your-github-refresh-token-here',
    ],
    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'your-google-client-id-here',
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'your-google-client-secret-here',
        'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost:8080/google-auth.php',
    ]
];
