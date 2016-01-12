
## Çeviri Sınıfı

Çeviri sınıfı uygulamanızı birden fazla farklı dilde yayına hazırlamak için dil dosyalarından dil metinlerini içeren satırları elde etmeyi sağlarlar. Dil dosyaları uygulamanızın <kbd>resources/translations</kbd> klasöründe tutulurlar. Hata mesajları ve diğer mesajları göstermek için kendi dil dosyalarınızı bu klasörde oluşturabilirsiniz.

> **Note:** Her bir dil dosyası kendine ait klasör içerisinde kayıtlı olmalıdır. Mesela ispanyolca dosyaları <kbd>app/translations/es</kbd> klasörüne kaydedilmelidir.

<ul>
    <li><a href="#creating-files">Dil Dosyaları Oluşturmak</a></li>
    <li><a href="#loading-files">Dil Dosyalarını Yüklemek</a></li>
    <li><a href="#fetching-lines">Dil Satırlarını Almak</a></li>
    <li><a href="#checking-lines">Dil Satırı Kontrolü</a></li>
    <li>
        <a href="#translation-middleware">Http Dil Katmanı</a>
        <ul>
            <li><a href="#config">Konfigürasyon</a></li>
            <li><a href="#how-it-works">Nasıl Çalışıyor</a></li>
        </ul>
    </li>
    <li><a href="#getLocale">Mevcut Dili Almak ve Değiştirmek</a></li>
    <li><a href="#routes">Routes.php için Dil Ayarları</a></li>
    <li><a href="#fallback">Bulunamayan Dil</a></li>
    <li><a href="#fallback-lines">Bulunamayan Dil Çevirisi</a></li>
</ul>

<a name="creating-files"></a> 

### Dil Dosyaları Oluşturmak

Dil dosyaları <kbd>resources/translations</kbd> klasörü altında oluşturulur. Her bir dil dosyası kendine ait klasör içerisinde kayıtlıdır. Uygulamanızdaki dil dosyalarına ait satırların birbirleriyle karışmaması için en iyi yöntemlerden biri satırları kategorilere göre <kbd>:</kbd> karakteri ile ayırmaktır. Örneğin "Games" adında bir modülümüz olduğunu varsayarsak bu modüle ait dil satırlarının aşağıdaki gibi olması önerilir.

```php
return array(

    /**
     * Label
     */
    'GAMES:MANAGEMENT:LABEL:ID'              => 'Id',
    'GAMES:MANAGEMENT:LABEL:NAME'            => 'Game Name',
    'GAMES:MANAGEMENT:LABEL:CATEGORIES'      => 'Categories',
    'GAMES:MANAGEMENT:LABEL:STATUS'          => 'Status',
    'GAMES:MANAGEMENT:LABEL:ORDER'           => 'Order',
    'GAMES:MANAGEMENT:LABEL:GAME_URL'        => 'Game URL',
    'GAMES:MANAGEMENT:LABEL:GAME_IP'         => 'Game IP',
    'GAMES:MANAGEMENT:LABEL:GAME_RESOLUTION' => 'Game Resolution',
    'GAMES:MANAGEMENT:LABEL:IMAGE'           => 'Image',
    'GAMES:MANAGEMENT:LABEL:DESCRIPTION'     => 'Description',
    'GAMES:MANAGEMENT:LABEL:ACTIVE'          => 'Active',
    'GAMES:MANAGEMENT:LABEL:PASSIVE'         => 'Passive',
    'GAMES:MANAGEMENT:LABEL:FILTER_ALL'      => 'All',
    'GAMES:MANAGEMENT:LABEL:EDIT'            => 'Edit',

    /**
     * Link
     */
    'GAMES:MANAGEMENT:LINK:EDIT'    => 'Edit Game',
    'GAMES:MANAGEMENT:LINK:ADD_NEW' => 'Add New Games',

    /**
     * Error
     */
    'GAMES:MANAGEMENT:ERROR:NOTVALIDRESOLUTION' => 'Game resolution is not valid. Ex: 800x600',

    /**
     * Button
     */
    'GAMES:MANAGEMENT:BUTTON:FILTER' => 'Filter',
    'GAMES:MANAGEMENT:BUTTON:SUBMIT' => 'Submit',

    /**
     * Notice
     */
    'GAMES:MANAGEMENT:NOTICE:CREATE' => 'Game successfully added.',
    'GAMES:MANAGEMENT:NOTICE:UPDATE' => 'Game successfully updated.',
);
```

<a name="loading-files"></a> 

### Dil Dosyalarını Yüklemek

Bir dil dosyası oluşturduktan sonra dosyayı kullanabilmek için ilk önce load() metodu kullanılır.

```php
- resources
    - translations
        - en
            games.php
```

```php
$this->translator->load('games');
```

<a name="fetching-lines"></a> 

### Dil Satırlarını Almak

Bir dil metnine ait değere basitçe ulaşmak için aşağıdaki yöntem kullanılır.

```php
echo $this->translator['GAMES:MANAGEMENT:LABEL:NAME'];  // Game Name
```

Eğer metin içerisinde <kbd>%s</kbd> yada <kbd>%d</kbd> gibi formatlar kullanılmışsa aşağıdaki gibi get() metodu kullanılır. Get metodu php <b>sprintf()</b> fonksiyonu gibi çalışır.

```php
echo $this->translator->get('OBULLO:VALIDATOR:MIN', 'Email', 6);  // The Email field must be at least 6 ch..
```

Eğer girilen metine ait dil satırı ilgili dil dosyasında bulunamazsa girilen metin çıktılanır.

```php
echo $this->translator['GAMES:MANAGEMENT:LABEL:NAME'];  // GAMES:MANAGEMENT:LABEL:NAME
```

<a name="checking-lines"></a>

### Dil Satırı Kontrolü

Bir dile ait satırın var olup olmadığını kontröl etmek için <kbd>has()</kbd> metodu kullanılır.

```php
var_dump($this->translator->has('EXAMPLE:UNDEFINED_TEXT'));  // false
```

<a name="translation-middleware"></a>

### Http Dil Katmanı

Dil katmanı aşağıdaki gibi bir istek geldiğinde eğer ziyaretçi tarayıcısında <kbd>locale</kbd> adlı çerez mevcut değilse varsayılan dili çereze kaydeder.

```php
http://example.com/en/home
```

<a name="config"></a>

#### Konfigürasyon

Dil katmanının çalıştırılmadan önce konfigüre edilmesi gerekir.

```php
$c['middleware']->add(
    [
        // 'Maintenance',
        // 'TrustedIp',
        // 'ParsedBody',
        'Translation',
        'View', 
        'Router',
        // 'Csrf'
    ]
);
```

Dil konfigürasyonu <kbd>app/$env/translator.php</kbd> dosyasında varsayılan olarak aşağıdaki gibi tanımlıdır.


```php
'uri' => [
    'segment' => true,
    'segmentNumber' => 0   // Uri segment number e.g. http://example.com/en/home    
],
'cookie' => [
    'name'   =>'locale', 
    'domain' => null,
    'expire' => (365 * 24 * 60 * 60), // 365 day
    'secure' => false,
    'httpOnly' => false,
    'path' => '/',
],
```

<a name="how-it-works"></a>

#### Nasıl Çalışıyor ?

Kullanıcı siteyi ziyaret ettiğinde dil katmanı çalışır ve aşağıdaki adımlardan sonra dil $çerezler[locale] değeri içerisinden okunur yada çerezlere $çerezler[locale] = 'dil' olarak kaydedilir.

Kullanıcının varsayılan dili <b>sırasıyla</b> aşağıdaki adımlara göre belirlenir.

* Eğer ziyaretçi <kbd>http://example.com/en/welcome</kbd> gibi bir URI get isteği ile geldiyse dil <kbd>en</kbd> olarak kaydedilir.
* Eğer ziyaretçi tarayıcısında <kbd>$_COOKIES['locale']</kbd> değeri mevcut ise varsayılan dil bu kabul edilir.
* Eğer ziyaretçinin tarayıcısı <kbd>locale_accept_from_http()</kbd> fonksiyonu ile incelenir ve tarayıcının geçerli dili bulunursa varsayılan dil bu kabul edilir.
* Eğer yukarıdaki tüm seçenekler ile mevcut dil bulunamazsa <kbd>$this->translator->getDefault()</kbd> metodu ile translator.php konfigürasyonunuzdaki <kbd>config['default']['locale']</kbd> değeri ile varsayılan dil belirlenir.

> **Not:** locale_accept_from_http() fonksiyonu <b>intl</b> genişlemesi gerektirir. Bu yüzden bu genişlemenin php konfigürasyonunuzda kurulu olması tavsiye edilir.

<a name="getLocale"></a>

### Mevcut Dili Almak ve Değiştirmek

Bir http isteği ile dil katmanı sayesinde çerezler içerisine kaydedilen varsayılan dili almak için aşağıdaki metot kullanılır.

```php
$this->translator->getLocale();  // en
```

Eğer çerezde kayıtlı varsayılan dili değiştirmek istiyorsanız set metodunu kullanmanız gerekir.

```php
$this->translator->setLocale('en');
```

Eğer çereze yazmak istemiyorsanız ikinci parametreyi <kbd>false</kbd> olarak girmeniz gerekir.

```php
$this->translator->setLocale('en', false);
```

<a name="routes"></a>

### Routes.php için Dil Ayarları

Eğer uygulamanızın <kbd>http://example.com/en/welcome/index</kbd> gibi bir dil desteği ile çalışmasını istiyorsanız aşağıdaki route kurallarını <kbd>app/routes.php</kbd> dosyası içerisine tanımlamanız gerekir.

```php
$c['router']->get('(en|es|de)/(.+)', '$2');
$c['router']->get('(en|es|de)', 'welcome/index');    
```

* İlk kural dil segmentinden sonra kontrolör, metot ve parametre çalıştırmayı sağlar. ( örn. http://example.com/en/examples )
* İkinci kural ise varsayılan açılış sayfası içindir. ( örn. http://example.com/en/ )

> **Not:** Uygulamanızın desteklediği dilleri düzenli ifadelerdeki parentez içlerine girmeniz gerekir. Yukarıda en,es ve de dilleri örnek gösterilmiştir.

<a name="fallback"></a>

### Bulunamayan Dil

Eğer kullanıcının seçtiği dile ait klasör uygulamanızda mevcut değilse bunun yerine başka bir dil yüklenir. Bu yönteme fallback denilir ve fallback dilini almak için aşağıdaki metot kullanılır.

```php
$this->translator->getFallback();  // en
```

Fallback değeri <kbd>translator.php</kbd> konfigürasyon dosyanızda tanımlıdır. Fallback dili Translation katmanı içerisindeki setFallback() fonksiyonu ile kontrol edilir.

```php
protected function setFallback()
{
    $fallback = $this->translator->getFallback();

    if (! $this->translator->hasFolder($this->getLocale())) {
        $this->translator->setLocale($fallback);
    }
}
```

Yukarıdaki satırlarda eğer seçilen dile ait klasör mevcut değilse translator sınıfı dil değeri fallback değeri ile güncelleniyor.

<a name="fallback-lines"></a>

### Bulunamayan Dil Çevirisi

Mevcut yüklü dil dosyanızda bir çeviri metni bulunamazsa fallback dil dosyanız devreye girer ve fallback dosyası yüklenerek mevcut olmayan çeviri bu dosya içerisinden çağrılır.
Bu özelliği kullanabilmek için <kbd>translator.php</kbd> konfigürasyon dosyanızdaki fallback değerinin <b>true</b> olması gerekir.

```php
'fallback' => array(
    'enabled' => true,
),
```