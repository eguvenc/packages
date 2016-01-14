
## Http Katmanları

Http katmanı <a href="http://en.wikipedia.org/wiki/Rack_%28web_server_interface%29">Rack Protokolü</a> nün php ye uyarlanmış bir versiyonudur. Katmanlar uygulamayı etkilemek, analiz etmek, request ve response nesneler ile uygulamanın çalışmasından sonraki veya önceki aşamayı etkilemek için kullanılırlar. Katmanlar <kbd>app/classes/Http</kbd> klasörü içerisinde yeralırlar ve basit php sınıflarıdır. Bir katman route yapısında tutturulabilir yada bağımsız olarak uygulamada evrensel olarak çalışabilir.

<ul>
    <li>
        <a href="#flow">İşleyiş</a>
        <ul>
            <li><a href="#hello-middlewares">Merhaba Katmanlar</a></li>
            <li><a href="#attach-to-routes">Katmanları Route Yapısına Tutturmak</a></li>
        </ul>
    </li>

</ul>

<a name="flow"></a>

### İşleyiş

Uygulamayı gezegenimizin çekirdeği gibi düşünürsek çekirdeğe doğru gittiğimizde dünyanın her bir katmanını bir http katmanı olarak kabul edebiliriz.

![Middlewares](images/middlewares.png?raw=true "Middlewares")

Yukarıdaki şemayı gözönüne alırsak uygulama çalıştığında en dışdaki katman ilk olarak çağrılır. Eğer bu katmandan bir <kbd>next</kbd> komutu cevabı elde edilirse bir sonraki katman çağrılır. Böylece bu aşamalar en içteki uygulama katmanı çalıştırılana kadar bir döngü içerisinde kademeli olarak devam eder. Uygulamaya eklenecek yeni bir katmanın hangi sıralamada çalışacağı <kbd>app/middlewares.php</kbd> dosyasından belirlenir.

Her katmanda bir <kbd>invoke()</kbd> metodu olmak zorundadır. Opsiyonel olarak ayrıca her bir katmanda <kbd>construct</kbd> metodu kullanılabilir ve bu metot bağımlılık enjeksiyonunu destekler.


<a name="hello-middlewares"></a>

#### Merhaba Katmanlar

Bİr hello katmanı oluşturup katmanların nasıl çalıştığını öğrenelim. Önce katmanı <kbd>app/middlewares.php</kbd> dosyası içerisinde tanımlayın.

```php
$c['middleware']->register(
    [
        'Hello' => 'Http\Middlewares\Hello',
    ]
);
```

Sonra uygulamaya dahil edin.

```php
$c['middleware']->add(
    [
        'Router',
        'Hello'
    ]
);
```

<kbd>Http\Middlewares\Hello.php</kbd> dosyasının içeriği

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

Şimdi uygulamanızın ilk açılış sayfasına gidip çıktıları kontrol edin. Normal şartlarda yukarıdakileri yaptı iseniz <kbd>2 adet</kbd> hello çıktısı almanız gerekir.


<a name="attach-to-routes"></a>

#### Katmanları Route Yapısına Tutturmak

Eğer katmanların sadece belirli url adreslerine özgü olmasını istiyorsanız <b>app/routes.php</b> dosyasında bir route grubu oluşturup katmanları bu gruba atamanız gerekir. Bunun için grup konfigürasyonu içerisinde middleware anahtarı kullanarak mevcut gruba birden fazla katman ekleyebilirsiniz. Unutulmaması gereken en önemli nokta katmanları <b>$this->attach();</b> metodu içerisinde düzenli ifadelerden yaralanarak ( regular expressions ) katmanın çalışması gereken url adresine tutturulması kısmıdır.

Bunun için size aşağıda bir örnek hazırladık.

```php
$c['router']->group(
    [
        'name' => 'GenericUsers',
        'domain' => 'mydomain.com',
        'middleware' => ['Maintenance']
    ],
    function () {
        $this->defaultPage('welcome');
        $this->attach('(.*)'); // Attach middleware to all pages of this group
    }
);
```

Katmanlar hakkında detaylı bilgi için [http-middlewares](https://github.com/obullo/http-middlewares) proje dökümentasyonunu inceleyebilirsiniz.