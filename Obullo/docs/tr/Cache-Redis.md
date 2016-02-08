
## Redis Sürücüsü / Veritabanı

Redis sürücüsü sunucunuzda php extension olarak kurulmayı gerektirir. Ubuntu ve benzer linux sistemleri altında redis kurulumu için <a href="https://github.com/obullo/warmup/tree/master/Redis" target="_blank">bu belgeden</a> yararlanabilirsiniz.

<ul>
<li> 
  <a href="#redis-configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#redis-service-provider">Servis Sağlayıcısı</a></li>
        <li><a href="#redis-service-provider-connections">Servis Sağlayıcısı Bağlantıları</a></li>
        <li><a href="#memcached-service">Servis</a></li>
    </ul>
</li>
<li>
    <a href="#redis-reference">Redis Cache Sürücü Referansı</a>
    <ul>
        <li><a href="#redis-has">$this->cache->has()</a></li>
        <li><a href="#redis-set">$this->cache->set()</a></li>
        <li><a href="#redis-setItems">$this->cache->setItems()</a></li>
        <li><a href="#redis-get">$this->cache->get()</a></li>
        <li><a href="#redis-remove">$this->cache->remove()</a></li>
        <li><a href="#redis-removeItems">$this->cache->removeItems()</a></li>
        <li><a href="#redis-replace">$this->cache->replace()</a></li>
        <li><a href="#redis-replaceItems">$this->cache->replaceItems()</a></li>
        <li><a href="#redis-setSerializer">$this->cache->setSerializer()</a></li>
        <li><a href="#redis-getSerializer">$this->cache->getSerializer()</a></li>
        <li><a href="#redis-flushAll">$this->cache->flushAll()</a></li>
    </ul>
</li>
<li>
    <a href="#redis-db-reference">Redis Veritabanı Referansı</a>
</li>
</ul>

<a name="redis-configuration"></a>

### Konfigürasyon

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcıların tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('ServiceProvider\Connector\Redis');
$container->addServiceProvider('ServiceProvider\Connector\CacheFactory');
```

Redis sürücüsü bağlantı ayarlarınızı <kbd>providers/redis.php</kbd> dosyasında tanımlamanız gerekir.

<a name="redis-service-provider"></a>

#### Servis Sağlayıcısı

Cache servis sağlayıcısı önbellekleme için ortak bir arayüz sağlar.

```php
$this->cache = $this->container->get('cache')->shared(
      [
        'driver' => 'redis', 
        'connection' => 'default'
      ]
);
$this->cache->method();
```
<a name="redis-service-provider-connections"></a>

#### Servis Sağlayıcısı Bağlantıları

Servis sağlayıcısı <kbd>connection</kbd> anahtarındaki bağlantı değerlerini <kbd>providers/redis.php</kbd> içerisinden alır.

```php

return array(

    'connections' => 
    [
        'default' => [ 
            'host' => '10.0.0.168',
            'port' => 6379,
            'options' => [
                'persistent' => false,
                'auth' => '123456',
                'timeout' => 30,
                'attempt' => 100,
                'serializer' => 'none',
                'database' => null,
                'prefix' => null,
            ]
        ],
        
    ]
);
```

<a name="memcached-service"></a>

#### Servis

Cache servisi aracılığı ile cache metotlarına aşağıdaki gibi erişilebilir.

```php
$this->container->get('cache')->metod();
```

Cache servisi için varsayılan sürücü türü <kbd>app/classes/ServiceProvider/Cache</kbd> servisinden belirlenir.

```php
$container->share(
    'cache',
    $container->get('redis')->shared(
        [
            'connection' => 'default'
        ]
    )
);
```

<a name="redis-reference"></a>

### Redis Cache Sürücü Referansı

Bu sınıf içerisinde tanımlı olmayan metotlar __call metodu ile php <kbd>Redis</kbd> sınıfından çağrılırlar. Anahtar içerisinde <kbd>:</kbd> karakterini kullanırsanız anahtarlar gruplanarak gösterilirler.

<a name="redis-has"></a>

##### $this->cache->has(string $key)

Bir anahtarın var olup olmadığını kontrol eder. Anahtar mevcut ise <kbd>true</kbd> değilse <kbd>false</kbd> değerinde döner.

<a name="redis-set"></a>

##### $this->cache->set(mixed $key, mixed $data, int optional $expiration)

Önbellek deposuna veri kaydeder. Kaydetme işlemlerinde <kbd>string</kbd> ve <kbd>array</kbd> türlerini kullanabilirsiniz Eğer ilk parametreye bir dizi gönderirseniz ikinci parametreyi artık sona erme süresi olarak kullanabilirsiniz.

<a name="redis-setItems"></a>

##### $this->cache->setItems(array $data, $ttl = 60);

Önbellek deposuna girilen dizi türünü ayrıştırarak kaydeder.

<a name="redis-get"></a>

##### $this->cache->get($key)

Önbellek deposundan veri okur. Okuma işlemlerinde string ve array türlerini kullanabilirsiniz. Anahtar içerisinde ":" karakterini kullanarak gruplanmış verilere ulaşabilirsiniz.

```php
$this->cache->get('key');           // Çıktı value
$this->cache->get('example:key');   // Çıktı value
```

<a name="redis-replace"></a>

##### $this->cache->replace(string $key, $value, $ttl = 60);

Varsayılan anahtara ait değeri yeni değer ile günceller.

<a name="redis-replaceItems"></a>

##### $this->cache->replaceItems(array $data, $ttl = 60);

Dizi türünde girilen yeni değerleri günceller.

<a name="redis-remove"></a>

##### $this->cache->remove(string $key);

Girilen anahtarı önbellekten siler.

<a name="redis-removeItems"></a>

##### $this->cache->removeItems(array $keys);

Dizi türünde girilen anahtarların tümünü siler.

<a name="redis-setSerializer"></a>

##### $this->cache->setSerializer(string $serializer);

Encode ve decode işlemleri için serileştirici türünü seçer.

* **none**     : Serileştirici kullanılmaz veriler raw biçiminde kaydedilir.
* **php**      : Php serialize() fonksiyonunu serileştirici olarak seçer.
* **json**     : Serileştiriciyi JSON encoder fonksiyonu olarak seçer.
* **igbinary** : Serileştiriciyi igbinary olarak seçer.

<a name="redis-getSerializer"></a>

##### $this->cache->getSerializer();

Geçerli serileştirici türüne geri döner.

<a name="redis-flushAll"></a>

##### $this->cache->flushAll()

Geçerli veritabanından tüm anahtarları siler. Bu işlemin sonucu daima <kbd>true</kbd> döner.

<a name="redis-db-reference"></a>

### Redis Veritabanı Referansı

Redis sürücüsünü eğer bir veritabanı olarak kullanmak istiyorsanız [Cache-Redis-Database.md](Cache-Redis-Database.md) dökümentasyonuna gözatabilirsiniz.