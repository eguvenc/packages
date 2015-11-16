
## Loglama Sınıfı

Logger sınıfı <kbd>handler</kbd> klasöründeki log sürücülerini kullanarak uygulamaya ait log verilerini <kbd>app/workers/logger</kbd> sınıfı yardımı ile direkt olarak (senkron) yada kuyruk servisi kullanarak (asenkron) olarak kaydeder. Logger sınıfı log verilerini arasındaki önemliliği destekler ve php SplPriorityQueue sınıfı yardımı ile log verilerini önem seviyelerine göre gruplar.

<ul>
    <li>
        <a href="#intro">Önbilgi</a>
        <ul>
            <li><a href="#features">Özellikler</a></li>
            <li><a href="#flow-chart">Akış Şeması</a></li>
        </ul>
    </li>

    <li>
        <a href="#configuration">Konfigürasyon</a>
    </li>

    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li>
                <a href="#service">Servis Konfigürasyonu</a>
                <ul>
                    <li><a href="#loading-service">Servisi Yüklemek</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        <a href="#writers">Yazıcılar</a>
        <ul>
            <li><a href="#add-handlers">Sürücü Eklemek</a></li>
            <li><a href="#set-writer">Ana Yazıcıyı Tanımlak</a></li>
        </ul>
    </li>

    <li>
        <a href="#filters">Filtreler</a>
        <ul>
            <li><a href="#define-filter">Filtre Tanımlamak</a></li>
            <li><a href="#global-filters">Evrensel Filtreler</a></li>
            <li><a href="#page-filters">Sayfa Filtreleri</a></li>
        </ul>
    </li>

    <li>
        <a href="#messages">Mesajlar</a>
        <ul>
            <li><a href="#severities">Log Seviyeleri</a></li>
            <li><a href="#log-messages">Log Mesajları</a></li>
            <li><a href="#loading-handlers">Farklı Log Yazıcılarına Loglama</a></li>
            <li><a href="#log-workers">Log Mesajlarını İşlemek</a></li>
        </ul>
    </li>

    <li>
        <a href="#queuing">Kuyruklama</a>
        <ul>
            <li><a href="#displaying-queue">Kuyruktaki İşleri Görüntülemek</a></li>
            <li><a href="#workers">İşciler İle Kuyruğu Tüketmek</a></li>
            <li><a href="#worker-parameters">İşci Parametreleri</a></li>
            <li><a href="#debug-mode">Hata Ayıklama Modu</a></li>
            <li><a href="#processing-jobs">Kuyruk Verilerini İşlemek</a></li>
            <li><a href="#removing-completed-jobs">Tamamlanan İşleri Kuyruktan Silmek</a></li>
            <li><a href="#save-worker-logs">İşcilere Ait Log Kayıtlarını Tutmak</a></li>
        </ul>
    </li>

    <li>
        <a href="#displaying-logs">Logları Görüntülemek</a>
        <ul>
            <li><a href="#from-console">Konsoldan Görüntülemek</a></li>
            <li><a href="#from-debugger">Debugger Modülü İle Görüntülemek</a></li>
        </ul>
    </li>
</ul>

<a name="intro"></a>

## Önbilgi

Uygulama çalıştığına konteyner içerisinden logger nesnesi çağırıldığında logger bir servis olarak tanımlı olduğu için logger servisi yüklenir. Servis ilk çalıştığında LogManager sınıfı yardımı ile konfigüre edilip Logger sınıfı nesnesine geri döner. Logger sınıfı konteyner içinden artık tekrar tekrar çağırıldığında önceden bir kez çalıştırılmış olan logger sınıfına ulaşılır.

<a name="features"></a>

### Özellikler

O2 Logger; 

* Log Filtreleri, ( Log mesajlarını isteklerinize göre filtreleme )
* Log Sürücüleri ( File, Email, Mongo, Syslog, Raw ),
* Log Biçimleyicileri
* Psr/Log standartı
* Kuyruğa Atma
* Konsoldan ve Web Konsolundan (Debugger) Log Görüntüleme
* Log Verilerini Tek Bir Yerden İşleme ( app/classes/Workers/Logger.php )

gibi özellikleri barındırır.

<a name="flow-chart"></a>

### Akış Şeması

Aşağıdaki akış şeması uygulamada bir log mesajının kaydedilirken hangi aşamalardan geçtiği ve loglamanın genel prensipleri hakkında size bir ön bilgi verecektir:

![Akış Şeması](images/log-flowchart.png?raw=true)

Uygulamada loglanmaya başlanan veriler önce bir dizi içerisinde toplanır ve php <a href="http://php.net/manual/tr/class.splpriorityqueue.php" target="_blank">SplPriorityQueue</a> sınıfı yardımı ile toplanan veriler önemlilik derecesine göre dizi içeriside sıralanırlar. Sıralanan log verileri log yazıcılarına gönderilmeden önce aşağıdaki iki olasılık sözkonusu olur.

* Kuyruk Servisinin Kapalı Olduğu Durum ( Varsayılan )

Eğer kuyruğa atma opsiyonu log servisinden kapalı ise mevcut sayfada bir dizi içerisine sıralanmış tüm log verileri uygulamanın kapatılmasından sonra kuyruğa önemlilik sırasına göre <kbd>app/classes/Workers/Logger</kbd> sınıfına gönderilirler.

Şemaya göre <kbd>app/classes/Workers/Logger</kbd> sınıfının çalışmasından sonra elde edilen veri çözümlenerek filtrelerden geçirilir ve log servisinden belirlenmiş yazma önceliklerine göre önce birincil log yazıcısı ve sonra varsa ikincil olan log yazıcıları gönderilen veri içerisindeki log kayıtlarını alarak yazma işlemlerini gerçekleştirirler.

* Kuyruk Servisinin Açık Olduğu Durum

Eğer kuyruğa atma opsiyonu log servisinden açık ise mevcut sayfada bir dizi içerisine sıralanmış tüm log verileri uygulamanın kapatılmasından sonra kuyruğa atılırlar. Kuyruğa gönderilme işlemi her sayfa için bir kere yapılır. Kuyruğa atılan log verilerini <kbd>app/classes/workers/Logger</kbd> sınıfı konsoldan çalıştırılarak <kbd>php task queue listen</kbd> komutu yardımı ile dinlenerek tüketilir. Konsoldan <kbd>php task queue listen</kbd> komutunun işlemci sayısına göre birden fazla çalıştırılması çoklu iş parçacıkları (multi threading) oluşturarak kuyruğun daha hızlı tüketilmesini sağlar. 

Şemaya göre <kbd>app/classes/Workers/Logger</kbd> sınıfının çalışmasından sonra elde edilen veri çözümlenerek RaidManager sınıfı ile log sürücülerinin yazma önceliklerini belirler. Belirlenen yazma önceliklerine göre önce birincil log yazıcısı ve sonra varsa ikincil olan log yazıcıları gönderilen veri içerisindeki log kayıtlarını alarak yazma işlemlerini gerçekleştirirler.

<a name="configuration"></a>

## Konfigürasyon

Uygulama loglarının aktif olması için <kbd>app/config/$env/config.php</kbd> dosyasından enabled anahtarının true olması gerekir.

```php
/**
 * Log
 *
 * Enabled: On / off logging
 */
'log' => [
    'enabled' => true,
],
```

Logger sınıfına ait detaylı konfigürasyon dosyası ise <kbd>app/config/$env/logger.php</kbd> dosyasında tutulur.


<a name="running"></a>

## Çalıştırma

Çalıştırma aşamasına geçmeden önce servis konfigürasyonunun çalıştığınız ortam değişkenine göre aşağıdaki gibi konfigüre edilmesi gerekir.

<a name="service"></a>

### Servis Konfigürasyonu

Örnekte gösterilen servis konfigürasyonu <kbd>app/classes/Service/Logger/Local.php</kbd> dosyasıdır.

```php
namespace Service\Logger;

use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Local implements ServiceInterface
{
    public function register(ContainerInterface $c)
    {
        $c['logger'] = function () use ($c) {
            
            $parameters = [
                'queue' => [
                    'enabled' => false,
                    'route' => 'logger.1',
                    'delay' => 0,
                ]
            ];
            $manager = new LogManager($c);
            $manager->setParameters($parameters);
            $logger = $manager->getLogger();
            /*
            |--------------------------------------------------------------------------
            | Register Filters
            |--------------------------------------------------------------------------
            */
            $logger->registerFilter('priority', 'Obullo\Log\Filters\PriorityFilter');
            /*
            |--------------------------------------------------------------------------
            | Register Handlers
            |--------------------------------------------------------------------------
            */
            $logger->registerHandler(5, 'file');
            $logger->registerHandler(4, 'mongo')->filter('priority@notIn', array(LOG_DEBUG));
            $logger->registerHandler(3, 'email')->filter('priority@notIn', array(LOG_DEBUG));
            /*
            |--------------------------------------------------------------------------
            | Set Primary Writer
            |--------------------------------------------------------------------------
            */
            $logger->setWriter('file')->filter('priority@notIn', array());
            return $logger;
        };
    }
}
```

Logger servisi her çalışma ortamı için ( local, test, production ) Env klasörü altında farklı konfigüre edilebilir. Örneğin local ortamda <kbd>file</kbd> yazıcısı servise konfigure edilmişken test ortamındaki yazıcınız <kbd>mongo</kbd> olabilir.

Böyle bir durum sözkonusu ise örneğin test ortamı için bir logger servisi kurmak istiyorsak bunun için bir <kbd>Service\Logger\Env\Test.php</kbd> dosyası yaratarak içerisine yukarıdaki içeriği kopyalamamız gerekir. Ardından bu sınıf içerisinde gereken değişiklikleri yapabilirsiniz. Eğer gerekli ise diğer çalışma ortamları içinde aynı işlemin tekrarlanması gerekir.

<a name="loading-service"></a>

### Servisi Yüklemek

```php
$this->c['logger']->method();
```
<a name="writers"></a>

## Yazıcılar

Yazıcılar log verilerini işleme yada gönderme işlemlerini gerçekleştiriler. Servis içerisinde birden fazla tanımlanabilirler.

<a name="add-handlers"></a>

#### Sürücü Eklemek

Sürücüler log verilerini kaydetmek yada transfer etmek gibi işlemleri yürütürler. Önceden aşağıdaki gibi servis dosyası içerisinde tanımlı olması gereken sürücüler genel log yazıcısı olarak kullanılabilecekleri gibi belirli bir sayfaya yada gerçekleşmesi çok sık olmayan olaylar için gönderim işleyicisi (push handler) olarak da kullanılabilirler.

```php
$logger->registerHandler(5, 'file');
$logger->registerHandler(4, 'mongo');
$logger->registerHandler(3, 'email');
```

Bir sürücünün yazma yada gönderim önceliği ilk parametreden belirlernir. Değeri yüksek olan sürücünün önemliliği de yüksek olur ve önemlilik değeri yüksek olan sürücülere ait veriler diğer sürücülerden önce yazma işlemlerini gerçekleştirirler. İkinci parametreye ise sürücü adı girilir.

<a name="set-writer"></a>

#### Ana Yazıcıyı Tanımlamak

Log ana yazıcısı <kbd>setWriter()</kbd> metodu ile eklenir. Yazıcı her sayfa görüntülenmesinden sonra toplanan log verilerinin yazma işlemlerini gerçekleştirir. Bir sürücünün bir yazıcı olarak eklenebilmesi için sürücünün yazma işlemine uygun olması gerekir örneğin email sürücüsü genel bir yazıcı olarak eklenmemelidir.

```php
$logger->setWriter('file');
```

Eğer birden fazla yazıcıya eş zamanlı yazmak isteniyorsa <kbd>Workers/Logger</kbd> sınıfı içerisinde ana yazıcıya ait gelen log kayıtlarının klonlanarak ikinci yazıcıya kaydedilmesi gerekir.

<a name="filters"></a>

## Filtreler

Log filtreleri array türünde gelen log verisini süzmek için tasarlanmış sınıflardır.

<a name="define-filter"></a>

### Filtre Tanımlamak

Bir log filtresi tanımlamak için ilk önce <kbd>app/classes/Log/Filters/</kbd> klasörü altında aşağıdaki prototipe benzer bir sınıf oluşturulmalıdır.

```php
namespace Log\Filters;

use Obullo\Container\ContainerInterface;

class HelloFilter
{
    public function __construct($params = null)
    {
        $this->params = $params;
    }

    public function filter(array $record)
    {
        /* Record Content
        Array ( 
            [channel] => system 
            [level] => debug 
            [message] => Uri Class Initialized 
            [context] => Array ( [uri] => /welcome/index ) ) 
        */
       return $record;  // Change record and return to new array.
    }
}
```

Oluşturduğunuz filtreyi registerFilter metodu birinci parametresinden filtre ismi ve metod ismi girerek, ikinci parametreyede aşağıdaki gibi sınıf yolu girerek tanımlamanız gerekir.

```php
$logger->registerFilter('filtername@method', 'Log\Filters\Class');
```

Eğer method ismi aşağıdaki gibi belirtilmez ise varsayılan olarak <b>filter</b> isimli method çağrılır.

```php
$logger->registerFilter('filtername', 'Log\Filters\Class');
```

Eğer örnekteki Hello filtresini çalıştırmak istiyorsak aşağıdaki kodu log servisi içerisine tanımlamamız gerekir.

```php
$logger->registerFilter('hello', 'Log\Filters\HelloFilter');
```

<a name="global-filters"></a>

### Evrensel Filtreler

Eğer bir filtre servis içerisinde tanımlandı ise bu türden filtreler uygulamanın her yerinde çalışacağından evrensel filtreler olarak adlandırılırlar. Evrensel filtreler Filtre Tanımlama başlığı altında anlatıldığı gibi tanımlanırlar ve tanımlı olan filtreler addWriter() metodundan sonra çalıştırılırlar.


```php
$logger->addWriter('email')
    ->filter('priority@in', array(LOG_NOTICE, LOG_ALERT))
    ->filter('anotherFilter@method')
    ->filter('anotherFilter@method');
```

"@" işareti ile method ismi tanımlar eğer "@" işareti girilmezse varsayılan olarak filter() metodu çalıştırılır.

```php
$logger->addWriter('email')->filter('priority@notIn', array(LOG_DEBUG));
```

> **Not:** Prodüksiyon ortamında olan bir uygulama için LOG_DEBUG seviyesi kapalı diğer log seviyelerinin (LOG_EMERG,LOG_ALERT,LOG_CRIT,LOG_ERR,LOG_WARNING,LOG_NOTICE) açık olması tavsiye edilir. Aksi durumda log veritabanları çok hızlı dolacaktır.

<a name="page-filters"></a>

### Sayfa Filtreleri

Belirli bir adres yada kontrolör içerisinden de geçerli sayfa için filtreleme yapılabilir. Filter metodu load metodundan sonra çalıştırılır.

```php
$this->logger->load('mongo')->filter('priority@notIn', array(LOG_DEBUG));

$this->logger->info('Hello World !');
$this->logger->notice('Hello Notice !');
$this->logger->alert('Hello alert !');

$this->logger->push();
```

Yukarıdaki örneği <kbd>/welcome/index</kbd> gibi herhangi bir sayfada kullanabilirsiniz. Örnekte mongo yazıcısı için bir filtre yaratılıyor ve yaratılan filtre geçerli sayfada <kbd>debug</kbd> sevisyesindeki log verilerini mongo yazıcısı log kayıtlarından çıkarıyor ve geriye kalan veriyi mongo yazıcısına gönderiyor.

<a name="messages"></a>

## Mesajlar

Bir log mesajı aşağıdaki prototipte tanımlandığı gibi oluşturulur.

```php
$this->logger->{seviye}(string $message, $context = array(), $priority = 0);
```

<a name="severities"></a>

### Log Seviyeleri

<table class="span9">
<thead>
<tr>
<th>Seviye</th>
<th>Değer</th>
<th>Sabit</th>
<th>Açıklama</th>
</tr>
</thead>
<tbody>
<tr>
<td>emergency</td>
<td>0</td>
<td>LOG_EMERG</td>
<td>Emergency: Sistem kullanılamaz.</td>
</tr>

<tr>
<td>alert</td>
<td>1</td>
<td>LOG_ALERT</td>
<td>Derhal müdahale edilmesi gereken eylemler. Örnek: Tüm web sitesinin düştüğü, veritabanına erişilemediği vb. durumlar. Bu log seviyesinde karşı tarafın SMS ile uyarılması tavsiye edilir.</td>
</tr>

<tr>
<td>critical</td>
<td>2</td>
<td>LOG_CRIT</td>
<td>Kritik durumlar. Örnek: Uygulama bileşeni ulaşılamaz durumda yada beklenmedik bir istisnai hata.</td>
</tr>

<tr>
<td>error</td>
<td>3</td>
<td>LOG_ERR</td>
<td>Çalıştırma hataları ani müdahaleler gerektirmez fakat genel olarak loglanıp monitörlenmelidir.</td>
</tr>

<tr>
<td>warning</td>
<td>4</td>
<td>LOG_WARNING</td>
<td>Hata olmayan istisnai olaylar. Örnek: Modası geçmiş bir web servisi ( API ),  kötü web servisi kullanımı, yanlış olmayan fakat istenmeyen durumlar.</td>
</tr>

<tr>
<td>notice</td>
<td>4</td>
<td>LOG_NOTICE</td>
<td>Normal fakat önemli olaylar.</td>
</tr>

<tr>
<td>info</td>
<td>6</td>
<td>LOG_INFO</td>
<td>Bilgi amaçlı istenen yada ilgi çekici olaylar. Örnek: Kullanıcı logları, SQL logları, Uygulama performans/durum bilgileri.</td>
</tr>

<tr>
<td>debug</td>
<td>7</td>
<td>LOG_DEBUG</td>
<td>Detaylı hata ayıklama bilgileri.</td>
</tr>
</tbody>
</table>

<a name="log-messages"></a>

### Log Mesajları

Aşağıdaki gibi oluşturulan bir log mesajı tanımlı log yazıcılarına gönderilir. Log mesajından önce bir kanal açmanız tavsiye edilir sonra bir log seviyesi seçip birinci parametreden log mesajını, opsiyonel olarak ikinci parametreden ise mesaja bağlı özel verilerinizi gönderebilirsiniz.

```php
$this->logger->channel('security');
$this->logger->alert('Possible hacking attempt !', array('username' => $username));
```

Diğer bir opsiyonel parametre olan üçüncü parametreden ise log mesajının önem seviyesi ( kaydedilme önceliği ) belirlenebilir. Önem seviyesi büyük olan log mesajı önce kaydedilecektir.

```php
$this->logger->alert('Alert', array('username' => $username), 3);
$this->logger->notice('Notice', array('username' => $username), 2);
$this->logger->notice('Another Notice', array('username' => $username), 1);
```

<a name="loading-handlers"></a>

### Farklı Log Yazıcılarına Loglama

Eğer sürekli olmayan bir log yazıcısına log verileri gönderilmek yada işlenmek isteniyorsa load ve push metotları kullanılır.

```php
$this->logger->load('mongo');
$this->logger->channel('security');               
$this->logger->alert('Possible hacking attempt !', array('username' => $username));
$this->logger->push();
```

Birden fazla log yazıcısı da aynı anda yüklenebilir.

```php
$this->logger->load('email');
$this->logger->load('mongo');  

$this->logger->channel('security');
$this->logger->alert('Something went wrong !', array('username' => $username));

$this->logger->channel('test');
$this->logger->info('User login attempt', array('username' => $username));

$this->logger->push();
```

> **Not:** Log yazıcıları yüklendiğinde load ve push metotları arasında kullanılan log metotlarına ait tüm veriler push metodu aracılığı ile yüklenen yazıcılara gönderilir. Load metodundan önceki ve push metodundan sonraki loglamalar varsayılan log yazıcılarına gönderilir.

<a name="log-workers"></a>

### Log Mesajlarını İşlemek

Tüm log mesajları <kbd>app/classes/Workers/Logger</kbd> sınıfı aracılığı ile işlenir. Bu sınıfa gelen log verilerini <b>fire</b> metodu çözümler ve ilgili log yazıcılarını kullanarak yazma işlemlerinin gerçekleşmesini sağlar.

```php
public function fire($job, array $event)
{
    print_r($event);

    $this->job = $job;
    $this->writers = $event['writers'];
    $this->process();
}
```

Yukarıda görüldüğü gibi <kbd>$event</kbd> parametresi ile tüm log kayıtları ve log bilgilerine ait olay verisi <kbd>app/classes/Workers/Logger</kbd> sınıfı <b>fire</b> metodu içerisinden çözümlenir. Servis konfigürasyonunda kuyruğa atma seçeneğinin kapalı veya açık olması durumunda herhangi bir değişiklik yapmanıza gerek kalmaz.

Eğer servis konfigürasyonunda kuyruğa atma seçeneği açık ise <kbd>Workers/Logger</kbd> sınıfı konsoldan <kbd>php task queue listen</kbd> komutu aracılığı ile birden fazla çalıştırılarak çoklu iş parçacıkları ile log verileri kuyruktan tüketilir ( Multi Threading ). Eğer kuyruklama ile uygulamanızı genişletmek istiyorsanız daha fazla bilgi için kuyruklama bölümünü inceleyiniz.

<a name="queuing"></a>

## Kuyruklama

Kuyruklamanın doğru çalışabilmesi için queue servisinin doğru kurulduğundan ve çalışıyor olduğundan emin olun. Kurulum doğru ise log servisi konfigürasyonundaki <kbd>queue => enabled</kbd> anahtarına ait değeri <b>true</b> ile değiştirdiğinizde log verileri artık queue servisinizde tanımlı olan kuyruk sürücünüze gönderilir. Kuyruklama queue servisi üzerinden yürütülür. Queue servisi log servisi içerisinde aşağıdaki tanımlı parametreleri kullanarak <kbd>Workers@Logger</kbd> adlı iş sınıfı üzerinden bir kanal açar ve bu kanal üzerinde <b>route</b> anahtarı değerinde bir kuyruk yaratır.

```php
$parameters = [
    'queue' => [
        'enabled' => true,
        'route' => 'logger.1',
        'delay' => 0,
    ]
];
```

Log mesajları <kbd>Obullo/Log/Logger</kbd> sınıfı içerisindeki <b>close</b> metodunda yukarıdaki parametreler kullanılarak queue servisi üzerinden aşağıdaki gibi kuyruğa gönderilirler.

```php
$this->c->get('queue')
    ->push(
        'Workers@Logger',
        $this->params['queue']['job'],
        $payload,
        $this->params['queue']['delay']
    );
```

<a name="displaying-queue"></a>

### Kuyruktaki İşleri Görüntülemek

Konsoldan php task show komutunu yazarak kuyruktaki işleri görüntüleyebilirsiniz.

```php
php task queue show --w=Workers@Logger --j=logger.1
```

```php
Worker : Workers@Logger
Job    : logger.1

------------------------------------------------------------------------------------------
Job ID  | Job Name            | Data 
------------------------------------------------------------------------------------------
1       | Workers@Logger      | {"time":1436249455,"record":[{"channel": .. }
```

<a name="workers"></a>

### İşçiler İle Kuyruğu Tüketmek

Kuyruğu tüketmek için konsoldan aşağıdaki komut ile bir php işçisi çalıştırmak gerekir.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --output=0
```

Yukarıdaki komut aynı anda birden fazla konsolda çalıştırıldığında <kbd>Obullo/Task/QueueController</kbd> sınıfı üzerinden her seferinde  <kbd>Obullo/Task/WorkerController.php</kbd> dosyasını çalıştırarak yeni bir iş parçaçığı oluşturur. Yerel ortamda birden fazla komut penceresi açarak kuyruğun eş zamanlı nasıl tüketildiğini test edebilirsiniz.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --delay=0 --memory=128 --timeout=0 --output=1
```
Yerel ortamda yada test işlemleri için output parametresini 1 olarak gönderdiğinizde yapılan işlere ait hata çıktılarını konsoldan görebilirsiniz.

Ayrıca UNIX benzeri işletim sistemlerinde prodüksiyon ortamında kuyruk tüketimini otomasyona geçirmek ve çoklu iş parçaları (multithreading) ile çalışmak için Supervisor adlı programdan yararlanabilirsiniz. <a href="http://supervisord.org/" target="_blank">http://supervisord.org/</a>.

> **Not:** Bir işlemci için açılması gereken optimum işçi sayısı 1 olmalıdır. Örneğin 16 çekirdekli bir sunucuya sahipseniz işçi sayısı 16 olmalıdır. İlgili makaleye bu bağlantıdan gözatabilirsiniz. <a href="http://stackoverflow.com/questions/1718465/optimal-number-of-threads-per-core">Optimal Number of Threads Per Core</a>.

<a name="worker-parameters"></a>

##### İşçi Parametreleri

<table>
<thead>
<tr>
<th>Parametre</th>
<th>Kısayol</th>
<th>Açıklama</th>
<th>Varsayılan</th>
</thead>
<tbody>
<tr>
<td>--worker</td>
<td>--w</td>
<td>Kuyruğun açılacağı kanalı (exchange) ve işe ait sınıf ismini belirler .</td>
<td>null</td>
</tr>
<tr>
<td>--job</td>
<td>--j</td>
<td>Kuyruğa ait iş ismini (route) belirler.</td>
<td>null</td>
</tr>
<tr>
<td>--delay</td>
<td>--d</td>
<td>Tamamlanmamış işler için gecikme zamanını belirler.</td>
<td>0</td>
</tr>
<tr>
<td>--memory</td>
<td>--m</td>
<td>Geçerli iş için kullanılabilecek maksimum belleği belirler. Değer MB cinsinden sayı olarak girilir.</td>
<td>128</td>
</tr>
<tr>
<td>--timeout</td>
<td>--t</td>
<td>Geçerli iş için maksimum çalışma süresini belirler.</td>
<td>0</td>
</tr>
<tr>
<td>--sleep</td>
<td>--s</td>
<td>Eğer kuyrukta iş yoksa girilen saniye kadar çalışma duraklatılır. En az 3 olarak girilmesi önerilir aksi durumda işlemci tüketimi artabilir.</td>
<td>3</td>
</tr>
<tr>
<td>--attempt</td>
<td>--a</td>
<td>Kuyruktaki işin en fazla kaç kere yapılma denemesine ait sayıyı belirler.</td>
<td>0</td>
</tr>
<tr>
<td>--output</td>
<td>--o</td>
<td>Output değeri 1 olması durumunda bulunan hatalar ekrana dökülür.</td>
<td>0</td>
</tr>
<tr>
<td>--env</td>
<td>--e</td>
<td>Ortam değişkenini worker uygulamasına gönderir.</td>
<td>null</td>
</tr>
<tr>
<td>--var</td>
<td>--v</td>
<td>Bu parametre göndermek istediğiniz özel parametreler için ayrılmıştır.</td>
<td>null</td>
</tr>
</tbody>
</table>

<a name="debug-mode"></a>

### Hata Ayıklama Modu

Output değeri 1 olması durumunda bulunan hatalar ekrana dökülür.

```php
php task queue listen --w=Workers@Logger --j=logger.1 --o=1
```

<a name="processing-jobs"></a>

### Kuyruk Verilerini İşlemek

<kbd>app/classes/Workers/Logger</kbd> sınfı fire metoduna gönderilen log verileri, istek tipi, yazıcı tipi, gönderilme süresi, log kayıtları gibi verileri aşağıdaki gibi bir array içerisinde gruplar. Php task listen komutu çalıştığında işçi veya işciler bu gruplanmış veriyi Workers/Logger sınıfı içerisinde arka planda çözümleyerek log yazıcılarına gönderirler.

```php
public function fire($job, array $event)
{
    print_r($event);

    $this->job = $job;
    $this->writers = $event['writers'];
    $this->process();
}
```

Eğer yukarıda görülen <kbd>app/classes/Workers/Logger</kbd> sınıfı fire metodu ikinci parametresi olan $data verisini ekrana dökmeniz halinde aşağıdaki gibi bir çıktı alırsınız.


```php
/*
Array
(
    [writers] => Array
        (
            [10] => Array
                (
                    [handler] => file
                    [request] => http
                    [type] => writer
                    [time] => 1436797060
                    [filters] => Array
                        (
                            [0] => Array
                                (
                                    [class] => Obullo\Log\Filter\PriorityFilter
                                    [method] => notIn
                                    [params] => Array
                                        (
                                        )

                                )

                        )

                    [record] => Array
                        (
                            [0] => Array
                                (
                                    [channel] => system
                                    [level] => debug
                                    [message] => Uri Class Initialized
                                    [context] => Array
                                        (
                                            [uri] => /welcome/index
                                        )

                                )
*/
```

Aşağıda log kuyruğunu çözümlemek için sadeleştirilmiş bir worker örnegi görülüyor.


```php
namespace Workers;

use Obullo\Queue\Job;
use Obullo\Queue\JobInterface;
use Obullo\Log\Filter\LogFilters;
use Obullo\Container\ContainerInterface;

Class Logger implements JobInterface
{
    public $c;
    public $job;
    public $writers;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function fire($job, array $event)
    {
        $this->job = $job;
        $this->writers = $event['writers'];
        $this->process();
    }

    public function process()
    {
        $handler = null;
        foreach ($this->writers as $event) {
            $handler = $event['handler'];
            if ($handler == 'file') {
                $handler = new FileHandler;
            }
            if (is_object($handler) && $handler->isAllowed($event)) { // Check write permissions

                $event = LogFilters::handle($event);

                $handler->write($event);  // Do job
                $handler->close();
                
                if ($this->job instanceof Job) {
                    $this->job->delete();  // Delete job from queue
                }
            }
        }
    }

}

/* Location: .app/classes/Workers/Logger.php */
```

Çözümlenenen log verileri process metodu içerisinde log yazıcılarına gönderilir ve yazma işlemleri tamamlanır.

<a name="removing-completed-jobs"></a>

### Tamamlanan İşleri Kuyruktan Silmek

Log verileri kuyruğa gönderilirken ilk parametre <b>iş</b> sınıfının yolu yani <kbd>Workers@Logger</kbd> girildiğinden kuyruk tüketilmeye başlandığında <kbd>Obullo\Queue\Job</kbd> sınıfına genişleyen <kbd>Obullo\Queue\Job\JobHandler\AMQPJob</kbd> sınıfı <kbd>app/classes/Workers/Logger</kbd> sınıfı fire metodu ilk parametresine gönderilir.

<kbd>app/classes/Workers/Logger</kbd> sınıfı fire metodu ilk parametresine gönderilen iş sınıfı ile kuyruktan alınan işler tamamlandığında delete metodu ile kuyruktan silinirler.

```php
if (is_object($handler) && $handler->isAllowed($data)) {

    $handler->write($data);  // Do job
    $handler->close();
    
    if ($this->job instanceof Job) {
        $this->job->delete();  // Delete job from queue
    }
}
```

<a name="save-worker-logs"></a>

### İşçilere Ait Log Kayıtlarını Tutmak

Varsayılan olarak konsoldan uygulamaya gelen işçi isteklerine gelen log kayıtları tutulmaz. İşçilere ait log verilerini kaydetmek için aşağıdaki gibi <kbd>app/config/$env/logger.php</kbd> dosyası içerisindeki yapılandırmalardan <kbd>app > worker > log</kbd> değerini açık hale getirmeniz gerekir.

```php
'app' => [
    'worker' => [
        'log' => true,
    ]
],
```

Bu işlemden sonra <kbd>php task listen</kbd> komutu ile işçiler konsoldan çalıştırıldığında işçilere ait log kayıtlarını elde etmiş olacaksınız. Kayıt edilen log verilerinden işçilere ait olanları bulabilmek için <kbd>request = "worker"</kbd> değeri ile log verilerinizi filtereleyebilirsiniz.

> **Not:** Bir dağıtık yapı yani log işleme ve diğer işleri http sunucusu yormamak için başka bir sunucuda Obullo çatısı ile kurmak mümkündür. Bunun için worker sunucunuza sadece bir Obullo sürümü indirip servisleri konfigüre etmeniz yeterli olur.

Kuyruklama hakkında detaylı bilgi için [Queue.md](Queue.md) dosyasına gözatabilirsiniz.


<a name="displaying-logs"></a>

### Logları Görüntülemek

Uygulama logları konsoldan ve web arayüzünden görütülenebilir. Web arayüzünden görüntüleme daha detaylı log görüntüleme ve hata ayıklamalar için önerilir. Web arayüzü (debugger modülü) web socket teknolojisi kullanır.

<a name="from-console"></a>

### Logları Konsoldan Takip Etmek

Loglamaya ait verileri konsolonuzdan takip edebilirsiniz. Bunu için proje ana dizinine girin.

```php
cd /var/www/myproject
```

Ardından <kbd>log</kbd> komutun çalıştırın.

```php
php task log
```

Log komutu varsayılan olarak file yazıcısına tanımlıdır. Eğer yerel veya test ortamında loglama için file yazıcısı kullanıyorsanız log verilerini aşağıdaki gibi konsolunuzdan takip edebilirsiniz.

![Logları Konsoldan Takip Etmek](images/log-console.jpg?raw=true "Logları Konsoldan Görüntüleme")

Log dosyasını temizlemek için <kbd>clear</kbd> komutunu kullanın.

```php
php task log clear
```

<a name="from-debugger"></a>

### Logları Debugger Modülü İle Takip Etmek

Debugger paketi [Debugger.md](Debugger.md) dökümentasyonunu inceleyiniz.

<a name="method-reference"></a>

#### Fonksiyon Referansı

------

##### $this->logger->enable();

Loglamayı açık hale getirir.

##### $this->logger->disable();

Loglamayı kapalı hale getirir.

##### $this->logger->isEnabled();

Loglamayı açık ise true değerine aksi durumda false değerine geri döner.

##### $this->logger->load(string $handler = 'email');

Farklı bir log yazıcısına log göndermek için bir log yazıcısını yükler. Push metodu ile birlikte kullanılır.

##### $this->logger->setWriter(string $name);

Ana log yazıcısını tanımlar.

##### $this->logger->getWriter();

Ana log yazıcısı ismine geri döner.

##### $this->logger->registerFilter(string $name, string $namespace);

Bir log filtresi tanımlar. Birinci parametreye filtre adı ikinci parametreye filtre sınıfına ait yol girilir.

##### $this->logger->registerHandler(integer $priority, string $name);

Bir log sürücüsü tanımlar. Birinci parametreye sürücü önem derecesi girilir, ikinci parametreye ise sürücü adı girilir. Eğer özel bir sürücü tanımlanmak isteniyorsa herhangi bir isim girilir ve bu isim <kbd>Workers@Logger</kbd> sınıfına geldiğinde ilgili kullanmak istediğiniz özel sürücü sınıfı ilan edilir.

##### $this->logger->filter(string $name, $params = array());

RegisterFilter metodu ile tanımlı olan bir log filtresinin çalıştırılmasına ait isteği log olayına işler.

##### $this->logger->push();

Load metodu ile yüklenen bir log yazıcısına ait log verilerini yazılması için log olayına işler.


#### Mesaj Referansı

------

##### $this->logger->channel(string $channel);

Bir log kanalı belirler.

##### $this->logger->emergency(string $message = '', $context = array(), integer $priority = 0);

Sistem kullanılamaz durumda ise bu log metodu kullanılmalıdır.

##### $this->logger->alert(string $message = '', $context = array(), integer $priority = 0);

Derhal müdahale edilmesi gereken eylemler. Örnek: Tüm web sitesinin düştüğü, veritabanına erişilemediği vb. durumlar. Bu log seviyesinde karşı tarafın SMS ile uyarılması tavsiye edilir.

##### $this->logger->critical(string $message = '', $context = array(), integer $priority = 0);

Kritik durumlar. Örnek: Uygulama bileşeni ulaşılamaz durumda yada beklenmedik bir istisnai hata oluştu ise bu log metodu kullanılmalıdır

##### $this->logger->error(string $message = '', $context = array(), integer $priority = 0);

Çalıştırma hataları ani müdahaleler gerektirmez fakat genel olarak loglanıp monitörlenmelidir.

##### $this->logger->warning(string $message = '', $context = array(), integer $priority = 0);

Hata olmayan istisnai olaylar. Örnek: Modası geçmiş bir web servisi ( API ),  kötü web servisi kullanımı, yanlış olmayan fakat istenmeyen durumlar.

##### $this->logger->notice(string $message = '', $context = array(), integer $priority = 0);

Normal fakat önemli olaylar.

##### $this->logger->info(string $message = '', $context = array(), integer $priority = 0);

Bilgi amaçlı istenen yada ilgi çekici olaylar. Örnek: Kullanıcı logları, SQL logları, Uygulama performans/durum bilgileri.

##### $this->logger->debug(string $message = '', $context = array(), integer $priority = 0);

Detaylı hata ayıklama bilgileri.