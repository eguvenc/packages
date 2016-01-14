
## Uygulama Sınıfı

Uygulama sınıfı, ortam değişkenine ulaşmak, servis sağlayıcı, bileşenleri veya katmanları eklemek, uygulamaya versiyonu almak gibi uygulama ile ilgili ana fonksiyonlarını barındıran sınıftır.

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
        <li><a href="#get-env-variable">Geçerli Ortam Değişkenini Almak</a></li>
        <li><a href="#existing-env-variables">Mevcut Ortam Değişkenleri</a></li>
        <li><a href="#create-env-variable-for-env-file">Ortam Değişkeni Klasörü Yaratmak</a></li>
        <li><a href="#create-a-new-env-variable">Yeni Bir Ortam Değişkeni Yaratmak</a></li>
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

#### Ortam Değişkeni Klasörü Yaratmak

Prodüksiyon ortamı üzerinden örnek verecek olursak bu klasöre ait config dosyaları içerisine yalnızca ortam değiştiğinde değişen anahtar değerlerini girmeniz yeterli olur. Çünkü konfigürasyon paketi geçerli ortam klasöründeki konfigürasyonlara ait değişen anahtarları <b>local</b> ortam anahtarlarıyla eşleşirse değiştirir aksi durumda olduğu gibi bırakır.

Mesala prodüksiyon ortamı içerisine aşağıdaki gibi bir <b>config.php</b> dosyası ekleseydik config.php dosyası içerisine sadece değişen anahtarları eklememiz yeterli olacaktı.

```php
- app
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

Bir servis sağlayıcısı <kbd>app/components.php</kbd> dosyasında kayıtlı ise <b>true</b> değilse <b>false</b> değerine geri döner.

##### $this->c['app']->version();

Güncel Obullo versiyonuna geri döner.

##### $this->c['app']->environments();

Ortam konfigürasyon dosyasında ( app/environments.php ) tanımlı olan ortam adlarına bir dizi içerisinde geri döner.

##### $this->c['app']->envArray();

Ortam konfigürasyon dosyasının ( app/environments.php ) içerisindeki tanımlı tüm diziye geri döner.

##### $this->c['app']->envPath();

Geçerli ortam değişkeninin dosya yoluna geri döner.