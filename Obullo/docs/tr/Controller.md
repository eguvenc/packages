
## Kontrolör Sınıfı

Kontrolör sınıfı uygulamanın kalbidir ve uygulamaya gelen HTTP isteklerinin nasıl yürütüleceğini kontrol eder.

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
        <li><a href="#method-arguments">Method Argümanları</a></li>
        <li><a href="#modules">Modüller</a></li>
        <li><a href="#middlewares">Http Katmanları</a></li>
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

Kontrolör dosyaları http route çözümlemesinden sonra <kbd>modules/</kbd> klasöründen çağrılarak çalıştırılır.

Bir http GET isteği çözümlemesi

```php
$router->post('product/list', 'shop/product/list'); 
```

Bir http POST isteği çözümlemesi

```php
$router->post('product/post', 'shop/product/post'); 
```

Route çözümlemeleri ilgili daha fazla bilgi için [Router.md](Router.md) dosyasını gözden geçirebilirsiniz.

<a name="what-is-the-controller"></a>

#### Kontrolör Nedir ?

Kontrolör dosyaları uygulamada http adres satırından çağrıldığı ismi ile bağlantılı olarak çözümlenebilen basit php sınıflarıdır. Kontrolör dosyaları uygulamada <kbd>app/modules/</kbd> klasörü altın çalışırlar.

Örnek bir http isteği.

```php
http://example.com/index.php/welcome
```

BU isteğe ait kontrolör dosyası.

```php
use Obullo\Http\Controller;

class Welcome extends Controller
{
    public function index()
    {
        $this->view->load('views::welcome');
    }
}
```

#### Modüller

Modüller de kontrolör dosyalarıdır. Tek farkları bir <kbd>app/modules/modülismi/</kbd> gibi bir dizin içinde ve bir <kbd>namespace</kbd> ile çalışabiliyor olmalıdır.

Aşağıdaki adres satırı blog adlı modül altında bulunan start isimli kontrolör dosyasını çağırır:

```php
example.com/index.php/examples/start
```

Yukarıdaki örnekte uygulama modüller altında önce <kbd>blog</kbd> isimli klasörü bulmayı dener eğer böyle bir klasör varsa daha sonra <kbd>Start</kbd> isimli kontrolör dosyasını arar ve bulursa onu yükleyerek <kbd>index</kbd> metodunu çalıştırır. 

Not: Metod ismi son segment olarak girilmediğinde varsayılan olarak index metodu çalışır.

<a name="running"></a>

### Çalıştırma

<a name="container-loader"></a>

#### Konteyner Yükleyici

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

Aşağıdaki gibi,

```php
$this->class
```

<kbd>$this</kbd> ile çağırılan bir sınıf__get() proxy metodu ile konteyner içerisinde çağırılmış olur.

```php
public function __get($class)
{
    return $this->getContainer()->get($class);
}
```

Çağrılan kütüphane <kbd>app/providers.php</kbd> dosyası aracılığı ile konteyner içerisine tanımlı bir <kbd>servis</kbd> olmalıdır.

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

Yukarıdaki url adresi tarayıcıda çalıştırıldığında URI sınıfı tarafından <kbd>desktop</kbd> segmenti <kbd>3.</kbd> ve <kbd>123</kbd> segmenti ise <kbd>4.</kbd> segment olarak çözümlenir.

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

Not: Eğer URI route özelliğini kullanıyorsanız fonksiyonunuza gelen segmentler route edilmiş segment değerleri olacaktır.

<a name="modules"></a>

#### Modüller

Modüller klasörleri kapsayan en dışdaki ana dizinlerdir ve alt dizinleri içerirler. Bir klasörü bir modül haline getirmek mümkündür, bunun için yapmanız gereken tek şey ana bir dizin açıp alt klasörlerinizi bu anadizin içerisine taşımak. 

Örneğin bir önceki örnekte kullandığımız <kbd>products</kbd> adlı dizini, <kbd>shop</kbd> adında bir modül oluşturup bu modül içerisine taşıyalım.

```php
-  app
-  modules
    - shop
       - products
            Computer.php
```

Bir modül altında sadece bir alt klasör açılabilir. Böyle bir değişiklikten sonra url adresini artık aşağıdaki gibi çağırmanız gerekir.

```php
example.com/index.php/shop/products/computer/index/desktop/123
```
<a name="middlewares"></a>

#### Http Katmanları

Katmanlar <kbd>app/classes/Http</kbd> klasörü içerisinde yeralan basit php sınıflarıdır. Bir katman route yapısında tutturulabilir yada uygulamada evrensel olarak çalışabilir. Http katmanları http çözümlemesinden önce <kbd>$request</kbd> yada <kbd>$response</kbd> nesnelerini etkilemek için kullanılırlar. Daha fazla bilgi için [App-Middlewares.md](App-Middlewares.md) dökümentasyonunu inceleyebilirsiniz.


<a name="welcome-page"></a>

#### İlk Açılış Sayfası

Uygulamaya eğer domain adresinizden sonra herhangi bir kontrolör segmenti gönderilmezse uygulama ilk açılış sayfası için varsayılan bir kontrolör tanımlamasına ihtiyaç duyar. Varsayılan kontrolör <kbd>app/routes.php</kbd> dosyasında tanımlı olmadığında uygulama hata verecektir.

Bu nedenle route dosyanızı açıp varsayılan kontrolör sınıfınızı defaultPage() metodu ile aşağıdaki gibi belirlemeniz gerekir.

```php
$router->domain($c['config']['url']['webhost']);
$router->defaultPage('welcome/index');
```

<a name="annotations"></a>

### Anotasyonlar

Bir anotasyon aslında bir metadata yı (örneğin yorum,  açıklama, tanıtım biçimini) yazıya, resime veya diğer veri türlerine tutturmaktır. Anotasyonlar genellikle orjinal bir verinin belirli bir bölümümü refere ederler.

Not: Anotasyonlar herhangi bir kurulum yapmayı gerektirmez ve uygulamanıza performans açısından ek bir yük getirmez. Php ReflectionClass sınıfı ile okunan anotasyonlar çekirdekte herhangi bir düzenli ifade işlemi kullanılmadan kolayca çözümlenir.

Şu anki sürümde biz anotasyonları sadece <kbd>Http Katmanlarını</kbd> atamak ve <kbd>Event</kbd> sınıfına tayin edilen <kbd>Olayları Dinlemek</kbd> için kullanıyoruz.

<a name="enabling-annotations"></a>

#### Anotasyonları Aktif Etmek

Config.php konfigürasyon dosyasını açın ve <kbd>annotations > enabled</kbd> anahtarının değerini <kbd>true</kbd> olarak güncelleyin.

```php
'extra' => [
    'annotations' => true,
],
```

Anotasyonlar hakkında daha fazla bilgiye [Annotations.md](Annotations.md) dökümentasyonundan ulaşabilirsiniz.

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
            <td><kbd>setContainer()</kbd></td>
            <td>Controller sınıfı içerisine container enjekte etmek için kullanılır. </td>
        </tr>
        <tr>
            <td><kbd>getContainer()</kbd></td>
            <td>Controller sınıfı içerisinden container nesnesine erişmek için kullanılır. </td>
        </tr>
    </tbody>
</table>
