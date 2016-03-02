
## Kontrolör Sınıfı

Kontrolör sınıfı uygulamanın kalbidir ve uygulamaya gelen HTTP isteklerinin nasıl yürütüleceğini kontrol eder.

<ul>
    <li><a href="#controller">Kontrolör Nedir ?</a></li>
    <li><a href="#folders">Klasörler</a></li>
    <li><a href="#primary-folders">Birincil Klasörler</a></li>
    <li><a href="#proxy-way">Proxy Yöntemi Nedir ?</a></li>
    <li><a href="#arguments">Argümanlar</a></li>
    <li><a href="#middlewares">Http Katmanları</a></li>
    <li><a href="#welcome-page">İlk Açılış Sayfası</a></li>
    <li><a href="#annotations">Anotasyonlar</a></li>
    <li><a href="#reserved-methods">Rezerve Edilmiş Metotlar</a></li>
</ul>

Kontrolör dosyaları http route çözümlemesinden sonra <kbd>folders/</kbd> klasöründen çağrılarak çalıştırılır.

Bir http GET isteği çözümlemesi

```php
$router->post('product/list', 'shop/product/list'); 
```

Bir http POST isteği çözümlemesi

```php
$router->post('product/post', 'shop/product/post'); 
```

Route çözümlemeleri ilgili daha fazla bilgi için [Router.md](Router.md) dosyasını gözden geçirebilirsiniz.

<a name="controller"></a>

### Kontrolör Nedir ?

Kontrolör dosyaları uygulamada http adres satırından çağrıldığı ismi ile bağlantılı olarak çözümlenebilen php sınıflarıdır. Kontrolör dosyaları uygulamada <kbd>app/folders/</kbd> klasörü altında çalışırlar.

Örnek bir http isteği.

```php
http://example.com/index.php/welcome
```

ve bu isteğe ait kontrolör dosyası.

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

<kbd>HelloWorld</kbd> gibi birden fazla kelime içeren bir kontrolör varsa sadece <kbd>ikinci</kbd> kelime büyük yazılmalı,

```php
example.com/index.php/examples/helloWorld
```

yada tüm kelimeler büyük yazılmalıdır.


```php
example.com/index.php/examples/HelloWorld
```

Aksi durumda kontrolör <kbd>helloworld</kbd> olarak çağrılırsa sayfaya ulaşılamaz.

<a name="folders"></a>

### Klasörler

Klasör içerisine konulan dosyalar <kbd>app/folders/klasöradı/</kbd> gibi bir dizin içinde ve php <kbd>namespace</kbd> ler ile çalışırlar. Klasörler kullanarak çalışmak uygulama esnekliğini arttırır ve mantıksal uygulamalar yaratmanızı sağlar. 

```php
example.com/index.php/examples/
```

Yukarıdaki adres satırı <kbd>examples</kbd> adlı dizin altında bulunan <kbd>Examples.php</kbd> isimli kontrolör dosyasını çağırır.

```php
namespace Examples;

use Obullo\Http\Controller;

class Examples extends Controller
{
    public function index()
    {
        $this->view->load('examples');
    }
}
```

Dizin ve kontrolör adı aynı ise uygulama bu kontrolör dosyasını <kbd>index</kbd> kontrolör olarak çözümler.

<a name="primary-folders"></a>

### Birincil Klasörler

Birincil klasörler, bir alt klasörü olan klasöre verilen addır.Örnek verirsek, uygulamanızda <kbd>app/folders/examples/captcha/</kbd> adlı bir dizin ve altında <kbd>Ajax.php</kbd> adlı bir kontrolörümüzün olduğunu varsayalım.

Bu dosyayı çözümlemek için ziyaret edeceğimiz adres aşağıdaki gibi olur.

```php
http://framework/examples/captcha/ajax
```

Bu çözümlemede en dıştaki klasör <kbd>birincil</kbd>, sonraki klasör ise alt klasördür.

```php
namespace Examples\Captcha;

use Obullo\Http\Controller;

class Ajax extends Controller
{
    public function index()
    {
        echo $this->uri->segment(0);  // examples
        echo $this->uri->segment(1);  // captcha

        echo $this->router->getPrimaryFolder();  // examples
        echo $this->router->getFolder();         // captcha
    }
}
```
Bir birincil klasör altına en fazla bir adet klasör açılabilir.

<a name="proxy-way"></a>

### Proxy Yöntemi Nedir ?

Proxy yöntemi <kbd>$container->get()</kbd> yazımının kolaylaştırmak için kullanılır ve aşağıdaki gibi <kbd>$this</kbd> ile bir servise ulaşılmaya çalışıldığında devreye girer.

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

<kbd>$this</kbd> ile çağırılan sınıflar,

```php
$this->class
```

<kbd>Obullo\Http\Controller</kbd> sınıfı içerisindeki __get() metodu aracılığı ile konteyner içerisinden çağırılmış olurlar.

```php
public function __get($class)
{
    return $this->container->get($class);
}
```

Çağırılan kütüphaneler <kbd>app/providers.php</kbd> dosyası aracılığı ile konteyner içerisine tanımlanmış <kbd>servis</kbd> ler olmalıdırlar.

<a name="arguments"></a>

### Argümanlar

Eğer adres satırında bir metot dan sonra gelen segmentler birden fazla ise bu segmentler metot argümanları olarak çözümlenir. Örneğin aşağıdaki gibi bir url adresimizin olduğunu varsayalım:

```php
example.com/products/computer/index/desktop/123
```

Products klasörü altına Computer.php adlı bir sınıf oluşturun.

```php
-  app
-  folders
    - products
        Computer.php
```

Yukarıdaki url adresi tarayıcıda çalıştırıldığında URI sınıfı tarafından segmentler aşağıdaki gibi çözümlenirler.

* products (0)
* computer (1)
* index (2)
* desktop (3)
* 123 (4)

```php
namespace Products;

use Obullo\Http\Controller;

class Computer extends Controller
{
    public function index($type, $id)
    {
        echo $type;  // desktop
        echo $id;    // 123
        
        echo $this->uri->segment(0);    // products
        echo $this->uri->segment(1);    // computer
        echo $this->uri->segment(2);    // index
        echo $this->uri->segment(3);    // desktop
        echo $this->uri->segment(4);    // 123

        echo $this->router->getFolder();  // products
        echo $this->router->getPrimaryFolder();  // null
    }
}

/* Location: .modules/products/computer.php */
```

<a name="middlewares"></a>

### Http Katmanları

Http katmanları http çözümlemesinden önce <kbd>$request</kbd> yada <kbd>$response</kbd> nesnelerini etkilemek için kullanılırlar. Katmanlar <kbd>app/classes/Http/Middlewares</kbd> klasörü içerisinde yeralan php sınıflarıdır. Bir katman route yapısında tutturulabilir yada uygulamada evrensel olarak çalışabilir. Daha fazla bilgi için [App-Middlewares.md](App-Middlewares.md) dökümentasyonunu inceleyebilirsiniz.

<a name="welcome-page"></a>

### İlk Açılış Sayfası

Uygulamanıza gelen bir adrese aşağıdaki gibi herhangi bir segment girilmezse,

```php
http://example.com/
```

router sınıfı ilk açılış sayfası için varsayılan bir kontrolör tanımlamasına ihtiyaç duyar. Bu nedenle <kbd>app/routes.php</kbd> dosyanızı açıp varsayılan kontrolör adresinizi <kbd>defaultPage</kbd> anahtarından konfigüre etmeniz gerekir.

```php
$router->configure(
    [
        'domain' => 'example.com',
        'defaultPage' => 'welcome/index',
    ]
);
```

<a name="annotations"></a>

### Anotasyonlar

Bir anotasyon aslında bir metadata yı (örneğin yorum,  açıklama, tanıtım biçimini) yazıya, resime veya diğer veri türlerine tutturmaktır. Anotasyonlar genellikle orjinal bir veriyi yada işlemi refere ederler. Şu anki sürümde anotasyonlar sadece <kbd>Http Katman</kbd> işlemleri için kullanılıyor.

Anotasyonları aktif etmek için <kbd>config.php</kbd>  dosyasını açın ve <kbd>annotations</kbd> anahtarının değerini <kbd>true</kbd> olarak güncelleyin.

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
            <td><kbd>__get()</kbd></td>
            <td>Container nesnesinden servislere ulaşmak için kullanılır.</td>
        </tr>
        <tr>
            <td><kbd>__set()</kbd></td>
            <td>Controller sınıfı içerisine nesne değerleri enjekte etmek için kullanılır. </td>
        </tr>
    </tbody>
</table>
