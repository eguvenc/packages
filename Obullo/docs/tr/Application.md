
## Uygulama Sınıfı ( Application )

Uygulama sınıfı, ortam değişkenine ulaşmak, servis sağlayıcı veya middleware eklemek, servis sağlayıcıya ulaşmak, uygulamaya versiyonu  gibi uygulamanın ana fonksiyonlarını barındıran sınıftır. Bu sınıf uygulamanın yüklenmesinden önce O2 çekirdek dosyası ( o2/Applicaiton/Http.php ) içerisinden konteyner (ioc) içine komponent olarak tanımlanır. Uygulama ortam değişkeni olmadan çalışamaz ve bu nedenle ortam çözümlemesi çekirdek yükleme seviyesinde <b>app/environments.php</b> dosyası okunarak <kbd>$c['app']->detectEnvironment();</kbd> metodu ile yapılır ve ortam değişkenine <kbd>$c['app']->env()</kbd> metodu ile uygulamanın her yerinden ulaşılabilir.

<ul>

<li>
    <a href="#application-flow">Genel Uygulama Akışı</a>
    <ul>
        <li><a href="#index-file">Index.php dosyası</a></li>
        <li><a href="#dispatcing-routes">Route Çözümlemeleri ve Http Katmanları</a></li>
        <li><a href="#http-and-console-requests">Http ve Konsol ( Cli ) İstekleri</a></li>
    </ul>
</li>

<li>
    <a href="#configuration-and-run">Konfigürasyon ve Çalıştırma</a>
    <ul>
        <li><a href="#create-env-file">Ortam Dosyası Değişkenleri ( .env.*.php ) Oluşturmak</a></li>
        <li><a href="#get-env-variable">Geçerli Ortam Değişkenini Almak</a></li>
        <li><a href="#existing-env-variables">Mevcut Ortam Değişkenleri</a></li>
        <li><a href="#create-env-variable-for-env-file">Ortam Değişkeni için Konfigürasyon Dosyalarını Yaratmak</a></li>
        <li><a href="#env-var">Konfigurasyon Değişkenleri ($c['var'])</a></li>
        <li><a href="#create-a-new-env-variable">Yeni Bir Ortam Değişkeni Yaratmak</a></li>
    </ul>
</li>

<li>
    <a href="#service-providers">Servis Sağlayıcıları</a>
    <ul>
        <li><a href="#service-providers">Servis Sağlayıcısı Nedir ?</a></li>
        <li><a href="#service-providers">Servis Sağlayıcılarını Tanımlamak</a></li>
    </ul>
</li>

<li>
    <a href="#components">Bileşenler</a>
    <ul>
        <li><a href="#defining-components">Bileşenleri Tanımlamak</a></li>
    </ul>
</li>

<li>
    <a href="#get-methods">Get Metotları</a>
    <ul>
        <li><a href="#get-methods-env">$c['app']->env()</a></li>
        <li><a href="#get-methods-environments">$c['app']->environments()</a></li>
        <li><a href="#get-methods-envArray">$c['app']->envArray()</a></li>
        <li><a href="#get-methods-envPath">$c['app']->envPath()</a></li>
        <li><a href="#get-methods-version">$c['app']->version()</a></li>
        <li><a href="#get-methods-provider">$c['provider']</a></li>
        <li><a href="#get-methods-x">$c['app']->x()</a></li>
    </ul>
</li>

<li>
    <a href="#set-methods">Set Metotları</a>
    <ul>
        <li><a href="#set-methods-register">$c['app']->provider()</a></li>
        <li><a href="#set-methods-middleware">$c['app']->middleware()</a></li>
        <li><a href="#set-methods-remove">$c['app']->remove()</a></li>
    </ul>
</li>

<li><a href="#application-class-references">Application Sınıfı Referansı</a></li>
</ul>

<a name='application-flow'></a>

### Genel Uygulama Akışı

Uygulamaya ait tüm isteklerin çözümlendiği dosya index.php dosyasıdır bu dosya sayesinde uygulama başlatılır. Tüm route çözümlemeleri bu dosya üzerinden yürütülür. 

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

#### Route Çözümlemeleri ve Http Katmanları

<kbd>index.php</kbd> dosyasına gelen bir http isteğinden sonra uri ve route sınıfı yüklenir uri sınıfı url değerlerini çözümleyerek route sınıfına gönderir, gerçek url çözümlemesi ise route sınıfında gerçekleşir. Çünkü route sınıfı <kbd>app/routes.php</kbd> dosyasında tanımlı olan route verilerini url değerleriyle karşılaştırarak çözümler ve çözümlenen route değerine ait Controller sınıfı <b>modules/</b> klasöründen çağrılarak çalıştırılır.

Bir http GET isteği çözümlemesi

```php                   
$c['router']->get('product/([0-9])', 'shop/product/$1');                            
```

Bir http POST isteği çözümlemesi

```php
$c['router']->post('product/post', 'shop/product/post')->middleware('Csrf'); 
```

Filterlenmemiş bir route çözümlenmesi

```php
$c['router']->match(['get', 'post'], 'product/page', 'shop/product/page');
```

Sadece yetkilendirilmiş kullanıcılara ait bir route örneği

```php
$c['router']->group(
    ['name' => 'AuthorizedUsers', 'middleware' => array('Auth', 'Guest')],
    function () {

        $this->defaultPage('welcome');
        $this->attach('membership/restricted');
    }
);
```

> Route çözümlemeleri ilgili daha fazla bilgi için [Router.md](Router.md) dosyasını gözden geçirebilirsiniz.

Eğer route yapınızda <b>middleware()</b> fonksiyonu ile yada middleware anahtarı içerisine tanımlanmış bir http katmanınız varsa ve gelen route isteği ile eşleşirse bu katman <b>app/Http/Middlewares</b> klasöründen çağrılarak çalıştırılır.

> Http katmanları ile ilgili daha fazla bilgi için [App-Middlewares.md](App-Middlewares.md) dosyasını gözden geçirebilirsiniz.


<a name='http-and-console-requests'></a>

#### Http ve Konsol ( Cli ) İstekleri

Obullo da uygulama http ve console isteklerine göre Http ve Cli sınıfları olarak ikiye ayrılır. Http isteğinden sonraki çözümlemede controller dosyası <b>modules/</b> klasöründen çağrılırken Cli istekleri ise konsoldan <kbd>$php task command</kbd> yöntemi ile <b>modules/tasks</b> klasörüne yönlendirilir.

Aşağıda <kbd>o2/Application/Http.php</kbd> dosyasının ilgili içeriği bize uygulama sınıfının konteyner içerisine nasıl tanımlandığını ve ortam değişkeninin uygulamanın yüklenme seviyesinde nasıl belirlendiğini gösteriyor.

```php
/**
 * Container
 * 
 * @var object
 */
$c = new Container;

$c['app'] = function () {
    return new Http;
};

/* Location: .Obullo/Application/Http.php */
```

> Obullo Http sınıfı konteyner içerisine $c['app'] olarak kaydedilir. Konsol ortamında ise o2/Application/Cli.php çağırıldığı için bu sınıf Http değil artık Cli sınıfıdır.

Uygulama sınıfını sabit tanımlamalar ( constants ), sınıf yükleyici ve konfigürasyon dosyasının yüklemesinden hemen sonraki aşamada tanımlı olarak gelir. Bunu daha iyi anlayabilmek için <b>kök dizindeki</b> index.php dosyasına bir göz atalım.


<a name="configuration-and-run"></a>

### Konfigürasyon ve Çalıştırma

Uygulamanızı doğru çalıştırabilmek için ilk aşamada bir ortam değişkenleri dosyası yaratmanız gerekir. Eğer uygulamayı yerel bir ortamda çalıştırıyorsanız proje ana dizinine <b>.env/local.php</b> yaratın. Ayrıca diğer mevcut olan her bir ortam için test veya production gibi aşamalara geldiğinizde iligili sunucularda bir <b>.env.ortam.php</b> dosyası yaratmanız gerekir.


<a name="create-env-file"></a>

#### Ortam Değişkenleri Dosyası ( .env.*.php ) Oluşturmak

<b>.env*</b> dosyaları servis ve sınıf konfigürasyonlarında ortak kullanılan bilgiler yada şifreler gibi daha çok paylaşılması mümkün olmayan hassas bilgileri içerir. Bu dosyalar içerisindeki anahtarlara <b>$c['var']['variable']</b> fonksiyonu ile ulaşılmaktadır. Takip eden örnekte bir .env dosyasının nasıl gözüktüğü daha kolay anlaşılabilir.

```php
return array(
    
    'COOKIE_DOMAIN' => '',

    'MYSQL_USERNAME' => 'root',
    'MYSQL_PASSWORD' => '123456',

    'MONGO_USERNAME' => 'root',
    'MONGO_PASSWORD' => '123456',

    'REDIS_HOST' => '127.0.0.1',
    'REDIS_AUTH' => '',
);

/* Location: .env.local.php */
```

> **Not:** Eğer bir versiyonlanma sistemi kullanıyorsanız <b>.env.*</b> dosyalarının gözardı (ignore) edilmesini sağlayarak bu dosyaların ortak kullanılmasını önleyebilirsiniz. Ortak kullanım önlediğinde her geliştiricinin kendine ait bir <b>env/local.php</b> konfigürasyon dosyası olacaktır. Uygulamanızı versiyonlanmak için <b>Git</b> yazılımını kullanıyorsanız ignore dosyalarını nasıl oluşturacağınız hakkında bu kaynak size yararlı olabilir. <a target="_blank" href="https://help.github.com/articles/ignoring-files/">https://help.github.com/articles/ignoring-files/</a>


Ortam değişikliği sözkonusu olduğunda .env* dosyalarını her bir ortam için bir defalığına kurmuş olamanız gerekir. Env dosyaları için dosya varmı kontrolü yapılmaz bu nedenle eğer uygulamanızda bu dosya mevcut değilse aşağıdaki gibi <b>php warning</b> hataları alırsınız.

```php
Warning: include(/var/www/example/.env/local.php): failed to open stream: 
No such file or directory in /o2/Config/Config.php on line 79
```

> **Not:**  Eğer <b>config.php</b> dosyasında <kbd>error > debug</kbd> değeri <b>false</b> ise boş bir sayfa görüntülenebilir bu gibi durumlarla karşılaşmamak için <b>local</b> ortamda <kbd>error > debug</kbd> değerini her zaman <b>true</b> yapmanız önerilir.

<a name="environment-configuration"></a>

#### Ortam Konfigürasyonu

Uygulamanız local, test, production veya yeni ekleyebileceğiniz çevre ortamlarında farklı konfigürasyonlar ile çalışabilir. Geçerli çevre ortamı bir konfigürasyon dosyasında oluşturmuş olduğunuz sunucu isimlerinin mevcut sunucu ismi ile karşılaştırılması sonucu elde edilir. Uygulamanızın hangi ortamda çalıştığını belirleyen konfigürasyon dosyası <b>app/environments.php</b> dosyasıdır.

Aşağıda <b>app/environments.php</b> dosyasına ait bir örneğini inceleyebilirsiniz.

```php
return array(

    'local' => [
        'john-desktop',     // hostname
        'localhost.ubuntu', // hostname
    ],

    'test' => [
        'localhost.test',
    ],

    'production' => [
        'localhost.production',
    ],
);

/* Location: .app/environments.php */
```

Linux benzeri işletim sistemlerinde bilgisayarınızın adını hostname komutuyla kolayca öğrenebilirsiniz.

```
root@localhost: hostname   // localhost.ubuntu
```

>**Not:** Local ortamda çalışırken her geliştiricinin kendine ait bilgisayar ismini <b>app/environments.php</b> dosyası <b>local</b> dizisi içerisine bir defalığına eklemesi gereklidir, prodüksiyon veya test gibi ortamlarda çalışmaya hazırlık için sunucu isimlerini yine bu konfigürasyon dosyasındaki prodüksiyon ve test dizileri altına tanımlamanız yeterli olacaktır. 

Konfigürasyon yapılmadığında yada sunucu isimleri geçerli sunucu ismi ile eşleşmediğinde uygulama size aşağıdaki gibi bir hata dönecektir.

```
We could not detect your application environment, please correct your app/environments.php hostnames.
```

<a name="get-env-variable"></a>

#### Geçerli Ortam Değişkenini Almak

Geçerli ortam değişkenine env() metodu ile ulaşılır.

```php
echo $c['app']->env();  // Çıktı  local
```

<a name="existing-env-variables"></a>

#### Mevcut Ortam Değişkenleri

<table>
    <thead>
        <tr>
            <th>Değişken</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>local</b></td>
            <td>Yerel sunucu ortamıdır, geliştiriciler tarafından uygulama bu ortam altında geliştirilir, her bir geliştiricinin bir defalığına <b>environments.php</b> dosyası içerisine kendi bilgisayarına ait ismi (hostname) tanımlaması gereklidir.Local sunucuda kök dizine <b>.env/local.php</b> dosyası oluşturup her bir geliştiricinin kendi çalışma ortamı servislerine ait <b>password, hostname, username</b> gibi bilgileri bu dosya içerisine koyması gereklidir.</td>
        </tr>
        <tr>
            <td><b>test</b></td>
            <td>Test sunucu ortamıdır, geliştiriciler tarafından uygulama bu ortamda test edilir sonuçlar başarılı ise prodüksiyon ortamında uygulama yayına alınır, test sunucu isimlerinin bir defalığına <b>environments.php</b> dosyası içerisine tanımlaması gereklidir.Test sunucusunda kök dizine <b>.env.test.php</b> dosyası oluşturulup hassas veriler ve uygulama servislerine ait şifre bilgileri bu dosya içerisinde tutulmalıdır.</td>
        </tr>
        <tr>
            <td><b>production</b></td>
            <td>Prodüksiyon sunucu ortamıdır, geliştiriciler tarafından testleri geçmiş başarılı uygulama prodüksiyon ortamında yayına alınır, prodüksiyon sunucu isimlerinin bir defalığına <b>environments.php</b> dosyası içerisine tanımlaması gereklidir. Prodüksiyon sunucusunda kök dizine <b>.env.production.php</b> dosyası oluşturulup hassas veriler ve uygulama servislerine ait şifre bilgileri bu dosya içerisinde tutulmalıdır.</td>
        </tr>
    </tbody>
</table>

<a name="create-env-variable-for-env-file"></a>

#### Ortam Değişkeni için Konfigürasyon Dosyalarını Yaratmak

Prodüksiyon ortamı üzerinden örnek verecek olursak bu klasöre ait config dosyaları içerisine yalnızca ortam değiştiğinde değişen anahtar değerlerini girmeniz yeterli olur. Çünkü konfigürasyon paketi geçerli ortam klasöründeki konfigürasyonlara ait değişen anahtarları <b>local</b> ortam anahtarlarıyla eşleşirse değiştirir aksi durumda olduğu gibi bırakır.

Mesala prodüksiyon ortamı içerisine aşağıdaki gibi bir <b>config.php</b> dosyası ekleseydik config.php dosyası içerisine sadece değişen anahtarları eklememiz yeterli olacaktı.

```php
- app
    - config
        - env
            + local
            - production
                config.php
                database.php
            + test
            - myenv
                config.php
                database.php
```

Aşağıdaki örnekte sadece dosya içerisindeki değişime uğrayan anahtarlar gözüküyor. Uygulama çalıştığında bu anahtarlar varolan local ortam anahtarları ile değiştirilirler.

Takip eden örnekte <kbd>production</kbd> ortamı için örnek bir <b>config.php</b> dosyası görülüyor.

```php
return array(
                    
    'error' => [
        'debug' => false,
    ],

    'log' =>   [
        'enabled' => false,
    ],

    'url' => [
        'webhost' => 'example.com',
        'baseurl' => '/',
    ],

    'cookie' => [
        'domain' => ''  // Set to .your-domain.com for site-wide cookies
    ],
);

/* Location: .config/production/config.php */
```

<a name="env-var"></a>

#### Konfigurasyon Değişkenleri ($c['var'])

$c['var'] yani <kbd>Obullo\Config\EnvVariable</kbd> sınıfı <kbd>Obullo/Application/Http.php</kbd> dosyasında ön tanımlı olarak gelir. <kbd>.env.*.php</kbd> dosyalarındaki değişkenler uygulama çalıştığında ilk önce <kb>$_ENV</kbd> değişkenine ve konfigürasyon dosyalarındaki anahtarlara atanırlar. Sonuç olarak $c['var'] değişkenleri konfigürasyon dosyaları içerisinde kullanıldıklarında bu dosyalardaki hassas ya da istisnai olan ortak değerlerin yönetimini kolaylaştırırlar.

Örnek bir env variable konfigürasyon çıktısı

```php
echo $c['var']['MONGO_USERNAME.root']; // Bu konfigürasyon boş gelirse default değer root olacaktır.
```

Yukarıdaki örnekte fonksiyonun birinci parametresi <kbd>$_ENV</kbd> değişkeninin içerisinden okunmak istenen anahtardır, noktadan sonraki ikinci parametre anahtarın varsayılan değerini tayin eder ve en son noktadan sonraki parametre anahtarın zorunlu olup olmadığını belirler.

Eğer en son parametre <kbd>required</kbd> olarak girilirse <kbd>$_ENV</kbd> değişkeni içerisinden anahtar değeri boş geldiğinde uygulama hata vererek işlem php <kbd>die()</kbd> metodu ile sonlanacaktır.

Boş gelemez zorunluluğuna bir örnek

```php
echo $c['var']['MONGO_USERNAME.root.required']; // Root parametresi boş gelemez.
```

Aşağıdaki örnekte ise mongo veritabanına ait konfigürasyon içerisine $_ENV değerlerinin bu sınıf ile nasıl atandığını görüyorsunuz.

```php
return array(

    'connections' =>
    [
        'default' => [
            'server' => 'mongodb://root:'.$c['var']['MONGO_PASSWORD.null'].'@localhost:27017',
            'options'  => ['connect' => true]
        ],
        'second' => [
            'server' => 'mongodb://test:123456@localhost:27017',
            'options'  => ['connect' => true]
        ]
    ],

);

/* Location: .config/local/mongo.php */
```

<a name="create-a-new-env-variable"></a>

#### Yeni Bir Ortam Değişkeni Yaratmak

Yeni bir ortam yaratmak için <b>app/environments.php</b> dosyasına ortam adını küçük harflerle girin. Aşağıdaki örnekte biz <b>myenv</b> adında bir ortam yaratttık.

```php
return array(
    'local' => [ ... ],
    'test' =>  [ ... ],
    'production' => [ ... ]
    'myenv' => [
        'example.hostname'
        'example2.hostname'
    ]
);

/* Location: .app/environments.php */
```

Yeni yarattığınız ortam klasörüne içine gerekli ise bir <b>config.php</b> dosyası ve database.php gibi diğer config dosyalarını yaratabilirsiniz. 

<a name="service-providers"></a>

### Servis Sağlayıcıları

Bir servis sağlayıcısı yazımlıcılara uygulamada kullandıkları yinelenen farklı konfigürasyonlara ait parçaları uygulamanın farklı bölümlerinde güvenli bir şekilde tekrar kullanabilmelerine olanak tanır. Bağımsız olarak kullanılabilecekleri gibi bir servis konfigürasyonunun içerisinde de kullanılabilirler.

#### Servis Sağlayıcısı Nedir ? 

Servis sağlayıcılarının tam olarak ne olduğu hakkında daha detaylı bilgi için [Container.md](Container.md) dosyasına bir gözatın.

#### Servis Sağlayıcılarını Tanımlamak

Servis sağlayıcıları servislerden farklı olarak uygulama sınıfı içerisinden tanımlanırlar ve uygulamanın çoğu yerinde sıklıkla kullanılan servis sağlayıcılarının önce <kbd>app/components.php</kbd> dosyasında tanımlı olmaları gerekir. Tanımlama sıralamasında öncelik önemlidir uygulamada ilk yüklenenen servis sağlayıcıları her zaman en üstte tanımlanmalıdır. Örneğin logger servis sağlayıcısı uygulama ilk yüklendiğinde en başta log servisi tarafından kullanıldığından bu servis sağlayıcısının her zaman en tepede ilan edilmesi gerekir.

Servis sağlayıcıları <kbd>app/components.php</kbd> dosyasına aşağıdaki gibi tanımlanırlar.

```php
/*
|--------------------------------------------------------------------------
| Register application service providers
|--------------------------------------------------------------------------
*/
$c['app']->provider(
    [
        'logger' => 'Obullo\Service\Provider\LoggerServiceProvider',
        'database' => 'Obullo\Service\Provider\DatabaseServiceProvider',
        'cache' => 'Obullo\Service\Provider\CacheServiceProvider',
        'redis' => 'Obullo\Service\Provider\RedisServiceProvider',
        'memcached' => 'Obullo\Service\Provider\MemcachedServiceProvider',
        'mailer' => 'Obullo\Service\Provider\MailerServiceProvider',
        'amqp' => 'Obullo\Service\Provider\AmqpServiceProvider',
    ]
);
```

<a name="components"></a>

### Bileşenler

Bileşenler uygulamada yüklendiğinde önceden tanımlanmış çekirdek sınıflardır uygulama içerisine takma adlar ile atanırlar ve uygulama çalıştığında bu takma isimlerle çağrılırlar.

<a name="defining-components"></a>

#### Bileşenleri Tanımlamak

Bir bileşenin uygulama içerisinde çalışabilmesi için <kbd>app/components.php</kbd> dosyasına tanımlı olması gerekir. Bileşenler uygulamanın her yerinde kullanılan yada kullanılma ihtimalleri yüksek olan sınıflardır. Bir bileşeni onun uygulama içerisindeki görevini bilmeden kaldırdıysanız uygulamanız düzgün çalışmayabilir. Bunun yanısıra uygulamanızda sık kullandığınız bileşenleri bu dosyaya tanımlayabilirsiniz. Bir bileşen tanımlandıktan sonra konteyner sınıfı içerisinde kayıt edilir ve çağrılmadığı sürece uygulamaya yüklenmez. Bileşenin yüklenmesi için aşağıdaki gibi en az bir defa çağrılması gerekir.

```php
$this->c['class'];
```

Mevcut bileşenler <kbd>app/components.php</kbd> dosyasında aşağıdaki gibi tanımlıdırlar.

```php
/*
|--------------------------------------------------------------------------
| Register core components
|--------------------------------------------------------------------------
*/
$c['app']->component(
    [
        'event' => 'Obullo\Event\Event',
        'exception' => 'Obullo\Error\Exception',
        'translator' => 'Obullo\Translation\Translator',
        'request' => 'Obullo\Http\Request',
        'response' => 'Obullo\Http\Response',
        'is' => 'Obullo\Http\Filters\Is',
        'clean' => 'Obullo\Http\Filters\Clean',
        'agent' => 'Obullo\Http\UserAgent',
        'layer' => 'Obullo\Layer\Request',
        'uri' => 'Obullo\Uri\Uri',
        'router' => 'Obullo\Router\Router',
    ]
);

/* Location: .app/components.php */
```

> **Not:** Mevcut bir bileşeni değiştirmek istiyorsanız isimlere karşılık gelen sınıf yolunu kendi sınıf yolunuz ile güncellemeniz gerekir.

<a name="get-methods"></a>

### Get Metotları

Get türündeki metotları uygulama sınıfında varolan verilere ulaşmanızı sağlar.

<a name="get-methods-env"></a>

##### $c['app']->env();

Geçerli ortam değişkenine döner.

```php
echo $c['app']->env();  // local
```

<a name="get-methods-environments"></a>

##### $c['app']->environments();

Ortam konfigürasyon dosyasında ( <b>app/environments.php</b> ) tanımlı olan ortam adlarına bir dizi içerisinde geri döner.

```php
print_r($c['app']->environments());

/* Çıktı
Array
(
    [0] => local
    [1] => test
    [2] => production
)
*/   
```

<a name="get-methods-envArray"></a>

##### $c['app']->envArray();

Ortam konfigürasyon dosyasının ( <b>app/environments.php</b> ) içerisindeki tanımlı tüm diziye geri döner.

```php
print_r($c['app']->envArray());

/* Çıktı
Array ( 
    'local' => array(
            [0] => my-desktop 
            [1] => someone.computer 
            [2] => anotherone.computer 
            [3] => john-desktop 
    ),
    'production' => array( .. )
)
*/
```

<a name="get-methods-envPath"></a>

##### $c['app']->envPath();

Geçerli ortam değişkeninin dosya yoluna geri döner.

```php
echo $c['app']->envPath();  // Çıktı  /var/www/project.com/config/local/
```
<a name="get-methods-version"></a>

##### $c['app']->version();

Mevctur Obullo sürümüne geri döner.

```php
$c['app']->version(); // Çıktı  2.1

\Obullo\Application\Application::VERSION // Çıktı  2.1
```

<a name="get-methods-provider"></a>

##### $c['$provider']->x();

```php
$this->db = $c['database']->get(['connection' => 'default']);
```

Uygulamaya tanımlanmış servis sağlayıcısı nesnesine geri döner. Tanımlı servis sağlayıcıları <kbd>app/components.php</kbd> dosyası içerisine kaydedilir.


<a name="get-methods-x"></a>

##### $this->c['app']->x();

Uygulama sınıfında eğer metod ( x ) tanımlı değilse Controller sınfından çağırır.

```php
$this->c['app']->test();  // Contoller sınıfı içerisindeki test metodunu çalıştırır.
```

##### $this->c['app']->uri->x();

Layer paketi isteği gönderildiğinde uri nesnesi istek gönderilen url değerinin yerel değişkenlerinden yeniden oluşturulur ve bu yüzden evrensel uri değişime uğrar. Böyle bir durumda bu method sizin ilk durumdaki http isteği yapılan evrensel uri nesnesine ulaşmanıza imkan tanır.

```php
$this->c['app']->uri->getPath();
```

##### $this->c['app']->router->x();

Uygulamada kullanılan evrensel <b>router</b> nesnesine geri dönerek bu nesnenin metotlarına ulaşmanızı sağlar. Uygulama içerisinde bir hiyerarşik katman ( HMVC bknz. [Layer](Layer.md) paketi  ) isteği gönderildiğinde router nesnesi istek gönderilen url değerinin yerel değişkenlerinden yeniden oluşturulur ve bu yüzden evrensel router değişime uğrar. Böyle bir durumda bu method ( x ) sizin ilk durumdaki http isteği yapılan evrensel router nesnesine ulaşmanıza imkan tanır.

```php
$this->c['app']->router->getMethod();
```

<a name="set-methods"></a>

### Set Metotları

Set türündeki metotlar uygulama sınıfındaki varolan değişkenlere yeni değerler atamanızı yada etkilemenizi sağlar.

<a name="set-methods-register"></a>

##### $this->c['app']->provider(array $provider);

<kbd>app/routes.php</kbd> dosyası içerisinde servis sağlayıcısı tanımlanmasını sağlar.

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

<a name="set-methods-middleware"></a>

##### $this->c['app']->middleware(mixed $middleware);

<kbd>app/middlewres.php</kbd> dosyası içerisinde uygulamaya yeni bir <b>evrensel</b> http katmanı eklenmesini sağlar. 

```php
$c['app']->middleware(new Http\Middlewares\Request);
```

Katman bu dosyada yada bir Controller sınıfı içerisinde dinamik olarak projeye dahil edilebilir.

```php
$this->c['app']->middleware('Test');
```

<a name="set-methods-remove"></a>

##### $this->c['app']->remove(string $middleware);

Tanımlı olan bir http katmanını uygulamadan siler.


<a name="application-class-references"></a>

#### Application Sınıfı Referansı

------

##### $this->c['app']->env();

Geçerli ortam değişkenine geri döner.

##### $this->c['app']->middleware(string | object $class, $params = array());

Uygulamaya dinamik olarak http katmanı ekler. Birinci parametre sınıf ismi veya nesnenin kendisi, ikinci parametre ise sınıf içerisine enjekte edilebilecek parametrelerdir.

##### $this->c['app']->x();

Uygulama sınıfında eğer metod ( x ) tanımlı değilse Controller sınfından çağırır.

##### $this->c['app']->router->x();

Uygulamada kullanılan evrensel <b>router</b> nesnesine geri dönerek bu nesnenin metotlarına ulaşmanızı sağlar. Uygulama içerisinde bir hiyerarşik katman ( HMVC bknz. [Layer](Layer.md) paketi  ) isteği gönderildiğinde router nesnesi istek gönderilen url değerinin yerel değişkenlerinden yeniden oluşturulur ve bu yüzden evrensel router değişime uğrar. Böyle bir durumda bu method ( x ) sizin ilk durumdaki http isteği yapılan evrensel router nesnesine ulaşmanıza imkan tanır.

##### $this->c['app']->uri->x();

Uygulamada kullanılan evrensel <b>uri</b> nesnesine geri dönerek bu nesnenin metotlarına ulaşmanızı sağlar. Uygulama içerisinde bir katman ( HMVC bknz. [Layer](Layer.md) paketi ) isteği gönderildiğinde uri nesnesi istek gönderilen url değerinin yerel değişkenlerinden yeniden oluşturulur ve bu yüzden evrensel uri değişime uğrar. Böyle bir durumda bu method sizin ilk durumdaki http isteği yapılan evrensel uri nesnesine ulaşmanıza imkan tanır.

##### $this->c['app']->register(array $providers);

<kbd>.app/components.php</kbd> dosyasında servis sağlayıcılarını uygulamaya tanımlamak için kullanılır. Uygulamanın çoğu yerinde sıklıkla kullanılan servis sağlayıcıların önce bu dosyada tanımlı olmaları gerekir. Tanımla sıralamasında öncelik önemlidir uygulamada ilk yüklenenen servis sağlayıcıları her zaman en üstte tanımlanmalıdır.

##### $this->c['app']->hasService(string $name)

Bir servis <kbd>app/classes/Service</kbd> klasöründe mevcut ise <b>true</b> değilse <b>false</b> değerine geri döner.

##### $this->c['app']->hasProvider(string $provider)

Bir servis sağlayıcısı <kbd>app/components.php</kbd> dosyasında kayıtlı ise <b>true</b> değilse <b>false</b> değerine geri döner.

##### $this->c['app']->provider(string $name)->get(array $params);

Uygulamaya tanımlanmış servis sağlayıcısı nesnesine geri döner. Tanımlı servis sağlayıcıları <kbd>app/components.php</kbd> dosyası içerisine kaydedilir.

##### $this->c['app']->version();

Güncel Obullo versiyonuna geri döner.

##### $this->c['app']->environments();

Ortam konfigürasyon dosyasında ( app/environments.php ) tanımlı olan ortam adlarına bir dizi içerisinde geri döner.

##### $this->c['app']->envArray();

Ortam konfigürasyon dosyasının ( app/environments.php ) içerisindeki tanımlı tüm diziye geri döner.

##### $this->c['app']->envPath();

Geçerli ortam değişkeninin dosya yoluna geri döner.