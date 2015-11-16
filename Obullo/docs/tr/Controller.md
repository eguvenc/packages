
## Kontrolör Sınıfı

Kontrolör sınıfı uygulamanın kalbidir ve uygulamaya gelen HTTP isteklerinin nasıl yürütüleceğini kontrol eder. Uygulama çalıştığı anda uygulama içerisinde kullanılan temel sınıflar ( config, uri, route, logger ) kontrolör sınıfı içerisine atanırlar.

<ul>

<li>
    <a href="#flow">İşleyiş</a>

    <ul>
        <li><a href="#what-is-the-controller">Kontrolör Nedir ?</a></li>
    </ul>

    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#container-loader">Konteyner Yükleyici</a></li>
        <li><a href="#example-page">Örnek Bir Açılış Sayfası</a></li>
        <li><a href="#without-container-loader">Sınıf Yükleyicinin Kullanılmadığı Durumlar</a></li>
        <li><a href="#method-arguments">Method Argümanları</a></li>
        <li><a href="#modules">Modüller</a></li>
        <li><a href="#welcome-page">İlk Açılış Sayfası</a></li>
    </ul>
</li>

<li>
    <a href="#annotations">Anotasyonlar</a>
    <ul>
        <li><a href="#enabling-annotations">Anotasyonları Aktif Etmek</a></li>
    </ul>
</li>

<li><a href="#reserved-methods">Rezerve Edilmiş Metotlar</a></li>

</ul>

<a name="flow"></a>

### İşleyiş

<kbd>index.php</kbd> dosyasına gelen bir http isteğinden sonra uri ve route sınıfı yüklenir uri sınıfı url değerlerini çözümleyerek route sınıfına gönderir, gerçek url çözümlemesi ise route sınıfında gerçekleşir. Çünkü route sınıfı <kbd>app/routes.php</kbd> dosyasında tanımlı olan route verilerini url değerleriyle karşılaştırarak çözümler ve çözümlenen route değerine ait Controller sınıfı <b>modules/</b> klasöründen çağrılarak çalıştırılır.

Bir http GET isteği çözümlemesi

![Akış Şeması](images/router-flowchart.png?raw=true)

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

> Http katmanları ile ilgili daha fazla bilgi için [Middlewares.md](Middlewares.md) dosyasını gözden geçirebilirsiniz.


<a name="what-is-the-controller"></a>

#### Kontrolör Nedir ?

Kontrolör dosyaları uygulamada http adres satırından çağrıldığı ismi ile bağlantılı olarak çözümlenebilen basit php sınıflarıdır. Kontrolör dosyaları uygulamada <kbd>.modules/modüladı/</kbd> klasörü altında tutulurlar. Uygulama içerisinde her bir kontrolör kendine ait isim alanı ( namespace ) ile belirtilmek zorundadır aksi takdirde çözümlenemezler.

Aşağıdaki adres satırı blog adlı modül altında bulunan start isimli kontrolör dosyasını çağırır:

```php
example.com/index.php/blog/start
```

Yukarıdaki örnekte uygulama modüller altında önce <kbd>blog</kbd> isimli klasörü bulmayı dener eğer böyle bir klasör varsa daha sonra <kbd>Start</kbd> isimli kontrolör dosyasını arar ve bulursa onu yükleyerek <kbd>index</kbd> metodunu çalıştırır. 


> **Not:** Metod ismi son segment olarak girilmediğinde varsayılan olarak index metodu çalışır.

<a name="running"></a>

### Çalıştırma

<a name="container-loader"></a>

#### Konteyner Yükleyici

Obullo da bir kontrolör sınıfı <kbd>__construct</kbd> metodu içerebilir. Construct metodu opsiyoneldir ilan edilirse içerisinde bir sınıf bir değişkene atabilir.


```php
namespace Welcome;

class Welcome extends \Controller
{
    public function __construct()
    {
        $this->example = new \Example;
    }
}
```

Konteyner içerisinden yüklenen sınıflara diğer metotlar içerisinden aşağıdaki gibi <kbd>$this</kbd> nesnesi yardımı ile ulaşılır.

```php
namespace Welcome;

class Welcome extends \Controller
{
    public function index()
    {
        echo $this->url->anchor("http://example.com/", "Hello World");

        $this->view->load('welcome');
    }

}
```

<kbd>$this</kbd> ile çağırılan bir sınıf konteyner nesnesi içerisinden çağrılır, nesne konteyner içerisinde kayıtlı değilse komponent dosyasi icersinde tanimli olan bileşenlerden cagrilir. Kontrolör sınıfı içerisinde  <kbd>$this</kbd> yöntemi ile çağırılan konteyner nesneleri aşağıdaki gibi <kbd>Obullo\Controller\Controller</kbd> ana kontrolör sınıfı içerisinde mevcut bulunan bir magic <b>__get</b> metodu ile yine konteyner sınıfı üzerinden çağrılmış olurlar.

```php
public function __get($key)
{
    return $this->__container[$key];
}
```
Çağrılan kütüphane bir <b>servis</b>, bir <b>komponent</b> yada konteyner içerisinde olmayan ve konteyner a kaydedilmek için çağırılan Obullo klasörü altındaki bir <b>sınıf</b> olabilir.


<a name="example-page"></a>

#### Örnek Bir Açılış Sayfası

Şimdi kontrolör sınıfını birazda iş başında görelim, aşağıdaki gibi <kbd>welcome</kbd> adında bir klasör yaratın.

```php
-  app
-  modules
    - welcome
       - view
           welcome.php
        Welcome.php
```

Metin editörünüzü kullanarak klasör içine yine <kbd>Welcome</kbd> adında bir kontrolör sınıfı oluşturun.

```php
namespace Welcome;

class Welcome extends \Controller
{
    public function index()
    {
        $this->view->load(
            'welcome',
            [
                'title' => 'Welcome to Obullo !',
            ]
        );
    }
}

/* Location: .modules/welcome/welcome.php */
```

Daha sonra adres satırına aşağıdaki gibi bir url yazıp çağırın.

```php
example.com/index.php/welcome
```

Sayfayı ziyaret ettiğinizde <kbd>welcome/welcome/index</kbd> metodu çalışmış olmalı.

Klasör ismi ve sınıf ismi <kbd>welcome/welcome</kbd> şeklinde aynı olduğunda route sınıfı adres çözümlemesi için sınıf ismine ihtiyaç duymaz yani <kbd>welcome/index</kbd> şeklinde sayfayı ziyaret ettiğinizde sayfa yine çalışmış olur. Eğer <kbd>welcome/hello</kbd> adında bir kontrolör sınıfımız olsaydı bu durumda adres satırını aşağıdaki gibi değiştirmemiz gerekirdi.

```php
example.com/index.php/welcome/hello/index
```

<a name="without-container-loader"></a>

#### Sınıf Yükleyicinin Kullanılmadığı Durumlar

Bazı sınıflar $this yöntemi ile yüklenmek istenmeyebilir bu gibi durumlarda sınıflara, servislere yada komponentlere array access <kbd>$this->c['class']</kbd> yöntemi ile konteyner içerisinden direkt erişilebilir.

Örneğin <kbd>view</kbd> sınıfına sadece <kbd>index()</kbd> metodu içerisinde ihtiyaç duysaydık <kbd>$this->view</kbd> yöntemini kullanmak yerine array access <kbd>$this->c['view']</kbd> yöntemini kullanarak ona aşağıdaki gibi erişebilirdik.


```php
namespace Welcome;

class Welcome extends \Controller
{
    public function index()
    {
        $this->c['view']->load(
            'welcome',
            [
                'title' => 'Welcome to Obullo !',
            ]
        );
    }
}

/* Location: .modules/welcome/welcome.php */
```

<a name="method-arguments"></a>

#### Method Argümanları

Eğer adres satırında bir metot dan sonra gelen segmentler birden fazla ise bu segmentler metot argümanları olarak çözümlenir. Örneğin aşağıdaki gibi bir url adresimizin olduğunu varsayalım:

```php
example.com/index.php/products/computer/index/desktop/123
```

Products klasörü altına Computer.php adlı bir sınıf oluşturun.

```php
-  app
-  modules
    - products
        Computer.php
```

Yukarıdaki url adresi tarayıcıda çalıştırıldığında URI sınıfı tarafından <kbd>desktop</kbd> segmenti <b>3.</b> ve <kbd>123</kbd> segmenti ise <b>4.</b> segment olarak çözümlenir.

```php
namespace Products;

class Computer extends \Controller
{
    public function index($type, $id)
    {
        echo $type;           // Çıktı  desktop
        echo $id;             // Çıktı  123 
        echo $this->uri->segment(3);    // Çıktı  123 
    }
}

/* Location: .modules/products/computer.php */
```

> **Not:** Eğer URI route özelliğini kullanıyorsanız fonksiyonunuza gelen segmentler route edilmiş segment değerleri olacaktır.

<a name="modules"></a>

#### Modüller

Modüller klasörleri kapsayan en dışdaki ana dizinlerdir ve alt dizinleri içerirler. Bir klasörü bir modül haline getirmek mümkündür, bunun için yapmanız gereken tek şey ana bir dizin açıp alt klasörlerinizi bu anadizin içerisine taşımak. 

Örneğin bir önceki örnekte kullandığımız <b>products</b> adlı dizini, <b>shop</b> adında bir modül oluşturup bu modül içerisine taşıyalım.

```php
-  app
-  modules
    - shop
       - products
            Computer.php
```

> **Not:** Şu anki sürümde bir modül altında sadece bir alt klasör açılabilir.

Böyle bir değişiklikten sonra url adresini artık aşağıdaki gibi çağırmanız gerekir.

```php
example.com/index.php/shop/products/computer/index/desktop/123
```

<a name="welcome-page"></a>

#### İlk Açılış Sayfası

Uygulamaya eğer domain adresinizden sonra herhangi bir kontrolör segmenti gönderilmezse uygulama ilk açılış sayfası için varsayılan bir kontrolör tanımlamasına ihtiyaç duyar. Varsayılan kontrolör <kbd>app/routes.php</kbd> dosyasında tanımlı olmadığında uygulama hata verecektir.

Bu nedenle route dosyanızı açıp varsayılan kontrolör sınıfınızı defaultPage() metodu ile aşağıdaki gibi belirlemeniz gerekir.

```php
$c['router']->domain($c['config']['url']['webhost']);
$c['router']->defaultPage('welcome/index');

/* Location: .routes.php */
```

<a name="annotations"></a>

### Anotasyonlar ( Annotations )

Bir anotasyon aslında bir metadata yı (örneğin yorum,  açıklama, tanıtım biçimini) yazıya, resime veya diğer veri türlerine tutturmaktır. Anotasyonlar genellikle orjinal bir verinin belirli bir bölümümü refere ederler.

> **Not:** Anotasyonlar herhangi bir kurulum yapmayı gerektirmez ve uygulamanıza performans açısından ek bir yük getirmez. Php ReflectionClass sınıfı ile okunan anotasyonlar çekirdekte herhangi bir düzenli ifade işlemi kullanılmadan kolayca çözümlenir.

Şu anki sürümde biz anotasyonları sadece <b>Http Katmanlarını</b> atamak ve <b>Event</b> sınıfına tayin edilen <b>Olayları Dinlemek</b> için kullanıyoruz.

<a name="enabling-annotations"></a>

#### Anotasyonları Aktif Etmek

Config.php konfigürasyon dosyasını açın ve <b>annotations > enabled</b> anahtarının değerini <b>true</b> olarak güncelleyin.

```php
'controller' => [
    'annotations' => true,
],
```

> **Not:** Anotasyonlar hakkında daha fazla bilgiye [Annotations.md](Annotations.md) dökümentasyonundan ulaşabilirsiniz.


<a name="reserved-methods"></a>

### Rezerve Edilmiş Metotlar

Kontrolör sınıfı içerisine tanımlanmış yada tanımlanması olası bazı metotlar çekirdek sınıflar tarafından sık sık kullanılır. Bu metotlara uygulamanın dışından erişmeye çalıştığınızda 404 hataları ile karşılaşırsınız.

<table>
    <thead>
        <tr>
            <th>Metot</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>__extend()</b></td>
            <td>View servisi tarafından kontrolör sınıfı içerisinde bir şablona genişlemek için kullanılır. </td>
        </tr>
    </tbody>
</table>

<kbd>__extend()</kbd> metodu hakkında daha detaylı bilgi için [View.md](View.md) dökümentasyonunu inceleyiniz.
