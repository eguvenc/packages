<?php

namespace Obullo\Captcha\Provider;

use RuntimeException;
use Obullo\Captcha\CaptchaResult;
use Obullo\Captcha\AbstractProvider;
use Obullo\Captcha\CaptchaInterface;

use Obullo\Url\UrlInterface as Url;
use Obullo\Log\LoggerInterface as Logger;
use Obullo\Session\SessionInterface as Session;
use Obullo\Translation\TranslatorInterface as Translator;

use Psr\Http\Message\UriInterface as Uri;
use Psr\Http\Message\RequestInterface as Request;

/**
 * Captcha Image Provider
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Image extends AbstractProvider implements CaptchaInterface
{
    protected $url;
    protected $request;
    protected $params = array();
    protected $session;
    protected $logger;
    protected $captcha;
    protected $translator;
    protected $html = '';         // Captcha html
    protected $config = array();  // Configuration data
    protected $imageId = '';      // Image unique id
    protected $yPeriod = 12;      // Wave Y axis
    protected $yAmplitude = 14;   // Wave Y amplitude
    protected $xPeriod = 11;      // Wave X axis
    protected $xAmplitude = 5;    // Wave Y amplitude
    protected $scale = 2;         // Wave default scale
    protected $image;             // Gd image content
    protected $code;              // Generated image code
    protected $fonts;             // Actual fonts
    protected $imageUrl;          // Captcha image display url with base url
    protected $width;             // Image width
    protected $configFontPath;    // Font path
    protected $defaultFontPath;   // Default font path
    protected $noiseColor;        // Noise color
    protected $textColor;         // Text color
    protected $imgName;           // Image name
    protected $validation = false;  // Form validation callback

    /**
     * Constructor
     * 
     * @param object $url        \Obullo\Url\UrlInterface
     * @param object $request    \Psr\Http\Message\RequestInterface
     * @param object $session    \Obullo\Session\SessionInterface
     * @param object $translator \Obullo\Translation\TranslatorInterface
     * @param object $logger     \Obullo\Log\LoggerInterface
     * @param array  $params     service parameters
     */
    public function __construct(
        Url $url,
        Request $request,
        Session $session,
        Translator $translator,
        Logger $logger,
        array $params
    ) {
        $this->url = $url;
        $this->request = $request;
        $this->params = $params;
        $this->logger = $logger;
        $this->session = $session;
        $this->translator = $translator;
        $this->params['background'] = 'none';
        $this->init();
        
        $this->logger->debug('Captcha Class Initialized');
    }

    /**
     * Initialize
     * 
     * @return void
     */
    public function init()
    {
        $this->buildHtml();
        $this->imageUrl = $this->url->basePath($this->params['form']['img']['attributes']['src']); // add Directory Seperator ( / )
        $this->defaultFontPath = RESOURCES .'fonts/';
    }

    /**
     * Set background type
     * 
     * Types: "secure" or "none"
     * 
     * @param string $bg background name
     * 
     * @return object
     */
    public function setBackground($bg = 'none')
    {
        $this->params['background'] = $bg;
        return $this;
    }

    /**
     * Set capthca id
     * 
     * @param string $captchaId captcha id
     * 
     * @return void
     */
    public function setInputId($captchaId)
    {
        $this->params['form']['input']['attributes']['id'] = $captchaId;
        return $this;
    }
    
    /**
     * Set image unique id
     * 
     * @param string $uniqId unique id
     * 
     * @return void
     */
    protected function setImageId($uniqId)
    {
        $this->imageId = $uniqId;
    }

    /**
     * Set background noise color
     * 
     * @param mixed $color color
     * 
     * @return object
     * @throws RuntimeException If you set unsupported color then throw exception.
     */
    public function setNoiseColor($color)
    {
        $validColors = $this->isValidColors($color);
        $this->params['text']['colors']['noise'] = $validColors;
        return $this;
    }

    /**
     * Set text color
     * 
     * @param array $color color
     * 
     * @return object
     * @throws RuntimeException If you set unsupported color then throw exception.
     */
    public function setColor($color)
    {
        $validColors = $this->isValidColors($color);
        $this->params['text']['colors']['text'] = $validColors;
        return $this;
    }

    /**
     * Set imagetruecolor() on / off
     * 
     * @param boolean $bool enable / disable true color feature
     *
     * @return void
     */
    public function setTrueColor($bool)
    {
        $this->params['image']['trueColor'] = $bool;
    }

    /**
     * Set text font size
     * 
     * @param int $size font size
     * 
     * @return object
     */
    public function setFontSize($size)
    {
        $this->params['font']['size'] = (int)$size;
        return $this;
    }

    /**
     * Set image height
     * 
     * @param int $height font height
     * 
     * @return object
     */
    public function setHeight($height)
    {
        $this->params['image']['height'] = (int)$height;
        return $this;
    }

    /**
     * Set pool
     * 
     * @param string $pool character pool
     * 
     * @return object
     */
    public function setPool($pool)
    {
        if (isset($this->params['characters']['pools'][$pool])) {
            $this->params['characters']['default']['pool'] = $pool;
        }
        return $this;
    }

    /**
     * Set character length
     * 
     * @param int $length character length
     * 
     * @return object
     */
    public function setChar($length)
    {
        $this->params['characters']['length'] = (int)$length;
        return $this;
    }

    /**
     * Set wave 
     * 
     * @param boolean $wave enable wave for font
     * 
     * @return object
     */
    public function setWave($wave)
    {
        $this->params['image']['wave'] = (bool)$wave;
        return $this;
    }

    /**
     * Set the code generated for CAPTCHA.
     * 
     * @param string $code generated code.
     * 
     * @return string
     */
    protected  function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Set font
     * 
     * @param mixed $font font name
     * 
     * @return object
     */
    public function setFont($font)
    {
        if (! is_array($font)) {
            $str  = str_replace('.ttf', '', $font); // Remove the .ttf extension.
            $font = array($str => $str);
        }
        $this->fonts = array_keys($font);
        return $this;
    }

    /**
     * Get fonts
     * 
     * @return array
     */
    public function getFonts()
    {
        return $this->fonts;
    }

    /**
     * Get captcha input name
     * 
     * @return string name
     */
    public function getInputName()
    {
        return $this->params['form']['input']['attributes']['name'];
    }

    /**
     * Get captcha image url
     * 
     * @return string image asset url
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Get captcha Image UniqId
     * 
     * @return string 
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Get the current captcha code
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Colors validation
     * 
     * @param mix $colors colors
     * 
     * @return If supported colors returns array otherwise get the exception.
     */
    public function isValidColors($colors)
    {
        if (! is_array($colors)) {
            $colors = array($colors);
        }
        foreach ($colors as $val) {
            if (! isset($this->params['colors'][$val])) {
                $invalidColors[] = $val;
            }
        }
        if (isset($invalidColors)) {
            throw new RuntimeException(
                sprintf(
                    'You can not use an unsupported "%s" color(s). It must be chosen from the following colors "%s".',
                    implode(',', $invalidColors),
                    implode(',', array_keys($this->params['colors']))
                )
            );
        }
        return $colors;
    }

    /**
     * Generate image code
     * 
     * @return void
     */
    protected function generateCode()
    {
        $code  = '';
        $defaultPool = $this->params['characters']['default']['pool'];
        $possible    = $this->params['characters']['pools'][$defaultPool];
        $charset     = strtoupper($this->params['locale']['charset']);

        for ($i = 0; $i < $this->params['characters']['length']; $i++) {
            $code .= mb_substr(
                $possible,
                mt_rand(0, mb_strlen($possible, $charset) - 1), 1, $charset
            );
        }
        $this->setCode($code);
    }

    /**
     * Create image captcha ans save into
     * captcha
     *
     * @return void
     */
    public function create()
    {
        $this->generateCode();  // generate captcha code
        $this->imageCreate();
        $this->filledEllipse();

        if ($this->params['image']['wave']) {
            $this->waveImage();
        }
        $this->imageLine();
        $this->imageGenerate(); // The last function for image.
        
        $this->session->set(
            $this->params['form']['input']['attributes']['name'],
            array(
                'image_name' => $this->getImageId(),
                'code'       => $this->getCode(),
                'expiration' => time() + $this->params['image']['expiration']
            )
        );
        $this->init(); // Reset variables
    }

    /**
     * Create image.
     * 
     * @return void
     */
    protected function imageCreate()
    {
        $randTextColor  = $this->params['text']['colors']['text'][array_rand($this->params['text']['colors']['text'])];
        $randNoiseColor = $this->params['text']['colors']['noise'][array_rand($this->params['text']['colors']['noise'])];
        $this->calculateWidth();
        // PHP.net recommends imagecreatetruecolor()
        // but it isn't always available
        if (function_exists('imagecreatetruecolor') && $this->params['image']['trueColor']) {
            $this->image = imagecreatetruecolor($this->width, $this->params['image']['height']);
        } else {
            $this->image = imagecreate($this->width, $this->params['image']['height']) or die('Cannot initialize new GD image stream');
        }
        imagecolorallocate($this->image, 255, 255, 255);
        $explodeColor     = explode(',', $this->params['colors'][$randTextColor]);
        $this->textColor  = imagecolorallocate($this->image, $explodeColor['0'], $explodeColor['1'], $explodeColor['2']);
        $explodeColor     = explode(',', $this->params['colors'][$randNoiseColor]);
        $this->noiseColor = imagecolorallocate($this->image, $explodeColor['0'], $explodeColor['1'], $explodeColor['2']);
    }

    /**
     * Set wave for captcha image
     * 
     * @return void
     */
    protected function waveImage()
    {
        $xp = $this->scale * $this->xPeriod * rand(1, 3);   // X-axis wave generation
        $k  = rand(0, 10);
        for ($i = 0; $i < ($this->width * $this->scale); $i++) {
            imagecopy($this->image, $this->image, $i - 1, sin($k + $i / $xp) * ($this->scale * $this->xAmplitude), $i, 0, 1, $this->params['image']['height'] * $this->scale);
        }
        $k  = rand(0, 10);                                   // Y-axis wave generation
        $yp = $this->scale * $this->yPeriod * rand(1, 2);
        for ($i = 0; $i < ($this->params['image']['height'] * $this->scale); $i++) {
            imagecopy($this->image, $this->image, sin($k + $i / $yp) * ($this->scale * $this->yAmplitude), $i - 1, 0, $i, $this->width * $this->scale, 1);
        }
    }

    /**
     * Calculator width
     * 
     * @return void
     */
    protected function calculateWidth()
    {
        $this->width = ($this->params['font']['size'] * $this->params['characters']['length']) + 40;
    }

    /**
     * Image filled ellipse
     * 
     * @return void
     */
    protected function filledEllipse()
    {
        $fonts = $this->getFonts();
        
        if (sizeof($fonts) == 0) {
            throw new RuntimeException('Image CAPTCHA requires fonts.');
        }
        $randFont = array_rand($fonts);
        $fontPath = $this->defaultFontPath . $fonts[$randFont].'.ttf';

        if ($this->params['background'] != 'none') {

            $wHvalue = $this->width / $this->params['image']['height'];
            $wHvalue = $this->params['image']['height'] * $wHvalue;
            for ($i = 0; $i < $wHvalue; $i++) {
                imagefilledellipse(
                    $this->image,
                    mt_rand(0, $this->width),
                    mt_rand(0, $this->params['image']['height']),
                    1,
                    1,
                    $this->noiseColor
                );
            }
        }
        $textbox = imagettfbbox($this->params['font']['size'], 0, $fontPath, $this->getCode()) or die('Error in imagettfbbox function');
        $x = ($this->width - $textbox[4]) / 2;
        $y = ($this->params['image']['height'] - $textbox[5]) / 2;

        $imageId = md5($this->session->get('session_id') . uniqid(time()));

        $this->setImageId($imageId);  // Generate an unique image id using the session id, an unique id and time.
        imagettftext($this->image, $this->params['font']['size'], 0, $x, $y, $this->textColor, $fontPath, $this->getCode()) or die('Error in imagettftext function');
    }

    /**
     * Image line
     * 
     * @return void
     */
    protected function imageLine()
    {
        if ($this->params['background'] != 'none') {
            $wHvalue = $this->width / $this->params['image']['height'];
            $wHvalue = $wHvalue / 2;
            for ($i = 0; $i < $wHvalue; $i++) {
                imageline(
                    $this->image,
                    mt_rand(0, $this->width),
                    mt_rand(0, $this->params['image']['height']),
                    mt_rand(0, $this->width),
                    mt_rand(0, $this->params['image']['height']),
                    $this->noiseColor
                );
            }
        }
    }

    /**
     * Image generate
     * 
     * @return void
     */
    protected function imageGenerate()
    {
        imagepng($this->image);
        imagedestroy($this->image);
    }

    /**
     * Validation captcha code
     * 
     * @param string $code captcha word
     * 
     * @return Captcha\CaptchaResult object
     */
    public function result($code)
    {
        $inputName = $this->params['form']['input']['attributes']['name'];
        
        if ($data = $this->session->get($inputName)) {
            return $this->validateCode($data, $code);
        }
        $this->result['code'] = CaptchaResult::FAILURE_CAPTCHA_NOT_FOUND;
        $this->result['messages'][] = $this->translator['OBULLO:VALIDATOR:CAPTCHA:NOT_FOUND'];
        return $this->createResult();
    }

    /**
     * Validate captcha code
     * 
     * @param array  $data captcha session data
     * @param string $code captcha code
     * 
     * @return Captcha\CaptchaResult object
     */
    protected function validateCode($data, $code)
    {           
        if ($data['expiration'] < time()) { // Expiration time of captcha ( second )
            $this->session->remove($this->params['form']['input']['attributes']['name']); // Remove captcha data from session.
            $this->result['code'] = CaptchaResult::FAILURE_EXPIRED;
            $this->result['messages'][] = $this->translator['OBULLO:VALIDATOR:CAPTCHA:EXPIRED'];
            return $this->createResult();
        }
        if ($code == $data['code']) {  // Is code correct ?
            $this->session->remove($this->params['form']['input']['attributes']['name']); // Remove
            $this->result['code'] = CaptchaResult::SUCCESS;
            $this->result['messages'][] = $this->translator['OBULLO:VALIDATOR:CAPTCHA:SUCCESS'];
            return $this->createResult();
        }
        $this->result['code'] = CaptchaResult::FAILURE_INVALID_CODE;
        $this->result['messages'][] = $this->translator['OBULLO:VALIDATOR:CAPTCHA:INVALID'];
        return $this->createResult();
    }

    /**
     * Build html
     * 
     * @return void
     */
    protected function buildHtml()
    {
        foreach ($this->params['form'] as $key => $val) {
            if (isset($val['attributes'])) {
                $this->html .= vsprintf(
                    '<%s %s/>',
                    array(
                        $key,
                        $this->buildAttributes($val['attributes'])
                    )
                );
            }
        }
    }

    /**
     * Build attributes
     * 
     * @param array $attributes attributes
     * 
     * @return string
     */
    protected function buildAttributes(array $attributes)
    {
        $attr = array();
        foreach ($attributes as $key => $value) {
            $attr[] = $key.'="'.$value.'"';
        }
        return count($attr) ? implode(' ', $attr) : '';
    }

    /**
     * Print captcha html
     * 
     * @return string html
     */
    public function printHtml()
    {
        return $this->html;
    }

    /**
     * Print refresh button tag
     * 
     * @return string
     */
    public function printRefreshButton()
    {
        return sprintf(
            $this->params['form']['refresh']['button'],
            $this->translator['OBULLO:VALIDATOR:CAPTCHA:REFRESH_BUTTON_LABEL']
        );
    }

    /**
     * Print javascript link
     * 
     * @return string
     */
    public function printJs()
    {
        return;
    }
}