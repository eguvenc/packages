
## Uygulama Sınıfı

Uygulama sınıfı, ortam değişkenine ulaşmak, servis sağlayıcı, bileşenleri veya katmanları eklemek, uygulama versiyonunu almak gibi uygulama ile ilgili ana fonksiyonlarını barındıran sınıftır.

<ul>
<li>
    <a href="#application-flow">İşleyiş</a>
    <ul>
        <li><a href="#index-file">Index.php dosyası</a></li>
        <li><a href="#dispatcing-routes">Route Kuralları ve Http Katmanları</a></li>
        <li><a href="#http-and-console-requests">Http ve Cli İstekleri</a></li>
    </ul>
</li>

<li>
    <a href="#config">Konfigürasyon ve Çalıştırma</a>
    <ul>
        <li><a href="#environments">Ortam Konfigürasyonu</a></li>
        <li><a href="#env-variable">Ortam Değişkeni</a></li>
        <li><a href="#create-a-new-env-variable">Yeni Bir Ortam Değişkeni Yaratmak</a></li>
    </ul>
</li>

<li>
    <a href="#components">Bileşenler</a>
    <ul>
        <li><a href="#defining-components">Bileşenleri Tanımlamak</a></li>
    </ul>
</li>

<li><a href="#get-methods">Get Metotları</a></li>
<li><a href="#set-methods">Set Metotları</a></li>

</ul>

<a name='application-flow'></a>

### İşleyiş

 Uygulama ortam değişkeni olmadan çalışamaz ve bu nedenle ortam çözümlemesi çekirdek yükleme seviyesinde <kbd>app/environments.php</kbd> dosyası okunarak <kbd>$c['app']->detectEnvironment()</kbd> metodu ile ortram belirlenir ve ortam değişkenine <kbd>$c['app']->env()</kbd> metodu ile uygulamanın her yerinden ulaşılabilir. Uygulamaya ait tüm isteklerin çözümlendiği dosya index.php dosyasıdır bu dosya sayesinde uygulama başlatılır.

<a name="index-file"></a>

#### Index.php dosyası

Bu dosyanın tarayıcıda gözükmemesini istiyorsanız bir <kbd>.htaccess</kbd> dosyası içerisine aşağıdaki kuralları yazmanız yeterli olacaktır.

```php
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond $1 !^(index\.php|assets|robots\.txt)
RewriteRule ^(.*)$ ./index.php/$1 [L,QSA]
```

<a name="dispatcing-routes"></a>

#### Route Kuralları ve Http Katmanları

<kbd>index.php</kbd> dosyasına gelen bir http isteğinden sonra uri ve route sınıfı yüklenir uri sınıfı url değerlerini çözümleyerek route sınıfına gönderir, gerçek url çözümlemesi ise route sınıfında gerçekleşir.

Bir http GET isteği çözümlemesi

```php                   
$c['router']->get('product/([0-9])', 'shop/product/$1');                            
```

Bir http POST isteği çözümlemesi

```php
$c['router']->post('product/post', 'shop/product/post')->middleware('Csrf'); 
```

GET ve POST isteklerini içeren bir route çözümlenmesi

```php
$c['router']->match(['get', 'post'], 'product/page', 'shop/product/page');
```

Sadece yetkilendirilmiş kullanıcılara ait bir route örneği

```php
$c['router']->group(
    [
        'name' => 'AuthorizedUsers',
        'middleware' => array('Auth', 'Guest')
    ],
    function () {

        $this->attach('membership/restricted');
    }
);
```

> Route çözümlemeleri ilgili daha fazla bilgi için [Router.md](Router.md) dosyasını gözden geçirebilirsiniz.
> Http katmanları ile ilgili daha fazla bilgi için [App-Middlewares.md](App-Middlewares.md) dosyasını gözden geçirebilirsiniz.


<a name='http-and-console-requests'></a>

#### Http ve Cli İstekleri

Yazılımda uygulama http ve konsol isteklerine göre Http ve Cli sınıfları olarak ikiye ayrılır. Http isteğinden sonraki çözümlemede kontrolör dosyaları <kbd>modules/</kbd> klasöründen çağrılırken Cli istekleri ise konsoldan <kbd>$php task command</kbd> yöntemi ile <kbd>modules/tasks</kbd> klasörüne yönlendirilir. Http istekleri <kbd>index.php</kbd> dosyasından Cli istekleri ise <kbd>cli.php</kbd> dosyasından çalışmaya başlar.

<a name='config'></a>

### Konfigurasyon ve Çalıştırma


<a name="environments"></a>

#### Ortam Konfigürasyonu

Uygulamanız local, test, production veya yeni eklenen bir çevre ortamı ile çalışabilir. Çevre ortamı bir konfigürasyon dosyasında oluşturulan sunucu isimlerinin mevcut sunucu ismi ile karşılaştırılması sonucu elde edilir. Bu dosya <kbd>app/environments.php</kbd> dosyasıdır.

```php
return array(

    'local' => [
        'john-desktop',
        'localhost.ubuntu',
    ],

    'test' => [
        'localhost.test',
    ],

    'production' => [
        'localhost.production',
    ],
);
```

Local ortamda çalışırken her geliştiricinin kendine ait bilgisayar ismini <kbd>app/environments.php</kbd> dosyası <kbd>local</kbd> dizisi içerisine bir defalığına eklemesi gereklidir. Linux benzeri işletim sistemlerinde bilgisayarınızın adını hostname komutuyla kolayca öğrenebilirsiniz.

```
root@localhost: hostname   // localhost.ubuntu
```

Prodüksiyon veya test gibi ortamlarda çalışmaya hazırlık için sunucu isimlerini yine bu konfigürasyon dosyasındaki prodüksiyon ve test dizileri altına tanımlamanız yeterli olacaktır. Sunucu isimleri geçerli sunucu ismi ile eşleşmediğinde aşağıdaki gibi bir hata ile karşılaşırsınız.

```
We could not detect your application environment, please correct your app/environments.php file.
```

<a name="env-variable"></a>

#### Ortam Değişkeni

* <b>Local</b> : Yerel sunucu ortamıdır, geliştiriciler tarafından uygulama bu ortam altında geliştirilir, her bir geliştiricinin bir defalığına <kbd>app/environments.php</kbd> dosyası içerisine kendi bilgisayarına ait ismi tanımlaması gereklidir.

* <b>Test</b> : Test sunucu ortamıdır, geliştiriciler tarafından uygulama bu ortamda test edilir sonuçlar başarılı ise prodüksiyon ortamında uygulama yayına alınır, test sunucu isimlerinin bir defalığına <kbd>app/environments.php</kbd> dosyası içerisine tanımlaması gereklidir.

* <b>Production</b> : Prodüksiyon sunucu ortamıdır, geliştiriciler tarafından testleri geçmiş başarılı uygulama prodüksiyon ortamında yayına alınır, prodüksiyon sunucu isimlerinin bir defalığına <kbd>app/environments.php</kbd>  dosyası içerisine tanımlaması gereklidir.

Geçerli ortam değişkenine env() metodu ile ulaşılır.

```php
echo $c['app']->env();  // Çıktı  local
```

<a name="create-a-new-env-variable"></a>

#### Yeni Bir Ortam Değişkeni Yaratmak

Yeni bir ortam yaratmak için <kbd>app/environments.php</kbd> dosyasına ortam adını küçük harflerle girin. Aşağıdaki örnekte sunucu isimleri ile birlikte <kbd>qa</kbd> adında bir ortam yaratılıyor.

```php
return array(
    .
    .
    'production' => [ ... ]
    'qa' => [
        'example.hostname'
        'example2.hostname'
    ]
);
```

Local çevre ortamından yeni yaratılan ortama konfigürasyon dosyalarını kopyalayın.

```php
- app
    - local
        config.php
        database.php
    - qa
        config.php
        database.php
```

Kopyaladığınız konfigürasyon dosyaları içerisine sadece değişime uğrayan anahtarları koymanız yeterli olur tüm konfigürasyon anahtarlarının olmasına gerek yoktur. Çünkü uygulama çalıştırıldığında varolmayan anahtarlar var olanlar ile birleştirilirler.

```php
return array(

    'log' => [
        'enabled' => true,
    ],
    'http' => [
        'webhost' => 'example.com',
    ],
    'extra' => [
        'annotations' => true,
    ],
    'cookie' => [
        'domain' => '.example.com',
    ],
    'security' => [
        'encryption' => [
            'key' => 'qa-secret-key',
        ],
    ],
);
```

Yukarıdaki örnekte <kbd>qa</kbd> ortamı için oluşturulmuş <kbd>config.php</kbd> dosyası görülüyor.

<a name="components"></a>

### Bileşenler

Bileşenler uygulamada yüklendiğinde önceden tanımlanmış çekirdek sınıflardır uygulama içerisine takma adlar ile atanırlar ve uygulama çalıştığında bu takma isimlerle çağrılırlar.

<a name="defining-components"></a>

#### Bileşenleri Tanımlamak

Bir bileşenin uygulama içerisinde çalışabilmesi için <kbd>app/components.php</kbd> dosyasına tanımlı olması gerekir. Bileşenler uygulamanın her yerinde kullanılma ihtimalleri yüksek olan sınıflardır. Bir bileşeni onun uygulama içerisindeki görevini bilmeden kaldırdıysanız uygulamanız düzgün çalışmayabilir. Bir bileşen tanımlandıktan sonra konteyner sınıfı içerisinde kayıt edilir ve çağrılmadığı sürece uygulamaya yüklenmez. Bileşenin yüklenmesi için aşağıdaki gibi en az bir defa çağrılması gerekir.

```php
$this->c['view'];
```

Bileşenler <kbd>app/components.php</kbd> dosyasında aşağıdaki gibi tanımlanırlar.

```php
$c['app']->component(
    [
        'is' => 'Obullo\Filters\Is',
        'view' => 'Obullo\View\View',
        'task' => 'Obullo\Cli\Task\Task',
        'form' => 'Obullo\Form\Form',
        'clean' => 'Obullo\Filters\Clean',
        'flash' => 'Obullo\Flash\Session',
        'router' => 'Obullo\Router\Router',
        'cookie' => 'Obullo\Cookie\Cookie',
        'element' => 'Obullo\Form\Element',
        'template' => 'Obullo\View\Template',
        'response' => 'Obullo\Http\Response',
        'validator' => 'Obullo\Validator\Validator',
        'middleware' => 'Obullo\Application\MiddlewareStack',
        'translator' => 'Obullo\Translation\Translator',
    ]
);
```

Eğer mevcut bir bileşeni değiştirmek istiyorsanız isimlere karşılık gelen sınıf yolunu kendi sınıf yolunuz ile güncellemeniz gerekir.

<a name="get-methods"></a>

### Get Metotları

Get türündeki metotlar uygulama sınıfı değerlerine ulaşmanızı sağlar.

<a name="get-methods-env"></a>

##### $this->app->env();

Geçerli ortam değişkenine döner.

```php
echo $this->app->env();  // local
```

yada 

```php
echo $c['app']->env();  // local
```

<a name="get-methods-version"></a>

##### $this->app->version();

Mevcut Obullo sürümüne geri döner.

```php
$c['app']->version(); // Çıktı 1.0
```

Yada

```php
\Obullo\Application\Application::VERSION // Çıktı 1.0
```

<a name="get-methods-x"></a>

##### $this->app->x();

Uygulama sınıfında içerisinde çağırılan metot tanımlı değilse Controller sınıfından çağırır.

```php
$this->app->test();  // Contoller sınıfı içerisindeki test metodunu çalıştırır.
```

```php
$this->c['app']->test();  // Contoller sınıfı içerisindeki test metodunu çalıştırır.
```

##### $this->app->uri->x();

Bir Layer ( Bknz. [Layer](Layer.md) paketi  ) isteği gönderildiğinde uri nesnesi istek gönderilen uri değeri ile yeniden oluşturulur ve bu nedenle evrensel uri nesnesi değişime uğrar. Böyle bir durumda bu metot ilk durumdaki http isteğinin uri nesnesine ulaşabilmeyi sağlar.

```php
$this->c['app']->uri->getPath();
```

##### $this->app->router->x();

Uygulamada kullanılan evrensel <b>router</b> nesnesine geri dönerek bu nesnenin metotlarına ulaşmanızı sağlar. Bir Layer isteği ( Bknz. [Layer](Layer.md) paketi  ) gönderildiğinde router nesnesi istek gönderilen uri değeri ile yeniden oluşturulur ve bu nedenle evrensel router nesnesi değişime uğrar. Böyle bir durumda bu metot ilk durumdaki http isteğinin router nesnesine ulaşabilmeyi sağlar.

```php
$this->c['app']->router->getMethod();
```

<a name="set-methods"></a>

### Set Metotları

Set türündeki metotlar uygulama sınıfındaki varolan değişkenlere yeni değerler atamanızı yada etkilemenizi sağlar.

<a name="set-methods-register"></a>

##### $c['app']->provider(array $providers);

<kbd>app/components.php</kbd> dosyası içerisinde servis sağlayıcısı tanımlanmasını sağlar.

```php
$c['app']->provider(
    [
        'logger' => 'Obullo\Service\Provider\LoggerServiceProvider',
        // 'database' => 'Obullo\Service\Provider\DatabaseServiceProvider',
        'database' => 'Obullo\Service\Provider\DoctrineDBALServiceProvider',
        'cache' => 'Obullo\Service\Provider\CacheServiceProvider',
        'redis' => 'Obullo\Service\Provider\RedisServiceProvider',
    ]
);
```

##### $c['app']->service(array $services);

<kbd>app/components.php</kbd> dosyası içerisinde servisler oluşturmanızı sağlar.

```php
$c['app']->service(
    [
        'logger' => 'Log\LogManager',
        'db' => 'Obullo\Database\DatabaseManager',
        'url' => 'Obullo\Url\UrlManager',
        'csrf' => 'Obullo\Security\CsrfManager',
    ]
);
```

##### $c['app']->component(array $components);

<kbd>app/components.php</kbd> dosyası içerisinde bileşenler oluşturmanızı sağlar.

```php
$c['app']->component(
    [
        'is' => 'Obullo\Filters\Is',
        'view' => 'Obullo\View\View',
        'task' => 'Obullo\Cli\Task\Task',
    ]
);
```

##### $c['app']->dependency(array $deps);

<kbd>app/components.php</kbd> dosyası içerisinde bağımlılık yönetimi için yeni sınıflar eklemenizi sağlar.

```php
$c['app']->dependency(
    [
        'dependency',
        'app',
        'middleware',
        'config',
        'layer',
        'logger',
        'request',
        'response',
        'session',
        'queue',
        'user',
        'csrf',
        'captcha',
        'recaptcha',
    ]
);
```


Kendi yarattığınız bir sınıfa bağımlılık listesine eklediğiniz sınıfları enjekte etmek için sınıfı,

```php
new Namespace/ClassName;
```

yerine

```php
$c['dependency']->resolve('Namespace\ClassName');
```

<kbd>resolve()</kbd> metodu ile çağırın. Eğer construct() metodu içerisinde girilen değişken isimleri yukarıdaki bağımlılık listenizle uyuşuyorsa aşağıdaki gibi $request, $config ve $logger bileşenlerini sınıfınıza enjekte edebilirsiniz.


```php
namespace Example;

use Psr\Http\Message\ServerRequestInterface as Request;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;

class MyClass
{
    protected $request;
    protected $config;
    protected $cookies;
    protected $logger;

    public function __construct(Request $request, Config $config, Logger $logger)
    {
        $this->cookies = $request->getCookieParams();
        $this->config = $config;
        $this->logger = $logger;

        $this->logger->debug('MyClass Initialized');
    }
}
```

```php
$myClass = $c['dependency']->resolve('Example\MyClass');

var_dump($myClass); // object
```
