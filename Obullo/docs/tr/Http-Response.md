
## Response Class

Http response sınıfının ana fonksiyonu finalize edilmiş web çıktısını tarayıcıya göndermektir. Tarayıcı başlıklarını, json yanıtı, http durum kodunu, 404 page not found yada özel bir hata mesajı göndermek response sınıfının diğer fonksiyonlarındandır. Ayrıca response sınıfı tarayıcıya gönderilen çıktıyı gzip yöntemi ile sıkıştırabilir fakat bu özellik opsiyoneldir ve bütün tarayıcılar desteklemeyebilir.

Http paketi Psr7 Standartlarını destekler ve Zend-Diactoros kütüphanesi bileşenlerinden oluşur.

<ul>
    <li><a href="#loading-class">Sınıfı Yüklemek</a></li>
    <li>
        <a href="#output-control">Çıktı Kontrolü</a>
        <ul>
            <li><a href="#enableOutput">$this->response->enableOutput()</a></li>
            <li><a href="#disableOutput">$this->response->disableOutput()</a></li>
            <li><a href="#write">$this->response->write()</a></li>
            <li><a href="#setOutput">$this->response->setOutput()</a></li>
            <li><a href="#getOutput">$this->response->getOutput()</a></li>
        </ul>
    </li>

    <li>
        <a href="#final-methods">Çıktı Sonlandırma Fonksiyonları</a>
        <ul>
            <li><a href="#finalize">$this->response->finalize()</a></li>
            <li><a href="#sendHeaders">$this->response->sendHeaders()</a></li>
        </ul>
    </li>

    <li>
        <a href="#header-methods">Http Başlık Fonksiyonları</a>
        <ul>
            <li><a href="#status">$this->response->withStatus()</a></li>
            <li><a href="#geStatusCode">$this->response->getStatusCode()</a></li>
            <li><a href="#geReasonPhrase">$this->response->geReasonPhrase()</a></li>
            <li><a href="#headers-set">$this->response->setHeader()</a></li>
            <li><a href="#headers-get">$this->response->getHeader()</a></li>
            <li><a href="#headers-get">$this->response->withoutHeader()</a></li>
            <li><a href="#headers-all">$this->response->getHeaders()</a></li>
        </ul>
    </li>

    <li>
        <a href="#custom-methods">Özel Çıktı Metotları</a>
        <ul>
            <li><a href="#response-json">$this->response->json()</a></li>
            <li><a href="#response-show404">$this->response->error404()</a></li>
            <li><a href="#response-showError">$this->response->error()</a></li>
        </ul>
    </li>

    <li>
        <a href="#compressing">Sıkıştırma</a>
        <ul>
            <li><a href="#compressing-test">Sıkıştırılmış Bir Sayfayı Test Etmek</a></li>
        </ul>
    </li>

</ul>

<a name="loading-class"></a>

### Sınıfı Yüklemek

```php
$this->c['response']->method();
```
Konteyner nesnesi ile yüklenmesi gerekir. Response sınıfı <kbd>app/components.php</kbd> dosyası içerisinde komponent olarak tanımlıdır.

> **Not:** Kontrolör sınıfı içerisinden bu sınıfa $this->response yöntemi ile de ulaşılabilir.

<a name="output-control"></a>

### Çıktı Kontrolü

Çıktıyı kontrol etmenizi sağlayan fonksiyonlardır.

<a name="enableOutput"></a>

##### $this->response->enableOutput();

Çıktılamayı aktif hale getirir bu opsiyon açık olduğunda tarayıcıya çıktı gönderilir.

<a name="disableOutput"></a>

##### $this->response->disableOutput();

Çıktılamayı pasif hale getirir bu opsiyon açık olduğunda tarayıcıya çıktı gönderilmez.

<a name="write"></a>

##### $this->response->write(string $output);

Çıktı gövdesine oluşturduğunuz çıktıları ekler.

```php
$this->response->write('<p>example append data</p>');
$this->response->write('<p>example append data</p>');
```
> **Not:** View paketi çıktıları oluştururken write fonksiyonunu kullanır. 

<a name="setOutput"></a>

##### $this->response->setOutput(string $data);

En son oluşan çıktıyı belirlemenizi sağlar. Bir örnek:

```php
$this->response->setOutput($data);
```
> **Not:** Eğer bu fonksiyonu kullanırsanız tüm çıktı girilen veri ile değiştirilir bu yüzden fonksiyon içerisinde en son çağrılan fonksiyon olmalıdır.

<a name="getOutput"></a>

##### $this->response->getOutput();

En son oluşturulmuş çıktı verisini almanızı sağlar. Bir örnek:

```php
$string = $this->response->getOutput();
```

<a name="final-methods"></a>

### Çıktı Sonlandırma Fonksiyonları

Aşağıdaki metotlar yalnızca çıktıyı kontrol etmek için kullanılırlar.

<a name="finalize"></a>

##### $this->response->finalize();

Çıktıyı oluşturduktan sonra sırasıyla http durum kodu, http başlıkları ve opsiyonlarını ve çıktının kendisini bir dizi içerisinde verir.

<a name="sendHeaders"></a>

##### $this->response->sendHeaders();

Tarayıcıya http başlıklarını göndermeyi sağlar.


<a name="header-methods"></a>

#### Http Başlık Fonksiyonları

Tarayıcı başlıklarını kontrol eden fonksiyonları içerir.

<a name="status"></a>

##### $this->response->withStatus($code = 401, 'text');

Tarayıcı gönderilen durum kodunu belirler.

```php
$this->reponse->withStatus('401');  // Http başlığını "Unauthorized" olarak ayarlar.
```
Http durum kodu listesi için [Buraya tıklayın](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html)

<a name="getStatusCode"></a>

##### $this->response->getStatusCode();

Mevcut http durum kodu değerini verir.

```php
echo $this->reponse->getStatusCode();   // 401
```

<a name="getReasonPhrase"></a>

##### $this->response->getReasonPhrase();

Mevcut http durum kodu mesajına geri döner.

```php
echo $this->reponse->getReasonPhrase();  // OK
```
Bazı örnek http durum mesajları

```php
(200) OK
(201) Created
(202) Accepted
(203) Non-Authoritative Information
(404) Not Found
```
<a name="headers-set"></a>

##### $this->response->withHeader(string $header, string $value = null, $replace = true);

Çıktı tarayıcıya gönderilmeden önce http başlıkları eklemenizi sağlar. Takip eden örnekte bir çıktının içerik türü belirleniyor.

```php
$this->response->headers->set("content-type", "application/json");
```

Ya da aşağıdaki gibi birden fazla başlık eklenebilir.

```php
$this->response->withHeader("HTTP/1.0 200 OK");
$this->response->withHeader("HTTP/1.1 200 OK");
$this->response->withHeader("last-modified", gmdate('D, d M Y H:i:s', time()).' GMT');
$this->response->withHeader("cache-control", "no-store, no-cache, must-revalidate");
$this->response->withHeader("cache-control", "post-check=0, pre-check=0");
$this->response->withHeader("pragma", "no-cache");
```

Http başlıkları tam listesi için [Buraya tıklayın](https://en.wikipedia.org/wiki/List_of_HTTP_header_fields)

<a name="headers-get"></a>

##### $this->response->getHeaders();

Http başlığına eklenmiş bir başlığın değerine döner.

```php
echo $this->response->headers->get('pragma');  // no-cache
```

<a name="headers-remove"></a>

##### $this->response->withoutHeader();

Http başlığından bir değeri siler.

```php
echo $this->response->withoutHeader('pragma');
```

<a name="headers-all"></a>

##### $this->response->getHeaders();

Http başlığında ekleniş tüm başlıklara döner.

```php
Array
(
    [content-type] => plain-text
    [pragma] => no-cache
)
```

##### $this->response->newInstance($body = 'php://memory', $status = 200, array $headers = []);

Yeni bir response nesnesi oluşturur.


<a name="custom-methods"></a>

#### Özel Çıktı Metotları

Json formatında yada 404 sayfa bulunamadı gibi özel başlıkları içeren metotlar aşağıda sıralanmıştır.

<a name="response-json"></a>

##### $this->response->json(array $data, mixed $header = 'default');

Json formatında kodlanmış bit metni http json başlığı ile birlikte tarayıcıya gönderir.

```php
echo $this->response->json(['test']);  // Çıktı [ "test" ]
```

İkinci parametre <kbd>config/response.php</kbd> konfigürasyon dosyasında tanımlı olan http başlıklığını kullanır varsayılan değer <kbd>default</kbd> değeridir.

```php
echo $this->response->json(['test'], 'second');
```

Yukarıda örnek tanımlı ise <kbd>second</kbd> adlı konfigürasyona ait http başlıklarını ekler.

<a name="response-show404"></a>

##### $this->response->error404();

<kbd>resources/templates/errors/404.php</kbd> html şablon dosyasını kullanarak <kbd>404 Page Not Found</kbd> hatası oluşturur.

```php
$this->response->error404();
```

<a name="response-showError"></a>

##### $this->response->error(string $message, $status_code = 500, $heading = 'An Error Was Encountered');

<kbd>resources/templates/errors/general.php</kbd> html şablon dosyasını kullanarak uygulamaya özel hatalar oluşturur.

```php
$this->response->error('Custom error message');
```

> **Not:** Hata mesajları girdilerine güvenlik amacıyla response sınıfı içerisinde özel karakter filtrelmesi yapılır.