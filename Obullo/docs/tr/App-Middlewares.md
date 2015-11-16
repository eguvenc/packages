
## Http Katmanları ( Middlewares )

Http katmanı Rack protokolünün php ye uyarlanmış bir versiyonudur. Bknz. <a href="http://en.wikipedia.org/wiki/Rack_%28web_server_interface%29">http://en.wikipedia.org/wiki/Rack_%28web_server_interface%29</a>. Http katmanları eski adıyla http filtreleridir. Uygulama içerisindeki katmanlar uygulamayı etkilemek, analiz etmek, uygulama ortamını yada request ve response nesnelerini uygulama çalışmasından sonra veya önce araya girerek etkilemek için kullanılırlar. Katmanlar <b>application</b> paketi içerisinde <b>middleware</b> sınıfına genişleyen basit php sınıflarıdır. Bir katman route yapısında tutturulabilir yada bağımsız olarak uygulamanın her yerinde çalışabilir.

<ul>
    <li>
        <a href="#flow">İşleyiş</a>
        <ul>
            <li>
                <a href="#how-to-use">Http Katmanları Nasıl Kullanılır ?</a>
                <ul>
                    <li><a href="#hello-world">Merhaba Dünya</a></li>
                </ul>
            </li>
            <li><a href="#attach-to-routes">Katmanları Route Yapısına Tutturmak</a></li>
        </ul>
    </li>
    <li>
        <a href="#middlewares">Katmanlar</a>
        <ul>
            <li><a href="#maintenance">Maintenance Katmanı</a></li>
            <li><a href="#auth">Auth Katmanı</a></li>
            <li><a href="#guest">Guest Katmanı</a></li>
            <li><a href="#methodNotAllowed">MethodNotAllowed Katmanı</a></li>
            <li><a href="#request">Request Katmanı</a></li>
            <li><a href="#https">Https Katmanı</a></li>
            <li><a href="#translation">Translation Katmanı</a></li>
            <li><a href="#rewriteLocale">RewriteLocale Katmanı</a></li>
            <li><a href="#csrf">Csrf Katmanı</a></li>
        </ul>
    </li>
</ul>

<a name="flow"></a>

### İşleyiş

Uygulamayı gezegenimizin çekirdeği gibi düşünürsek çekirdeğe doğru gittğimizde dünyanın her bir katmanını bir http katmanı olarak kabul etmeliyiz. Bu çerçevede uygulama index.php dosyasındaki run metodu ile çalıştığında en dışdaki katman ilk olarak çağrılır. Eğer bu katmandan bir <kbd>next</kbd> komutu cevabı elde edilirse bu katman opsiyonel olarak ona en yakın olan bir sonraki katmanı çağırır. Bu aşamalar dünyanın çekirdeğine inilip en içteki katman uygulamayı çalıştırana kadar bir döngü içerisinde kademeli olarak devam eder.

Her katmanda bir <kbd>call()</kbd> metodu olmak zorundadır.<kbd>Call</kbd> metodu controller içerisindeki istek yapılan metodun çalıştırılmasından önceki ve sonraki katmanları oluşurulur. Böylelikle metodun çalıştırılma seviyesinden önceki ve sonraki işlemler kontrol altına alınmış olur. Opsiyonel olarak ayrıca her bir katmanda <kbd>construct</kbd> metodu kullanılabilir.

Uygulamaya $c['app']->middleware() komutu ile yeni bir katman eklediğimizde eklenen katman en dıştaki yeni katman olur ve varsa bir önceki dış katmanı yada uygulamanın kendisini kuşatır.

<a name="how-to-use"></a>

#### Http Katmanları Nasıl Kullanılır ?

Uygulama sınıfı içerisindeki <kbd>$c['app']->middleware('katman');</kbd> metodu uygulamaya dinamik olarak bir katman ekler. Yeni katman bir önceki katmanı kuşatır, eğer bir önceki katman yoksa uygulamanın kendisini kuşatılmış olur.

<a name="hello-world"></a>

##### Merhaba Dünya

Bİr hello katmanı oluşturup katmanların nasıl çalıştığını öğrenelim. Eğer hello katmanını global olarak uygulamanın her yerinde çalıştırmak istiyorsak onu aşağıdaki gibi <kbd>app/middlewares.php</kbd> dosyası içerisine eklememiz gerekir.

```php
$c['app']->middleware(new Http\Middlewares\Hello);
```

<kbd>Http\Middlewares\Hello.php</kbd> dosyasının içeriği


```php
namespace Http\Middlewares;

use Obullo\Application\Middleware;

class Hello extends Middleware
{
    public function call()
    {
        $this->c['response']->write('<pre>Hello <b>before</b> middleware of index</pre>');
        $this->next->call();
        $this->c['response']->write('<pre>Hello <b>after</b> middleware of index</pre>');
    }
}

/* Location: .Http/Middlewares/Hello.php */
```

Şimdi uygulamanızın ilk açılış sayfasına gidip çıktıları kontrol edin. Normal şartlarda yukarıdakileri yaptı iseniz <kbd>2 adet</kbd> hello çıktısı almanız gerekir.

## Unutulmaması Gerekenler

* Registering Middleware
* Middleware Parameters


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

Yukarıdaki örnekte maintenance katmanı $this->attach('(.*)'); metodu ile geçerli domain e ait grubun tüm sayfalarına atanmış oldu.

Proje ana dizininde iken konsolunuza <kbd>php task domain down root</kbd> komutunu kullanarak maintenance filtresinin çalışıp çalışmadığını kontrol edebilirsiniz. <kbd>php task domain up root</kbd> komutu ile tekrar web siteniz maintenance modundan çıkıp gezilebilir hale gelecektir.


Yetkilendirme kontrolü ile igili diğer bir örneği yine sizin için hazırladık.

```php
$c['router']->group(
    [
        'name' => 'AuthorizedUsers',
        'domain' => 'mydomain.com', 
        'middleware' => ['Auth', 'Guest']
    ],
    function () {
    
        $this->defaultPage('welcome');
        $this->attach('welcome/restricted'); // Attach middleware just for this url
    }
);
```

Yukarıdaki örnekte de <kbd>Guest</kbd> katmanı kullanılarak <b>welcome/restricted</b> sayfasına oturum açmamış kullanıcıların girmesi engellenmiştir. Guest katmanı diğer katmanlar gibi <kbd>app/classes/Http/Middlewares/</kbd> klasörü içerisinde yer alır ve <kbd>call()</kbd> seviyesinde <kbd>$this->user->identity->guest()</kbd> metodu ile kullanıcının yetkisi olup olmadığını kontrol eder. Kontrol sonucunda yetkisi olmayan kullanıcılar login sayfasına yönlendirilirler.

<a name="middlewares"></a>

## Katmanlar

Http katmanları <kbd>app/classes/Http/Middlewares</kbd> klasörü içerisinde yeralırlar fakat katmanlar içinde kullanılan daha önceden uygulama ihtiyaçlarına göre hazırlanmış katman özellikleri ( Traits ) http katmanları içerisinden <b>use</b> komutu ile <kbd>Obullo/Application/Middlewares</kbd> klasöründen çağrılırlar.

Aşağıdaki Maintenance katmanına bir gözatalım.

```php
namespace Http\Middlewares;

use Obullo\Application\Middleware;
use Obullo\Application\Middlewares\UnderMaintenanceTrait;

class Maintenance extends Middleware
{
    use UnderMaintenanceTrait;

    public function __construct(array $params)
    {
        $this->domainIsDown($params);
    }

    public function call()
    {
        $this->next->call();
    }
}
```

Yukarıda maintenance katmanında görüldüğü gibi <b>use</b> komutu ile UnderMaintenanceTrait özelliği çağırılarak <b>$this->domainIsDown();</b> metoduna genişledik. Sizde uygulamanıza özgü özellikleri katmanlar içerisinden bu yöntemle çağırabilirsiniz.


Mevcut sürümde bulunan http katmanları aşağıdaki gibidir. Http katmanları konsol komutları ile istenildiği zaman kaldırılıp kurulabilir.

<a name="maintenance"></a>

#### Maintenance Katmanı

> Maintenance katmanı uygulamanızda tanımlı olan domain adreslerine göre uygulamanızı konsoldan bakıma alma işlevlerini kontrol eder. Detaylı bilgi için [Middleware-Maintenance.md](Middleware-Maintenance.md) dosyasını inceleyiniz.

<a name="auth"></a>

#### Auth Katmanı

> Başarılı oturum açmış ( yetkinlendirilmiş ) kullanıcılara ait katmandır. Detaylı bilgi için [Middleware-Auth.md](Middleware-Auth.md) dosyasını inceleyiniz.

<a name="guest"></a>

#### Guest Katmanı

> Oturum açmamış ( yetkinlendirilmemiş ) kullanıcılara ait bir katman oluşturur. Bu katman auth paketini çağırarak kullanıcının sisteme yetkisi olup olmadığını kontrol eder ve yetkisi olmayan kullanıcıları sistem dışına yönlendirir. Genellikle route yapısında Auth katmanı ile birlikte kullanılır. Detaylı bilgi için [Middleware-Auth.md](Middleware-Auth.md) dosyasını inceleyiniz.

<a name="methodNotAllowed"></a>

#### MethodNotAllowed Katmanı

> Uygulamaya gelen Http isteklerine göre metot türlerini filtrelemeyi sağlar. Belirlenen http metotları ( get, post, put, delete ) dışında bir istek gelirse isteği <kbd>HTTP Error 405 Method Not Allowed</kbd>  sayfası ile engeller. Detaylı bilgi için [Middleware-MethodNotAllowed.md](Middleware-MethodNotAllowed.md) dosyasını inceleyiniz.

<a name="request"></a>

#### Request Katmanı

> Uygulamaya gelen Http isteklerinin tümünü evrensel olarak filtrelemeyi sağlayan çekirdek katmandır. Detaylı bilgi için [Middleware-Request.md](Middleware-Request.md) dosyasını inceleyiniz.

<a name="https"></a>

#### Https Katmanı

> Uygulamada belirli adreslere gelen <kbd>http://</kbd> isteklerini <kbd>https://</kbd> protokolüne yönlendirir. Detaylı bilgi için [Middleware-Https.md](Middleware-Https.md) dosyasını inceleyiniz.

<a name="translation"></a>

#### Translation Katmanı

> Uygulamaya gelen http isteklerinin tümü için <kbd>locale</kbd> anahtarlı çereze varsayılan yerel dili yada url den gönderilen dili kaydeder. Detaylı bilgi için [Middleware-Translation.md](Middleware-Translation.md) dosyasını inceleyiniz.

<a name="rewriteLocale"></a>

#### RewriteLocale Katmanı

> Bu katman uygulamaya <kbd>http://example.com/welcome</kbd> olarak gelen istekleri mevcut yerel dili ekleyerek <kbd>http://example.com/en/welcome</kbd> adresine yönlendirir. Detaylı bilgi için [Middleware-RewriteLocale.md](Middleware-RewriteLocale.md) dosyasını inceleyiniz.

<a name="csrf"></a>

#### Csrf Katmanı

> Csrf katmanı Cross Request Forgery güvenlik tehdidine karşı uygulamanızdaki formlarda oluşturduğunuz güvenlik algoritmasını http <kbd>POST</kbd> istekleri geldiğinde sunucu tarafında doğrular, doğrulama başarılı olmazsa katman içerisinden kullanıcı hata sayfasına yönlendirilir. Detaylı bilgi için [Middleware-Csrf.md](Middleware-Csrf.md) dosyasını inceleyiniz.
