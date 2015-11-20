<?php

return array(
    
    'params' => [

        'locale' => [
            'charset' => 'UTF-8'
        ],
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
        'font' => [
            'size' => 30,
            'path' => '/assets/fonts',
        ],
        'image' => [
            'trueColor'  => true,
            'type'       => 'png',
            'wave'       => true,
            'height'     => 80,
            'expiration' => 1440,
        ],
        'colors' => [
            'red'    => '255,0,0',
            'blue'   => '0,0,255',
            'green'  => '0,102,0',
            'black'  => '0,0,0',
            'yellow' => '255,255,0',
            'cyan'   => '0,146,134',
        ],
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
                    'src'   =>  '/captcha/create',
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
        'text' => [
            'colors' =>  [
                'text' => ['red'],
                'noise' => ['red']
            ]
        ],
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
    ],

    'methods' => [
        'setParameters' => [
            'setMod' => 'secure',
            'setPool' => 'alpha',
            'setChar' => 5,
            'setFont' => ['NightSkK','AlphaSmoke','Popsf'],
            'setFontSize' => 20,
            'setHeight' => 36,
            'setWave' => false,
            'setColor' => ['red', 'black'],
            'setTrueColor' => false,
            'setNoiseColor' => ['red'],
        ]
    ]
);