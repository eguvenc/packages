
## Konsol Arayüzü ( Cli )

Cli paketi yani Command Line Interface komut satırından yürütülen işlemler için yardımcı paketler içerir. Framework konsol arayüzü projenizin ana dizinindeki **task** dosyası üzerinden çalışır.

<ul>
<li>
    <a href="#flow">İşleyiş</a>
    <ul>
        <li><a href="#cli-uri">Uri Sınıfı</a></li>
        <li><a href="#cli-router">Router Sınıfı</a></li>
    </ul>
</li>

<li>
    <a href="#running-console-commands">Konsol Komutlarını Çalıştırmak</a>
    <ul>
        <li><a href="#arguments">Argümanlar</a></li>
        <li><a href="#log-command">Log Komutu</a></li>
        <li><a href="#help-commands">Help Komutları</a></li>
        <li><a href="#middleware-command">Middleware Komutu</a></li>
        <li><a href="#module-command">Module Komutu</a></li>
        <li><a href="#domain-command">Domain Komutu</a></li>
        <li><a href="#debugger-command">Debugger Komutu</a></li>
        <li><a href="#queue-command">Queue Komutu</a></li>
        <li><a href="#run-your-commands">Kendi Komutlarınızı Çalıştırmak</a></li>
        <li><a href="#external-commands">Konsol Komutlarını Dışarıdan Çalıştırmak</a></li>
        <li><a href="#shortcuts">Argümanlar İçin Kısayollar</a></li>
    </ul>
</li>


<li><a href="#method-reference">Fonksiyon Referansı</a></li>
</ul>

<a name="flow"></a>

### İşleyiş

Framework komut satırından yürütülen işlemleri <kbd>modules/tasks</kbd> klasörü içerisinde yaratılmış olan kontrolör dosyalarına istek göndererek yürütür. Framework konsol arayüzü projenizin ana dizinindeki **task** dosyası üzerinden çalışır. Bu dosyaya gelen konsol istekleri <kbd>Obullo/Application/Cli.php</kbd> dosyasını çalıştırarak çağırılan kontrolör dosyalarını çözümler.

> **Not:** Bir task kontrolör dosyasının normal bir kontrolör dosyasından hiçbir farkı yoktur sadece dosyanın üzerinde <b>namespace</b> için <b>Tasks</b> belirtilmek zorundadır.

Uygulama konsol dosyaları <kbd>modules/tasks</kbd> klasörü içerisinde mevcut değilse <kbd>Obullo\Cli\Task</kbd> klasöründen yüklenirler. Örneğin Log kontrolör dosyası sadece Obullo\Cli\Task dizininde mevcut olduğundan bu dizinden çağırılır.

```php
php task log
```

<a name="cli-uri"></a>

### Uri Sınıfı

Uri sınıfı <kbd>.modules/tasks</kbd> dizini içindeki komutlara "--" sembolü ile gönderilen konsol argümanlarını çözümlemek için kullanılır. Sınıf task komutu ile gönderilen isteklere ait argümanları çözümleyerek <kbd>$this->uri</kbd> nesnesi ile bu argümanların yönetilmesini kolaylaştırır. Cli arayüzünde argüman çözümleme esnasında Cli nesnesi <kbd>Application/Cli</kbd> sınıfı içerisinden uygulama içerisine kendiliğinden dahil edilir.

Sınıfı daha iyi anlamak için aşağıdaki gibi <kbd>.modules/tasks</kbd> dizini altında bir task controller yaratın ve yaratığınız task komutuna bir argüman gönderin.

```php
namespace Tasks;

use Obullo\Cli\Console;

class Hello extends \Controller {
  
    public function index()
    {
        echo Console::logo("Welcome to Hello Controller");
        echo Console::description("This is my first task controller.");

        $planet = $this->uri->argument('planet');

        echo Console::text("Hello ".$planet, 'yellow');
        echo Console::newline(2);
    }
}

/* Location: .modules/tasks/Hello.php */
```

Konsoldan hello komutunu <b>planet</b> argümanı ile aşağıdaki gibi çalıştırdığınızda bir **Hello World** çıktısı almanız gerekir.

```php
php task hello --planet=World
```

> **Not:** Herhangi bir task controller sınıfı içerisinde http katmanları çalışmaz.

Argümanları sayısal olarak da alabilirsiniz.

```php
$planet = $this->uri->segment(0);
```

Aşağıdaki gibi standart parametreler de desteklenmektedir.

```php
namespace Tasks;

class Hello extends \Controller {

    public function index($planet = '')
    {
        echo Console::logo("Welcome to Hello Controller");
        echo Console::description("This is my first task controller.");

        echo Console::text("Hello ".$planet, 'yellow');
        echo Console::newline(2);
    }
}
```

Konsoldan hello komutunu <b>planet</b> argümanı ile aşağıdaki gibi çalıştırdığınızda bir **Hello World** çıktısı almanız gerekir.

```php
php task hello World
```

<a name="cli-router"></a>

### Router Sınıfı

Cli router sınıfı http router ile benzer metotlara sahiptir. Router sınıfı bir task sınıfı içerisinde kullanıldığında çalıştırılan sınıfın adı, metot adı, isim alanı ve host bilgileri gibi bilgileri verir.


```php
namespace Tasks;

class Hello extends \Controller {

    public function index()
    {
        echo "Task Controller : " . $this->router->getClass()."\n";
        echo "Method : ". $this->router->getMethod()."\n";
        echo "Namespace : ". $this->router->getNamespace()."\n";
        echo "Uri : ". $this->router->getPath()."\n";
        echo "Host: ". $this->router->getHost()."\n";
    }
}
```

Help komutunu çalıştırın

```php
php task hello index --h=test
```

Çalıştırdığınızda aşağıdaki gibi bir çıktı almanız gerekir.

```php
Task Controller : Help
Method : index
Namespace : \Obullo\Cli\Task\Help
Uri : help/index/--host=test
Host: test
```

<a name="running-console-commands"></a>

### Konsol Komutlarını Çalıştırmak

Konsol arayüzüne gönderilen her url task komutu http arayüzüne benzer bir şekilde <b>class/method</b> olarak çözümlenir. Konsol komutlarındaki url çözümlemesinin http arayüzünden farkı argümanları "--" öneki key => value olarak da gönderebilmenize olanak sağlayarak konsol işlerini kolaylaştırmasıdır. Diğer bir fark ise konsol komutlarında adres çözümlemesi için forward slash "/" yerine boşluk " " karakteri kullanılmasıdır.

Daha iyi anlamak için terminalinizi açıp aşağıdaki komutu çalıştırın.

```php
php task hello
```

Yukarıdaki komut ana dizindeki task dosyasına bir istek göndererek <kbd>.modules/tasks/</kbd> klasörü altındaki <b>Hello</b> adlı controller dosyasının <b>index</b> metodunu çalıştırır.

```php
- modules
  - tasks
      Hello.php
```

> **Not:** Eğer bir method ismi yazmazsanız varsayılan method her zaman "index" metodudur. Fakat argümanlar gönderiyorsanız index metodunu yazmanız gerekir.

<a name="arguments"></a>

#### Argümanlar

Argümanlar method çözümlemesinin hemen ardından gönderilirler. Aşağıdaki örnekte uygulamaya bir middleware eklemek için add metodu çözümlemesinden sonra Csrf argümanı gönderiliyor.

```php
php task middleware add Csrf
```

Bir kuyruğu dinlemek için kullanılan konsol komutuna bir başka örnek.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --memory=128 --sleep=3 --output=1
```

Kısayolları da kullanabilirsiniz

```php
php task queue listen --w=Workers@Logger --j=logger.1 --m=128 --s=3 --o=1
```

<a name="log-command"></a>

#### Log Komutu

Eğer <kbd>config/local/config.php</kbd> dosyasındaki log > enabled anahtarı true olarak ayarlandı ise uygulamayı gezdiğinizde konsol dan uygulama loglarını eş zamanlı takip edebilirsiniz.

Bunun için terminalinizi açın ve aşağıdaki komutu yazın.

```php
php task log
```

Yukarıdaki komut <kbd>modules/tasks/Log</kbd> sınıfını çalıştırır ve <kbd>.resources/data/logs/http.log</kbd> dosyasını okuyarak uygulamaya ait http isteklerinin loglarını ekrana döker.


```php
php task log --dir=ajax
```

Yukarıdaki komut ise  <kbd>modules/tasks/Log</kbd> sınıfını çalıştırır ve <kbd>.resources/data/logs/ajax.log</kbd> dosyasını okuyarak uygulamaya ait ajax isteklerinin loglarını ekrana döker.

<a name="help-commands"></a>

#### Help Komutları

Help metotlarını çalıştırdığınızda bir yardım ekranı ile karşılaşırsınız ve help metodu standart olarak tüm task kontrolör dosyalarında bulunur. Takip eden örnekte log komutuna ait yardım çıktısı gösteriliyor.

```php
php task log help
```

```php
Help:

Available Commands

    clear    : Clear log data ( also removes the queue logs ).
    help     : Help

Available Arguments

    --dir    : Sets log direction for reader. Directions : cli, ajax, http ( default )
    --db     : Database name if mongo driver used.
    --table  : Collection name if mongo driver used.

Usage:

php task log --dir=value

    php task log 
    php task log --dir=cli
    php task log --dir=ajax
    php task log --dir=http --table=logs

Description:

Read log data from "/var/www/framework/resources/data/logs" folder.
```

Clear metodunu çalıştırdığınızda komut <kbd>.resources/data/logs</kbd> dizininden tüm log kayıtlarını siler.

```php
php task log clear
```

> **Not:** Diğer Task komutları hakkında daha fazla bilgiye Obullo\Task paketi dökümentasyonundan ulaşabilirsiniz

<a name="middleware-command"></a>

#### Middleware Komutu

<kbd>Obullo/Application/Middlewares</kbd> klasörūndaki mevcut bir http katmanını uygulamanızın <kbd>app/classes/Http/Middlewares</kbd> klasörūne kopyalar.

Https katmanı için örnek bir kurulum

```php
php task middleware add https
```

Https katmanı için örnek bir kaldırma

```php
php task middleware remove https
```

> Katmanlar hakkında daha geniş bilgi için [Middlewares.md](Middlewares.md) dosyasına gözatın.

<a name="module-command"></a>

#### Module Komutu

<kbd>Obullo/Application/Modules</kbd> klasörūndaki mevcut bir modülü uygulamanızın <kbd>modules/</kbd> klasörūne kopyalar.

Debugger modülü için örnek bir kurulum

```php
php task module add debugger
```

Debugger modülü için örnek bir kaldırma

```php
php task module remove debugger
```

> Modüller hakkında daha geniş bilgi için [Modules.md](Modules.md) dosyasına gözatın.

<a name="domain-command"></a>

#### App Komutu

App komutu maintenance katmanını uygulamaya ekler. Eğer <kbd>config/maintenance.php</kbd> dosyanızda tanımlı domain adresleriniz yada isim alanlarınız varsa uygulamanızın konsoldan bakıma alma işlevlerini yürütebilirsiniz. 

Maintenance katmanı için örnek bir kurulum

```php
php task middleware add maintenance
```

Maintenance katmanı için örnek bir kurulum

```php
php task middleware remove maintenance
```

Uygulamanızı bakıma almak için aşağıdaki komutu çalıştırın.

```php
php task app down root
```

Uygulamanızı bakımdan çıkarmak için aşağıdaki komutu çalıştırın.

```php
php task app up root
```

> Maintenance katmanı hakkında daha geniş bilgi için [Middleware-Maintenance.md](Middleware-Maintenance.md) dosyasına gözatın.

<a name="debugger-command"></a>

#### Debugger Komutu

Debugger modülü uygulamanın geliştirilmesi esnasında uygulama isteklerinden sonra oluşan ortam bileşenleri ve arka plan log verilerini görselleştirir.

Debugger modülü için örnek bir kurulum

```php
php task module add debugger
```

Debugger modülü için örnek bir kaldırma

```php
php task module remove debugger
```
Debug sunucusunu çalıştırmak için aşağıdaki komutu kullanın.

```php
php task debugger
```

Debugger konsolonu görüntülemek için <kbd>/debugger</kbd> sayfasını ziyaret edin

```php
http://myproject/debugger
```

> Debugger modülü hakkında daha geniş bilgi için Debugger paketi [Debbuger.md](Debugger.md) belgesine gözatın.

<a name="queue-command"></a>

#### Queue Komutu

Kuyruğa atılan işleri <kbd>Obullo\Task\QueueController</kbd> sınıfına istek göndererek tüketir.

Örnek bir kuyruk dinleme komutu

```php
php task queue listen --worker=Logger --job=Server1.Logger --memory=128 --sleep=3--tries=0 --output=1
```

> Queue komutu hakkında daha geniş bilgi için [Queue.md](Queue.md) dosyasına gözatın.

<a name="run-your-commands"></a>

#### Kendi Komutlarınızı Çalıştırmak

Modules task klasörū içerisinde kendinize ait task dosyaları yaratabilirsiniz. Bunun için http arayüzündeki controller sınıfına benzer bir şekilde bir kontrolör dosyası yaratın ve namespace bölümünü <b>Tasks</b> olarak değiştirin.

```php
namespace Tasks;

class Hello extends \Controller {
  
    public function index()
    {
        echo Console::logo("Welcome to Hello Controller");
        echo Console::description("This is my first task controller.");
    }
}

/* Location: .modules/tasks/Hello.php */
```

Şimdi oluşturduğunuz komutu aşağıdaki gibi çalıştırın.

```php
php task hello
```

<a name="external-commands"></a>

#### Konsol Komutlarını Dışarıdan Çalıştırmak

Eğer bir konsol komutu crontab gibi bir uygulama üzerinden dışarıdan çalıştırılmak isteniyorsa aşağıdaki task dosyasının tam dosya yolu girilmelir.

```php
php /var/www/framework/task help
```

<a name="shortcuts"></a>

#### Argümanlar İçin Kısayollar

<table>
<thead>
<tr>
<th>Kısayol</th>
<th>Argüman</th>
</thead>
<tbody>
<tr>
<td>--o</td>
<td>--output</td>
</tr>
<tr>
<td>--w</td>
<td>--worker</td>
</tr>
<tr>
<td>--j</td>
<td>--job</td>
</tr>
<tr>
<td>--d</td>
<td>--delay</td>
</tr>
<tr>
<td>--m</td>
<td>--memory</td>
</tr>
<tr>
<td>--t</td>
<td>--timeout</td>
</tr>
<tr>
<td>--s</td>
<td>--sleep</td>
</tr>
<tr>
<td>--a</td>
<td>--attempt</td>
</tr>
<tr>
<td>--v</td>
<td>--var</td>
</tr>
<tr>
<td>--h</td>
<td>--host</td>
</tr>
<tr>
<td>--e</td>
<td>--env</td>
</tr>
</tbody>
</table>


<a name="method-reference"></a>

#### Uri Sınıfı

------

##### $this->uri->argument(string $name, string $defalt = '');

Girilen isme göre konsol komutundan gönderilen argümanın değerine geri döner.

##### $this->uri->getArguments();

Çözümlenen argüman listesine "--key=value" olarak bir dizi içerisinde geri döner.

##### $this->uri->segment(integer $n, string $default = '');

Argüman değerini anahtarlar yerine sayılarla alır ve elde edilen argüman değerine geri döner.

##### $this->uri->getSegments();

Çözümlenen argümanların listesine sadece "value" olarak bir dizi içerisinde geri döner.

##### $this->uri->getClass();

Çözümlenen sınıf ismine geri döner.

##### $this->uri->getMethod();

Çözümlenen metot ismine geri döner.

##### $this->uri->getPath();

Çözümlenen tüm konsol komutuna argümanları ile birlikte string formatında geri döner.

##### $this->uri->getShortcuts();

Argümanlar için tanımlı olan tüm kısayollara bir dizi içerisinde geri döner.

##### $this->uri->clear();

Sınıf içerisindeki tüm değişkenlerin değerlerini başa döndürür.

#### Router Sınıfı

------

##### $this->router->getClass();

Konsoldan gönderilan ilk parametre değerini yani sınıf adını verir.

##### $this->router->getMethod();

Konsoldan gönderilan ilk parametre değerini yani metot adını verir.

##### $this->router->getPath();

Tüm konsol girdisine konsol parametreleri ile birlikte geri döner.

##### $this->router->getHost();

Eğer parametre olarak bir host değeri gönderilmişse bu değere aksi durumda null değerine geri döner.