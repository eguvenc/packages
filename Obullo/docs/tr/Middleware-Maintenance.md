
## Maintenance Katmanı

> Maintenance eklentisi uygulamanıza ait domain adresleri yada isim alanlarıyla belirlenmiş uygulamanın bütününü yada belirli kısımlarını bakıma alma özelliği sunar. 

<a name="maintenance-add"></a>

### Kurulum

```php
php task middleware add maintenance
```

<a name="maintenance-remove"></a>

### Kaldırma

```php
php task middleware remove maintenance
```

Eğer app/routes.php içinde bu katmanı kullandıysanız middleware dizileri içinden silin.

<a name="maintenance-run"></a>

### Çalıştırma

Uygulamanızı bakıma almak için aşağıdaki komutu çalıştırın.

```php
php task app down root
```

Uygulamanızı bakımdan çıkarmak için aşağıdaki komutu çalıştırın.

```php
php task app up root
```

<a name="maintenance-configuration"></a>

### Konfigürasyon

Eğer tanımlı değilse <kbd>config/$env/maintenance.php</kbd> dosyası içerisinden uygulamanıza domainlere ait regex ( düzenli ) ifadeleri belirleyin.

```php

return array(

    'root' => [
        'maintenance' => 'up',
    ],
    'mydomain' => [
        'maintenance' => 'up',
        'regex' => 'mydomain.com',
    ],
    'subdomain' => [
        'maintenance' => 'up',
        'regex' => 'sub.domain.com',
    ],
);

/* Location: .config/local/maintenance.php */
```

Dosya içerisindeki <b>maintenance</b> anahtarları domain adresinin bakıma alınıp alınmadığını kontrol eder, <b>regex</b> anahtarı ise geçerli domain adresleriyle eşleşme yapılabilmesine olanak sağlar. Domain adresinize uygun düzenli ifadeyi regex kısmına girin.

Domain adresinizi route yapısına tutturmak <kbd>app/routes.php</kbd> dosyası içerisinde domain grubunuza ait <b>domain</b> ve <b>middleware</b> anahtarlarını aşağıdaki gibi güncelleyin.

```php
$c['router']->group(
    [
        'name' => 'GenericUsers',
        'domain' => 'sub.domain.com', 
        'middleware' => array('Maintenance')
    ],
    function () {

        $this->attach('(.*)');
    }
);
```

Şimdi test için bakım konfigürasyon dosyanızdaki ilgili domain adına adına ait <b>maintenance</b> değerini <b>down</b> olarak güncelleyin,

```php
php task app down subdomain
```

ve ardından alan adınızın bakıma alınıp alınmadığını sayfayı ziyaret ederek kontrol edin.

```php
http://sub.domain.com/
```

Herşey yolunda ise <kbd>resources/templates/errors/maintenance.php</kbd> dosyası içerisinde bulunan <b>Service Unavailable</b> yazısı ile karşılaşmanız gerekir.


>**Not:** Dosyadaki ilk anahtar olan **root** anahtarı kök anahtardır ve uygulamanızdaki tüm domain adreslerini kapatıp açmak için kullanılır.