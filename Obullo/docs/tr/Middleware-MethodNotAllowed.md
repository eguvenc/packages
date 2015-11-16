
## MethodNotAllowed Katmanı

> Uygulamaya gelen Http isteklerine göre metot türlerini filtrelemeyi sağlar. Belirlenen http metotları ( get, post, put, delete ) dışında bir istek gelirse isteği HTTP Error 405 Method not allowed sayfası ile engeller.

### Konfigürasyon

Framework çekirdeğinde çalışan bir katmandır herhangi bir kurulum ve konfigürasyon gerektirmez. Anotasyonlar ile birlikte kullanılabilmesi için <kbd>config/$env/config.php</kbd> dosyasından <b>annotations > enabled</b> anahtarının açık ( <b>true</b> ) olması gerekir.

#### Çalıştırma

Anotasyonlar ile controller sınıfı içerisinden veya route yapısı içerisinden çalıştırılabilir.

#### Anotasyonlar ile kontrolör sınıfı içerisinden çalıştırma

```php
/**
 * Index
 *
 * @middleware->method("get", "post");
 * 
 * @return void
 */
public function index()
{
    // ..
}

/* Location: .modules/welcome/controller/welcome.php */
```

<kbd>http://project/hello</kbd> sayfasına post ve delete haricinde örneğin bir get isteği geldiğinde bu istek engellenecektir.

#### Route yapısı içerisinden çalıştırma

```php
$c['router']->group(
    ['name' => 'GenericUsers','domain' => 'mydomain.com', 'middleware' => array()],
    function () {

        $this->defaultPage('welcome');

        $this->match(['post', 'delete'], 'hello$', 'welcome/index');
    }
);
```

Yukarıdaki örnekte <kbd>/hello</kbd> adresine yalnızca <b>POST</b> ve <b>DELETE</b> http istek yöntemleriyle erişilebilir. Uygulamanızda <b>/hello</b> adresini ziyaret ettiğinizde bir <kbd>HTTP Error 405 Method Not Allowed</kbd> hatası almamız gerekir.