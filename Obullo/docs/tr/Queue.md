
## Kuyruklama

Kuyruklama paketi uzun sürmesi beklenen işlemlere ( loglama, email gönderme, sipariş alma gibi. ) ait verileri mesaj gönderim protokolü  ( AMQP ) üzerinden arkaplanda işlem sırasına sokar. Kuyruğa atılan veriler eş zamanlı işlemler (multi threading) ile tüketilerek işler arkaplanda tamamlanır ve kuyruktan silinir, böylece uzun süren işlemler ön yüzde sadece işlem sırasına atıldığından uygulamanıza gelen http istekleri yorulmamış olur.

<ul>

<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#service-configuration">Servis Konfigürasyonu</a></li>
        <li><a href="#service-provider-configuration">Servis Sağlayıcı Konfigürasyonu</a></li>
        <li><a href="#server-requirements">Sunucu Gereksinimleri</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#loading-service">Servisi Yüklemek</a></li>
        <li><a href="#queuing-a-job">Bir İşi Kuyruğa Atmak</a></li>
        <li><a href="#delaying-a-job">Bir İşin Çalışmasını Geciktirmek</a></li>
    </ul>
</li>

<li>
    <a href="#workers">İşçiler</a>
    <ul>
        <li><a href="#define-worker">İşçi Tanımlamak</a></li>
        <li><a href="#delete-job">İşi Kuyruktan Silmek</a></li>
        <li><a href="#release-job">İşi Kuyruğa Tekrar Atmak</a></li>
        <li><a href="#job-attempt">İşin Denenme Sayısını Almak</a></li>
        <li><a href="#job-id">İşin ID Değerini Almak</a></li>
        <li><a href="#job-name">İşin Adını Almak (Kuyruk Adı)</a></li>
        <li><a href="#job-body">İşe Ait Veriyi Almak</a></li>
    </ul>
</li>

<li>
    <a href="#running-workers">Konsoldan İşçileri Çalıştırmak</a>
    <ul>
        <li><a href="#show">Kuyruğu Listelemek</a></li>
        <li><a href="#listen">Kuyruğu Dinlemek</a></li>
        <li>
            <a href="#worker-parameters">İşçi Parametreleri</a>
        </li>
        <li><a href="#save-worker-logs">İşçilere Ait Log Kayıtlarını Tutmak</a></li>
    </ul>
</li>

<li>
    <a href="#threading">Çoklu İş Parçalarını Kontrol Etmek</a> (Multi Threading)</a>
    <ul>
        <li><a href="#supervisor">Ubuntu Altında Supervisor Kurulumu</a></li>
        <li><a href="#creating-first-worker">İlk İşçimizi Yaratalım</a></li>
        <li><a href="#multiple-workers">Birden Fazla İşçiyi Çalıştırmak</a></li>
        <li><a href="#starting-all-workers">Bütün İşçileri Başlatmak</a></li>
        <li><a href="#displaying-all-workers">Bütün İşçileri Görüntülemek</a></li>
        <li><a href="#stopping-all-workers">Bütün İşçileri Durdurmak</a></li>
        <li><a href="#stopping-all-workers">Bütün İşçileri Durdurmak</a></li>
        <li><a href="#worker-logs">İşci Loglarını Görüntülemek</a></li>
        <li><a href="#additional-info">Ek Bilgiler</a></li>
        <ul>
          <li><a href="#startup-config">Sistem Açılışında Otomatik Başlatma</a></li>
          <li><a href="#web-interface">Supervisord İçin Web Arayüzü</a></li>
        </ul>
    </ul>
</li>

<li>
    <a href="#saving-failed-jobs">Başarısız İşleri Kaydetmek</a>
    <ul>
        <li><a href="#failure-config">Konfigürasyon</a></li>
        <li><a href="#failure-sql-file">SQL Dosyası</a></li>
    </ul>
</li>

<li>
    <a href="#cloud-solutions">Bulut Çözümler</a>
    <ul>
        <li><a href="#cloud-amqp">CloudAMQP Servisi</a></li>
    </ul>
</li>


<li>
    <a href="#method-reference">Fonksiyon Referansı</a>
    <ul>
        <li><a href="#queue-reference">Queue Sınıfı Referansı</a></li>
        <li><a href="#job-reference">Job Sınıfı Referansı</a></li>
    </ul>
</li>

</ul>

<a name="configuration"></a>

### Konfigürasyon

Queue servisi ana konfigürasyonu <kbd>config/$env/queue/amqp.php</kbd> dosyasından konfigüre edilir. Dosya içerisindeki <kbd>exchange</kbd> anahtarına AMQP sürücüsüne ait ayarlar konfigüre edilirken <kbd>connections</kbd> anahtarına ise AMQP servis sağlayıcısı için gereken bağlantı bilgileri girilir.

```php
return array(

    'amqp' => [

        'exchange' => [
            'type' => 'direct',
            'flag' => 'durable',
        ],
        'connections' => 
        [
            'default' => [
                'host'  => '127.0.0.1',
                'port'  => 5672,
                'username' => 'root',
                'password' => $c['var']['AMQP_PASSWORD'],
                'vhost' => '/',
            ]
        ],
    ],
);
```
<a name="server-requirements"></a>

#### Sunucu Gereksinimleri

Kuyruklama servisinin çalışabilmesi için php AMQP extension kurulu olması gerekir. Php AMQP arayüzü ile çalışan birçok kuyruklama yazılımı mevcuttur. Bunlardan bir tanesi de <a href="https://www.rabbitmq.com/" target="_blank">RabbitMQ</a> yazılımıdır. Aşağıdaki linkten RabbitMQ yazılımı için Ubuntu işletim sistemi altında gerçekleştilen örnek bir kurulum bulabilirsiniz.

<a href="https://github.com/obullo/warmup/tree/master/AMQP/RabbitMQ">RabbitMQ ve Php AMQP Extension Kurulumu </a>

##### Diğer AMQP Yazılımları ve Servisler

* <a href="http://zeromq.org/bindings:php/" target="_blank">ZeroMQ</a>
* <a href="https://qpid.apache.org/" target="_blank">Apache Qpid</a>
* <a href="https://www.cloudamqp.com/" target="_blank">Cloud AMQP</a>


<a name="service-configuration"></a>

#### Servis Konfigürasyonu

Queue paketini kullanabilmeniz için aşağıdaki gibi servis ayarlarınının yapılandırılmış olması gerekir.

```php
namespace Service;

use Obullo\Queue\QueueManager;
use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Queue implements ServiceInterface
{
    public function register(ContainerInterface $c)
    {
        $c['queue'] = function () use ($c) {

            $parameters = [
                'class' => '\Obullo\Queue\Handler\Amqp',
                'provider' => [
                    'name' => 'amqp',
                    'params' => [
                        'connection' => 'default'
                    ]
                ]
            ];
            $manager = new QueueManager($c);
            $manager->setParameters($parameters);
            $handler = $manager->getHandler();
            return $handler;
        };
    }
}
```

Mevcut Kuyruk Sınıfları

* \Obullo\Queue\Handler\Amqp
* \Obullo\Queue\Handler\AmqpLib


<a name="service-provider-configuration"></a>

#### Servis Sağlayıcı Konfigürasyonu

Servis ayarlarında tanımladığınız servis sağlayıcısının <kbd>app/components.php</kbd> içerisinden tanımlı olması gerekir.

```php
$c['app']->provider(
    [
        'database' => 'Obullo\Service\Provider\Database',
        // 'database' => 'Obullo\Service\Provider\DoctrineDBAL',
        // 'qb' => 'Obullo\Service\Provider\DoctrineQueryBuilder',
        'cache' => 'Obullo\Service\Provider\Cache',
        'redis' => 'Obullo\Service\Provider\Redis',
        'memcached' => 'Obullo\Service\Provider\Memcached',
        'amqp' => 'Obullo\Service\Provider\Amqp',
        // 'amqp' => 'Obullo\Service\Provider\AmqpLib',
        'mongo' => 'Obullo\Service\Provider\Mongo',
    ]
);
```

Mevcut Servis Sağlayıcıları 

* amqp ( PECL )
* amqpLib ( Composer / php-amqplib )

Varsayılan servis sağlayıcısı pecl <b>amqp</b> sınıfıdır. Eğer servis sağlayıcı sınıfını <kbd>AmqpLib</kbd> olarak değiştirirseniz queue servisi içerisindeki class parametresini <kbd>\Obullo\Queue\Handler\AmqpLib</kbd> olarak değiştirmeniz gerekir.


<a name="running"></a>

### Çalıştırma

Servis konteyner içerisinden çağırıldığında tanımlı olan queue arayüzü üzerinden ( AMQP ) kuyruklama metotlarına ulaşılmış olur. 

<a name="loading-service"></a>

#### Servisi Yüklemek

Queue servisi aracılığı ile queue metotlarına aşağıdaki gibi erişilebilir.

```php
$this->c['queue']->metod();
```

<a name="queuing-a-job"></a>

#### İşi Kuyruğa Atmak

Bir işi kuyruğa atmak için <kbd>$this->queue->push()</kbd> metodu kullanılır.

```php
$this->queue->push(
    'Workers@Mailer',
    'mailer.1',
    array('mailer' => 'x', 'message' => 'Hello World !')
);
```

Birinci parametreye <kbd>app/classes/Workers/</kbd> klasörü altındaki işçiye ait sınıf yolu, ikinci parametreye kuyruk adı, üçüncü parametreye ise kuyruğa gönderilecek veriler girilir. Opsiyonel olan dördüncü parametreye ise varsa amqp sürücüsüne ait gönderim seçenekleri girilebilir.

Aşağıda RabbitMQ AMQP sağlayıcısına ait web panelinden kuyruğa atılmış bir iş örneği görülüyor.

```php
http://localhost:15672/
```

![RabbitMQ](images/rabbitmq.png?raw=true)


<a name="delaying-a-job"></a>

#### İşin Çalışmasını Geciktirmek

```php
$this->queue->later(
    $delay = 60,
    'Workers@Order',
    'orders',
    array('order_id' => 'x', 'order_data' => [])
);
```

Eğer later metodu kullanılarak ilk parametreye integer türünde (unix time) bir zaman değeri girilirse girilen veri kuyruğa belirlenen süre kadar gecikmeli olarak eklenir.

<a name="workers"></a>

### İşçiler

Uygulamada kuyruğu tüketen her işçi <kbd>app/classes/Workers/</kbd> klasörü altında çalışır. Bir işi kuyruğa gönderirken iş parametresine uygulamanızdaki klasör yolunu vererek kuyruğu tüketecek işçi belirlenir.

```php
$this->queue->push(
    'Workers@Mailer',
    'mailer.1',
    array('mailer' => 'x', 'message' => 'Hello World !')
);
```

Yukarıdaki örnekte <kbd>Workers@Mailer</kbd> adlı işe ait girilen veriler <kbd>mailer.1</kbd> adlı kuyruğa gönderilir.

<a name="define-worker"></a>

#### İşçi Tanımlamak

Kuyruğa gönderme esnasında parametre olarak girilen işçi adı <kbd>app/classes/Workers/</kbd> klasörü altında bir sınıf olarak yaratılmalıdır. Worker sınıfı içerisinde tanımlı olan fire metodu ilk parametresine iş sınıfı ikinci parametresine ise işe ait kuyruk verileri gönderilir.

Aşağıda <kbd>Workers@Mailer</kbd> örneği görülüyor.

```php
namespace Workers;

use Obullo\Queue\Job;
use Obullo\Queue\JobInterface;
use Obullo\Container\ContainerInterface;

class Mailer implements JobInterface
{
    protected $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function fire($job, array $data)
    {
        switch ($data['mailer']) { 
        case 'x': 

            print_r($data);

            // Send mail message using "x" mail provider

            break;
        }
        if ($job instanceof Job) {
            $job->delete(); 
        }       
    }
}

/* Location: .app/classes/Workers/Mailer.php */
```

> **Not:** Bir iş sınıfı içerisinde sadece <kbd>fire</kbd> metodu ilan edilmesi job nesnesini ve kuyruğa atılan veriyi almak için yeterlidir.


<a name="delete-job"></a>

#### İşi Kuyruktan Silmek

Fire metodu ile elde edilen iş nesnesi iş tamamlandıktan sonra <kbd>$job->delete()</kbd> metodu ile silinmelidir. Aksi durumda tüm işler kuyruklama yazılımında birikir.

```php
public function fire($job, array $data)
{
    imageResize($data);  // Do job

    if ($job instanceof Job) {  // Delete completed job
        $job->delete(); 
    }       
}
```

<a name="release-job"></a>

#### İşi Kuyruğa Tekrar Atmak

Eğer bir iş herhangi bir tekrar kuyruğa atılmak isteniyorsa bunu <kbd>release</kbd> metodu ile yapabilirsiniz.

```php
public function fire($job, array $data)
{
    // Process the job...

    $job->release();
}
```

İlk parametreye bir sayı girerek işin tekrar kuyruğa atılma zamanını geciktirebilirsiniz.

```php
$job->release(5);
```

<a name="job-attempt"></a>

#### İşin Denenme Sayısını Almak

Eğer iş işlenirken herhangi bir istisnai hata sözkonusu oluğunda, hatalı iş otomatik olarak tekrar kuyruğa atılır. Hatalı bir işin denenme sayısını <kbd>$job->getAttempts()</kbd> metodu ile elde edebilirsiniz.

```php
if ($job->getAttempts() > 3)
{
    //
}
```

<a name="job-id"></a>

#### İşin ID Değerini Almak

İhtiyaç duyulduğunda işin ID değerini almak için <kbd>$job->getId()</kbd> metodunu kullanabilirsiniz.

```php
$job->getId();  // 15
```

<a name="job-name"></a>

#### İşin Adını Almak (Kuyruk Adı)

İhtiyaç duyulduğunda kuyruk / iş adını almak için <kbd>$job->getName()</kbd> metodunu kullanabilirsiniz.

```php
$job->getName();  // mailer.1
```

<a name="job-body"></a>

#### İşe Ait Veriyi Almak

İhtiyaç duyulduğunda işe ait veriyi almak için <kbd>$job->getRawBody()</kbd> metodunu kullanabilirsiniz.

```php
$job->getRawBody();

// {"job":"Workers\\Mailer","data":{"files":[{"name":"logs.jpg","type":"image\/jpeg","fileurl
```

<a name="running-workers"></a>

### Konsoldan İşçileri Çalıştırmak

<a name="show"></a>

#### Kuyruğu Listelemek

Konsol komutunu çalıştırabilmek için proje ana dizinine girin.

```php
cd /var/www/myproject/
```

Show komutu ile kuyruktaki tüm işleri görebilirsiniz.

```php
php task queue show --worker=Workers@Mailer --job=mailer.1 --output=1
```

```php
Following "mailer.1" queue ... 

------------------------------------------------------------------------------------------
Job ID  | Job Name            | Data 
------------------------------------------------------------------------------------------
1       | Workers@Mailer      | {"files":[{"name":"logs.jpg","type":"image\/jpeg", .. }

2       | Workers@Mailer      | {"files":[{"name":"logs.jpg","type":"image\/jpeg","fileurl":"..}
```

<a name="listen"></a>

#### Kuyruğu Dinlemek

Bir işçiyi çalıştarak bir kuyruğu dinlemek onu tüketmek anlamında gelir. Kuyruğu tüketmek için konsoldan aşağıdaki komut çalıştırılmalıdır.

```php
php task queue listen --worker=Workers@Mailer --job=mailer.1 --output=1
```

##### Gelişmiş parametreler kullanmak

Gelişmiş parametreler kullanarak işçinin kullanabileceği maximum hafıza, gecikme süresi, bekleme süresi gibi özellikleri belirleyebilirsiniz.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --delay=0 --memory=128 --timeout=0 --sleep=3
```

##### Parametreler için kısayollar

Ayrıca işçi tanımlanırken aşağıdaki gibi kısa yollar da kullanabilir.

```php
php task queue listen --w=Workers@Mailer --j=mailer.1 --o=1
```

##### Hata Ayıklama Modu

Konsoldan output değerini 1 yapmanız durumunda bulunan hatalar ekrana dökülür. Bu parametre <kbd>test</kbd> veya <kbd>local</kbd> çalışma ortamlarında kullanılmalıdır.

```php
php task queue listen --w=Workers@Logger --j=logger.1 --o=1
```

<a name="worker-parameters"></a>

##### İşçi Parametreleri

<a name="worker"></a>
<a name="job"></a>
<a name="delay"></a>
<a name="memory"></a>
<a name="timeout"></a>
<a name="sleep"></a>
<a name="attempt"></a>
<a name="output"></a>
<a name="env"></a>
<a name="var"></a>

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
<td>Kuyrukta bir hatadan dolayı yapılamayan bir iş için en fazla kaç kere daha deneme yapılacağını belirler. Bir sayı verilmez ise iş el ile silinene kadar kuyrukta kalır.</td>
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

<a name="save-worker-logs"></a>

#### İşçilere Ait Log Kayıtlarını Tutmak

Varsayılan olarak konsoldan uygulamaya gelen işçi isteklerine gelen log kayıtları tutulmaz. İşçilere ait log verilerini kaydetmek için aşağıdaki gibi <kbd>app/config/$env/logger.php</kbd> dosyası içerisindeki yapılandırmalardan <kbd>app > worker > log</kbd> değerini açık hale getirmeniz gerekir.

```php
'app' => [
    'worker' => [
        'log' => true,
    ]
],
```

Bu işlemden sonra <kbd>php task listen</kbd> komutu ile işçiler konsoldan çalıştırıldığında işçilere ait log kayıtlarını elde etmiş olacaksınız. Kayıt edilen log verilerinden işçilere ait olanları bulabilmek için <kbd>request = "worker"</kbd> değeri ile log verilerinizi filtereleyebilirsiniz.

> **Not:** Log işleme ve diğer işleri http sunucusu yormamak için başka bir sunucuda Obullo çatısı ile kurmak mümkündür ( dağıtık yapı ). Bunun için worker sunucunuza sadece bir Obullo sürümü indirip servisleri konfigüre etmeniz yeterli olur.

<a name="threading"></a>

### Çoklu İş Parçalarını Kontrol Etmek ( Multi Threading )

Uygulamanızda php işçilerini eş zamanlı çalıştırarak kuyruktaki verileri tüketme işlemi multi threading olarak anılır.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --output=0
```

Yukarıdaki komut aynı anda birden fazla konsolda çalıştırıldığında <kbd>Obullo/Task/QueueController</kbd> sınıfı üzerinden her seferinde  <kbd>Obullo/Task/WorkerController.php</kbd> dosyasını çalıştırarak yeni bir iş parçaçığı oluşturur. Yerel ortamda birden fazla komut penceresi açarak kuyruğun eş zamanlı nasıl tüketildiğini test edebilirsiniz.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --delay=0 --memory=128 --timeout=0 --output=1
```
Yerel ortamda yada test işlemleri için output parametresini 1 olarak gönderdiğinizde yapılan işlere ait hata çıktılarını konsoldan görebilirsiniz.

Ayrıca UNIX benzeri işletim sistemlerinde prodüksiyon ortamında kuyruk tüketimini otomasyona geçirmek ve çoklu iş parçaları (multithreading) ile çalışmak için Supervisor adlı programdan yararlanabilirsiniz. <a href="http://supervisord.org/" target="_blank">http://supervisord.org/</a>.

<a name="supervisor"></a>

#### Ubuntu Altında Supervisor Kurulumu

Supervisor UNIX benzeri işletim sistemlerinde kullanıcılarının bir dizi işlemi kontrol etmesini sağlayan bir istemci/suncu sistemidir. Daha fazla bilgi için bu adresi <a href="http://supervisord.org/">http://supervisord.org/</a> ziyaret edebilirsiniz.

Kurulum için aşağıdaki komutları konsolunuzdan çalıştırın

```php
sudo apt-get install supervisor
```

Supervisor konsoluna giriş

```php
supervisorctl
```

Tüm yardım komutlarını listelemek

```php
supervisor> help

default commands (type help <topic>):
=====================================
add    clear  fg        open  quit    remove  restart   start   stop  update 
avail  exit   maintail  pid   reload  reread  shutdown  status  tail  version
```

<a name="creating-first-worker"></a>

### İlk İşçimizi Yaratalım

Supervisor konfigürasyon klasörüne girin.

```php
cd /etc/supervisor/conf.d
```

Tüm konfigürasyon dosyalarını listeleyin.

```php
ll

total 16
drwxr-xr-x 2 root root 4096 May 31 13:19 ./
drwxr-xr-x 3 root root 4096 May 31 13:10 ../
-rw-r--j-- 1 root root  142 May  9  2011 README
```

Favori editörünüzle bir .conf dosyası yaratın.


```php
vi myMailer.conf
```

```php
[program:myMailer]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/project/task queue listen --worker:Workers@Mailer --job:mailer.1 --memory:128 --delay=0 --timeout=3
numprocs=3
autostart=true
autorestart=true
stdout_logfile=/var/www/project/data/logs/myMailerProcess.log
stdout_logfile_maxbytes=1MB
```

> **Not:** Burada <kbd>numprocs=3</kbd> sayısı yani 3, işlem başına açılacak iş parçaçığı (thread) anlamına gelir. Bu sayı optimum performans için sunucunuzun işlemci sayısı ile aynı olmalıdır. Örneğin 16 çekirdekli bir makineye sahipseniz bu sayıyı 16 yapabilirsiniz. Böylece bir işi 16 işçi eş zamanlı çalışarak daha kısa zamanda bitirecektir.

Bir işlemci için açılması gereken optimum işçi sayısının neden 1 olması gerektiği hakkındaki makaleye bu bağlantıdan gözatabilirsiniz. <a href="http://stackoverflow.com/questions/1718465/optimal-number-of-threads-per-core">Optimal Number of Threads Per Core</a>.

<a name="multiple-workers"></a>

#### Birden Fazla İşçiyi Çalıştırmak

Birden fazla iş yaratmak için yeni bir konfigürasyon dosyası yaratın ve bu iş için gereken parametreleri girin. Aşağıda resim küçültme işi için bir örnek gösteriliyor.

```php
vi myImages.conf
```

```php
[program:myImages]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/project/task queue listen --worker:Workers\ImageResizer --job:images.1 --memory=256 
numprocs=10
autostart=true
autorestart=true
stdout_logfile=/var/www/project/data/logs/myImageResizerProcess.log
stdout_logfile_maxbytes=1MB
```

<a name="starting-all-workers"></a>

#### Bütün İşçileri Başlatmak

Tanımladığınız tüm işleri başlatmak için <kbd>start all</kbd> komutunu kullanabilirsiniz.

```php
supervisorctl start all

myMailer_02: started
myMailer_01: started
myMailer_00: started

myImages_02: started
myImages_01: started
myImages_00: started
```

<a name="displaying-all-workers"></a>

#### Bütün İşçileri Görüntülemek

Tanımladığınız tüm işleri görmek için <kbd>supervisorctl</kbd> komutunu kullanabilirsiniz.

```php
supervisorctl

myMailer:myMailer_00           RUNNING    pid 16847, uptime 0:01:41
myMailer:myMailer_01           RUNNING    pid 16846, uptime 0:01:41
myMailer:myMailer_02           RUNNING    pid 16845, uptime 0:01:41
```

<a name="stopping-all-workers"></a>

#### Bütün İşçileri Durdurmak

Tanımladığınız tüm işleri başlatmak için <kbd>stop all</kbd> komutunu kullanabilirsiniz.

```php
supervisorctl stop all

myMailer_02: stopped
myMailer_01: stopped
myMailer_00: stopped
```
<a name="worker-logs"></a>

#### İşçi Loglarını Görüntülemek

İşçi loglarını takip etmek için aşağıdaki komutu konsolunuzdan çalıştırabilirsiniz.

```php
supervisorctl maintail -f
```

<a name="additional-info"></a>

### Ek Bilgiler

<a name="startup-config"></a>

#### Sistem Açılışında Otomatik Başlatma

Bunun için supervisord programını otomatik başlatma dosyanıza ekleyin. Bu dosya kullandığınız işletim sisteminize göre değişkenlik gösterecektir.

<a name="web-interface"></a>

#### Supervisord İçin Web Arayüzü

Aşağıdaki makalede supervisor programında açılan işleri görsel olarak yönetmenizi sağlayan bir <a href="http://iambusychangingtheworld.blogspot.com.tr/2013/11/supervisord-using-built-in-web.html">web arayüzü</a> bulabilirsiniz.

<a name="saving-failed-jobs"></a>

### Başarısız İşleri Kaydetmek

Eğer iş işlenirken herhangi bir istisnai hata sözkonusu oluğunda, hatalı iş otomatik olarak tekrar kuyruğa atılır fakat gerçekleşen hataları takip edebilmek için işleri bir veritabanına kaydetmek gerekir. Aşağıda başarısız işleri veritabanına kaydedebilmek için sırası ile yapmanız gerekenler anlatılıyor.

<a name="failed-jobs-config"></a>

#### Konfigürasyon

Başarısız işlere ait ayarlar <kbd>app/config/$env/queue.php</kbd> konfigürasyon dosyası failedJob anahtarından düzenlenir. Başarısız işlere ait kaydedici sınıf <kbd>storage</kbd> anahtarından düzenlenebilir. Mevcut kayıt edici sınıf <kbd>Obullo\Queue\Failed\Storage\Database</kbd> olarak belirlenmiştir. Eğer Obullo dizini içerisindeki bu sınıf ihtiyaçlarınızı karşılamıyorsa <kbd>storage</kbd> değerini <kbd>\Failed\Storage\MyClass</kbd> şeklinde değiştirerek bu sınıfı özelleştirebilirsiniz. Bu şekilde girilen bir kaydedici sınıfı <kbd>app/classes/Failed/Storage/</kbd> klasöründen çağrılacaktır.


```php
'failedJob' => 
[
    'enabled' => true,
    'storage' => '\Obullo\Queue\Failed\Storage\Database',
    'provider' => [
        'name' => 'database',
        'params' => [
            'connection' => 'failed',
        ]
    ],
    'table' => 'failures',
]
```

Başarısız işlerin kaydedilebilmesi için konfigürasyonda <kbd>enabled</kbd> anahtarının true olması gerekir.

#### Kaydetme Arayüzü

Kaydedici sınıflar yada sizin yaratmış olduğunuz yeni bir kaydedici sınıf aşağıdaki arayüzü kullanmak zorundadır.

```php
interface StorageInterface
{
    public function save(array $event);
    public function exists($file, $line);
    public function update($id, array $event);
}

/* Location: .Obullo/Queue/Failed/Storage.php */
```

Mevcut kaydedici sınıf aşağıdaki gibi StorageInterface arayüzünü kullanır.

```php
class Database implements StorageInterface
{
    // 
}

/* Location: .Obullo/Queue/Failed/Storage/Database.php */
```

<a name="failed-jobs-database"></a>

#### Veritabanına Kaydetmek

Mevcut kaydedici (\Obullo\Queue\Failed\Storage\Database) sınıfına ait veritabanı (Database.sql) sql dosyasına <kbd>Obullo/Queue/Failed/</kbd> klasöründen ulaşabilirsiniz.

```php
--
-- Database: `failed`
--

-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `failed`;

USE `failed`;

--
-- Table structure for table `failures`
--
CREATE TABLE IF NOT EXISTS `failures` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`job_id`  int(11) NOT NULL ,
`job_name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`job_body`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`job_attempts`  int(11) NOT NULL DEFAULT 0 ,
`error_level`  tinyint(3) NOT NULL ,
`error_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_file`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_line`  int UNSIGNED NOT NULL ,
`error_trace`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_xdebug`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_priority`  tinyint(4) NOT NULL ,
`failure_repeat`  int(11) NOT NULL DEFAULT 0 ,
`failure_first_date`  int(11) NOT NULL COMMENT 'unix timestamp' ,
`failure_last_date`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='Failed Jobs'
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT;
```

##### Kurulum ve Test

* Yukarıdaki SQL kodu ile veritabanını oluşturun.
* Test için <kbd>Workers/</kbd> içerisinde herhangi bir işçi dosyası fire metodu içerisinde bir istisnai hata yada php hatası yaratın.

```php
public function fire($job, array $data)
{
    echo $a;  // Error storage test.
    throw new \Exception("Exception storage test.");
}
```

* Başarısız işlerin kaydedilebilmesi için <kbd>enabled</kbd> anahtarının true olması gerekir. Queue konfigürasyon dosyasınızdan bu değeri kontrol edin.
* İşçi dosyanızı konsoldan çalıştırın.

Eğer herşey yolunda gittiyse bulunan hatalar veritabanına kaydedilmiş olmalı.


<a name="cloud-solutions"></a>

### Bulut Çözümler

Eğer kuyruklama işi için bulut bir çözüm düşüyorsanız aşağıdaki bu servisleri listeledik.

<a name="cloud-amqp"></a>

#### Cloud AMQP Servisi

Eğer RabbitMQ kullanıyor ve dağıtık bir kuyruklama servisi arıyorsanız <a href="https://www.cloudamqp.com/" target="_blank">cloud amqp</a> servisine bir gözatın.

##### Konfigürasyon

Cloudamqp.com web panelinden aldığınız bilgileri <kbd>app/config/$env/queue.php</kbd> dosyasına aşağıdaki tanımlayın.

```php
'amqp' => [

    'exchange' => [
        'type' => 'direct',
        'flag' => 'durable',
    ],
    
    'connections' => 
    [
        'second' => [
            'host'  => 'owl.rmq.cloudamqp.com',
            'port'  => 5672,
            'username'  => 'pktvnxjy',
            'password'  => 'zIrSZwcGRNsUE5KTDo0UQHotwBF59J1N',
            'vhost' => 'pktvnxjy'
        ]

    ],
],

```

##### Servis Sağlayıcısı Kurulumu

CloudAMQP şuanda PECl Amqp sağlayıcısını desteklemiyor bu yüzden cloudamqp ile sorunsuz çalışabilmek için AmqpLib servis sağlayıcısını aşağıdaki aktif edin.

```php
$c['app']->provider(
    [
        .
        // 'amqp' => 'Obullo\Service\Provider\Amqp',
        'amqp' => 'Obullo\Service\Provider\AmqpLib',
        .
        .
    ]
);
```

##### Servis Kurulumu

Servisler içerisinden queue servisi class parametresini aşağıdaki gibi güncelleyin.

```php
$parameters = [
    'class' => '\Obullo\Queue\Handler\AmqpLib',
    'provider' => [
        'name' => 'amqp',
        'params' => [
            'connection' => 'default'
        ]
    ]
];
```

Bu değişiklikten sonra artık kuyruklama sınıfınız cloudamqp ile çalışmaya hazır.


<a name="queue-reference"></a>

#### Queue Sınıfı Referansı

------

> Kuyruğa atma, silme ve okuma gibi işlemleri yürütür.

##### $this->queue->push(string $job, string $route, array $data, array $options = array());

Bir işi kuyruk sağlayıcınıza gönderir. Birinci parametreye iş adı, ikinci parametreye kuyruk adı, üçüncü parametreye kuyruğa ait veri, opsiyonel olan son parametreye ise varsa gönderim opsiyonları girilebilir.

##### $this->queue->later(int $delay, $job, string $route, array, $data, array $options = array());

Bir işi kuyruk sağlayıcınıza gecikmeli olarak gönderir. Push metodundan tek farkı ilk parametrenin gecikme süresi için ayrılmış olmasıdır.

##### $this->queue->pop(string $job, string $queue);

Girilen kuyruk adına göre kuyruktan bir veriyi okur ve sonraki veriye geçer. Exchange metodu ile birlikte kullanılmalıdır.

```php
while (true) {
    $job = $this->queue->pop('Workers@Mailer', 'mailer.1');
    if (! is_null($job)) {
        echo $job->getRawBody()."\n";
    }
}
```

##### $this->queue->delete(string $queue);

Kuyruktaki tüm veriyi kuyruk adı ile birlikte kalıcı olarak siler.


<a name="job-reference"></a>

#### Job Sınıfı Referansı

------

> Kuyruğa atılan işi yönetir. Kuyruktaki işe ait detaylı bilgilere ulaşmanızı sağlar. İşçiler klasöründe içerisindeki sınıflarda bulunan fire metodu ile elde edilen $job nesnesidir.

##### $job->delete()

Bir işi tamamlandıktan sonra kuyruktan bu metot ile silinir.

##### $job->isDeleted()

Eğer iş kuyruktan silinmişse <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine döner.

##### $job->release($delay = 0)

Bir işi tekrar kuyruğa atmak için kullanılır. Birinci parametreden varsa işin gecikme süresi tamsayı biçiminde girilir.

##### $job->getId()

İşe ait id değerini verir.

##### $job->getName()

İşin adını yani kuyruk adını verir.

##### $job->getAttempts()

Hatalı bir işin tekrar denenme sayısını verir.

##### $job->getRawBody()

İşe yüklenen veriyi json string biçiminde verir.