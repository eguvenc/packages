
## Kullanıcı Ajanı Sınıfı

Kullanıcı ajanı sınıfı kullanıcının tarayıcı bilgilerini tanımlamaya yardımcı olur. Bu bilgiler kullanıcının bir robot mu gerçek mi olduğu kullanıcının sitenizi ziyaret ederken hangi aygıtı kullandığını ( mobil, desktop, tablet ) içeren bilgilerdir. Ek olarak tarayıcının desteklediği dil ve karakter türü gibi referens bilgileri de bu sınıf yardımı ile elde edilir.

<ul>
    <li><a href="#loading-class">Sınıfı Yüklemek</a></li>
    <li>
        <a href="#methods">Metotlar</a>
        <ul>
            <li><a href="#isBrowser">$this->agent->isBrowser()</a></li>
            <li><a href="#isRobot">$this->agent->isRobot()</a></li>
            <li><a href="#isMobile">$this->agent->isMobile()</a></li>
            <li><a href="#isReferral">$this->agent->isReferral()</a></li>
            <li><a href="#getAgent">$this->agent->getAgent()</a></li>
            <li><a href="#getPlatform">$this->agent->getPlatform()</a></li>
            <li><a href="#getBrowser">$this->agent->getBrowser()</a></li>
            <li><a href="#getBrowserVersion">$this->agent->getBrowserVersion()</a></li>
            <li><a href="#getRobotName">$this->agent->getRobotName()</a></li>
            <li><a href="#getMobileDevice">$this->agent->getMobileDevice()</a></li>
            <li><a href="#getReferrer">$this->agent->getReferrer()</a></li>
            <li><a href="#getLanguages">$this->agent->getLanguages()</a></li>
            <li><a href="#getCharsets">$this->agent->getCharsets()</a></li>
            <li><a href="#getAcceptLang">$this->agent->getAcceptLang()</a></li>
            <li><a href="#getAcceptCharset">$this->agent->getAcceptCharset()</a></li>
            <li><a href="#getConfigName">$this->agent->getConfigName()</a></li>
        </ul>
    </li>
</ul>

<a name="loading-class"></a>

### Sınıfı Yüklemek

```php
$this->c['agent']->method();
```

> **Not:** Kontrolör sınıfı içerisinden bu sınıfa $this->agent yöntemi ile de ulaşılabilir. User agent sınıfı <kbd>app/components.php</kbd> komponent olarak tanımlıdır.

Kullanıcı ajanı sınıfı ilk yüklendiği anda sitenizi ziyaret eden kullanıcının aygıtını tanımlamaya çalışır. Bu aygıt bir web tarayıcısı, bir mobil araç yada bir robot olabilir. Ayrıca aygıtın çalıştığı platform bilgisini de elde eder.

<a name="methods"></a>

### Metotlar

<a name="isBrowser"></a>

##### $this->agent->isBrowser();

Eğer kullanıcı ajanı bilinen bir tarayıcı ise <b>true</b> değerine aksi durumda <b>false</b> değerine geri döner.

```php
if ($this->agent->isBrowser()) {
    echo $this->agent->getBrowser().'/'.$this->agent->getBrowserVersion();  // Safari/537.36
}
```
> **Not:** Bu örnekteki Safari tarayıcısı tarayıcı tanımlamalarında mevctuttut. Eğer tanımlı olmayan tarayıcılar uygulamanızı ziyaret ediyorsa bu tarayıcıları .config/agents.php dosyasına eklemeniz önerilir.

<a name="isRobot"></a>

##### $this->agent->isRobot();

Eğer kullanıcı ajanı bilinen bir robot ise <b>true</b> değerine aksi durumda <b>false</b> değerine geri döner.

```php
if ($this->agent->isRobot()) {
    echo 'This is a '. $this->agent->getRobotName() .' robot.';
}
```

> **Not:** Kullanıcı ajanı sınıfı konfigürasyon dosyasında en çok bilinen robot tanımlamalarını içerir. Eğer uygulamanızı sürekli ziyaret eden konfigürasyon dosyasında tanımlı olmayan bir robot ismi varsa  .config/agents.php dosyasında ekleyebilirsiniz.

<a name="isMobile"></a>

##### $this->agent->isMobile();

Eğer kullanıcı ajanı bilinen bir mobil aygıt ise <b>true</b> değerine aksi durumda <b>false</b> değerine geri döner.

```php
if ($this->agent->isMobile()) {
    echo 'This is a '. $this->agent->getMobileDevice() .' mobil device.';
}
```
<a name="isReferral"></a>

##### $this->agent->isReferral();

Eğer kullanıcı ajanı başka bir siteden referanslı ise <b>true</b> değerine aksi durumda <b>false</b> değerine geri döner.

```php
if ($this->agent->isReferral()) {
    $referrer = $this->agent->getReferrer();
}
```

<a name="getAgent"></a>

##### $this->agent->getAgent();

Kullanıcı ajanı bilgilerini içeren tam çıktıya geri döner. Bu çıktı genellikle aşağıdaki gibi gözükür.

```php
* The PC:
    * Mozilla/5.0 
        (X11; Ubuntu; Linux x86_64; rv:34.0) 
        Gecko/20100101 Firefox/34.0
    * Mozilla/5.0 
        (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 
        (KHTML, like Gecko) Version/7.0.3 Safari/537.75.14
    * Mozilla/5.0 
        (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)
* The Mobile Phone:
    * Mozilla/5.0 
        (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 
        (KHTML, like Gecko) Chrome/34.0.1847.114 Mobile Safari/537.36
```

<a name="getPlatform"></a>

##### $this->agent->getPlatform();

Uygulamanızı ziyaret eden kullanıcının işletim sistemi adına geri döner (Linux, Windows, OS X, etc.).

```php
* The PC:
    * Linux
    * Mac OS X
    * Windows 7
* The Mobile Phone:
    * Android
```

<a name="getBrowser"></a>

##### $this->agent->getBrowser();

Uygulamanızı ziyaret eden web tarayıcısının ismine geri döner.

```php
* The PC:
    * Firefox
    * Safari
    * Internet Explorer
* The Mobile Phone:
    * Chrome
```

<a name="getBrowserVersion"></a>

##### $this->agent->getBrowserVersion();

Uygulamanızı ziyaret eden web tarayıcısının versiyon numarasına geri döner.

```php
* The PC:
    * 34.0
    * 537.75.14
    * 10.0
* The Mobile Phone:
    * 34.0
```

<a name="getRobotName"></a>

##### $this->agent->getRobotName();

Web sitenizi ziyaret eden robotun (bot) ismine geri döner.

```php
if ($this->agent->isRobot()) {
    echo $this->agent->getRobotName();  // Googlebot   
}
```

<a name="getMobileDevice"></a>

##### $this->agent->getMobileDevice();

Web sitenizi ziyaret eden mobil aygıtın ismine geri döner.

```php
if ($this->agent->isMobile()) {
    echo  $this->agent->getMobileDevice();  // Android
}
```

<a name="getReferrer"></a>

##### $this->agent->getReferrer();

Http referer yani web sitenizi ziyaret etmeden önceden ziyaret edilen diğer url adresine geri döner.

<a name="getLanguages"></a>

##### $this->agent->getLanguages();

Tarayıcı tarafından kabul edilen dillere bir dizi içerisinde geri döner.

```php
print_r($this->agent->getLanguages());
```

```php
Array ( [0] => en-us [1] => en )
```

<a name="getCharsets"></a>

##### $this->agent->getCharsets();

Tarayıcıda tanımlı olan karakter setlerine bir dizi içerisinde geri döner.

```php
print_r($this->agent->getCharsets());
```

```php
Array ( [0] => utf-8 )
```

```php
print_r($this->agent->getCharsets());
```

```php
Array ( [0] => Undefined )
```

<a name="getAcceptLang"></a>

##### $this->agent->getAcceptLang($lang = 'en');

Eğer kullanıcı tarayıcısı belirli bir dili destekliyorsa <b>true</b> değerine aksi durumda <b>false</b> değerine geri döner.

```php
if ($this->agent->getAcceptLang('en')) {
    echo 'Yes ! Accept english !';
}
```

> **Not:** Bu fonksiyon her zaman güvenilir sonuçlar vermez ve bazı tarayıcılar bu özelliği desteklemez.

<a name="getAcceptCharset"></a>

##### $this->agent->getAcceptCharset($charset = 'en');

```php
if ($this->agent->getAcceptCharset('utf-8')) {
    echo 'Yes ! Accept utf-8 !';
}
```

<a name="getConfigName"></a>

##### $this->agent->getConfigName(string $method);

Sınıf içerisindeki bir metodun değerine ait konfigürasyon dosyasında tanımlı olan anahtar ismine geri döner.

```php
echo $this->agent->getConfigName('platform');  // linux
echo $this->agent->getConfigName('browser');   // Firefox
echo $this->agent->getConfigName('mobile');    // android
echo $this->agent->getConfigName('robot');    //  msnbot
```