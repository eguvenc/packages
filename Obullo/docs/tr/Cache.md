
## Cache Servisi

Cache servisi sık kullanılan önbellekleme türleri için basit ve ortak bir arayüz sağlar.

<ul>

<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#service-provider">Servis Sağlayıcısı</a> ( CacheFactory )</li>
    </ul>
</li>

<li>
    <a href="#running">Servis</a>
    <ul>
        <li><a href="#cache-drivers">Önbellek Sürücüleri</a></li>
        <li>
            <a href="#interface">Ortak Arayüz Metotları</a>
            <ul>
                <li><a href="#common-has">$this->cache->has()</a></li>
                <li><a href="#common-set">$this->cache->set()</a></li>
                <li><a href="#common-setItems">$this->cache->setItems()</a></li>
                <li><a href="#common-get">$this->cache->get()</a></li>
                <li><a href="#common-remove">$this->cache->remove()</a></li>
                <li><a href="#common-removeItems">$this->cache->removeItems()</a></li>
                <li><a href="#common-replace">$this->cache->replace()</a></li>
                <li><a href="#common-replaceItems">$this->cache->replaceItems()</a></li>
                <li><a href="#common-setSerializer">$this->cache->setSerializer()</a></li>
                <li><a href="#common-getSerializer">$this->cache->getSerializer()</a></li>
                <li><a href="#common-flushAll">$this->cache->flushAll()</a></li>
            </ul>
        </li>
    </ul>
</li>

<li>
    <a href="#drivers">Sürücüler</a>
    <ul>
        <li><a href="#file">File</a></li>
        <li><a href="#file">Apc</a></li>
        <li><a href="#memcache">Memcache</a></li>
        <li><a href="#memcached">Memcached</a></li>
        <li><a href="#redis">Redis</a></li>
    </ul>
</li>

</ul>

<a name="configuration"></a>

### Konfigürasyon

Cache sınıfı konfigürasyonu <kbd>providers/$sürücü.php</kbd> dosyasından konfigüre edilir. Örneğin memcached sürücüsü için <kbd>providers/memcached.php</kbd> dosyasını konfigüre etmeniz gerekir.

<a name="service-provider"></a>

#### Servis Sağlayıcısı

Servis kurulumu için tek yapmanız gereken kullanmak istediğiniz servis sağlayıcısının parametrelerini servis konfigürasyonuna girmek, aşağıdaki örnekte <kbd>default</kbd> bağlantısı seçilmiştir.

```php
$this->container->get('cacheFactory')->shared(
    [
        'driver' => 'redis'
        'connection' => 'default'
    ]
);
```

<a name="running"></a>

### Servis

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

<a name="cache-drivers"></a>

#### Önbellek Sürcüleri

Bu sürüm için varolan cache sürücüleri aşağıdaki gibidir:

* Apc
* File
* Memcache
* Memcached
* Redis

Sürücü seçimi yapılırken küçük harfler kullanılmalıdır.

<a name="interface"></a>

#### Ortak Arayüz Metotları

Cache sürücüleri CacheInterface arayüzünü kullanırlar. Bu arayüz size cache servisinde hangi metotların ortak kullanıldığı gösterir ve eğer yeni bir sürücü yazacaksınız sizi bu metotları sınıfınıza dahil etmeye zorlar.

```php
interface CacheInterface
{
    public function has($key);
    public function set($key, $data, $ttl = 60);
    public function setItems(array $data, $ttl = 60);
    public function get($key);
    public function replace($key, $data, $ttl = 60);
    public function replaceItems(array $data, $ttl = 60);
    public function remove($key);
    public function removeItems(array $keys);
    public function flushAll();
}
```

<a name="common-has"></a>

##### $this->cache->has(string $key);

Eğer girilen anahtar önbellekte mevcut ise <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine döner.

<a name="common-set"></a>

##### $this->cache->set(string $key, $value, $ttl = 60);

Önbellek deposuna veri kaydeder. Birinci parametre anahtar, ikici parametre değer, üçüncü parametre ise anahtara ait verinin yok olma süresidir. Üçüncü parametrenin varsayılan değeri <kbd>60</kbd> saniyedir. Eğer üçüncü parametreyi <kbd>0</kbd> olarak girerseniz önbelleğe kaydettiğiniz anahtar kalıcı olur.

<a name="common-setItems"></a>

##### $this->cache->setItems(array $data, $ttl = 60);

Önbellek deposuna girilen dizi türünü ayrıştırarak kaydeder. 

<a name="common-get"></a>

##### $this->cache->get(string $key);

Önbellek deposundan veri okur.

<a name="common-remove"></a>

##### $this->cache->remove(string $key);

Anahtarı ve bu anahtara kaydedilen değeri bütünüyle siler.

<a name="common-removeItems"></a>

##### $this->cache->removeItems(array $keys);

Dizi türünde girilen anahtarların tümünü siler.

<a name="common-replace"></a>

##### $this->cache->replace(string $key, $value, $ttl = 60);

Varsayılan anahtara ait değeri yeni değer ile günceller.

<a name="common-replaceItems"></a>

##### $this->cache->replaceItems(array $data, $ttl = 60);

Dizi türünde girilen yeni değerleri günceller.

<a name="common-setSerializer"></a>

##### $this->cache->setSerializer($serializer = null);

Varsa sürücünüzün desteklediği serileştirici türünü seçer.

<a name="common-getSerializer"></a>

##### $this->cache->getSerializer();

Geçerli serileştirici türüne geri döner.

<a name="common-flushAll"></a>

##### $this->cache->flushAll()

Bellek içerisindeki tüm anahtarları ve değerlerini yokeder.


<a name="drivers"></a>

### Sürücüler

Şu anki sürümde aşağıdaki sürücüler desteklenmektedir.

<a name="file"></a>

#### File

Varsayılan önbellek sürücüsüdür ortak arayüz metotlarını kullanarak text dosyalarına kayıt yapar.

<a name="apc"></a>

#### Apc

PECL eklentisi ile kurulum gerektirir ortak arayüz metotlarını kullanarak sunucu önbelleğine kayıt yapar. Kurulum ve sunucu gereksinimleri için <a href="http://php.net/manual/tr/book.apc.php">http://php.net/manual/tr/book.apc.php</a> adresini ziyaret ediniz.

<a name="memcache"></a>

#### Memcache

Php eklentisi ile kurulum gerektirir ortak arayüz metotlarını kullanarak sunucu önbelleğine kayıt yapar. Kurulum ve sunucu gereksinimleri için <a href="http://php.net/manual/tr/book.memcache.php">http://php.net/manual/tr/book.memcache.php</a> adresini ziyaret ediniz.

<a name="memcached"></a>

#### Memcached

Memcached sürücüsü kurulum konfigürasyon ve sınıf referansı için [Cache-Memcached.md](Cache-Memcached.md) dosyasını okuyunuz.

<a name="redis"></a>

#### Redis

Redis sürücüsü kurulum konfigürasyon ve sınıf referansı için [Cache-Redis.md](Cache-Redis.md) dosyasını okuyunuz.