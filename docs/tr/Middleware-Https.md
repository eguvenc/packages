
## Https Katmanı

> Uygulamada belirli adreslere gelen <b>http://</b> isteklerini <b>https://</b> protokolüne yönlendirir.

### Kurulum

```php
php task middleware add https
```

### Kaldırma

```php
php task middleware remove https
```

Eğer route yapınızda bu katmanı kullandıysanız app/routes.php dosyasından ayrıca silin.
Eğerbu katmana ait anotasyonlar kontrolör sınıfları üzerinde kullanıldıysa bir <b>Search - Replace</b> operasyonu ile ilgili anotasyonları silin.

### Çalıştırma

Aşağıdaki örnek tek bir route için https katmanı tayin etmenizi sağlar.

```php
$c['router']->get('hello$', 'welcome/index')->middleware('Https');
```

Fakat uygulamada birden fazla güvenli adresiniz varsa onları aşağıdaki gibi bir grup içinde tanımlamak daha doğru olacaktır.

```php
$c['router']->group(
    ['name' => 'Secure', 'domain' => 'framework', 'middleware' => array('Https')],
    function () {

        $this->get('orders/pay');
        $this->get('orders/bank_transfer');
        $this->get('hello$', 'welcome/index');
        
        $this->attach('.*');
    }
);
```