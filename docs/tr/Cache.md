
## Cache Sınıfı

Cache paketi çeşitli önbellekleme ( cache ) türleri için birleşik bir arayüz sağlar. Cache paket konfigürasyonu ortam tabanlı konfigürasyon dosyası <kbd>config/$env/cache/</kbd> dosyasından yönetilir.

<ul>

<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#service-configuration">Servis Konfigürasyonu</a></li>
        <li><a href="#service-setup">Servis Kurulumu</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#loading-service">Servisi Yüklemek</a></li>
        <li><a href="#cache-drivers">Önbellek Sürücüleri</a></li>
        <li>
            <a href="#interface">Ortak Arayüz Metotları</a>
            <ul>
                <li><a href="#common-set">$this->cache->set()</a></li>
                <li><a href="#common-get">$this->cache->get()</a></li>
                <li><a href="#common-delete">$this->cache->delete()</a></li>
                <li><a href="#common-replace">$this->cache->replace()</a></li>
                <li><a href="#common-exists">$this->cache->exists()</a></li>
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

Cache sınıfı konfigürasyonu <kbd>config/$env/cache/$driver.php</kbd> dosyasından konfigüre edilir. Örneğin local ortam ve memcached sürücüsü için <kbd>config/local/cache/memcached.php</kbd> dosyasını konfigüre etmeniz gerekir.

<a name="service-configuration"></a>

#### Servis Konfigürasyonu

Servisler uygulama içerisinde parametreleri değişmez olan ve tüm kütüphaneler tarafından ortak ( paylaşımlı ) kullanılan sınıflardır. Kimi durumlarda servisler kolay yönetilebilmek için bağımsız olan bir servis sağlayıcısına ihtiyaç duyarlar.

Cache servisi uygulama içerisinde bazı yerlerde paylaşımlı olarak bazı yerlerde de parametre değişikliği gerektirdiği ( paylaşımsız yada bağımsız ) kullanıldığı için kimi zaman farklı ihtiyaçlara cevap veremez.

Bir örnek vermek gerekirse uygulamada servis olarak kurduğunuz cache kütüphanesi her zaman <b>serializer</b> parametresi ile kullanılmaya konfigüre edilmiştir ve değiştirilemez. Fakat bazı yerlerde <b>"none"</b> parametresini kullanmanız gerekir bu durumda servis sağlayıcı imdadımıza yetişir ve <b>"none"</b> parametresini kullanmanıza imkan sağlar. Böylece cache kütüphanesi yeni bir nesne oluşturarak servis sağlayıcısının diğer cache servisi ile karışmasını önler.

Default bağlantısına ait aşağıdaki birinci örnekte servis sağlayıcı konfigürasyon dosyasında serializer tipi <kbd>none</kbd> olarak ayarlanmış olan <kbd>default</kbd> bağlantısına bağlanır.

```php
$this->c['app']->provider('cache')->get(['driver' => 'redis', 'connection' => 'default']);
```

Second bağlantısına ait aşağıdaki ikinci örnekte servis sağlayıcı konfigürasyon dosyasında serializer tipi <kbd>php</kbd> olarak ayarlanmış olan <kbd>second</kbd> bağlantısına bağlanır.

```php
$this->c['app']->provider('cache')->get(['driver' => 'redis', 'connection' => 'second']);
```

Uygulamada cache servisi yüklendiğinde servis içerisinde <kbd>CacheManager</kbd> sınıfı getProvider metodu ile tanımlı olan servis sağlayıcısına bağlanır.

<a name="service-setup"></a>

#### Servis Kurulumu

Servis kurulumu için tek yapmanız gereken kullanmak istediğiniz servis sağlayıcısının parametrelerini servis konfigürasyonuna girmek, aşağıdaki örnekte <kbd>default</kbd> bağlantısı seçilmiştir.

```php
namespace Service;

use Obullo\Cache\CacheManager;
use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Cache implements ServiceInterface
{
    public function register(ContainerInterface $c)
    {
        $c['cache'] = function () use ($c) {
            
            $parameters = [
                'provider' => [
                    'name' => 'cache',
                    'params' => [
                        'driver' => 'redis',
                        'connection' => 'default'
                    ]
                ]
            ];
            $manager = new CacheManager($c);
            $manager->setParameters($parameters);
            return $manager->getProvider();
        };
    }
}
/* Location: .classes/Service/Cache.php */
```

<a name="running"></a>

### Çalıştırma

------

<a name="loading-service"></a>

#### Servisi Yüklemek

Cache servisi aracılığı ile cache metotlarına aşğıdaki gibi erişilebilir.

```php
$this->c['cache']->metod();
```
<a name="cache-drivers"></a>

#### Önbellek Sürcüleri

Bu sürüm için varolan cache sürücüleri aşağıdaki gibidir:

* Apc
* File
* Memcache
* Memcached
* Redis

Sürücü seçimi yapılırken küçük harfler kullanılmalıdır. Örnek : redis. Her bir önbellek türünün konfigürasyonuna <kbd>config/cache/$sürücü.php</kbd> adıyla ulaşılabilir.

<a name="interface"></a>

#### Ortak Arayüz Metotları

Cache sürücüleri handler interface arayüzünü kullanırlar. Handler interface size cache servisinde hangi metotların ortak kullanıldığı gösterir ve eğer yeni bir sürücü yazacaksınız sizi bu metotları sınıfınıza dahil etmeye zorlar. Cache sürücüsü ortak metotları aşağıdaki gibidir.

```php
interface CacheInterface
{
    public function connect();
    public function exists($key);
    public function set($key, $data = 60, $ttl = 60);
    public function get($key);
    public function replace($key, $data = 60, $ttl = 60);
    public function delete($key);
}
```

<a name="common-set"></a>
<a name="common-get"></a>
<a name="common-delete"></a>
<a name="common-replace"></a>
<a name="common-exists"></a>

##### $this->cache->set(mixed $key, $value, $ttl = 60);

Önbellek deposuna veri kaydeder. Birinci parametre anahtar, ikici parametre değer, üçüncü parametre ise anahtara ait verinin yok olma süresidir. Üçüncü parametrenin varsayılan değeri 60 saniyedir. Eğer üçüncü parametreyi "0" olarak girerseniz önbelleğe kaydetiğiniz anahtar siz silmedikçe silinmeyecektir. Yani kalıcı olacaktır.

##### $this->cache->get(string $key);

Önbellek deposundan veri okur.

##### $this->cache->set(array $key, $ttl = 60);

Eğer ilk parametreye bir dizi gönderirseniz ikinci parametreyi artık sona erme süresi olarak kullanabilirsiniz.

##### $this->cache->delete(string $key);

Anahtarı ve bu anahtara kaydedilen değeri bütünüyle siler.

##### $this->cache->replace(mixed $key, $value, $ttl = 60);

Varsayılan anahtara ait değeri yeni değer ile günceller.

##### $this->cache->exists(string $key);

Eğer girilen anahtar önbellekte mevcut ise <b>true</b> değerine aksi durumda <b>false</b> değerine döner.

<a name="drivers"></a>

### Sürücüler

------

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