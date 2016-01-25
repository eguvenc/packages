
## Url Sınıfı

Url sınıfı uygulamanızda kullandığınız iç ve dış html linklerini oluşturmanıza yardımcı olmayı sağlayan fonksiyonları içerir.

<ul>
    <li>
        <a href="#setup">Kurulum</a>
        <ul>
            <li><a href="#config">Servis Konfigürasyonu</a></li>
        </ul>
    </li>

    <li>
        <a href="#methods">Metotlara Erişim</a>
        <ul>
            <li><a href="#anchor">$this->url->anchor()</a></li>
            <li><a href="#withProtocolAnchor">$this->url->withProtocol()->anchor()</a></li>
            <li><a href="#asset">$this->url->asset()</a></li>
            <li><a href="#withUrlAsset">$this->url->withUrl()->asset()</a></li>
            <li><a href="#baseUrl">$this->url->baseUrl()</a></li>
            <li><a href="#siteUrl">$this->url->siteUrl()</a></li>
            <li><a href="#withProtocolSiteUrl">$this->url->withProtocol()->siteUrl()</a></li>
            <li><a href="#currentUrl">$this->url->currentUrl()</a></li>
            <li><a href="#prep">$this->url->prep()</a></li>
        </ul>
    </li>
</ul>

### Servis Konfigürasyonu

<a name="config"></a>

Url sınıfı <kbd>app/components.php</kbd> dosyasında servis olarak tanımlıdır. Url sınıfına ait konfigürasyon parametreleri <kbd>app/$env/providers/url.php</kbd> dosyasından konfigüre edilir.

```php
'params' => [

    'baseurl'  => '/',
    'assets'   => [
        'url' => '/',
        'folder' => '/assets/',
    ]
]
```

* <b>baseurl</b> : Url fonksiyonları kök adresi, genellikle "/" karakteri yeterli olur.
* <b>assets.url</b> : Kaynaklar klasörü kök adresi genellikle "/" karakteri yeterli olur fakar eğer bir içerik sağlayıcı (cdn) kullanıyorsunuz buraya cdn adresinizi girebilirsiniz.
* <b>assets.folder</b> : Uygulamanız içerisinde "public/" klasörü altındaki kaynaklar klasörünü belirler varsayılan klasör "/assets/" klasörüdür.

<a name="methods"></a>

### Metotlara Erişim

```php
$this->c['url']->method();
```

<a name="anchor"></a>

#### $this->url->anchor()

Yerel site adresinize göre standart bir HTML bağlantı çıktısı oluşturur.

```php
echo $this->url->anchor('welcome', 'Click Here');
```
Çıktı

```php
<a href="http://example.com/welcome">Click Here</a>
```

Üçüncü parametreden ek nitelikler gönderilebilir.


```php
echo $this->url->anchor('welcome', 'Click Here', ' title="Welcome" class="btn btn-default" ');
```

```php
<a href="/welcome" title="Welcome" class="btn btn-default">Click Here</a>
```

<a name="withProtocolAnchor"></a>

#### $this->url->withProtocol()->anchor()

Eğer geçerli protokol ile bir bağlantı oluşturulmak isteniyorsa withProtocol() metodu kullanılır.


```php
echo $this->url->withProtocol()->anchor('test.com', 'Welcome');
```

Çıktı

```php
<a href="http://test.com">Click Here</a>
```

Kesin bir protokol berlirtilirse aşağıdaki gibi bir çıktı alınır.

```php
echo $this->url->withProtocol('https://')->anchor('test.com', 'Welcome');
```

Çıktı

```php
<a href="https://test.com">Click Here</a>
```

<a name="asset"></a>

#### $this->url->asset()

Public dizini içerisinde yer alan bir kaynak dosyasına ait url adresi oluşturmak için asset fonksiyonu kullanılır.

```php
echo $this->url->asset('css/welcome.css');
```
Çıktı

```
/assets/css/welcome.css 
```

Bir resim dosyası için oluşturulan kaynak url.

```php
echo $this->url->asset('images/logo.png');
```

Çıktı

```
/assets/images/logo.png 
```

Eğer konfigürasyon dosyanızda bir dış url tanımlı ise.

```php
'assets'   => [
    'url' => 'static.example.com',
    'folder' => '/assets/',
]
```

O zaman alacağınız çıktı aşağıdaki gibi olur.

```
http://static.example.com/assets/images/logo.png 
```

<a name="withUrlAsset"></a>

#### $this->url->withUrl()->asset()

Harici bir url ile de bir kaynak url oluşturulabilir.

```php
echo $this->url
    ->withUrl('test.com')
    ->asset('images/logo.png');
```

Çıktı

```php
http://test.com/assets/images/logo.png
```

Geçerli protokol ile bir kaynak url.

```php
echo $this->url->withProtocol()->withUrl('test.com')->asset('images/logo.png');
```

Çıktı

```php
http://test.com/assets/images/logo.png
```

Güvenli protokol ile bir kaynak url.

```php
echo $this->url->withProtocol('https://')->withUrl('test.com')->asset('images/logo.png');
```

Çıktı

```php
https://test.com/assets/images/logo.png
```

<a name="baseUrl"></a>

#### $this->url->baseUrl()

Konfigürasyonda tanımlı olan kök url adresine geri döner.

```php
echo $this->url->baseUrl();
```

Çıktı

```php
/ 
```

Bir url adresi ile birlikte kök url adresi alınabilir.

```php
echo $this->url->baseUrl('examples/forms');
```

Çıktı

```php
/examples/forms
```

<a name="siteUrl"></a>

#### $this->url->siteUrl()

Base Url adresi ile beraber oluşturulması istenen site url adresine geri döner.

```php
echo $this->url->siteUrl();
```

Çıktı

```php
/
```

Eğer konfigurasyon dosyasında base Url adresi tanımlı ise 

```php
'baseurl'  => 'example.com',
```

Site 

```php
echo $this->url->siteUrl('examples/forms');
```

Çıktı

```php
http://example.com/examples/forms
```

<a name="withProtocolSiteUrl"></a>

#### $this->url->withProtocol()->siteUrl()

Girilen url adresi ile beraber base Url adresini döner. Eğer protokol metodu kullanılırsa konfigürasyonda olan tanımlı protokol geçerli protokol ile değiştirilir.

```php
echo $this->url->withProtocol()->siteUrl('examples/forms');
```

Muhtemel çıktılar

```php
http://example.com/examples/forms
```

Geçerli protokol güvenli ise

```php
https://example.com/examples/forms
```

Güvenli protokole zorlama

```php
echo $this->url->withProtocol('https://')->siteUrl('examples/forms');
```

Çıktı

```php
https://example.com/examples/forms
```

<a name="currentUrl"></a>

#### $this->url->currentUrl()

Tarayıcıda kullanıcının gezdiği geçerli url adresine geri döner.

```php
echo $this->url->currentUrl();
```

Çıktı

```
/welcome?foo=bar 
```

<a name="prep"></a>

#### $this->url->prep()

Girilen url adresinin başında <kbd>http://</kbd> protokolü eksik ise tamamlar.

```php
echo $this->url->prep('example.com');
```

Çıktı

```php
http://example.com
```

Protokol mevcut ise herhangi bir değişilik yapılmaz.


```php
echo $this->url->prep('https://example.com');
```

Çıktı

```php
https://example.com
```