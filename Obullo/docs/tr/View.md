
## Görünümler

Bir view dosyası basitçe html başlık ve gövdesinden oluşan bütün bir web sayfası olabileceği gibi, belirli bir sayfanın parçası (header, footer, sidebar) da olabilir.

<ul>
    <li>
        <a href="#loading-class">View Sınıfı</a>
        <ul>
            <li><a href="#engines">Görünüm Motorları</a></li>
            <li><a href="#serviceProvider">Servis Sağlayıcı</a></li>
            <li><a href="#addFolder">$this->view->addFolder()</a></li>
            <li><a href="#load">$this->view->load()</a></li>
            <li><a href="#get">$this->view->get()</a></li>
            <li><a href="#withStream">$this->view->withStream()</a></li>
            <li><a href="#withData">$this->view->withData()</a></li>
        </ul>
    </li>

    <li>
        <a href="#layers">Katmanlar</a>
        <ul>
            <li>
              <a href="#controllers">View Kontrolör</a>
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

<a name="engines"></a>

#### Görünüm Motorları

View sınıfı görünüm dosyalarını işlemek için harici kütüphaneler kullanır. View sınıfının hangi motoru kullanacağı servis sağlayıcısı üzerinden <kbd>engine</kbd> konfigürasyonu ile belirlenir. Takip eden liste kullanılabilceğiniz motorları gösteriyor.

* Native ( Varsayılan )
* <a href="http://platesphp.com/" target="_blank">Plates</a>

Varsayılan ( Native ) görünüm motoru ile daha yüksek performans elde etmeniz muhtemeldir, fakat daha gelişmiş özelliklere ihtiyacınız varsa diğer görünüm motorlarını da tercih edebilirsiniz.

<a name="serviceProvider"></a>

#### Servis Sağlayıcı

View sınıfına ait servis sağlayıcısı <kbd>app/providers.php</kbd> dosyasından yönetilir.

```php
$container->share('view', 'Obullo\View\View')
    ->withArgument($container)
    ->withArgument($container->get('logger'))
    ->withArgument(
        [
            'engine' => 'Obullo\View\Plates\Plates',
        ]
    )
    ->withMethodCall(
        'addFolder',
        [
            'views',
            FOLDERS .'views/view/'
        ]
    )
    ->withMethodCall(
        'addFolder',
        [
            'templates',
            RESOURCES.'/templates/'
        ]
    );
```

Servis sağlayıcısı view sınıfına ait önkonfigurasyonu yönetir.

<a name="addFolder"></a>

#### $this->view->addFolder()

Görünüm sınıfına klasörler konfigüre eder.

```php
$this->view->addFolder('templates', '/resources/templates/');
```

Tanımladığınız klasörden view dosyalarına ulaşmak için <kbd>::</kbd> sembolü kullanılır.

```php
$string = $this->view->get('templates::maintenance');
```

<a name="load"></a>

#### $this->view->load()

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

<a name="get"></a>

#### $this->view->get()

Bir view dosyası <kbd>$response</kbd> nesnesi içerisine gönderilmek yerine <kbd>string</kbd> türünde alınmak isteniyorsa get metodu kullanılır.

<a name="withStream"></a>

#### $this->view->withStream()

Daha çok bir görünüm dosyası ile bir response gövdesi oluşturmak için kullanılır. Bu metod <kbd>Obullo\Http\Stream</kbd> nesnesine geri döner.

```php
$body = $this->view->withStream()->get('templates::maintenance');

return $response->withStatus(404)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

yada string türünde bir girdi ile stream nesnesi aşağıdaki gibi elde edilebilir.

```php
$body = $this->view->withStream("Example view")->get();
```

<a name="withData"></a>

#### $this->view->withData()

Değişken atamak için diğer bir alternatiftir.

```php
$this->view->withData('foo', 'bar');
```

String türü veya aşağıdaki gibi array türü ile veriler gönderilebilir.

```php
$this->view->withData(
  [
     'key' => 'value'
  ]
);
```

<a name="layers"></a>

### Katmanlar

Çerçeve içerisinde katman paketi sayesinde view dosyaları kontrolör dosyaları içerisinden zincirleme olarak içe içe çağırılabilir. Bu da her view dosyasına ait bir kontrolör dosyasının yaratılabileceği anlamına gelir. Bknz. [Layer.md](Layer.md)

![Layers](images/layer-ui-components.png?raw=true "")

Bu mimariyi kullanmanın faydalarını aşağıdaki gibi sıralayabiliriz.

* <b>Arayüz Tutarlılığı:</b> Katmanlı programlama görünen varlıkları ( views ) kesin parçalara ayırır ve her bölüm kendisinden sorumlu olduğu fonksiyonu çalıştırır ( view controller ) böylece her katman bir layout yada widget hissi verir.
* <b>Bakımı Kolay Uygulamalar:</b> Parçalara bölünen kullanıcı arayüzü bileşenleri MVC tasarım desenine bağlı kaldıkları için bakım kolaylığı sağlarlar.
* <b>Mantıksal Uygulamalar:</b> Katmanlar birbirleri ile etkişim içerisinde olabilecekleri gibi uygulama üzerinde hakimiyet ve önbelleklenebilme özellikleri ile genişleyebilir mantıksal uygulamalar yaratmayı sağlarlar. Bölümsel olarak birbirinden ayrılan katmanlar bir <kbd>web servis</kbd> gibi de çalışabilirler. Oluşturulan her katmana bir http yada ajax isteği ile ulaşılabilir.


<a name="controllers"></a>

#### View Kontrolör

View Kontrolör <kbd>/folders/views/</kbd> klasörü içerisinde oluşturulan kontrolör dosyalarıdır. Bu dosyalara dışarıdan http yada ajax isteği ile, yada içeriden aşağıdaki gibi

```php
$output = $this->layer->get('views/$controllerName');
```

metodu ile ulaşılabilmektedir.

<a name="layerGet"></a>

##### $this->layer->get()

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

Eğer bir uygulamada en dıştaki yani ilk oluşturulan katmana ait <kbd>$request</kbd> nesnesi değerlerine ulaşılmak isteniyorsa <kbd>$this->request</kbd> yöntemi yerine <kbd>$this->app->request</kbd> yöntemi kullanılmalıdır.


```php
$this->app->request->get('link')
```

Böylece katman isteklerinden sonra değişime uğramamış orijinal request nesnesine ulaşılmış olur.