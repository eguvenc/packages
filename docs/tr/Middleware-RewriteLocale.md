
## RewriteLocale Katmanı

> Bu katman uygulamaya <b>http://example.com/welcome</b> olarak gelen istekleri mevcut yerel dili ekleyerek <b>http://example.com/en/welcome</b> adresine yönlendirir.

### Kurulum

```php
php task middleware add rewriteLocale
```

### Kaldırma

```php
php task middleware remove rewriteLocale
```

Eğer route yapınızda bu katmanı kullandıysanız app/routes.php dosyasından ayrıca silin.

### Çalıştırma

Aşağıdaki örnek genel ziyaretçiler route grubu için RewriteLocale katmanını çalıştırır.

```php
$c['router']->group(
    [
        'name' => 'Locale', 
        'domain' => 'mydomain.com', 
        'middleware' => array('RewriteLocale')
    ],
    function () use ($c) {

        $this->defaultPage('welcome');

        $this->get('(?:en|tr|de|nl)/(.*)', '$1');
        $this->get('(?:en|tr|de|nl)', 'welcome');

        $this->attach('.*');
    }
);
```

Sadece belirli dizinler için katmanın çalışması sınırlandırılabilir.

```php
$c['router']->group(
    [
        'name' => 'Locale',
        'domain' => '^example.com$',
        'middleware' => array('RewriteLocale')
    ],
    function () {

        $this->defaultPage('welcome');

        $this->get('(?:en|tr|de|nl)/(.*)', '$1');           // Dispatch request for http://example.com/en/folder/class
        $this->get('(?:en|tr|de|nl)', 'welcome/index');     // if request http://example.com/en  -> redirect it to default controller

        $this->attach('/');         // Run middlewares for below the urls
        $this->attach('welcome');
        $this->attach('sports/.*');
        $this->attach('support/.*');
    }
);
```