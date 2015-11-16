<?php

return array(
    
    /**
     * ReCaptcha default locale
     */
    'locale' => [
        'lang' => 'en'
    ],

    /**
     * ReCaptcha api keys
     *
     * Site : Public site key
     * Secret : Secret site key
     */
    'api' => [
        'key' => [
            'site' => '6LcWtwUTAAAAACzJjC2NVhHipNPzCtjKa5tiE6tM',
            'secret' => '6LcWtwUTAAAAAEwwpWdoBMT7dJcAPlborJ-QyW6C',
        ]
    ],

    /**
     * User settings
     *
     * AutoSendIp : The end user's ip address. (optional)
     */
    'user' => [
        'autoSendIp' => false
    ],

    /**
     * ReCaptcha input configuration.
     *
     * Input : Creates hidden input for validator class
     * Validator : Whether to use Obullo validator object.
     */
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
);