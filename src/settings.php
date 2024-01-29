<?php
return [
    'settings' => [
        'displayErrorDetails' => true,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        'rpt_renderer' => [
            'template_path' => __DIR__ . '/../rpt/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
        
        // Configuración de mi APP
        'app_token_name'   => 'APP-TOKEN',
        'connectionString' => [
             'dns'  => 'sqlsrv:Server=20.15.201.237;Database=Tusa;',
            //'dns'  => 'odbc:Driver={SQL Server};Server=8.tcp.ngrok.io,19347;Database=Tusa;',
            'user' => 'tusasa', 
            'pass' => '51s73m452019'
        ]
    ],
];
