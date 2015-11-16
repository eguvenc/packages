<?php

return array(
    
    /**
     * Locale charset
     */
    'locale' => [
        'charset' => 'UTF-8'
    ],

    /**
     * Image characters
     * 
     * Default Pool : numbers - alpha - random
     * Pools : numbers - alpha - random
     * Length : Character length of captcha code
     */
    'characters' => [
        'default' => [
            'pool' => 'random'
        ],
        'pools' => [
            'numbers' => '23456789',
            'alpha'   => 'ABCDEFGHJKLMNPRSTUVWXYZ',
            'random'  => '23456789ABCDEFGHJKLMNPRSTUVWXYZ'
        ],
        'length' => 5
    ],

    /**
     * Image font
     *
     * Size : Font size
     * Path : Font path
     */
    'font' => [
        'size' => 30,
        'path' => '/assets/fonts',
    ],

    /**
     * Captcha image
     *
     * TrueColor: Php imagecreatetruecolor() recommended, but it isn't always available
     * Type : Set image extension
     * Wave : Image wave for more strong captchas.
     * Height : Height of captcha image, we calculate the "width" auto no need to set it.
     * Expiration : Expiration time of captcha
     */
    'image' => [
        'trueColor'  => true,
        'type'       => 'png',
        'wave'       => true,
        'height'     => 80,
        'expiration' => 1440,
    ],

    /**
     * Rgb color schema
     */
    'colors' => [
        'red'    => '255,0,0',
        'blue'   => '0,0,255',
        'green'  => '0,102,0',
        'black'  => '0,0,0',
        'yellow' => '255,255,0',
        'cyan'   => '0,146,134',
    ],

    /**
     * Form & input attributes
     *
     * Input : Captcha answer input properties.
     * Img : Captcha image src tag properties.
     * Refresh : Captcha refresh button properties.
     * Validation : Whether to use Obullo validation library.
     */
    'form' => [
        'input' => [
            'attributes' => [
                'type'  => 'text',
                'name'  => 'captcha_answer',
                'class' => 'captcha',
                'id'    => 'captcha_answer'         
            ]
        ],
        'img' => [
            'attributes' => [             
                'src'   =>  '/index.php/captcha/create',
                'style' => 'display:block;',
                'id'    => 'captcha_image',
                'class' => ''
            ]
        ],
        'refresh' => [
            'button' => '<input type="button" value="%s" onclick="oResetCaptcha(this.form);" style="margin-bottom:5px;" />',
            'script' => '<script type="text/javascript">
                function oResetCaptcha(form) {
                  form.%s.src="%s?noCache=" + Math.random();
                  form.%s.value = "";
                }
            </script>',
        ],
        'validation' => [
            'callback' => true,
        ]
    ],

    /**
     * Captcha text
     *
     * Colors text : Text colors. You can enter one or more items.
     * Colors noise : Noise colors. You can enter one or more items.
     */
    'text' => [
        'colors' =>  [
            'text' => ['red'],
            'noise' => ['red']
        ]
    ],

    /**
     * Default captcha fonts
     */
    'fonts' => [
        'AlphaSmoke'             => 'AlphaSmoke.ttf',
        'Anglican'               => 'Anglican.ttf',
        'Bknuckss'               => 'Bknuckss.ttf',
        'KingthingsFlashbang'    => 'KingthingsFlashbang.ttf',
        'NightSkK'               => 'NightSkK.ttf',
        'Notjustatoy'            => 'Notjustatoy.ttf',
        'Popsf'                  => 'Popsf.ttf',
        'SurreAlfreak'           => 'SurreAlfreak.ttf',
    ],
);