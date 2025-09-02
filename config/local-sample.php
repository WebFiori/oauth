<?php
return [
    'microsoft' => [
        'client_id' => getenv('MICROSOFT_CLIENT_ID') ?: 'your-client-id-here',
        'client_secret' => getenv('MICROSOFT_CLIENT_SECRET') ?: 'your-client-secret-here',
        'redirect_uri' => getenv('MICROSOFT_REDIRECT_URI') ?: 'http://localhost:8080/callback',
        'tenant_id' => getenv('MICROSOFT_TENANT_ID') ?: 'common'
    ]
];
