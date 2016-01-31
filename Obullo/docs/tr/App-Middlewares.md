
## Http Katmanları

Http katmanı <a href="http://en.wikipedia.org/wiki/Rack_%28web_server_interface%29">Rack Protokolü</a> nün php ye uyarlanmış bir versiyonudur. Katmanlar uygulamayı etkilemek, analiz etmek, request ve response nesneler ile uygulamanın çalışmasından sonraki veya önceki aşamayı etkilemek için kullanılırlar. Katmanlar <kbd>app/classes/Http</kbd> klasörü içerisinde yeralırlar ve basit php sınıflarıdır. Bir katman route yapısında tutturulabilir yada bağımsız olarak uygulamada evrensel olarak çalışabilir.

<ul>
    <li>
        <a href="#flow">İşleyiş</a>
        <ul>
            <li><a href="#hello-middlewares">Merhaba Katmanlar</a></li>
            <li><a href="#popular-middlewares">Popüler Katmanlar</a></li>
        </ul>
    </li>

    <li>
        <a href="#assign">Katman Atama</a>
        <ul>
            <li><a href="#globally">Evrensel Yöntem</a></li>
            <li><a href="#by-routes">Route Yöntemi</a></li>
            <li><a href="#by-annotations">Anotasyon Yöntemi</a></li>
        </ul>
    </li>

    <li>
        <a href="#features">Özellikler</a>
        <ul>
            <li><a href="#inject-components">Komponent Enjeksiyonu</a></li>
            <li><a href="#inject-container">Konteyner Enjeksiyonu</a></li>
            <li><a href="#inject-parameters">Parametre Enjeksiyonu</a></li>
            <li><a href="#terminate">Sonlandırma Metodu</a></li>
        </ul>
    </li>
</ul>

<a name="flow"></a>

### İşleyiş

Uygulamayı gezegenimizin çekirdeği gibi düşünürsek çekirdeğe doğru gittiğimizde dünyanın her bir katmanını bir http katmanı olarak kabul edebiliriz.

![Middlewares](images/middlewares.png?raw=true "Middlewares")

Yukarıdaki şemayı gözönüne alırsak uygulama çalıştığında en dıştaki katman ilk olarak çağrılır. Eğer bu katmandan bir <kbd>next</kbd> komutu cevabı elde edilirse bir sonraki katman çağrılır. Böylece bu aşamalar en içteki uygulama katmanı çalıştırılana kadar bir döngü içerisinde kademeli olarak devam eder. Uygulamaya eklenecek yeni bir katmanın hangi sıralamada çalışacağı <kbd>app/middlewares.php</kbd> dosyasından belirlenir. Her katmanda bir <kbd>invoke()</kbd> metodu olmak zorundadır.

<a name="hello-middlewares"></a>

#### Merhaba Katmanlar

Bir hello katmanı oluşturup katmanların nasıl çalıştığını öğrenelim. Önce <kbd>app/classes/Http/Middlewares/Hello.php</kbd> adında bir katman yaratın.

```php
class Hello implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        $response->getBody()->write(
            'Hello before middleware'
        );
        
        $response = $next($request, $response);

        $response->getBody()->write(
            'Hello after middleware'
        );
        return $response;
    }
}
```

Sonra katmanı <kbd>app/middlewares.php</kbd> dosyası içerisinde tanımlayın.

```php
$c['middleware']->register(
    [
        'Hello' => 'Http\Middlewares\Hello',
    ]
);
```

Ve son olarak bu evrensel katmanı uygulamaya dahil edin.

```php
$c['middleware']->add(
    [
        'Router',
        'Hello'
    ]
);
```

Şimdi uygulamanızın ilk açılış sayfasına gidip çıktıları kontrol edin. Normal şartlarda yukarıdakileri yaptı iseniz <kbd>2 adet</kbd> hello çıktısı almanız gerekir.

<a name="popular-middlewares"></a>

#### Popüler Katmanlar

En çok kullanılan katmanları görmek ve uygulamanıza dahil etmek için [http-middlewares](https://github.com/obullo/http-middlewares) adresini ziyaret edin.

<a name="assign"></a>

### Katman Atama

Uygulamanıza katmanlar ekleyip çıkarabilmenin 3 yöntemi vardır.

<a name="globally"></a>

#### Evrensel Yöntem

Eğer bir katmanı <kbd>app/middlewares.php</kbd> dosyası add() metodu içerisine eklerseniz katman, evrensel olarak uygulamanın bütününde çalışmaya başlar.

```php
$c['middleware']->add(
    [
        'Router',
        'Hello'
    ]
);
```

<a name="by-routes"></a>

#### Route Yöntemi

Eğer katmanların sadece belirli url adreslerinde çalışmasını istiyorsanız <kbd>app/routes.php</kbd> dosyasında bir route grubu oluşturup katmanları bu gruba atamanız gerekir.

```php
$c['router']->group(
    [
        'name' => 'AuthorizedUsers',
        'middleware' => array('Auth', 'Guest')
    ],
    function () {

        $this->attach('membership/.*');
        $this->attach('forum/.*');
    }
);
```

Bir route grubuna atanan katmanların çalışabilmesi için yukarıdaki gibi <kbd>$this->attach()</kbd> metodu ile istenilen url adreslerine tuturulması gerekir.


```php
$c['router']->match(['post'], 'order/pay')->middleware('XssClean');
```

Yukarıdaki gibi <kbd>middleware()</kbd> metodu ile de tek bir route kuralı için katman atamak mümkündür.


<a name="by-annotations"></a>

#### Anotasyon Yöntemi

Anotasyonlar yardımı ile katmanları ekleyip çıkarmak için <kbd>@middleware</kbd> komutunu kullanabilirsiniz.

```php
/**
 * Index
 *
 * @middleware->remove("Maintenance");
 *
 * @return void
 */
```

Yada <kbd>when</kbd> komutu ile sadece post işlemlerinde katmanlar ekleyebiliriz.

```php
/**
 * Index
 *
 * @middleware->when("post")->add("Xss");
 * 
 * @return void
 */
```

Anotasyonlar hakkında daha fazla bilgi için [Annotations.md](Annotations.md) dökümentasyonuna göz atın.

<a name="features"></a>

### Özellikler

Katman sınıfı içerisindeki bazı metotlar uygulamaya özgü özel işlevleri yerine getirir. Bu metotlar aşağıda örneklendirilmiştir.

<a name="inject-components"></a>

#### Komponent Enjeksiyonu

Bir katmana <kbd>app/providers.php</kbd> içerisindeki tanımlı kütüphaneleri enjekte edebilmek için <kbd>__construct()</kbd> metodu içerisinden aşağıdaki gibi kütüphane isimlerinin çağırılması yeterli olur.

```php
/**
 * Constructor
 * 
 * @param Config     $config     config
 * @param Translator $translator translator
 */
public function __construct(Config $config, Translator $translator)
{
    $this->config = $config->load('translator');
    $this->translator = $translator;
}
```

Yukarıdaki örnek <kbd>Translation</kbd> katmanından alıntıdır.

<a name="inject-container"></a>

#### Konteyner Enjeksiyonu

Bir katmana <kbd>Container</kbd> nesnesini enjekte edebilmek için katmanın <kbd>ContainerAwareInterface</kbd> arayüzüne genişlemesi gerekir.

```php
class App implements MiddlewareInterface, ContainerAwareInterface {}
```

Sonra katman sınıfı içerisinde setContainer() metodu ilan edilerek <kbd>$container</kbd> nesnesi elde edilebilir.

```php
public function setContainer(Container $container = null)
{
    $this->container = $container;

    return $this;
}
```

Yukarıdaki örnek <kbd>App</kbd> katmanından alıntıdır.

<a name="inject-parameters"></a>

#### Parametre Enjeksiyonu

Bir katmana <kbd>array $params</kbd> değişkeni ile parametreler enjekte edebilmek için katmanın <kbd>ParamsAwareInterface</kbd> arayüzüne genişlemesi gerekir.

```php
class Maintenance implements MiddlewareInterface, ParamsAwareInterface {}
```

Sonra katman sınıfı içerisinde setParams() metodu ilan edilerek <kbd>$params</kbd> değişkeni elde edilebilir.

```php
public function setParams(array $params)
{
    $this->params = $params;

    return $this;
}
```

Yukarıdaki örnek <kbd>Maintenance</kbd> katmanından alıntıdır.

<a name="terminate"></a>

#### Sonlandırma Metodu

Eğer bir katman içerisinde <kbd>terminate()</kbd> metodu kullanılırsa bu metot içerisine yazılan tüm işlevler uygulama kapatıldıktan sonra gerçekleşir.

```php
class Debugger implements MiddlewareInterface, ContainerAwareInterface, TerminableInterface()
```

Katmanın sonlandırılabilir olarak tanımlanabilmesi için <kbd>TerminableInterface</kbd> arayüzüne genişlemesi ve katman içinde <kbd>terminate()</kbd> metodunun ilan edilmesi gerekir.

```php
public function terminate()
{
    // terminable methods
}
```

Kapatma işlevi <kbd>app/classes/Http/Middlewares/FinalHandler/Zend</kbd> dosyası içerisindeki <kbd>register_shutdown_function()</kbd> fonksiyonu ile yürütülür.