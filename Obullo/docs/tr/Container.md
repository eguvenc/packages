
## Konteyner Sınıfı

Obullo Container sınıfı PHP 5.4 ve üzeri sürümler için <kbd>hafif yükte</kbd> ve ortam tabanlı çalışabilen bir bağımlılık enjeksiyon çözümüdür. Uygulamanızda servisler, bileşenler ve servis sağlayıcıları oluşturabilmeyi sağlar.

> **Not:** Uygulamada gördüğünüz bir <b>$c</b> değişkeni her zaman konteyner sınıfını temsil eder.

<ul>
<li>
    <a href="#how-it-works">Nasıl Çalışıyor ?</a>
</li>
<li>
    <a href="#services">Servisler</a>
    <ul>
        <li><a href="#service-definition">Servisleri Tanımlamak</a></li>
        <li><a href="#service-load">Servisleri Yüklemek</a></li>
    </ul>
</li>
<li>
    <a href="#service-providers">Servis Sağlayıcıları</a>
    <ul>
        <li><a href="#service-provider-definition">Servis Sağlayıcılarını Tanımlamak</a></li>
        <li><a href="#service-provider-load">Servis Sağlayıcılarını Yüklemek</a></li>
        <li><a href="#service-providers-list">Mevcut Servis Sağlayıcıları</a></li>
        <li><a href="#service-providers-custom">Kendi Servis Sağlayıcılarınızı Tanımlamak</a></li>
    </ul>
</li>
<li><a href="#application-doc">Uygulama Sınıfı Belgelerine Bir Gözatın</a></li>
<li><a href="#method-reference">Fonksiyon Referansı</a></li>
</ul>

<a name="how-it-works"></a>

### Nasıl Çalışıyor ?

Konteyner içerisine bir nesne tek tek tanımlabilir.

```php
$c['myclass'] = function () {
    return new MyClass;
}
```

Tanımlanan sınıflara aşağıdaki gibi ulaşılır.

```php
$c['myclass'];  // yeni nesne
$c['myclass'];  // eski nesne
$c['myclass'];  // eski nesne
```

Eğer <b>raw()</b> fonksiyonunu kullanırsanız closure() fonksiyonu elde edilir.

```php
$closure = $this->c->raw('nesne');
$closure();  //  yeni nesne
$closure();  //  yeni nesne
```

<a name="services"></a>

### Servisler

Servisler uygulama kalitesini arttıran aracı sınıflardır. Bir sınıfın servis haline getirilmesinin nedeni nesneyi konfigürasyon ayarları veya metotları ile birlikte bir dosya içerisinden yükleyerek yazılımınızın esnekliğini arttırmaktır.

<a name="service-definition"></a>

#### Servisleri Tanımlamak

Servis konfigürasyonları <kbd>app/$env/service/</kbd> klasörü içerisinde tanımlanırlar ve çevre ortamı değiştiğinde ( local, test, production ) farklı davranışlar sergileyebilirler. Aşağıda session servisine ait konfigürasyon gösteriliyor.

```php
return array(
    'params' => [
        'provider' => [
            'name' => 'cache',
            'params' => [
                'driver' => 'redis',
                'connection' => 'default'
            ]
        ],
        'storage' => [
            'key' => 'sessions:',
            'lifetime' => 3600,
        ],
        'cookie' => [..],
    ],
    'methods' => [
        'setParameters' => [
            'registerSaveHandler' => '\Obullo\Session\SaveHandler\Cache',
            'setName' => '',
            'start' => '',
        ]
    ]
);
```

Bir servisin çalışabilmesi için yardımcı bir sınıf üzerinden ( Service Manager ) yapılandırılması ve bu sınıfın <kbd>app/components.php</kbd> dosyasında aşağıdaki gibi tanımlanması gerekir.

```php
$c['app']->service(
    [
        'session' => 'Obullo\Session\SessionManager',
    ]
);
```

Bu tanımlamadan sonra artık <kbd>session</kbd> nesnesine konteyner içerisinden aşağıdaki gibi ulaşılabilir.

```php
$this->c['session']->method();
```

Session Manager dosyasının içeriği

```php
class SessionManager implements ServiceInterface
{
    protected $c;
    public function __construct(Container $container)
    {
        $this->c = $container;
    }
    public function setParams(array $params)
    {
        $this->c['session.params'] = $params;
    }
    public function register()
    {
        $this->c['session'] = function () {

            $params   = $this->c['session.params'];
            $provider = $params['provider']['name'];

            return new Session(
                $this->c[$provider],  // Service Provider
                $this->c['request'],
                $this->c['logger'],
                $params
            );

        };
    }
}
```

Servis yüklendikten sonra servis parametrelerine konteyner içerisinden aşağıdaki gibi her yerden ulaşılabilir.

```php
print_r($this->c['session.params']);
```

<a name="service-load"></a>

#### Servisleri Yüklemek

Konteyner içerisine bir kez kaydedilen bir sınıf uygulama içerisine tekrar tekrar çağrıldığında sınıfa ait değişken değerleri hep aynı kalır.

```php
$this->c['session'];	 // yeni nesne
$this->c['session'];	 // eski nesne
$this->c['session'];	 // eski nesne
```

Controller sınıfında <b>$c</b> nesnesi bu sınıfa önceden <kbd>$this->c</kbd> olarak kayıtlı geldiğinden Controller sınıfı içerisinde <b>$c</b> değişkeni hep <kbd>$this->c</kbd> olarak kullanılır. 

```php
$this->c['session'];
```

Konteyner içerisindeki kütüphaneler Controller içerisinden <kbd>$this->class</kbd> proxy yöntemi ile çağırılır.

```php
$this->session->method();
```

Servislerin ve kütüphanelerin Controller sınıfı içerisinde nasıl kullanıldığına dair bir örnek

```php
namespace Welcome;

class Welcome extends \Controller
{
    public function index()
    {
    	$this->session->set('test', 'Hello Services !');
    }
}

/* Location: .modules/welcome/welcome.php */
```

<a name="service-providers"></a>

### Servis Sağlayıcıları

Bir servis sağlayıcısı yazımlıcılara uygulamada kullandıkları yinelenen farklı konfigürasyonlara ait parçaları uygulamanın farklı bölümlerinde güvenli bir şekilde tekrar kullanabilmelerine olanak tanır. Bağımsız olarak kullanılabilecekleri gibi bir servis konfigürasyonunun içerisinde de kullanılabilirler.

Uygulamada kullanılan servis sağlayıcısı bir <b>bağlantı yönetimi</b> ile ilgili ise farklı parametreler gönderilerek açılan bağlantıları yönetirler ve her yazılımcının aynı parametreler ile uygulamada birden fazla bağlantı açmasının önüne geçerler.

Bir servis sağlayıcısı sınıfı yanlış yazılmış yada yapılandırılmış ise onu uygulamanızda kullandığınız bölümlerin hepsi yanlış çalışmaya başlar. Bu yüzden servis sağlayıcıları bir uygulama çalışırken en kritik rolü üstlenirler.

<a name="service-provider-definition"></a>

#### Servis Sağlayıcılarını Tanımlamak

Servis sağlayıcılarının <kbd>app/components.php</kbd> dosyasında tanımlı olmaları gerekir. Tanımlama sıralamasında öncelik önemlidir. Uygulamada ilk yüklenenen servis sağlayıcıları her zaman en üstte tanımlanmalıdır.

```php
$c['app']->provider(
    [
        'database' => 'Obullo\Service\Provider\DatabaseServiceProvider',
        'cache' => 'Obullo\Service\Provider\CacheServiceProvider',
        'redis' => 'Obullo\Service\Provider\RedisServiceProvider',
        'memcached' => 'Obullo\Service\Provider\MemcachedServiceProvider',
        'amqp' => 'Obullo\Service\Provider\AmqpServiceProvider',
    ]
);
```

<a name="service-provider-load"></a>

#### Servis Sağlayıcılarını Yüklemek

Bir servis sağlayıcısı nesnelerde olduğu gibi konteyner içerisinden çağrılarak yüklenir. Aşağıdaki örnekte cache servis sağlayıcısından konfigürasyonda varolan <b>default</b> bağlantı tanımlamasını kullanarak <b>get()</b> metodu ile bir bağlantı getirmesi talep ediliyor.

```php
$this->cache = $this->c['cache']->get(
    [
        'driver' => 'redis',
        'connection' => 'default'
    ]
);
```

Servis sağlayıcıları varolan bağlantıları yönetebilmek için aşağıdaki gibi <b>connections</b> anahtarına sahip bir konfigürasyon dosyasına ihtiyaç duyarlar. Aşağıda redis için <b>default</b> bağlantısına ait bir konfigürasyon örneği gösteriliyor.

```php
return array(

    'connections' => 
    [
        'default' => [
            'host' => '127.0.0.1',
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
        
        'second' => [

        ],

    ],

    'nodes' => [
        [
            'host' => '',
            'port' => 6379,
        ]
    ],

);

/* Location: .app/local/cache/redis.php */
```

Eğer <b>second</b> bağlantısına ait bir bağlantı isteseydik o zaman servis sağlayıcımızı aşağıdaki gibi çağırmalıydık.

```php
$this->cache = $this->c['cache']->get(
    [
        'driver' => 'redis',
        'connection' => 'second'
    ]
);
```

Eğer Cache servis sağlayıcısından konfigürasyonda olmayan bir bağlantı talep etseydik aşağıdaki gibi <b>factory()</b> fonksiyonunu kullanmalıydık.

```php
$this->cache = $this->c['cache']->factory(
    [
        'driver' => 'redis',
        'options' => array(
        	'host' => '127.0.0.1',
	        'port' => 6379,
	        'options' => array(
	            'persistent' => false,
	            'auth' => '123456',
	            'timeout' => 30,
	            'attempt' => 100,
	            'serializer' => 'igbinary',
	            'database' => null,
	            'prefix' => null,
	        )
       )
    ]
);
```

Servis sağlayıcısı bir kez yüklendikten sonra artık cache metotlarına erişebilirsiniz.

```php
$this->cache->method();
```

<a name="service-providers-list"></a>

#### Mevcut Servis Sağlayıcıları 

Mevcut servis sağlayıcıları composer paketi <kbd>vendor/obullo/service-providers</kbd> klasörü altındadır. Aşağıdaki tablo varolan servis sağlayıcılarının bir listesini gösteriyor.

<table>
    <thead>
        <tr>
            <th>Sağlayıcı</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>amqp</b></td>
            <td>Uygulamanızdaki queue.php konfigürasyonunu kullanarak AMQP bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><b>cache</b></td>
            <td>Uygulamanızdaki cache.php konfigürasyonunu kullanarak sürücülere göre cache bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><b>database</b></td>
            <td>Uygulamanızdaki database.php konfigürasyonunu kullanarak seçilen database sürücüsüne göre ilişkili database (RBDMS) nesnelerini yönetir.</td>
        </tr>
        <tr>
            <td><b>qb</b></td>
            <td>Uygulamanızdaki database servis sağlayıcısını kullanarak QueryBuilder nesnesini oluşturur.</td>
        </tr>
        <tr>
            <td><b>memcached</b></td>
            <td>Uygulamanızdaki cache/memcached.php konfigürasyonunu kullanarak memcached bağlantılarını yönetmenize yardımcı olur.</td>
        </tr>
        <tr>
            <td><b>mongo</b></td>
            <td>Uygulamanızdaki mongo.php konfigürasyonunu kullanarak mongo db bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><b>pdo</b></td>
            <td>Uygulamanızdaki database.php konfigürasyonunu kullanarak pdo bağlantılarını yönetmenize yardımcı olur.</td>
        </tr>
        <tr>
            <td><b>redis</b></td>
            <td>Uygulamanızdaki cache/redis.php konfigürasyonunu kullanarak redis bağlantılarını yönetmenize yardımcı olur.</td>
        </tr>
    </tbody>
</table>

Yukarıda anlatılan her bir servis sağlayıcısına ait dökümentasyona <a href="https://github.com/obullo/service-providers" target="_blank">Servis Providers Docs</a> bağlantısından erişebilirsiniz. 

<a name="service-providers-custom"></a>

#### Kendi Servis Sağlayıcılarınızı Tanımlamak

Eğer kendi oluşturduğunuz servis sağlayıcınızı çalıştırmak istiyorsanız <kbd>.app/classes/Service/Providers</kbd> klasörü altında aşağıdaki örnekte gösterildiği gibi bir servis sağlayıcı oluşturmalısınız.

```php
namespace Service\Providers;

use RuntimeException;
use Obullo\Container\ContainerInterface as Container;
use Obullo\Container\AbstractServiceProvider;
use Obullo\Container\ServiceProviderInterface;

class Cache extends AbstractServiceProvider implements ServiceProviderInterface
{
    protected $c;

    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    public function get($params = array())
    {
        // ...
    }

    public function factory($params = array())
    {
        // ..
    }
}

/* Location: .app/classes/Service/Providers/Cache.php */
```

Get metodu zorunlu diğer metotlar opsiyoneldir.

```php
interface ServiceProviderInterface
{
    public function get($params = array());
}
```

Servis sağlayıcısını aşağıdaki gibi <kbd>.app/components.php</kbd> dosyası içerisine tanımlayın.

```php
$c['app']->provider(
    [
        'cache' => 'Service\Providers\CacheServiceProvider'
    ]
);

/* Location: .app/components.php */
```

Artık servis sağlayıcınız uygulama içerisinde çalışmaya hazır.

<a name="application-doc"></a>

### Uygulama Sınıfı Belgelerine Bir Gözatın

Eğer konteyner sınıfını kavradıysanız Obullo çerçevesi hakkında temel olan çoğu şeyi öğrendiniz demektir bununla beraber çerçeveye daha hakim olmak için [Application.md](Application.md) dökümentasyonuna gözatmanız yararlı olabilir.

<a name="method-reference"></a>

#### Fonksiyon Referansı

##### $c['class']

Konteyner içerisinde kayıtlı bir sınıfı getirir.

##### $c->raw(string $class)

Kayıtlı sınıfa isimsiz fonksiyon içerisinde geri döner. İsimsiz fonksiyon çalıştırıldığında yeni bir nesne elde edilmiş olur.

##### $c->has(string $class)

Bir sınıfın uygulamadaki kısa adının konteyner içerisine kayıtlı olup olmadığını kontrol eder. Kayıtlı ise <b>true</b> değilse <b>false</b> değerine geri döner.

##### $c->active(string $class)

Bir sınıfın uygulama içerisinde daha önceden kullanılıp kullanılmadığını kontrol eder. Kullanılmış ise sınıf o seviyede uygulamada yüklüdür ve <b>true</b> aksi durumda <b>false</b> değerine geri döner.

##### $c->keys()

Tanımlı tüm sınıfların anahtar adlarına bir dizi içerisinde geri döner.

##### $c->getServices()

Kayıtlı tüm servislere bir dizi içerisinde geri döner.

