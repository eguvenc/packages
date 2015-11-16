
## Memcached Sürücüsü

Memcached sürücüsü sunucunuza php extension olarak kurulmayı gerektirir. Ubuntu ve benzer linux sistemleri altında memcached kurulumuna dair <b>warmup</b> adı verilen dökümentasyon topluluğunun hazırladığı <a href="https://github.com/obullo/warmup/tree/master/Memcached" target="_blank">bu belgeden</a> yararlanabilirsiniz.

<ul>
<li> 
  <a href="#memcached-configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#memcached-nodes">Çoklu Sunucular</a></li>
        <li><a href="#memcached-service">Servis Kurulumu</a></li>
        <li><a href="#memcached-service-provider">Servis Sağlayıcısı</a></li>
        <li><a href="#memcached-service-provider-connections">Servis Sağlayıcısı Bağlantıları</a></li>
        <li>
            <a href="#memcached-reference">Memcached Referansı</a>
            <ul>
                <li><a href="#memcached-setSerializer">$this->cache->setSerializer()</a></li>
                <li><a href="#memcached-setSerializer">$this->cache->setSerializer()</a></li>
                <li><a href="#memcached-getSerializer">$this->cache->getSerializer()</a></li>
                <li><a href="#memcached-setOption">$this->cache->setOption()</a></li>
                <li><a href="#memcached-getOption">$this->cache->getOption()</a></li>
                <li><a href="#memcached-set">$this->cache->set()</a></li>
                <li><a href="#memcached-get">$this->cache->get()</a></li>
                <li><a href="#memcached-getAllKeys">$this->cache->getAllKeys()</a></li>
                <li><a href="#memcached-getAllData">$this->cache->getAllData()</a></li>
                <li><a href="#memcached-getMetaData">$this->cache->getMetaData()</a></li>
                <li><a href="#memcached-exists">$this->cache->exists()</a></li>
                <li><a href="#memcached-replace">$this->cache->replace()</a></li>
                <li><a href="#memcached-delete">$this->cache->delete()</a></li>
                <li><a href="#memcached-flushAll">$this->cache->flushAll()</a></li>
                <li><a href="#memcached-info">$this->cache->info()</a></li>
            </ul>
        </li>
    </ul>
</li>
</ul>

<a name="memcached-configuration"></a>

### Konfigürasyon

Memcached sürücüsü bağlantı ayarlarınızı <kbd>config/env.$env/cache/memcached.php</kbd> dosyasında tanımlamanız gerekir.

<a name="memcached-nodes"></a>

#### Çoklu Sunucular ( Nodes )

Birden fazla memcached sunucunuz varsa konfigürasyon dosyasındaki diğer sunucu adreslerini aşağıdaki gibi nodes dizisi içerisine girmeniz gerekir.

```php
  'connections' => 
  [
      'default' => [ .. ],
      'nodes' => [
          [
              'host' => '10.0.0.168',
              'port' => 11211,
              'weight' => 1
          ],
          [
              'host' => '10.0.0.169',
              'port' => 11211,
              'weight' => 2
          ]

      ]
  ],
```

<a name="memcached-service"></a>

#### Servis Kurulumu

Eğer uygulama içerisinde cache servisinin memcached kullanmasını istiyorsanız <kbd>app/Classes/Service/Cache.php</kbd> dosyasındaki <b>driver</b> anahtarını <b>memcached</b> olarak değiştirin.

```php
$this->c['app']->provider('cache')->get(['driver' => 'memcached', 'connection' => 'default']);
```

<a name="memcached-service-provider"></a>

#### Servis Sağlayıcısı

Cache kütüphanesi bağımsız olarak kullanılmak istendiği durumlarda servis sağlayıcısından direkt olarak çağrılabilir. Servis sağlayıcı yüklendiği zaman kütüphaneyi bir değişkene atayıp yarattığınız bağlantıya ait metotlara ulaşabilirsiniz.

```php
$this->cache = $this->c['app']->provider('cache')->get(
      [
        'driver' => 'memcached', 
        'connection' => 'default'
      ]
);
$this->cache->method();
```

<a name="memcached-service-provider-connections"></a>

#### Servis Sağlayıcısı Bağlantıları

Servis sağlayıcısı <b>connection</b> anahtarındaki bağlantı değerini önceden <kbd>config/$env/cache</kbd> klasöründe tanımlı olan <b>$sürücü.php</b> dosyası connections dizisi içerisinden alır. Aşağıda memcached sürücüsü <b>default</b> bağlantısına ait bir örnek görülüyor.

```php

return array(

    'connections' => 
    [
        'default' => [
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 1,
            'options' => [

            ]
        ]
    ]
);
```

<a name="memcached-reference"></a>
<a name="memcached-setSerializer"></a>
<a name="memcached-getSerializer"></a>
<a name="memcached-setOption"></a>
<a name="memcached-getOption"></a>
<a name="memcached-set"></a>
<a name="memcached-get"></a>
<a name="memcached-getAllKeys"></a>
<a name="memcached-getAllData"></a>
<a name="memcached-getMetaData"></a>
<a name="memcached-exists"></a>
<a name="memcached-replace"></a>
<a name="memcached-delete"></a>
<a name="memcached-flushAll"></a>
<a name="memcached-info"></a>

#### Memcached Referansı

------

> Bu sınıf içerisinde tanımlı olmayan metotlar __call metodu ile php Memcached sınıfından çağrılırlar.

##### $this->cache->setSerializer($serializer = 'php');

Geçerli serileştirici türünü seçer. Serileştirici tipleri : <b>php</b>, <b>igbinary</b> ve <b>json</b> dır.

##### $this->cache->getSerializer();

Geçerli serileştirici türünü geri döner. Serileştirici tipleri : <b>php</b>, <b>igbinary</b> ve <b>json</b> dır.

##### $this->cache->setOption($option = 'OPT_SERIALIZER', $value = 'SERIALIZER_PHP');

Memcached için bir opsiyon tanımlar. Birer sabit olan opsiyonlar parametrelerden string olarak kabul edilir. Sabitler ( Constants ) hakkında daha detaylı bilgi için <a href="http://www.php.net/manual/en/memcached.constants.php">Memcached Sabitleri</a> ne bir gözatın.

##### $this->cache->getOption($option = 'OPT_SERIALIZER');

Daha önceden set edilmiş opsiyonun değerine döner. Opsiyon sabitleri parametreden string olarak kabul edilir. Daha detaylı bilgi için <a href="http://www.php.net/manual/en/memcached.constants.php">Memcached Sabitleri</a> ne bir gözatın.

##### $this->cache->set(mixed $key, mixed $data, int $ttl = 60);

Girilen anahtara veri kaydeder, son parametre sona erme süresine "0" girilirse veri siz silinceye kadar yok olmaz. Eğer ilk parametreye bir dizi gönderirseniz ikinci parametreyi artık sona erme süresi olarak kullanabilirsiniz.

##### $this->cache->get(string $key);

Anahtara atanmış değere geri döner. Anahtar mevcut değilse <b>false</b> değerine döner. Anahtar bir dizi de olabilir.

##### $this->cache->getAllKeys();

Kayıtlı tüm anahtarlara geri döner.

##### $this->cache->getAllData();

Kayıtı tüm verilere geri döner.

##### $this->cache->getMetaData(string $key);

Girilen anahtarın meta verisine geri döner.

##### $this->cache->exists(string $key);

Girilen anahtar eğer mevcut ise <b>true</b> değilse <b>false</b> değerine döner.

##### $this->cache->replace(string|array $key);

Girilen anahtar değerini günceller.

##### $this->cache->delete(string $key);

Girilen anahtarı önbellekten siler.

##### $this->cache->flushAll(int $expiration = 1);

Sunucudaki tüm anahatarları belirtilen süre içerisinde siler.

##### $this->cache->info();

Sunucuda yüklü yazılım hakkında bilgiler verir.