<?php
return [
    'microsoft' => [
        'client_id' => getenv('MS_CLIENT_ID') ?: 'your-client-id-here',
        'client_secret' => getenv('MS_CLIENT_SECRET') ?: 'your-client-secret-here',
        'redirect_uri' => getenv('MS_REDIRECT_URI') ?: 'http://localhost:8080/callback',
        'tenant_id' => getenv('MS_TENANT_ID') ?: 'common',
        'refresh_token' => getenv('MS_RFR_TOKEN') ?: 'your-refresh-token-here',
    ]
];
