
## Csrf Katmanı

> Csrf katmanı Cross Request Forgery güvenlik tehdidine karşı uygulamanızdaki formlarda oluşturduğunuz güvenlik algoritmasını http POST istekleri geldiğinde sunucu tarafında doğrular, doğrulama başarılı olmazsa katman içerisinden kullanıcı hata sayfasına yönlendirilir.

Cross Request Forgery güvenlik tehdidi hakkında daha detaylı bilgi için <a href="http://shiflett.org/articles/cross-site-request-forgeries">bu makalaye</a> gözatabilirsiniz.

### Konfigürasyon

<kbd>local/service/csrf.php</kbd> dosyasından csrf protection değerini <b>true</b> olarak değiştirin.

```php
return array(
            
    'params' => [
    
        'protection' => false,
        'token' => [
            'name' => 'csrf_token',
            'refresh' => 30,
        ],   
    ]                            

);

/* Location: .local/service/csrf.php */
```

Eğer <kbd>Form/Element</kbd> paketini kullanmıyorsanız uygulamanızdaki csrf güvenliği gereken form taglarına aşağıdaki gibi güvenlik değeri oluşturmanız gerekir.


```html
<form action="buy.php" method="post">
<input type="hidden" name="<?php echo $this->c['csrf']->getTokenName() ?>" 
value="<?php echo $this->c['csrf']->getToken(); ?>" />
<p>
Symbol: <input type="text" name="symbol" /><br />
Shares: <input type="text" name="shares" /><br />
<input type="submit" value="Buy" />
</p>
</form>
```

Eğer form element paketini kullanıyorsanız form metodu sizin için csrf değerini kendiliğinden oluşturur.

```php
echo $this->element->form('/buy', array('method' => 'post'));
```

#### Kurulum

Aşağıdaki kaynaktan <b>Csrf.php</b> dosyasını uygulamanızın <kbd>app/classes/Http/Middlewares/</kbd> klasörüne kopyalayın.

```php
http://github.com/obullo/http-middlewares/
```

#### Çalıştırma

Csrf doğrulama katmanının uygulamanın her yerinde çalışmasını istiyorsanız katmanı <kbd>app/middlewares.php</kbd> dosyasına ekleyin.

```php
/*
|--------------------------------------------------------------------------
| Request
|--------------------------------------------------------------------------
*/
$c['middleware']->add(
    [
        // 'View',
        'Router',
        'Csrf'
    ]
);

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
        'name' => 'Orders', 
        'domain' => 'mydomain.com', 
        'middleware' => array()
    ],
    function () use ($c) {

        $this->match(['get', 'post'], 'accounts/orders/post')->middleware("Csrf");
        $this->match(['get', 'post'], 'accounts/orders/delete')->middleware("Csrf");

        $this->get('accounts/orders/list');

        $this->attach('.*');
    }
);
```

Bu örnekte sadece siparişler sınıfına ait post metodunda csrf katmanını çalıştırmış olduk.


#### İstisnai Durumlarda Csrf Katmanını Kaldırmak

Csrf katmanı global olarak tanımlandığında tüm http POST isteklerinde çalışır. Fakat bu katmanı istenmeyen yerlerde anotasyonlar yardımı ile kaldırabilirsiniz.

```php
/**
 * Index
 *
 * @middleware->remove("Csrf");
 * 
 * @return void
 */
public function index()
{
    $this->view->load(
        'welcome',
        [
            'title' => 'Welcome to Obullo !',
        ]
    );
}
```