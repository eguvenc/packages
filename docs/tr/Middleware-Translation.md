
## Translation Katmanı

> Uygulamaya gelen http isteklerinin tümü için <b>locale</b> anahtarlı çereze varsayılan yerel dili yada url den gönderilen dili kaydeder.

### Konfigürasyon

Uygulamanın tüm isteklerinde evrensel olarak çalışan bir katmandır. <kbd>app/middlewares.php</kbd> dosyası içerisinde tanımlanması gerekir.

> **Not:** Http katmanlarında önemlilik sırası en yüksek olan katman en son tanımlanan katmandır.

```php
/*
|--------------------------------------------------------------------------
| Translations
|--------------------------------------------------------------------------
*/
$c['app']->middleware(new Http\Middlewares\Translation);

/*
|--------------------------------------------------------------------------
| Request
|--------------------------------------------------------------------------
*/
$c['app']->middleware(new Http\Middlewares\Request);


/* Location: .app/middlewares.php */
```

Ayrıca translation paketinin konfigürasyon dosyası <kbd>config/translator.php</kbd> dosyasını konfigüre etmeyi unutmayın.

```php
return array(

    'locale' => [
        'default'  => 'en',  // Default selected.
    ],

    'fallback' => [
        'enabled' => false,
        'locale' => 'es',
    ],

    'uri' => [
        'segment'       => true, // Uri segment number e.g. http://example.com/en/home
        'segmentNumber' => 0       
    ],

    'cookie' => [
        'name'   =>'locale',               // Translation value cookie name
        'domain' => $c['var']['COOKIE_DOMAIN.null'], // Set to .your-domain.com for site-wide cookies
        'expire' => (365 * 24 * 60 * 60),  // 365 day
        'secure' => false,                 // Cookie will only be set if a secure HTTPS connection exists.
        'httpOnly' => false,               // When true the cookie will be made accessible only 
        'path' => '/',                     // through the HTTP protocol
    ],

    'languages' => [
                        'en' => 'english', // Available Languages
                        'de' => 'deutsch',
                        'es' => 'spanish',
                        'tr' => 'turkish',
                        'fr' => 'french',
                    ],

    'debug' => false,


/* Location: ./config/translator.php */
```

### Kurulum

```php
php task middleware add Translation
```

### Kaldırma

```php
php task middleware remove Translation
```

Ayrıca <kbd>app/middlewares.php</kbd> dosyası içerisinden katmanı silin.
Varsa <kbd>app/routes.php</kbd> dosyasından ilgili route ları kaldırın.

### Çalıştırma

Yerel dilin doğru seçilebilmesi için herbir route grubunuza aşağıdaki gibi desktelenen dilleri içeren (?:en|tr|de) gibi bir yazım kuralı eklemeniz gerekir.

```php
$c['router']->group(
    [
        'name' => 'Locale',
        'domain' => 'mydomain.com',
        'middleware' => array()
    ],
    function () {

        $this->defaultPage('welcome');

        $this->get('(?:en|tr|de|nl)/(.*)', '$1');  // For dynamic url requests http://example.com/en/welcome
        $this->get('(?:en|tr|de|nl)', 'welcome');  // For default page request http://example.com/en
    }
);
```

Uygulamanızı <kbd>http://myproject/en/welcome</kbd> gibi ziyaret ettiğinizde yerel dil <b>locale</b> adlı çereze <b>en</b> olarak kaydedilecektir. Artık geçerli yerel dili <kbd>$this->c['translator']->getLocale()</kbd> fonksiyonu ile çağırabilirsiniz.