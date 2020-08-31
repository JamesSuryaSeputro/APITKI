<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' =>[
            "host"=> "mysql:unix_socket=/cloudsql/krisjaya-2020:asia-southeast2:krisjayadb3",
            "dbname"=> "kevindb",
            "user"=>"james",
            "pass"=>"strongpass5"
        ]
    ],
];
