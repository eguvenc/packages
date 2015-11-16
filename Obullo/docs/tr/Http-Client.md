
## Http Servisi

Http servisi farklı türdeki sunuculara bağlanmaya ve farklı protokollerle iletişim kurmaya yarar. Http, https, ftp, gopher, telnet, dict, file ve ldap protokoller ile çalışabilir. Ayrıca, HTTPS sertifikalarını, HTTP POST, HTTP PUT gönderimleri ve FTP ile karşıya yükleme işlemleri yapabilir, HTTP form karşıya yüklemesini, vekilleri, çerezleri ve kullanıcı ve parolalı kimlik doğrulamasını da desteklemektedir.

> ***Not:*** Http servisi composer Guzzle paketi kurulumu gerektirir.

### Kurulum

#### Composer ve Guzzle Kurulumu

Composer kurulumu

```php
curl -sS https://getcomposer.org/installer | php
```

Guzzle paketini konsoldan composer.phar dosyası ile paketlerinize ekleyebilirsiniz.


```php
php composer.phar require guzzlehttp/guzzle:~6.0
```

Eğer composer.json dosyasınız mevcut ise alternatif olarak bu dosyadan da aşağıdaki gibi konfigüre edilebilir.

```php
{
	"require": {
	"guzzlehttp/guzzle": "~6.0"
	}
}
```

Paketleri güncelleyin

```php
php composer dump-autoload
php composer update
```

Eğer önceden composer kurulumu yapılmadıysa yüklemeden sonra composer autoloader dosyasını uygulamaya eklemeniz gerekir.

```php
require 'vendor/autoload.php';
```

Composer için daha detaylı kurulum bilgilerine [Composer.md](Composer.md) dosyasına gözatabilirsiniz.

#### Servis Konfigürasyonu

Http servisi varsayılan olarak Guzzle kütüphanesini kullanır ve çalışabilmesi için aşağıdaki gibi bir servis kurulumuna ihtiyaç duyar.

```php
namespace Service;

use GuzzleHttp\Client;
use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Http implements ServiceInterface
{
    public function register(ContainerInterface $c)
    {
        $c['http'] = function () {
            return new Client;
        };
    }
}

/* Location: .app/classes/Service/Http.php */
```

Guzzle hakkında detaylı dökümentasyona bu belgeden <a href="https://media.readthedocs.org/pdf/guzzle/latest/guzzle.pdf">https://media.readthedocs.org/pdf/guzzle/latest/guzzle.pdf</a> ulaşabilirsiniz.

#### Çalıştırma

```php
$this->c['http'];
```

```php
echo $this->http->get(
    'http://httpbin.org/get', [
        'query' => ['foo' => 'bar']
    ]
)->getBody();
```

##### Get

```php
echo $this->http->get(
    'http://httpbin.org/get',
    [
        'query' => ['foo' => 'bar']
    ]
)->getBody();
```

```php
{
  "args": {
    "foo": "bar"
  }, 
  "headers": {
    "Cache-Control": "max-age=259200", 
    "Host": "httpbin.org", 
    "User-Agent": "GuzzleHttp/6.0.1 curl/7.35.0 PHP/5.5.9-1ubuntu4.9", 
    "Via": "1.1 localhost:3128 (squid/2.7.STABLE9)"
  }, 
  "origin": "*", 
  "url": "http://httpbin.org/get?foo=bar"
}
```

##### Head

```php
$headers = $this->http->head(
    'http://httpbin.org/get',
    [
        'query' => ['foo' => 'bar']
    ]
)->getHeaders();

print_r($headers);
```

```php
/*
Array
(
    [Server] => Array
        (
            [0] => nginx
        )

    [Date] => Array
        (
            [0] => Thu, 11 Jun 2015 07:11:49 GMT
        )
*/
```

##### Post

```php
echo $this->http->post(
    'http://httpbin.org/post',
    [
        'form_params' => [
            'field_name' => 'abc',
            'nested_field' => [
                'nested' => 'hello'
            ]
        ]
    ]
)->getBody();
```

```php
{
  "args": {}, 
  "data": "", 
  "files": {}, 
  "form": {
    "field_name": "abc", 
    "nested_field[nested]": "hello"
  }, 
  "headers": {
    "Cache-Control": "max-age=259200", 
    "Content-Length": "45", 
    "Content-Type": "application/x-www-form-urlencoded", 
    "Host": "httpbin.org", 
    "User-Agent": "GuzzleHttp/6.0.1 curl/7.35.0 PHP/5.5.9-1ubuntu4.9", 
    "Via": "1.1 localhost:3128 (squid/2.7.STABLE9)"
  }, 
  "json": null, 
  "origin": "*", 
  "url": "http://httpbin.org/post"
}
```

Multipart Form Örneği

```php
echo $this->http->post(
    'http://httpbin.org/post',
    [
        'form_params' => [
            'field_name' => 'abc',
        ],
        'multipart' => array(
            [
                'name' => 'file_name',
                'contents' => fopen(ASSETS.'images/logo.png', 'r'),
            ],
            [
                'name' => 'other_file',
                'contents' => 'hello',
                'filename' => 'hello.txt',
                'headers' => [
                    'X-Foo' => 'this is an extra header to include'
                ]
            ]
        )
    ]
)->getBody();
```

```php
{
  "args": {}, 
  "data": "", 
  "files": {
    "file_name": "data:image/png;base64,iVBORw0KGgoAAAANSUhEU ...", 
    "other_file": "hello"
  }, 
  "form": {}, 
  "headers": {
    "Cache-Control": "max-age=259200", 
    "Content-Length": "21269", 
    "Content-Type": "multipart/form-data; boundary=5579381b2539b", 
    "Host": "httpbin.org", 
    "User-Agent": "GuzzleHttp/6.0.1 curl/7.35.0 PHP/5.5.9-1ubuntu4.9", 
    "Via": "1.1 localhost:3128 (squid/2.7.STABLE9)"
  }, 
  "json": null, 
  "origin": "*", 
  "url": "http://httpbin.org/post"
}
```

##### Upload

Guzzle dosya yüklemeleri için birden fazla yöntem sunar. Örneğin stream türü içeren bir veriyi string türünde, fopen fonksiyonundan dönen bir resource türünü yada body seçeneğine Psr\Http\Message\StreamInterface arrayüzünü kullanarak upload işlemi yapabilirsiniz.

String örneği

```php
$r = $client->post('http://httpbin.org/post', ['body' => 'raw data']);
```

Resource örneği

```php
$body = fopen('/path/to/file', 'r');

$r = $client->post('http://httpbin.org/post', ['body' => $body]);
```

stream_for() metodu ile PSR-7 stream örneği

```php
$body = \GuzzleHttp\Psr7\stream_for('hello!');
$r = $client->post('http://httpbin.org/post', ['body' => $body]);
```

##### Delete

```php
echo $this->http->delete(
    'http://httpbin.org/delete',
    [
        'query' => ['id' => '4']
    ]
)->getBody();
```

##### Put

```php
echo $this->http->put('http://httpbin.org/put', ['body' => '{"test":"1"}'])->getBody();
```

```php
{
  "args": {}, 
  "data": "{\"test\":\"1\"}", 
  "files": {}, 
  "form": {}, 
  "headers": {
    "Cache-Control": "max-age=259200", 
    "Content-Length": "12", 
    "Host": "httpbin.org", 
    "User-Agent": "GuzzleHttp/6.0.1 curl/7.35.0 PHP/5.5.9-1ubuntu4.9", 
    "Via": "1.1 localhost:3128 (squid/2.7.STABLE9)"
  }, 
  "json": {
    "test": "1"
  }, 
  "origin": "10.0.0.122, 78.189.19.92", 
  "url": "http://httpbin.org/put"
}
```

##### Patch

```php
echo $this->http->patch('http://httpbin.org/patch', ['body' => '{"test":"1"}'])->getBody();
```

Guzzle hakkında detaylı dökümentasyona bu belgeden <a href="https://media.readthedocs.org/pdf/guzzle/latest/guzzle.pdf">https://media.readthedocs.org/pdf/guzzle/latest/guzzle.pdf</a> ulaşabilirsiniz.