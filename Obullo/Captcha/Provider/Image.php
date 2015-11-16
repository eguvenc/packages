<?php

namespace Obullo\Captcha\Provider;

use RuntimeException;
use Obullo\Captcha\CaptchaResult;
use Obullo\Captcha\AbstractProvider;
use Obullo\Captcha\ProviderInterface;

use Obullo\Log\LoggerInterface;
use Obullo\Url\UrlInterface;
use Obullo\Session\SessionInterface;
use Obullo\Container\ContainerInterface;
use Obullo\Translation\TranslatorInterface;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Captcha Image Provider
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Image extends AbstractProvider implements ProviderInterface
{
    protected $c;
    protected $url;
    protected $request;
    protected $session;
    protected $logger;
    protected $captcha;
    protected $translator;
    protected $html = '';         // Captcha html
    protected $config = array();  // Configuration data
    protected $imageId = '';      // Image unique id
    protected $mods = ['cool', 'secure'];
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

    /**
     * Constructor
     *
     * @param object $c          \Obullo\Container\ContainerInterface
     * @param object $url        \Obullo\Url\UrlInterface
     * @param object $request    \Psr\Http\Message\RequestInterface
     * @param object $session    \Obullo\Session\SessionInterface
     * @param object $translator \Obullo\Translation\TranslatorInterface
     * @param object $logger     \Obullo\Log\LoggerInterface
     * @param array  $params     service parameters
     */
    public function __construct(
        ContainerInterface $c,
        UrlInterface $url,
        RequestInterface $request,
        SessionInterface $session,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        array $params
    ) {
        $this->c = $c;
        $this->url = $url;
        $this->request = $request;
        $this->config = $params;
        $this->logger = $logger;
        $this->session = $session;
        $this->translator = $translator;
        $this->translator->load('captcha');
        $this->config['mod'] = 'cool';
        $this->init();
        $this->logger->debug('Captcha Class Initialized');
    }

    /**
     * Set captcha parameters
     * 
     * @param array $params parameters
     *
     * @return $this
     */
    public function setParameters($params = array())
    {
        if (count($params) > 0) {
            foreach ($params as $method => $arg) {
                $this->{$method}($arg);
            }
        }
    }

    /**
     * Initialize
     * 
     * @return void
     */
    public function init()
    {
        $this->buildHtml();
        $this->fonts = array_keys($this->config['fonts']);
        $this->imageUrl = $this->url->getSiteUrl($this->config['form']['img']['attributes']['src']); // add Directory Seperator ( / )
        $this->configFontPath  = ROOT . $this->config['font']['path'] . '/';
        $this->defaultFontPath = OBULLO . 'Captcha/Fonts/';
    }

    /**
     * Set captcha mode
     * 
     * Types: "secure" or "cool"
     * 
     * @param string $mod string
     * 
     * @return object
     */
    public function setMod($mod)
    {
        if (! $this->isAllowedMod($mod)) {
            throw new RuntimeException(
                sprintf(
                    'Unsupported mod. You can choose from the following list "%s".',
                    implode(',', $this->mods)
                )
            );
        }
        $this->config['mod'] = $mod;
        return $this;
    }

    /**
     * Set capthca id
     * 
     * @param string $captchaId captcha id
     * 
     * @return void
     */
    public function setCaptchaId($captchaId)
    {
        $this->config['form']['input']['attributes']['id'] = $captchaId;
        return $this;
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
        $this->config['text']['colors']['noise'] = $validColors;
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
        $this->config['text']['colors']['text'] = $validColors;
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
        $this->config['image']['trueColor'] = $bool;
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
        $this->config['font']['size'] = (int)$size;
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
        $this->config['image']['height'] = (int)$height;
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
        if (isset($this->config['characters']['pools'][$pool])) {
            $this->config['characters']['default']['pool'] = $pool;
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
        $this->config['characters']['length'] = (int)$length;
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
        $this->config['image']['wave'] = (bool)$wave;
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
        $this->fonts = $font;
        return $this;
    }

    /**
     * Exclude font you don't want
     * 
     * @param mixed $font font
     * 
     * @return object
     */
    public function excludeFont($font)
    {
        if (! is_array($font)) {
            $font = array($font);
        }
        $this->setFont(array_diff($this->getFonts(), $font));
        return $this;
    }

    /**
     * Append font
     * 
     * @param string $font font name
     * 
     * @return object
     */
    public function appendFont($font)
    {
        $this->fonts[] = str_replace('.ttf', '', $font); // Remove the .ttf extension.

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
        return $this->config['form']['input']['attributes']['name'];
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
     * Is permitted mod
     * 
     * @param string $mod mod
     * 
     * @return boolean
     */
    public function isAllowedMod($mod)
    {
        if (! in_array(strtolower($mod), $this->mods)) {
            return false;
        }
        return true;
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
            if (! isset($this->config['colors'][$val])) {
                $invalidColors[] = $val;
            }
        }
        if (isset($invalidColors)) {
            throw new RuntimeException(
                sprintf(
                    'You can not use an unsupported "%s" color(s). It must be chosen from the following colors "%s".',
                    implode(',', $invalidColors),
                    implode(',', array_keys($this->config['colors']))
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
        $defaultPool = $this->config['characters']['default']['pool'];
        $possible = $this->config['characters']['pools'][$defaultPool];

        for ($i = 0; $i < $this->config['characters']['length']; $i++) {
            $code .= mb_substr(
                $possible,
                mt_rand(0, mb_strlen($possible, $this->config['locale']['charset']) - 1), 1, $this->config['locale']['charset']
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
        if ($this->config['image']['wave']) {
            $this->waveImage();
        }
        $this->imageLine();
        $this->imageGenerate(); // The last function for image.
        
        $this->session->set(
            $this->config['form']['input']['attributes']['name'],
            array(
                'image_name' => $this->getImageId(),
                'code'       => $this->getCode(),
                'expiration' => time() + $this->config['image']['expiration']
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
        $randTextColor  = $this->config['text']['colors']['text'][array_rand($this->config['text']['colors']['text'])];
        $randNoiseColor = $this->config['text']['colors']['noise'][array_rand($this->config['text']['colors']['noise'])];
        $this->calculateWidth();
        // PHP.net recommends imagecreatetruecolor()
        // but it isn't always available
        if (function_exists('imagecreatetruecolor') && $this->config['image']['trueColor']) {
            $this->image = imagecreatetruecolor($this->width, $this->config['image']['height']);
        } else {
            $this->image = imagecreate($this->width, $this->config['image']['height']) or die('Cannot initialize new GD image stream');
        }
        imagecolorallocate($this->image, 255, 255, 255);
        $explodeColor     = explode(',', $this->config['colors'][$randTextColor]);
        $this->textColor  = imagecolorallocate($this->image, $explodeColor['0'], $explodeColor['1'], $explodeColor['2']);
        $explodeColor     = explode(',', $this->config['colors'][$randNoiseColor]);
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
            imagecopy($this->image, $this->image, $i - 1, sin($k + $i / $xp) * ($this->scale * $this->xAmplitude), $i, 0, 1, $this->config['image']['height'] * $this->scale);
        }
        $k  = rand(0, 10);                                   // Y-axis wave generation
        $yp = $this->scale * $this->yPeriod * rand(1, 2);
        for ($i = 0; $i < ($this->config['image']['height'] * $this->scale); $i++) {
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
        $this->width = ($this->config['font']['size'] * $this->config['characters']['length']) + 40;
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
        $fontPath = $this->defaultFontPath . $this->config['fonts'][$fonts[$randFont]];

        if (strpos($fonts[$randFont], '.ttf')) {
            $fontPath = $this->configFontPath . $this->config['fonts'][$fonts[$randFont]];
        }
        if ($this->config['mod'] != 'cool') {
            $wHvalue = $this->width / $this->config['image']['height'];
            $wHvalue = $this->config['image']['height'] * $wHvalue;
            for ($i = 0; $i < $wHvalue; $i++) {
                imagefilledellipse(
                    $this->image,
                    mt_rand(0, $this->width),
                    mt_rand(0, $this->config['image']['height']),
                    1,
                    1,
                    $this->noiseColor
                );
            }
        }
        $textbox = imagettfbbox($this->config['font']['size'], 0, $fontPath, $this->getCode()) or die('Error in imagettfbbox function');
        $x = ($this->width - $textbox[4]) / 2;
        $y = ($this->config['image']['height'] - $textbox[5]) / 2;

        $this->setImageId(md5($this->session->get('session_id') . uniqid(time())));  // Generate an unique image id using the session id, an unique id and time.
        imagettftext($this->image, $this->config['font']['size'], 0, $x, $y, $this->textColor, $fontPath, $this->getCode()) or die('Error in imagettftext function');
    }

    /**
     * Image line
     * 
     * @return void
     */
    protected function imageLine()
    {
        if ($this->config['mod'] != 'cool') {
            $wHvalue = $this->width / $this->config['image']['height'];
            $wHvalue = $wHvalue / 2;
            for ($i = 0; $i < $wHvalue; $i++) {
                imageline(
                    $this->image,
                    mt_rand(0, $this->width),
                    mt_rand(0, $this->config['image']['height']),
                    mt_rand(0, $this->width),
                    mt_rand(0, $this->config['image']['height']),
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
        header('Content-Type: image/png');
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
    public function result($code = null)
    {
        if ($code == null) {
            $code = $this->request->post($this->config['form']['input']['attributes']['name']);
        }
        if ($data = $this->session->get($this->config['form']['input']['attributes']['name'])) {
            return $this->validateCode($data, $code);
        }
        $this->result['code'] = CaptchaResult::FAILURE_CAPTCHA_NOT_FOUND;
        $this->result['messages'][] = $this->translator['OBULLO:CAPTCHA:NOT_FOUND'];
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
            $this->session->remove($this->config['form']['input']['attributes']['name']); // Remove captcha data from session.
            $this->result['code'] = CaptchaResult::FAILURE_EXPIRED;
            $this->result['messages'][] = $this->translator['OBULLO:CAPTCHA:EXPIRED'];
            return $this->createResult();
        }
        if ($code == $data['code']) {  // Is code correct ?
            $this->session->remove($this->config['form']['input']['attributes']['name']); // Remove
            $this->result['code'] = CaptchaResult::SUCCESS;
            $this->result['messages'][] = $this->translator['OBULLO:CAPTCHA:SUCCESS'];
            return $this->createResult();
        }
        $this->result['code'] = CaptchaResult::FAILURE_INVALID_CODE;
        $this->result['messages'][] = $this->translator['OBULLO:CAPTCHA:INVALID'];
        return $this->createResult();
    }

    /**
     * Build html
     * 
     * @return void
     */
    protected function buildHtml()
    {
        foreach ($this->config['form'] as $key => $val) {
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
        return sprintf($this->config['form']['refresh']['button'], $this->translator['OBULLO:CAPTCHA:REFRESH_BUTTON_LABEL']);
    }

    /**
     * Print javascript link
     * 
     * @return string
     */
    public function printJs()
    {
        return sprintf(
            $this->config['form']['refresh']['script'],
            $this->config['form']['img']['attributes']['id'],
            $this->config['form']['img']['attributes']['src'],
            $this->config['form']['input']['attributes']['id']
        );
    }

    /**
     * We call this function using $this->validator->bind($this->captcha) method.
     * 
     * @return void
     */
    public function callbackFunction()
    {
        $post  = $this->request->isPost();
        $label = $this->translator['OBULLO:CAPTCHA:LABEL'];
        $rules = 'required|exact('.$this->config['characters']['length'].')|trim';

        if ($this->config['form']['validation']['callback'] && $post) {  // Add callback if we have http post
            $rules.= '|callback_captcha';  // Add callback validation rule
            $self = $this;
            $this->c['validator']->func(
                'callback_captcha',
                function () use ($self, $label) {
                    if ($self->result()->isValid() == false) {
                        $this->setMessage($this->translator->get('OBULLO:CAPTCHA:VALIDATION', $label));
                        return false;
                    }
                    return true;
                }
            );
        }
        if ($post) {
            $this->c['validator']->setRules(
                $this->config['form']['input']['attributes']['name'],
                $label,
                $rules
            );
        }
    }
}