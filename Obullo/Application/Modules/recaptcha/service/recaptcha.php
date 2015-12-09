<?php

return array(
    
    'params' => [

        'locale' => [
            'lang' => 'en'
        ],
        'api' => [
            'key' => [
                'site' => '',
                'secret' => '',
            ]
        ],
        'user' => [
            'autoSendIp' => false
        ],
        'form' => [
            'input' => [
                'attributes' => [
                    'name' => 'recaptcha',
                    'id' => 'recaptcha',
                    'type' => 'text',
                    'value' => 1,
                    'style' => 'display:none;',
                ]
            ],
            'validation' => [
                'callback' => true,
            ]
        ]
    ],

    'methods' => [
        'setParameters' => [
            'setLang' => 'en',
        ]
    ]
);