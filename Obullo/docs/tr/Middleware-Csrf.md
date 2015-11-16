
## Csrf Katmanı

> Csrf katmanı Cross Request Forgery güvenlik tehdidine karşı uygulamanızdaki formlarda oluşturduğunuz güvenlik algoritmasını http POST istekleri geldiğinde sunucu tarafında doğrular, doğrulama başarılı olmazsa katman içerisinden kullanıcı hata sayfasına yönlendirilir.

Cross Request Forgery güvenlik tehdidi hakkında daha detaylı bilgi için <a href="http://shiflett.org/articles/cross-site-request-forgeries">bu makalaye</a> gözatabilirsiniz.

### Konfigürasyon

<kbd>config/security.php</kbd> dosyasından csrf protection değerini true olarak değiştirin.

```php
return array(
            
    'csrf' => [                      
        'protection' => true,
     ],                                 

);

/* End of file config.php */
/* Location: .config/security.php */

```

Eğer <kbd>Form/Element</kbd> paketini kullanmıyorsanız uygulamanızdaki csrf güvenliği gereken form taglarına aşağıdaki gibi güvenlik değeri oluşturmanız gerekir.

```php
<form action="/buy" method="post">
<input type="hidden" name="<?php echo $this->c['csrf']->getTokenName() ?>" 
value="<?php echo $this->c['csrf']->getToken(); ?>" />
</form>
``` 

Eğer form element paketini kullanıyorsanız form metodu sizin için csrf değerini kendiliğinden oluşturur.


```php
echo $this->element->form('/buy', array('method' => 'post'));
```

### Kurulum

```php
php task middleware add csrf
```

### Kaldırma

```php
php task middleware remove csrf
```

Katmanı ayrıca <kbd>app/middlewares.php</kbd> dosyasından kaldırmanız gerekir.

### Çalıştırma

Csrf doğrulama katmanının uygulamanın her yerinde çalışmasını istiyorsanız katmanı <kbd>app/middlewares.php</kbd> dosyasına ekleyin.

```php
/*
|--------------------------------------------------------------------------
| Csrf
|--------------------------------------------------------------------------
*/
$c['app']->middleware('Http\Middlewares\Csrf');
/*
|--------------------------------------------------------------------------
| Request
|--------------------------------------------------------------------------
*/
$c['app']->middleware('Http\Middlewares\Request');


/* Location: .app/middlewares.php */
```

Katman evrensel olarak eklendiğinde tüm http POST isteklerinde çalışır. Fakat çalışmasını <b>istemediğiniz</b> metotlarda katmanı aşağıdaki gibi anotasyonlar ( annotations ) yardımı ile kaldırabilirsiniz.

```php
/**
 * Update
 *
 * @middleware->when("post")->remove("Csrf");
 * 
 * @return void
 */
public function update()
{
    if ($this->c['request']->isPost()) {

        // Form verilerini işle
    }
}
```

Eğer Csrf katmanının uygulamanın sadece belirli yerlerinde doğrulama yapmasını istiyorsanız aşağıdaki gibi katman ismini <kbd>app/routes.php</kbd> dosyasına ekleyin.

```php
$c['router']->group(
    [
        'name' => 'Membership', 
        'domain' => $c['config']['domain']['mydomain.com'], 
        'middleware' => array('Maintenance')
    ],
    function () use ($c) {

        $this->match(['get', 'post'], 'accounts/orders/post')->middleware("Csrf");
        $this->match(['get', 'post'], 'accounts/orders/delete')->middleware("Csrf");

        $this->get('accounts/orders/list');

        $this->attach('.*');
    }
);
```

Bu örnekte sadece üye hesapları modülü altında siparişler sınıfına ait post metodunda csrf katmanını çalıştırmış olduk.