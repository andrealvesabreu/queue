<?php
return [
    'type' => 'queue',
    'config' => [
        [
            'name' => 'redisteste',
            'driver' => 'redis',
            'host' => 'localhost',
            'persisted' => true,
            'port' => 6379,
            'pass' => '',
            'database' => 15,
            'queue' => 'queuetest',
            'consumer' => [
                'Test',
                'fn'
            ]
        ],
        [
            'name' => 'track',
            'driver' => 'rabbit',
            'host' => 'localhost',
            'vhost' => 'services',
            'port' => 5672,
            'user' => 'admin',
            'pass' => 's0m3p4ssw0rd',
            'exchange' => 'tracking',
            'queue_type' => 'direct',
            'queue' => 'track',
            'persisted' => true,
            'consumer' => 'Test'
        ]
    ]
];
