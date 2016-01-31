
## Görünümler

Bir view dosyası basitçe html başlık ve gövdesinden oluşan bütün bir web sayfası olabileceği gibi, belirli bir sayfanın parçası (header, footer, sidebar) da olabilir.

<ul>
    <li>
        <a href="#loading-class">View Sınıfı</a>
        <ul>
            <li><a href="#serviceProvider">Servis Sağlayıcı</a></li>
            <li><a href="#addFolder">$this->view->addFolder()</a></li>
            <li><a href="#load">$this->view->load()</a></li>
            <li><a href="#get">$this->view->get()</a></li>
            <li><a href="#getStream">$this->view->getStream()</a></li>
            <li>
                <a href="#variables">Değişkenler Atamak</a> 
                <ul>
                    <li><a href="#array">Array yöntemi</a></li>
                    <li><a href="#assign">$this->view->assign()</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        <a href="#layers">Katmanlar</a>
        <ul>
            <li>
              <a href="#controllers">View Controller</a>
                <ul>
                    <li><a href="#layerGet">$this->layer->get()</a></li>
                </ul>
              </li>
        </ul>
    </li>
</ul>

<a name="loading-class"></a>

### View Sınıfı

Bir view dosyası bir modüle ait ise modül dizini içerisindeki <kbd>/modules/$moduleName/view</kbd> klasörü içerisinden, bir kontrolöre bağlı bir view dosyası ise <kbd>/modules/views/</kbd> klasöründen çağrılır.

<a name="serviceProvider"></a>

#### Servis Sağlayıcı

View sınıfına ait servis sağlayıcısı <kbd>app/providers.php</kbd> dosyasından yönetilir.

```php
$container->share('view', 'Obullo\View\View')
    ->withArgument($container)
    ->withArgument($container->get('logger'))
    ->withArgument($config->getParams())
    ->withMethodCall(
        'addFolder',
        [
            'templates',
            TEMPLATES
        ]
    );
```

Servis sağlayıcısı view sınıfına ait önkonfigurasyonu yönetir.

<a name="addFolder"></a>

#### $this->view->addFolder();

Görünüm sınıfına klasörler konfigüre eder.

```php
$this->view->addFolder('templates', '/resources/templates/');
```

Tanımladığınız klasörden view dosyalarına ulaşmak için <kbd>:</kbd> karakteri kullanılır.

```php
$string = $this->view->get('templates::maintenance');
```

<a name="load"></a>

#### $this->view->load();

```php
$this->view->load(
  'welcome', 
  [
    'foo' => 'bar'
  ]
);
```

Welcome kontrolör dosyasında olduğu gibi kontrolör dosyası bir modül içerisinde değilse <kbd>/modules/views/view</kbd> klasöründe oluşturulmalıdır.

```php
<html>
<head>
  <title>Welcome</title>
</head>

<body>
  <?php echo $foo ?>
</body>
</html>
```

View klasörü içerisinde iç içe klasörler açılabilir. 

```php
echo $this->view->load('subfolder/filename');
```

<a name="get"></a>

#### $this->view->get();

Views modülü Footer kontrolör dosyasında olduğu gibi eğer bir view dosyası <kbd>$response</kbd> nesnesi içerisine gönderilmek yerine <kbd>string</kbd> türünde alınmak isteniyorsa get metodu kullanılır.

```php
namespace Views;

use Obullo\Http\Controller;

class Footer extends Controller
{
    public function index()
    {        
        echo $this->view->get(
            'footer',
            [
                'footer' => 'Footer Controller'
            ]
        );
    }
}
```

<a name="variables"></a>

#### Değişkenler Atamak

View nesnesine load metotlarının ikinci parameteresinden array türünde veri göndererek yada assign metodu ile değişkenler atanabilir.

<a name="array"></a>

##### Array Yöntemi

```php
$this->view->load(
  'welcome', 
  [
    'foo' => 'bar',
    'colors' => [
        'red',
        'green',
        'blue',
    ]
  ]
);
```

<a name="assign"></a>

##### $this->view->assign();

Assign metodu da değişken atamak için diğer bir alternatiftir.

```php
$this->view->assign('foo', 'bar');
```

String türü veya aşağıdaki array türü ile veriler gönderilebilir.

```php
$this->view->assign(
  [
     'key' => 'value'
  ]
);
```

<a name="getStream"></a>

##### $this->view->getStream();

Varolan bir görünüm dosyası ile bir response gövdesi oluşturmak için kullanılır. Bu metot <kbd>Obullo\Http\Stream</kbd> nesnesine geri döner.

```php
$body = $this->view->getStream('templates::maintenance');

return $response->withStatus(404)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

<a name="layers"></a>

### Katmanlar

> Obullo çerçevesinde katman paketi sayesinde view dosyaları kontrolör dosyaları içerisinden zincirleme olarak içe içe çağırılabilir. Bu da her view dosyasına ait bir kontrolör dosyasının yaratılabileceği anlamına gelir. Bknz. [Layer.md](Layer.md)

![Layers](images/layer-ui-components.png?raw=true "")

Bu mimariyi kullanmanın faydalarını aşağıdaki gibi sıralayabiliriz.

* <b>Arayüz Tutarlılığı:</b> Katmanlı programlama görünen varlıkları ( views ) kesin parçalara ayırır ve her bölüm kendisinden sorumlu olduğu fonksiyonu çalıştırır ( view controller ) böylece her katman bir layout yada widget hissi verir.
* <b>Bakımı Kolay Uygulamalar:</b> Parçalara bölünen kullanıcı arayüzü bileşenleri MVC tasarım desenine bağlı kaldıkları için bakım kolaylığı sağlarlar.
* <b>Mantıksal Uygulamalar:</b> Katmanlar birbirleri ile etkişim içerisinde olabilecekleri gibi uygulama üzerinde hakimiyet ve önbelleklenebilme özellikleri ile genişleyebilir mantıksal uygulamalar yaratmayı sağlarlar. Bölümsel olarak birbirinden ayrılan katmanlar bir <kbd>web servis</kbd> gibi de çalışabilirler. Oluşturulan her katmana bir http yada ajax isteği ile ulaşılabilir.


<a name="controllers"></a>

#### View Controller

View kontrolörler <kbd>/modules/views/</kbd> klasörü içerisinde oluşturulan kontrolör dosyalarıdır. Bu dosyalara dışarıdan http yada ajax isteği ile yada içeriden aşağıdaki gibi

```php
$output = $this->layer->get('views/$controller');
```

metodu ile ulaşılabilmektedir.

<a name="layerGet"></a>

#### $this->layer->get()

Layer get metodu bir kontrolör dosyasını uygulamanız içerisinden GET metodu ile çağırmanızı sağlar. Katmanlar ile aşağıdaki gibi bir navigasyon menü bir view kontrolör aracılığı ile kolayca yaratılabilir.

```php
namespace Examples\Layers;

use Obullo\Http\Controller;

class Navbar extends Controller
{
    public function index()
    {   
        $this->view->load(
            'navbar',
            [
                'header' => $this->layer->get('views/navbar'),
                'footer' => $this->layer->get('views/footer'),
            ]
        );
    }
}
```

Views klasöründen çağrılan navbar adlı kontrolör dosyasının içeriği.

```php
namespace Views;

use Obullo\Http\Controller;

class Navbar extends Controller
{
    public function index()
    {
        $link = ($this->app->request->get('link')) ? $this->app->request->get('link') : 'welcome';

        $li = '';
        $navbar = [
            'welcome' => 'Welcome',
            'about'   => 'About', 
            'contact' => 'Contact',
        ];
        foreach ($navbar as $key => $value) {
            $class = '';
            if ($link == $key) {
                $class = ' class="active" ';
            }
            $li.= "<li $class>".$this->url->anchor('examples/layers/navbar?link='.$key, $value)."</li>";
        }
        echo $this->view->get(
            'navbar',
            [
                'li' => $li
            ]
        );
    }
}
```

Projenizdeki örnekler modülüne giderek çalışan versiyonu görebilirsiniz.

```php
http://myproject/examples/layers/navbar
```

Katmanların nasıl çalıştığını anlamak için oluşturduğunuz bir katmana ajax yada http isteği ile dışarıdan ulaşmayı deneyin.

```php
http://myproject/views/navbar
```
