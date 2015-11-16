
## Konfigürasyon Sınıfı 

Konfigürasyon sınıfı <kbd>app/config</kbd> klasöründeki uygulamanıza ait konfigürasyon dosyalarını yönetir. Bu sınıf uygulama içerisinde konfigürasyon dosyalarını çevre ortamına ( environments ) göre geçerli klasörden yükler ve çağrıldığında tüm yüklenen konfigürasyon dosyalarına ait konfigürasyonlardan oluşan bir diziye geri döner. 

> **Not:** Varsayılan konfigürasyon dosyası <kbd>config/env/local/config.php</kbd> dosyasıdır ve bu dosya uygulama çalıştırıldığında uygulamaya kendiliğinden dahil edilir. Bu dosyayı ayrıca yüklememelisiniz.


<ul>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#loading-class">Sınıfı Yüklemek</a></li>
        <li><a href="#loading-config-files">Konfigürasyon Dosyalarını Yüklemek</a></li>
        <li><a href="#accessing-config-variables">Konfigürasyon Dosyalarına Erişim</a></li>
        <li><a href="#writing-config-files">Konfigürasyon Dosyalarına Yazmak</a></li>
        <li><a href="#shared-config-files">Paylaşımlı Konfigürasyon Dosyaları</a></li>
    </ul>

    <a href="#environment-config">Ortam Konfigürasyonu</a>
    <ul>
        <li><a href="#environent-variable">Ortam Değişkeni</a></li>
        <li><a href="#environent-variables">Mevcut Ortam Değişkenleri</a></li>
        <li><a href="#env-files">Ortam Değişkenleri Dosyası ( .env.*.php ) Oluşturmak</a></li>
        <li><a href="#env-var">Konfigürasyon Değişkenleri ($c['var'])</a></li>
        <li><a href="#create-your-environment">Yeni Bir Ortam Değişkeni Yaratmak</a></li>
        <li><a href="#creating-environment-config">Ortam Konfigürasyon Dosyası Yaratmak</a></li>
    </ul>
</li>

</ul>

<a name="running"></a>

### Çalıştırma

Konfigürasyon sınıfı uygulamaya çalışmaya başladığında yüklenir ve her zaman uygulama içerisinde yüklüdür.

<a name="loading-class"></a>

#### Sınıfı Yüklemek

```php
$this->c['config']->method();
```

<a name="loading-config-files"></a>

#### Konfigürasyon Dosyalarını Yüklemek

Bir konfigürasyon dosyası config sınıfı içerisindeki <kbd>load()</kbd> metodu ile yüklenir.

```php
$this->c['config']->load('database');
```

Yukarıda verilen örnekte çevre ortamını "local" ayarlandığını varsayarsak <kbd>database.php</kbd> dosyası <kbd>config/env/local/</kbd> klasöründen çağrılır. Bir konfigürasyon dosyası bir kez yüklendiğinde ona config sınıfı ile her yerden ulaşabilmek mümkündür.

```php
echo $this->c['config']['database']['connections']['db']['host'];  // Çıktı localhost
```

Bununla beraber config sınıfı içerisindeki load metodu yüklenen dosyanın konfigürasyonuna geri döner.

```php
echo $this->c['config']->load('database')['connections']['db']['host'];   // Çıktı localhost
```

<a name="accessing-config-variables"></a>

#### Konfigürasyon Dosyalarına Erişim

Bir konfigürasyon dizisine erişim dizi erişimi ( Array Access ) yöntemi ile gerçekleşir. Bu yöntem konfigürasyon sekmelerine aşağıdaki biçiminde erişmemizi sağlayarak konfigürasyonlara erişimi kolaylaştırır. Array Access yöntemi ile ilgili daha fazla bilgiye Php dökümentasyonu <a href="http://php.net/manual/tr/class.arrayaccess.php" target="_blank">http://php.net/manual/tr/class.arrayaccess.php</a> sayfasından ulaşabilirsiniz.

```php
$this->c['config']['item']['subitem'];
```
<a name="writing-config-files"></a>

#### Konfigürasyon Dosyalarına Yazmak

Config sınıfı içerisindeki write metodu <kbd>config/env.$env/</kbd> klasörü içerisindeki config dosyalarınıza yeni konfigürasyon verileri kaydetmenizi sağlar. Takip eden örnekte <kbd>config/env/local/domain.php</kbd> domain konfigürasyon dosyasındaki <b>maintenance</b> değerini güncelliyoruz.

```php
$newArray = $this->c['config']['domain'];
$newArray['root']['maintenance'] = 'down';  // Yeni değerleri atayalım

$this->c['config']->write('domain.php', $newArray);
```

Şimdi domain.php dosyanız aşağıdaki gibi güncellenmiş olmalı.

```php
return array(

    'root' => [
        'maintenance' => 'down',
        'regex' => null,
    ],
    'mydomain.com' => [
        'maintenance' => 'up',
        'regex' => '^framework$',
    ],
    'sub.domain.com' => [
        'maintenance' => 'up',
        'regex' => '^sub.domain.com$',
    ],
);

/* Location: ./var/www/framework/config/local/domain.php */
```

Yukarıdaki örnek <kbd>config/env.$env/</kbd> klasörü altındaki dosyalara yazma işlemi yapar. Eğer env klasörü dışında olan yani paylaşımlı bir konfigürasyon dosyasına yazma işlemi gerçekleştimek istiyorsak <b>"../"</b> dizinden çıkma karakteri kullanarak kaydetme işlemini gerçekleştirmemiz gerekir.

```php
$newArray = $this->c['config']->load('agents');
$newArray['platforms']['pc']['test'] = 'Merhaba yeni platform';  // Yeni değerleri atayalım

$this->c['config']->write('../agents.php', $newArray);
```

Şimdi <kbd>.config/agents.php</kbd> dosyasına bir gözatın.

```php
return array(
    
    'platforms' => [

        'pc' => [
            'gnu' => 'GNU/Linux',
            'unix' => 'Unknown Unix OS',
            'test' => 'Merhaba yeni platform',

/* Location: .config/agents.php */
```

<a name="shared-config-files"></a>

#### Paylaşımlı Konfigürasyon Dosyaları

Herhangi bir ortam değişkeni klasörü içerisinde yer almayıp <kbd>config/</kbd> klasörü kök dizininde yer alan diğer bir deyişle dışarıda kalan konfigürasyon dosyaları paylaşımlı konfigürasyon dosyaları olarak adlandırılırlar. Bir konfigürasyon dosyasının paylaşımlı mı yoksa ortam klasörüne mi ait olup olmadığı uygulama tarafından kendiliğinden belirlenir.

```php
$this->config->load('agents');
```

<a name="environment-config"></a>

### Ortam Konfigürasyonu

Uygulamanızın hangi ortamda çalıştığını belirleyen konfigürasyon dosyasıdır. Ortam değişkeni <b>app/environments.php</b> dosyasına tanımlayacağınız sunucu isimlerinin ( <b>hostname</b> ) geçerli sunucu ismi ile karşılaştırması sonucu ile elde edilir. Aşağıda <b>app/environments.php</b> dosyasının bir örneğini inceleyebilirsiniz.

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

Uygulamanıza ait çevre ortamı aşağıdaki metola elde edilir.

```
echo $this->c['app']->env();  // local
```

>**Not:** Local ortamda çalışırken her geliştiricinin kendine ait bilgisayar ismini <b>app/environments.php</b> dosyası <b>local</b> dizisi içerisine bir defalığına eklemesi gereklidir, prodüksiyon veya test gibi ortamlarda çalışmaya hazırlık için sunucu isimlerini yine bu konfigürasyon dosyasındaki prodüksiyon ve test dizileri altına tanımlamanız yeterli olacaktır. 

Konfigürasyon yapılmadığında yada sunucu isimleri geçerli sunucu ismi ile eşleşmediğinde uygulama size aşağıdaki gibi bir hata dönecektir.

```
We could not detect your application environment, please correct your app/environments.php hostnames.
```

<a name="environment-variable"></a>

#### Ortam Değişkeni

Geçerli ortam değişkenine geri döner.

```php
echo $c['app']->env();  // Çıktı  local
```

<a name="environment-variables"></a>

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

<a name="env-files"></a>

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

/* Location: .env/local.php */
```

> **Not:** Eğer bir versiyonlanma sistemi kullanıyorsanız <b>.env.*</b> dosyalarının gözardı (ignore) edilmesini sağlayarak bu dosyaların ortak kullanılmasını önleyebilirsiniz. Ortak kullanım önlediğinde her geliştiricinin kendine ait bir <b>env/local.php</b> konfigürasyon dosyası olacaktır. Uygulamanızı versiyonlanmak için <b>Git</b> yazılımını kullanıyorsanız ignore dosyalarını nasıl oluşturacağınız hakkında bu kaynak size yararlı olabilir. <a target="_blank" href="https://help.github.com/articles/ignoring-files/">https://help.github.com/articles/ignoring-files/</a>


Ortam değişikliği sözkonusu olduğunda .env* dosyalarını her bir ortam için bir defalığına kurmuş olamanız gerekir. Env dosyaları için dosya varmı kontrolü yapılmaz bu nedenle eğer uygulamanızda bu dosya mevcut değilse aşağıdaki gibi <b>php warning</b> hataları alırsınız.

```php
Warning: include(/var/www/example/config/local.php): failed to open stream: 
No such file or directory in /o2/Config/Config.php on line 79
```

> **Not:**  Eğer <b>config.php</b> dosyasında <kbd>error > debug</kbd> değeri <b>false</b> ise boş bir sayfa görüntülenebilir bu gibi durumlarla karşılaşmamak için <b>local</b> ortamda <kbd>error > debug</kbd> değerini her zaman <b>true</b> yapmanız önerilir.

<a name="env-var"></a>

#### Konfigürasyon Değişkenleri ($c['var'])

$c['var'] yani <kbd>Obullo\Config\EnvVariable</kbd> sınıfı <kbd>Obullo/Application/Http.php</kbd> dosyasında ön tanımlı olarak gelir. <kbd>.env.*.php</kbd> dosyalarındaki değişkenler uygulama çalıştığında ilk önce <kb>$_ENV</kbd> değişkenine ve konfigürasyon dosyalarındaki anahtarlara atanırlar. Sonuç olarak $c['var'] değişkenleri konfigürasyon dosyaları içerisinde kullanıldıklarında bu dosyalardaki hassas ya da istisnai olan ortak değerlerin yönetimini kolaylaştırırlar.


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

<a name="create-your-environment"></a>

#### Yeni Bir Ortam Değişkeni Yaratmak

Yeni bir ortam yaratmak için <kbd>app/environments.php</kbd> dosyasına ortam adını küçük harflerle girin. Aşağıdaki örnekte biz <kbd>env.my</kbd> adında bir ortam yaratttık.

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

<a name="creating-environment-config"></a>

#### Ortam Konfigürasyon Dosyası Yaratmak

Prodüksiyon ortamı üzerinden örnek verecek olursak bu klasöre ait config dosyaları içerisine yalnızca ortam değiştiğinde değişen anahtar değerlerini girmeniz yeterli olur. Çünkü konfigürasyon paketi geçerli ortam klasöründeki konfigürasyonlara ait değişen anahtarları <b>local</b> ortam anahtarlarıyla eşleşirse değiştirir aksi durumda olduğu gibi bırakır.

Mesala prodüksiyon ortamı içerisine aşağıdaki gibi bir <b>config.php</b> dosyası ekleseydik config.php dosyası içerisine sadece değişen anahtarları eklememiz yeterli olacaktı.

```php
- app
    - config
        + local
        - production
            config.php
            database.php
        + test
        - my
            config.php
            database.php
```

Aşağıdaki örnekte sadece dosya içerisindeki değişime uğrayan anahtarlar gözüküyor. Uygulama çalıştığında bu anahtarlar varolan env/local ortam anahtarları ile değiştirilirler.

Takip eden örnekte <kbd>production</kbd> ortamı için örnek bir <b>config.php</b> dosyası görülüyor.

```php
return array(
                    
    'error' => [
        'debug' => false,   // Debugging feature "disabled"" in "production" environment.
    ],

    'log' =>   [
        'enabled' => false,
    ],

    'url' => [
        'webhost' => 'example.com',
        'baseurl' => '/',
        'assets' => 'http://cdn.example.com/assets/',
    ],

    'http' => [
        'debugger' => [
            'enabled' => false
        ]
    ],

    'cookie' => [
        'domain' => ''  // Set to .your-domain.com for site-wide cookies

    ],
);

/* Location: .config/production/config.php */
```

#### Config Sınıfı Referansı

------

##### $this->config->load(string $filename);

Konfigürasyon dosyalarınızı <kbd>config/env.$env/</kbd> yada <kbd>config/</kbd> dizininden yükler. Dosya bu iki dizinden birinde mevcut değilse php hataları ile karşılaşılır.

##### $this->config['name']['item'];

Konfigürasyon sınıfı içerisine yüklenmiş bir dosyaya ait konfigürasyona erişmeyi sağlar.

##### $this->config->array['name']['item'] = 'value';

Yüklü olan bir konfigürasyona dinamik olarak yeni değerler atar.

##### $this->config->write(string $filename, array $data);

<kbd>config/</kbd> klasöründeki konfigürasyon dosyalarına veri yazmayı sağlar.


#### EnvVariable Sınıfı Referansı

------

##### $c['var']['x'];

Bir konfigürasyon dosyası içerisinde çevre ortamına duyarlı bir değişkene ulaşmayı sağlar.

##### $c['var']['x.default'];

Bir konfigürasyon dosyası içerisinde çevre ortamına duyarlı bir değişkenin değeri yoksa varsayılan olarak girilen ("default") değerin atanmasını sağlar.

##### $c['var']['x.null'];

Bir konfigürasyon dosyası içerisinde çevre ortamına duyarlı bir değişkenin değeri yoksa varsayılan olarak <b>"null"</b> boş değeri atanmasını sağlar.

##### $c['var']['x.default.required']; yada $c['var']['x.required'];

Bir konfigürasyon dosyası içerisinde çevre ortamına duyarlı bir değişkenin değeri yoksa uygulamanın durarak genel hata vermesini sağlar.