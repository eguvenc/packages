
# Obullo Container

Obullo Container PHP 5.4 ve üzeri sürümler için <kbd>hafif yükte</kbd> ve ortam tabanlı çalışabilen bir bağımlılık enjeksiyon çözümüdür. Uygulamanızda servisler, bileşenler ve servis sağlayıcıları oluşturabilmeyi sağlar.

### Kurulum

Demo dosyası altında örnek uygulamayı çalıştırabilmek için <kbd>composer.json</kbd> dosyasınıza <kbd>app/classes</kbd> dizini için aşağıdaki gibi bir yükleyici tanımlayın.

```php
{
    "autoload": {
        "psr-4": {
            "": "app/classes"
        }
    }
}
```

Container paketini yükleyin.

```php
composer require obullo/container
```

Yükleyicilerin sorunsuz çalışabilmesi için composer autoload önbelleğini temizleyin.

```php
composer dump-autoload
```

### Demo Projeyi Çalıştırmak

<kbd>demo/</kbd> projenizi aşağıdaki gibi ana dizine kopyalayın.

```php
- demo
    - app
        - classes
            + Component
            + Service
            FooServiceManager.php
            LogServiceManager.php
        - config
            - local
                - service
                    foo.php
                    logger.php
    - vendor
        - obullo
            + container
    composer.json
    index.php
```

```php
http://demo/index.php
```

Proje ana dizinindeki index.php dosyanızı çalıştırın.

### Nasıl Çalışıyor ?

Servis yükleyici kullanarak <kbd>app/config/service</kbd> klasöründeki tüm servis konfigürasyonları servis çağırıldığında otomatik yüklenir.

#### Servis Yükleyicisi

<kbd>Php</kbd> biçimindeki dosyalar için php servis yükleyicisi kullanılarak servis konfigürasyonları aşağıdaki gibi yüklenirler.

```php
use Obullo\Container\Loader;
use Obullo\Container\Container;

$c = new Container(new Loader('app/config/local/service', 'php'));

$c->service(
    [
        'foo' => 'FooServiceManager',
        'logger => 'LogServiceManager'
    ]
);
```

<kbd>Xml</kbd> biçimindeki servis konfigürasyon dosyaları için ikinci parametreyi xml olarak değiştirmeniz gerekir.

```php
use Obullo\Container\Loader;
use Obullo\Container\Container;

$c = new Container(new Loader('app/config/local/service', 'php'));

$c->service(
    [
        'foo' => 'FooServiceManager',
        'logger => 'LogServiceManager'
    ]
);
```

Servis yükleyicisi kullanıldığında servislerin çalışabilmesi için <kbd>service()</kbd> metodu ile konteyner içerisine kayıt edilmeleri gerekir. Yükleyici kullanıldığında <kbd>service</kbd> klasörü olarak tanımladığınız klasör içerisinden servis konfigürasyon dosyaları array biçiminde çözümlenerek konteyner içerisine enjekte edilirler.

Servis yükleyici yani dosya biçiminde servisler kullanmak <kbd>istemiyorsanız</kbd> yada dinamik olarak bir servis tanımlamanız gerekiyorsa servisleri el ile de tek tek tanımlayabilirsiniz.

```php
$c['myclass'] = function () {
    return new MyClass;
}
```

Tanımlanan servisler aşağıdaki gibi çağırılırlar.

```php
$c['foot'];  // foo servisi
$c['myclass'];  // myclass servisi
```

Eğer yükleyici kullanmak istemezseniz konteyner içerisine hiçbirşey göndermeyin.

```php
$c = new Container;
```

#### Dosya Tabanlı Servisler Yaratmak

Demo uygulamanız içerisindeki <kbd>app/config/local/service</kbd> dizini içerisine servis dosyalarınızı kaydedin. Aşağıda Foo servisi için FooServiceManager.php adlı servis dosyası örneği gösteriliyor.

```php
- myproject
    - app
        + classes
        - config
            - local
                - service
                    foo.php
                    logger.php

```

Aşağıdaki foo.php konfigürasyon dosyasında <kbd>params</kbd> parametresi servis sınıfına gönderilecek parametreleri belirler. Params anahtarına girilen parametreler konteyner içerisine kayıt edilirler. Son anahtar <kbd>methods</kbd> ise eğer servis içerisinden fonksiyon çalıştırılmak suretiyle set edilmesi gereken konfigürasyonlar varsa, servis içerisinden konfigürasyona girdiğiniz php metotlarını çağırır.

```php
return array(

    'params' => [
        'foo' => 'bar'
    ],
    'methods' => [
        'exampleMethod' => ['arg1', 'arg2']
    ]
);

// Location: app/classes/local/service/foo.php
```

Foo servisi içerisindeki Foo sınıfı ayrı bir dosya olarak dışarıda da tutulabilir burada daha iyi anlaşılması için foo sınıfı ve servis manager dosyası bir arada gösteriliyor.

```php
use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Foo {
    protected $params;
    public function __construct(array $params)
    {
        $this->params = $params;
    }
    public function bar()
    {
        echo "<br>Hello foo service bar() method !";
    }
    public function exampleMethod()
    {
        echo "<br>Hello example method !";   
    }
}

class FooServiceManager implements ServiceInterface {
    protected $c;
    public function __construct(ContainerInterface $c = null, array $params)
    {
        $this->c = $c;
        $this->c['foo.params'] = $params;
    }
    public function register() 
    {
        $this->c['foo'] = function () {
            return new Foo($this->c['foo.params']);
        };
    }
}

// Location: app/classes/FooServiceManager.php
```

Yukarıdaki örnekte <kbd>FooServiceManager</kbd> sınıfı gerçek <kbd>Foo</kbd> sınıfını kontrol etmeyi sağlar. Uygulamanızda konteyner içerisine kayıt edilen servis parametrelerine aşağıdaki gibi her yerden ulaşabilirsiniz.

```php
print_r($c['foo.params']);  // Array ( [foo] => bar ) 
```

#### Ortam Değişkenini Belirlemek

Dosya biçimindeki ortam tabanlı servislerin çalışabilmesi için servislerin yüklendiği <kbd>app/config/$env/service</kbd> dosya yolu $env değerini bir değişkene atamanız yeterli olur.

```php
$env = 'local';

$c = new Container(new Loader("app/config/$env/service", 'php'));
```

#### Servisleri Çözümlemek

Tanımlanan servisler konteyner içerisinden ArrayAccess yönetimi ile çözümlenerek uygulamanızda kullanılabilir hale gelirler.

```php
$foo = $c['foo'];
echo $foo->bar();  // Hello foo service !
```

Obullo konteyner Get metodunu da destekler.

```php
$foo = $c->get('foo');
echo $foo->bar();  // Hello foo service !
```

Eğer paylaşımlı olan bir servisin yeni değişken değerleriyle (new instance) ilan edilmesini istiyorsanız raw komutunu kullanın. 

```php
$foo = $c['foo'];  // old foo
$foo = $c['foo'];  // old foo

$foo = $c->raw('foo');  // new foo
$newFoo = $foo();

echo $newFoo->bar();  // Hello foo service !
```

### Bir Uygulama Sınıfı Yaratın !

Eğer Obullo çerçevesi kullanıyorsanız aşağıdaki örneği yapmanıza gerek kalmaz eğer başka bir uygulama kullanıyorsanız servis sağlayıcıları ve bileşenler oluşturabilmek için aşağıdaki gibi bir uygulama sınıfı yaratın.

```php
class Application {
    protected $c;
    protected $dependency;
    function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }
    function service(array $services)
    {
        $this->c->service($services);
        return $this;
    }
    function provider(array $providers)
    {
        $this->c->provider($providers);
        return $this;
    }
    function component(array $namespaces)
    {   
        $this->dependency = new Dependency($this->c);
        foreach ($namespaces as $cid => $Class) {
            $this->dependency->addComponent($cid, $Class);
            $this->dependency->addDependency($cid);
        }
        return $this;
    }
    function dependency(array $deps)
    {
        foreach ($deps as $cid) {
            $this->dependency->addDependency($cid);
        }
        return $this;
    }
    function removeDependency(array $deps)
    {
        foreach ($deps as $cid) {
            $this->dependency->removeDependency($cid);
        }
        return $this;
    }
}
```

Uygulama sınıfını konteyner içerisine kaydedin.

```php
$c['app'] = function () use ($c) {
    return new \Application($c);
};
```

Uygulama sınıfız artık hazır şimdi servis sağlayıcıları ve bileşenlerinizi aşağıdaki gibi oluşturabilirsiniz.

```php
$c['app']->provider(
    [
        'redis' => 'Service\Provider\Redis',
        'memcached' => 'Service\Provider\Memcached'
    ]
)->service(
    [
        'foo' => 'FooServiceManager',
        'logger' => 'LogServiceManager'
    ]
)->component(
    [
        'config' => 'Component\Config',
        'test' => 'Component\Test',
    ]
)->dependency(
    [
        'app',
        'logger'
    ]
);
```

### Servisler

Servislerin çözümlenebilmeleri için <kbd>service()</kbd> metodu kullanılarak tanımlanmaları gerekir.

```php
$c['app']->service(
    [
        'foo' => 'FooServiceManager',
        'logger' => 'LogServiceManager'
    ]
);
```

Tanımladığınz servislere ait konfigürasyon dosyalarını <kbd>app/config/local/service</kbd> klasörü içerisinde oluşturmayı unutmayın.

### Servis Sağlayıcıları

Bir servis sağlayıcısı yazımlıcılara uygulamada kullandıkları yinelenen farklı konfigürasyonlara ait parçaları uygulamanın farklı bölümlerinde güvenli bir şekilde tekrar kullanabilmelerine olanak tanır. Bağımsız olarak kullanılabilecekleri gibi bir servis konfigürasyonunun içerisinde de kullanılabilirler.

Uygulamada kullanılan servis sağlayıcısı bir <b>bağlantı yönetimi</b> ile ilgili ise farklı parametreler gönderilerek açılan bağlantıları yönetirler ve her yazılımcının aynı parametreler ile uygulamada birden fazla bağlantı açmasının önüne geçerler.

Bir servis sağlayıcısı sınıfı yanlış yazılmış yada yapılandırılmış ise onu uygulamanızda kullandığınız bölümlerin hepsi yanlış çalışmaya başlar. Bu yüzden servis sağlayıcıları bir uygulama çalışırken en kritik rolü üstlenirler ve değişmez olmaları önerilir.

Daha fazla bilgi için Obullo <a href="https://github.com/obullo/service" target="_blank">servis</a> paketine gözatabilirsiniz.

#### Servis Sağlayıcılarını Tanımlamak

Demo uygulamanızda kendi servis sağlayıcılarınızı <kbd>app/classes/Service/Provider</kbd> klasörü altında tutmanız önerilir.

```php
$c['app']->provider(
    [
        'redis' => 'Service\Provider\Redis',
        'memcached' => 'Service\Provider\Memcached',
   ]
);
```

Servis sağlayıcılar aşağıdaki gibi app sınıfı içerisinden tanımlanırlar.


```php
$c['app']->provider(
    [
        'database' => 'Obullo\Service\Provider\Database',
        // 'database' => 'Obullo\Service\Provider\DoctrineDBAL',
        // 'qb' => 'Obullo\Service\Provider\DoctrineQueryBuilder',
        'cache' => 'Obullo\Service\Provider\Cache',
        'redis' => 'Obullo\Service\Provider\Redis',
        'memcached' => 'Obullo\Service\Provider\Memcached',
        // 'memcache' => 'Obullo\Service\Provider\Memcache',
        'amqp' => 'Obullo\Service\Provider\Amqp',
        // 'amqp' => 'Obullo\Service\Provider\AmqpLib',
        'mongo' => 'Obullo\Service\Provider\Mongo',
    ]
);

// Location: .app/providers.php
```

#### Bir Servis Sağlayıcısını Çözümlemek

Servis sağlayıcıları bir uygulamada aşağıdaki gibi application <kbd>provider</kbd> metodu iler çağırılırlar. Aşağıdaki örnekte default veritabanı konfigürasyonuna ait bağlantı elde ediliyor.

```php
$redis = $c['redis']->get(['connection' => 'default']);

var_dump($redis); // Varsayılan bağlantıya ait Redis nesnesi
```

Bu örnekte ise redis sınıfına ait second adlı bağlantı alınıyor.

```php
$redis = $c['redis']->get(['connection' => 'second']);

var_dump($redis);  // İkinci bağlantıya ait Redis nesnesi
```

Memcached servis sağlayıcısı için aşağıda bir başka örnek gösteriliyor.

```php
$memcached = $c['memcached']->get(['connection' => 'default']);

var_dump($memcached);
```

Daha fazla bilgi için Obullo <a href="https://github.com/obullo/service" target="_blank">servis</a> paketini inceleyin.

### Bileşenler

Bileşenler uygulama içerisinde sıklıkla kullanılan ve birbirlerine bağımlılıkları olan sınıflardır. Bileşenler servisler gibi çözümlenir ve konteyner içerisine kaydedilirler. Servislerde olduğu gibi herhangi bir sınıf bileşen haline getirilebilir.

#### Bileşenleri Tanımlamak

```php
$c['app']->component(
    [
        'config' => 'Component\Config',
        'test' => 'Component\Test',
    ]
)->dependency(['app']);
```

Varsa bileşen bağımlılıklarını <kbd>dependency()</kbd> metodu ile tanımlayın.

Eğer bir bileşenin <kbd>__construct()</kbd> metodu parametreleri, bağımlılıklar içerisinde tanımlı bileşenlerden biri ile uyuşuyorsa bu sınıfın bağımlılıkları otomatik olarak bu sınıfa enjekte edilir. Konteyner sınıfının bir sınıfa enjekte edilebilmesi için parametreyi <kbd>$c</kbd> yada <kbd>$container</kbd> olarak tanımlamanız yeterli olur.

```php
namespace Component;

class Test {

    /**
     * Constructor
     * 
     * @param object $c      container
     * @param object $config config component
     * @param object $app    app component
     * @param object $logger logger service
     * @param mixed  $mixed  example none object parameter
     * 
     * @return void
     */
    public function __construct(ContainerInterface $c, $config, $app, $logger, $mixed = null)
    {
        $this->c = $c;
        $this->config = $config;
        $this->app = $app;
        $this->logger = $logger;
    }

    public function run()
    {
        $html = "Hello im a test component and my dependecies are \n\n";
        $html.= "<i>".get_class($this->c)."</i>,\n";
        $html.= "<i>".get_class($this->config)."</i>,\n";
        $html.= "<i>".get_class($this->app)."</i>, \n";
        $html.= "<i>".get_class($this->logger)."</i> \n";

        echo nl2br($html);
    }
}
```

#### Bir bileşeni çözümlemek

Bileşenler servisler gibi çözümlenir ve konteyner içerisine kaydedilirler. Örneğin demo projenizde tanımlı olan config bileşenini elde etmek için konteyner içerisinden sadece bileşen adını <kbd>config</kbd> olarak yazmanız gerekir.

```php
$config = $c['config'];
var_dump($config);
```

Bağımlılıkları test etmek için demo uygulamanızda test bileşeni run() metodunu çalıştırın.

```php
$c['test']->run();  

// Hello im a test component and my dependecies are

// Obullo\Container\Container,
// Component\Config,
// Application,
// Logger
```

Obullo Container ile artık bileşenler, servisler ve servis sağlayıcılarını destekleyen uygulamalar yaratmaya hazırsınız.