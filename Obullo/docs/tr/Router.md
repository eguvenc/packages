
## Router Sınıfı

Router sınıfı uygulamanızda index.php dosyasına gelen istekleri <kbd>app/routes.php</kbd> dosyanızdaki route tanımlamalarına göre url yönlendirme, http katmanı çalıştırma, http isteklerini filtreleme gibi işlevleri yerine getirir.

<ul>

<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#domain-name">Alan Adı</a></li>
        <li><a href="#index.php">Index.php</a></li>
        <li><a href="#default-page">Varsayılan Açılış Sayfası</a></li>
        <li><a href="#404-errors">404 Hata Yönetimi</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#loading">Bileşeni Yüklemek</a></li>
        <li><a href="#url-rewriting">Url Yönlendirme</a></li>
    </ul>
</li>

<li>
    <a href="#routing">Route Kuralları Oluşturmak</a>
    <ul>
        <li><a href="#route-types">İstek Türleri</a></li>
        <li><a href="#regex">Düzenli İfadeler</a></li>
        <li><a href="#closures">İsimsiz Fonksiyonlar</a></li>
        <li><a href="#parameters">Parametreler</a></li>
        <li><a href="#route-groups">Route Grupları</a></li>
        <li><a href="#sub-domains">Alt Alan Adları ve Gruplar</a></li>
        <li><a href="#regex-sub-domains">Alt Alan Adları ve Düzenli İfadeler</a></li>
        <li><a href="#uri-match">Url Eşleşmesi ve Düzenli İfadeler</a></li>
    </ul>
</li>

<li>
    <a href="#middlewares">Http Katmanlarını Route Kurallarına Atamak</a>
    <ul>
        <li><a href="#route-md-assignment">Bir Kural İçin Katman Çalıştırmak</a></li>
        <li><a href="#group-md-assignment">Bir Gruba Katman Atamak</a></li>
        <li><a href="#inside-group-md-assignment">Bir Grup İçinden Katman Atamak</a></li>
        <li><a href="#regex-md">Düzenli İfadeler Kullanmak</a></li>
    </ul>
</li>

<li>
    <a href="#additional-info">Ek Bilgiler</a>
    <ul>
        <li><a href="#modules">Modüller</a></li>
    </ul>
</li>

<li><a href="#method-reference">Fonksiyon Referansı</a></li>

</ul>

<a name="configuration"></a>

### Konfigürasyon

Router sınıfı konfigürasyon değerlerini aldıktan sonra router kurallarınızı çalıştırmaya başlar bu yüzden <kbd>$c['router']->configuration()</kbd> metodunun en tepede ilan edilmesi gerekir.

<a name="domain-name"></a>

#### Alan Adı

Router sınıfı url yönlendirmelerini çalıştırabilmek için geçerli <b>kök domain</b> adresini bilmek zorundadır. Domain adresini aşağıdaki gibi tanımlayabilir,

```php
$c['router']->configuration(
    [
        'domain' => 'example.com',
        'defaultPage' => 'welcome',
        'error404' => null
    ]
);
```

ya da ana konfigürasyon dosyasından gelmesini sağlayabilirsiniz.

```php
$c['config']['url']['webhost']; 
```

Kök domain adresinizi başında <b>"www."</b> öneki olmadan girin.

```php
myproject.com 
```

<a name="index.php"></a>

#### Index.php dosyası

Bu dosyanın tarayıcıda gözükmemesini istiyorsanız bir <kbd>.htaccess</kbd> dosyası içerisine aşağıdaki kuralları yazmanız yeterli olacaktır.

```php
Options -Indexes

RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]

RewriteCond $1 !^(index\.php|resources/assets|robots\.txt)
RewriteRule ^(.*)$ ./index.php/$1 [L,QSA]
```

* Dosyadaki ilk kod bloğu güvenlik amacıyla dizin indexlemeyi kapatır.
* İkinci kod bloğu tüm dosya ve dizin isteklerini güvenlik amacıyla index.php dosyasına yönlendirir.
* Son kod bloğu ise sadece parentezler ( ) içerisinde olan dosya ve dizinler için direkt dosya erişimine izin verir.

> **Not:** .htaccess dosyanızın çalışabilmesi için sunucunuzda apache mod_rewrite modülünün etkin olması gerekir.

<a name="default-page"></a>

#### Varsayılan Açılış Sayfası

Konfigürasyon kısmında defaulPage anahtarı varsayılan açılış sayfasına ait kontrolör dosyasını belirler.

```php

$c['router']->configuration(
    [
        'domain' => 'example.com',
        'defaultPage' => 'home/class/index',
        'error404' => null
    ]
);
```

Eğer varsayılan sayfa konfigüre edilmemişse <kbd>welcome/index</kbd> sayfası görüntülenir.

<a name="404-errors"></a>

#### 404 Hata Yönetimi

Error404 anahtarı 404 hataları olması durumunda uygulamanın çalıştıracağı kontrolör dosyasını belirler. Null değeri girerseniz uygulama resources/templates klasöründen varsayılan şablonu yükler.

```php

$c['router']->configuration(
    [
        'domain' => 'example.com',
        'defaultPage' => 'home/class/index',
        'error404' => 'errors/pageNotFound'
    ]
);
```

Yuklarıdaki tanımlamadan değer herhangi bir 404 hatası oluştuğunda uygulama <kbd>errors/</kbd> dizini altında <kbd>PageNotFound</kbd> kontrolör dosyasını çalıştırır.

<a name="running"></a>

### Çalıştırma

Bileşen konteyner içerisinden çağırıldığında tanımlı olan router metotlarına ulaşılmış olur. 

<a name="loading"></a>

#### Bileşeni Yüklemek

```php
$this->c['router']->method();
```

<a name="url-rewriting"></a>

#### Url Yönlendirme

Tipik olarak bir URL girdisi ile ve ona uyan dizin arasında (<kbd>dizin/sınıf/metot/argümanlar</kbd>)  birebir ilişki vardır. Bir URI içerisindeki bölümler aşağıdaki kalıbı izler.

```php
example.com/dizin/sınıf/metot/id
```

Aşağıdaki URL takip eden dizin yapısındaki dosyayı çalıştırır.

```php
example.com/index.php/shop/product/index/1
```

Birebir ilişkili kontrolör sınıfınının görünümü

```php
- modules
  - shop
      Product.php
```

Fakat bazı durumlarda bu birebir ilişki yerine farklı <kbd>dizin/sınıf/method</kbd> ilişkisi yeniden kurgulanmak istenebilir. Örnek vermek gerekirse mevcut URL adreslerinizin aşağıdaki gibi olduğunu varsayalım.

```php
example.com/shop/product/index/1
example.com/shop/product/index/2
```

Normalde URL nin 2. bölümü sınıf ismi için rezerve edilmiştir, fakat yukarıdaki örnekte <b>shop</b> ve <b>product</b> bölümünü silip sadece değerler ile gönderilen bir URL biçimine dönüştürmek (url rewriting) için bir route kuralı tanımlamanız gerekir.

```php
$c['router']->get('([0-9])/(.*)', 'shop/product/index/$1/$2');
```

Bu tanımlamadan sonra aşağıdaki gibi bir URL <b>shop</b> dizinine yönlendirilir ve product sınıfı çalıştırılarak sonraki değerler argüman olarak gönderilir.  

```php
example.com/2/mp3-player
```

> **Not:**  Varsayılan metot her zaman <b>index</b> metodudur fakat açılış sayfasında bu metodu yazmanıza gerek kalmaz. Eğer argüman göndermek zorundaysanız bu durum da index metodunu yazmanız gerekir.


<a name="routing"></a>

### Route Kuralları Oluşturmak

Uygulama içerisindeki tüm route kuralları <kbd>app/routes.php</kbd> dosyası içerisinde tutulur. Route kurallarında düzenli ifadeler kullanılabilir.

```php
http://example.com/54/test/whatever
```
Yukarıdaki gibi bir URL adresini framework içerisinde başka bir route adresine yönlendirmek istiyorsanız aşağıdaki gibi bir route kuralı yazmanız gerekir.

```php
$c['router']->get('([0-9]+)/([a-z]+).*', 'welcome/index/$1/$2');
```

Bu kurala göre URL adresi ancak ilk bölümü 0-9 sayıları arasında olan, ikinci bölümü a-z karakterkerini içeren ve üçüncü bölümü herhangi bir değerden oluşan adres <kbd>welcome/index</kbd> sayfasına yönlendirilir.

<b>GET kuralı</b> - example.com/welcome/ örnek url adresine gelen http get isteklerini girilen değere yönlendirir.

```php
$c['router']->get('welcome(.*)', 'home/index/$1');
```

Route kuralları <kdd>düzenli ifadeler</kdd> (regex) yada <kbd>/wildcards</kbd> kullanılarak tanılanabilir.

<b>POST kuralı</b> - example.com/welcome/ örnek url adresine gelen http post isteklerini girilen değere yönlendirir.

```php
$c['router']->post('welcome/(.+)', 'home/index/$1');
```

<b>Birden fazla http isteğini kabul etmek</b> ( GET, POST, DELETE, PUT ve diğerleri )

```php
$c['router']->match(['get','post'], 'welcome/(.+)', 'home/index/$1');
```

yukarıdaki örnekte eğer bir URL "welcome/$arg/$arg .." değerini içeriyorsa gelen argümanlar "home/home/index/$arg" yani home dizini içerisinde home sınıfı index metoduna gönderilir.

```php
$c['router']->put('welcome(.*)', 'home/index/$1');
```

Eğer yukarıdaki gibi put metodu tanımlanmış bir kurala GET isteği gönderilirse "Http Error 405 Get method not allowed" hatası ile karşılaşırsınız.

```php
$c['router']->get(
    'welcome/index', null,
    function () use ($c) {
        $c['view']->load('dummy');
    }
);
```

Eğer rewrite özelliğini kullanmak istemiyorsanız rewrite parametresine yukarıdaki gibi null değeri girin.

<a name="route-types"></a>

#### İstek Türleri

Route kuralları yazıldığında aynı zamanda http isteklerini istek tipine göre filtrelemeyi sağlar. Aşağıdaki tablo route kuralları için mevcut http metotlarını gösteriyor.

<table>
  <thead>
    <tr>
    <th>Metot</th>
    <th>Açıklama</th>
    <th>Örnek</th>
    </tr>
  </thead>
  <tbody>
    <tr>
    <td>post</td>
    <td>Bir route kuralının sadece POST isteğinde çalışmasını sağlar.</td>
    <td>$c['router']->post($url, $rewrite)</td>
    </tr>
    <tr>
    <td>get</td>
    <td>Bir route kuralının sadece GET isteğinde çalışmasını sağlar.</td>
    <td>$c['router']->get($url, $rewrite)</td>
    </tr>
    <tr>
    <td>put</td>
    <td>Bir route kuralının sadece PUT isteğinde çalışmasını sağlar.</td>
    <td>$c['router']->put($url, $rewrite)</td>
    </tr>
    <tr>
    <td>delete</td>
    <td>Bir route kuralının sadece DELETE isteğinde çalışmasını sağlar.</td>
    <td>$c['router']->delete($url, $rewrite)</td>
    </tr>
    <tr>
    <td>match</td>
    <td>Bir route kuralının sadece girilen istek tiplerinde çalışmasını sağlar.</td>
    <td>$c['router']->match(['get','post'], $url, $rewrite)</td>
    </tr>
  </tbody>
</table>

<a name="regex"></a>

#### Düzenli İfadeler

Eğer regex yani düzenli ifadeler kullanmayı tercih ediyorsanız route kuralları içerisinde herhangi bir düzenli ifadeyi referans çağırımlı (back-references) olarak kullanabilirsiniz.

> **Not:** Eğer referans çağırımı kullanıyorsanız çift backslash kullanmak yerine dolar $ işareti kullanmanız gerekir.

Tipik bir referanslı regex örneği.

```php
$c['router']->get('([0-9]+)/([a-z]+)', 'welcome/index/$1/$2');
```

Yukarıdaki örnekte <kbd>example.com/1/test</kbd> adresine benzer bir URL <kbd>Welcome/welcome</kbd> kontrolör sınıfı index metodu parametresine <kbd>1 - 2</kbd> argümanlarını gönderir.

<a name="closures"></a>

#### İsimsiz Fonksiyonlar

Route kuralları içerisinde isimsiz fonksiyonlar da kullanabilmek mümkündür.

```php
$c['router']->get(
    'welcome/([0-9]+)/([a-z]+)', 'welcome/index/$1/$2', 
    function () use ($c) {
        $c['view']->load('dummy');
    }
);
```

Bu örnekte, <kbd>example.com/welcome/123/test</kbd> adresine benzer bir URL <kbd>Welcome/welcome</kbd>  kontrolör sınıfı index metodu parametresine <kbd>123 - test</kbd> argümanlarını gönderir, eğer url eşleşirse isimsiz fonksiyon çalıştırılır ve <kbd>.modules/welcome/view/</kbd> dizininden dummy.php adlı view dosyası yüklenir.

<a name="parameters"></a>

#### Parametreler

Eğer girilen bölümleri fonksiyon içerisinden belirli kriterlere göre parametreler ile almak istiyorsanız süslü parentezler { } kullanın.

```php
$c['router']->get(
    'welcome/index/{id}/{name}', null,
    function ($id, $name) use ($c) {
        $c['response']->error($id.'-'.$name);
    }
)->where(['id' => '([0-9]+)', 'name' => '([a-z]+)']);
```

Yukarıdaki örnekte <kbd>/welcome/index/123/test</kbd> adresine benzer bir URL <kbd>where()</kbd> fonksiyonu içerisine girilen kriterlerle uyuştuğunda isimsiz fonksiyon içerisine girilen fonksiyonu çalıştırır.

```php
welcome/index/123/test
```

Yukarıdaki örnek çalıştırıldığında, düzenli ifadeler route kuralı ile uyuşuyor ise sayfanın $id ve $name argümanlarından oluşan hata sayfasını çıktılaması gerekir.

```php
$c['router']->get(
    '{id}/{name}/{any}', 'shop/index/$1/$2/$3',
    function ($id, $name, $any) use ($c) {
        echo $id.'-'.$name.'-'.$any;
    }
)->where(array('id' => '([0-9]+)', 'name' => '([a-z]+)', 'any' => '(.*)'));
```

Bu örnekte ise <kbd>{id}/{name}/{any}</kbd> olarak girilen URI şeması <kbd>/123/electronic/mp3_player/</kbd> adresine benzer bir URL ile uyuştuğunda girdiğiniz düzenli ifade ile değiştirilir ve rewrite değerine <kbd>$1/$2/$3</kbd> olarak girdiğiniz URL argümanları isimsiz fonksiyona parametre olarak gönderilir.

Yukarıdaki route kuralının çalışabilmesi için aşağıdaki gibi bir URL çağırılması gerekir.

```php
shop.example.com/123/electronic/mp3_player
```

Gelişmiş bir örnek:

```php
$c['router']->get(
    'shop/{id}/{name}', null,
    function ($id, $name) use ($c) {
        
        $db = $c['database']->get(['connection' => 'default']);
        $db->prepare('SELECT * FROM products WHERE id = ?');
        $db->bindValue(1, $id, PARAM_INT);
        $db->execute();

        if ($db->row() == false) {
            $c['response']->error(
                sprintf(
                  'The product %s not found',
                  $name
                )
            );
        }
    }
)->where(['id' => '([0-9]+)', 'name' => '([a-z]+)']);
```

Bu örnekte ise <kbd>shop/{id}/{name}</kbd> olarak girilen URI şeması eğer <kbd>/shop/123/mp3_player</kbd> adresine benzer bir URL ile eşleşirse, parametre olarak alınan ID değeri veritabanı içerisinde sorgulanır ve bulunamazsa kullanıcıya bir hata mesajı gösterilir.

<a name="route-groups"></a>

#### Route Grupları

Route grupları bir kurallar bütününü topluca yönetmenizi sağlar. Grup kuralları belirli <b>alt domainler</b> için çalıştırılabildiği gibi belirli <b>http katmanlarına</b> da tayin edilebilirler. Örneğin tanımladığınız route grubunda belirlediğiniz http katmanlarının çalışmasını istiyorsanız grup tanımlamalarına katman isimlerini girdikten sonra <kbd>$this->attach()</kbd> metodu ile katmanı istediğiniz URL adreslerine tuturmanız gerekir. Birden fazla katman middleware dizisi içine girilebilir.

```php
$c['router']->group(
    [
        'name' => 'Test',
        'middleware' => array('MethodNotAllowed')
    ],
    function () {

        $this->attach('welcome');
        $this->attach('welcome/test');
    }
);
```

Bu tanımlamadan sonra eğer buna benzer bir URL <kbd>/welcome</kbd> çağırırsanız <b>MethodNotAllowed</b> katmanı çalışır ve aşağıdaki hata ile karşılaşırsınız.

```php
Http Error 405 Get method not allowed.
```

> **Not:** Route gurubu seçeneklerine isim (name) değeri girmek zorunludur.

<a name="sub-domains"></a>

#### Alt Alan Adları ve Gruplar

Eğer bir gurubu belirli bir alt alan adına tayin ederseniz grup içerisindeki route kuralları yalnızca bu alan adı için geçerli olur. Aşağıdaki örnekte <kbd>shop.example.com</kbd> alan adı için bir grup tanımladık.

```php
$c['router']->group(
    [
        'name' => 'Shop',
        'domain' => 'shop.example.com'
    ], 
    function () {

        $this->defaultPage('welcome');

        $this->get('welcome/(.+)', 'home/index');
        $this->get('product/([0-9])', 'product/list/$1');
    }
);
```

Tarayıcınızdan bu URL yi çağırdığınızda bu alt alan adı için tanımlanan route kuralları çalışmaya başlar.

```php
http://shop.example.com/product/123
```

Aşağıda <kbd>account.example.com</kbd> adlı bir alt alan adı için kurallar tanımladık.

```php
$c['router']->group(
    [
        'name' => 'Accounts',
        'domain' => 'account.example.com'
    ],
    function () {

        $this->get(
            '{id}/{name}/{any}', 'user/account/$1/$2/$3',
            function ($id, $name, $any) {
                echo $id.'-'.$name.'-'.$any;
            }
        )->where(array('id' => '([0-9]+)', 'name' => '([a-z]+)', 'any' => '(.+)'));
    }
);
```

Tarayıcınızdan aşağıdaki gibi bir URL çağırdığınızda bu alt alan adı için yazılan kurallar çalışmış olur.


```php
http://account.example.com/123/john/test
```

<a name="regex-sub-domains"></a>

#### Alt Alan Adları ve Düzenli İfadeler

Alt alan adlarınızda eğer <kbd>sports19.example.com</kbd>, <kbd>sports20.example.com</kbd>, <kbd>sports21.example.com</kbd> gibi değişen sayılar mevcut ise alan adı kısmında düzenli ifadeler kullanarak route grubuna alan adınızı tayin edebilirsiniz.

```php
$c['router']->group(
    [
        'name' => 'Sports',
        'domain' => 'sports.*\d.example.com',
        'middleware' => array('Maintenance')
    ],
    function ($subname) {

        echo $subname;  // sports20

        $this->defaultPage('welcome');
        $this->attach('.*');
    }
);
```

<a name="uri-match"></a>

#### Url Eşleşmesi ve Düzenli İfadeler

Eğer bir grubun URL den çağırılan değer ile eşleşme olduğunda çalışmasını istiyorsanız <kbd>match</kbd> ifadesi kullanmanız gerekir.

```php
$c['router']->group(
    [
        'match' => 'admin'
    ],
    function () use ($c) {

        // Admin modülüne ait kurallar
        // $this->get();
        // $this->post();
    }
);
```

Tarayıcınızdan Admin modülünü ziyaret ettiğinizde bu modülün ismi geçen route grupları çalışmış olur.

```php
http://example.com/admin/membership/login
```

Aynı anda uri ve domain eşleşmesi gerekiyorsa her iki ifadeyide kullanın.

```php
$c['router']->group(
    [
        'match' => 'admin'
        'domain' => 'example.com'
    ],
    function () use ($c) {

        // Admin modülüne ait kurallar
    }
);
```

Eğer düzenli bir ifade kullanmanız gerekiyorsa domain ifadesinde olduğu gibi match ifadesi de düzenli ifadeleri destekler.

```php
$c['router']->group(
    [
        'match' => 'admin/([0-9]+)/([a-z]+).*'
    ],
    function () use ($c) {

        // Admin modülüne ait kurallar
    }
);
```

<a name="middlewares"></a>

### Http Katmanlarını Route Kurallarına Atamak

Http katmanları tek bir route kuralına atanarak direkt çalıştırılabilecekleri gibi bir route grubuna da tutturulduktan sonra <kbd>attach()</kbd> metodu ile çalıştırılabilirler.

<a name="route-md-assignment"></a>

#### Bir Kural İçin Katman Çalıştırmak

Tek bir route kuralı için katmanlar atayabilmek mümkündür. Aşağıdaki örnekte <b>/hello</b> sayfasına güvenli olmayan bir get yada post isteği geldiğinde <b>welcome/index</b> sayfasına yönlendirilir ve [Https katmanı](Middleware-Https.md) çalıştırılarak istek <kbd>https://</kbd> protokolü ile çalışmaya zorlanır.

```php
$c['router']->match(['get', 'post'], 'hello$', 'welcome/index')->middleware(['Https']);
```

Eğer birden fazla katman çalıştırmak isterseniz katmanları bir dizi içerisinde girin.

```php
$c['router']->get('membership/restricted')->middleware(array('auth', 'guest'));
```

<a name="group-md-assignment"></a>

#### Bir Gruba Katman Atamak

Bir grup için oluşturulan katmanı grup fonksiyonu içerisinde çalıştırabilmek için <kbd>$this->attach()</kbd> metodu kullanılır.

```php
$c['router']->group(
    array('name' => 'shop', 'domain' => 'shop.example.com', 'middleware' => array('Https')), 
    function () {

        $this->get('welcome/.+', 'home/index');
        $this->get('product/{id}', 'product/list/$1');

        $this->attach('.*');
    }
);
```

<a name="inside-group-md-assignment"></a>

#### Bir Grup İçinden Katman Atamak

Aşağıdaki örnekte <kbd>http://</kbd> protokolüyle ile güvenli olmayan bir istek geldiğinde istek [Https katmanı](Middleware-Https.md) çalıştırılarak <kbd>https://</kbd> protokolü ile çalışmaya zorlanıyor. Ayrıca <kbd>orders/pay</kbd> ve <kbd>orders/pay/post</kbd> sayfalarındaki formlar için [Csrf katmanı](Middleware-Csrf.md) çalıştırılıyor.

```php
$c['router']->group(
    ['name' => 'SecurePayment', 'domain' => 'pay.example.com', 'middleware' => array('Https')],
    function () {

        $this->match(['get', 'post'], 'orders/pay')->middleware('Csrf');
        $this->match(['post'], 'orders/pay/post')->middleware('Csrf');
        
        $this->attach('.*');
    }
);
```

> **Not:** middleware(); fonksiyonu her bir route isteğine bir katman eklemenizi sağlar fakat gruba tayin edilen aynı isimde zaten genel bir katman var ise bu durumda route isteğine birer birer katman atamanız anlamsız olur böyle bir durumda ilgili katman uygulamaya yanlışlıkla iki kez eklenmiş olacaktır. Bu yüzden birer birer atanabilecek katman isimleri grup opsiyonu içerisinde kullanılmamalıdır.

<a name="regex-md"></a>
 
#### Düzenli İfadeler Kullanmak

Bir grup içerisinde kullanılan katmanlar bazen URL adresinde belirli bölümler içerinde çalıştırılmak istenmeyebilir. Aşağıdaki gibi URL adreslerimizin olduğunu varsayalım.

```php
http://www.example.com/test/bad_segment
http://www.example.com/test/good_segment1
http://www.example.com/test/good_segment2
```

Buna benzer durumlarda aşağıdaki gibi katmanların sadece belirli URL adreslerinde çalışmasını sağlayabilirsiniz.

```php
$c['router']->group(
    ['name' => 'Test', 'domain' => 'example.com', 'middleware' => array('Test')],
    function () {

        $this->attach('^(test/(?!bad_segment).*)$');
    }
);
```

Veya aşağıdaki gibi katmanları sadece sadece belirli url parçalarını içeren kelimeler ile sınırlandararak tanımlanan sayfalar hariç tüm sayfalarda Auth ve Guest katmanları çalıştırılmasını sağlayabilirsiniz.

```php
$c['router']->group(
    ['name' => 'auth', 'domain' => 'example.com', 'middleware' => ['Auth', 'Guest']],
    function () {
        $this->attach('^(?!login|logout|test|cart|payment).*$');
    }
);
```

<a name="additional-info"></a>

### Ek Bilgiler

<a name="modules"></a>

#### Modüller

Alt klasörleri olan ve ana dizinleri kapsayan dizinlere modül adı verilir. Bir modül sadece içinde bulunan klasörleri kapsayan genel bir dizindir ve modül altında tekrar bir modül açılamaz. Modüllere ulaşmak için modül adı URL adresinden girilmelidir.

```php
example.com/admin/membership/login/index
```

Aşağıdaki örnekte shop klasörü bir dizin olarak görülüyor, <b>admin</b> klasörü ise bir modüldür ve membership isimli klasörü kapsar.

```php
- modules
    - shop
      + view
        Product.php
    - admin
        - membership
            + view
              - Login.php
              - Logout.php
        + dashboard
    + views

```

Bir modülün çözümlenebilmesi için kontrolörler içerisindeki <b>namespace</b> değeri aşağıdaki olmalıdır.

```php
namespace Admin\Membership;

class Login extends \Controller
{
    public function index()
    {
        // ..
    }
}
```

<a name="method-reference"></a>

#### Set Metotları

------

##### $c['router']->configuration(array $params);

Geçerli domain adresi, varsayılan açılış sayfasını ve 404 error sayfasını konfigüre eder.

##### $c['router']->defaultPage($page);

Konfigüre edilmiş varsayılan açılış sayfasını yeniden konfigüre eder.

##### $c['router']->error404($page);

Konfigüre edilmiş error 404 sayfasını yeniden konfigüre eder.

##### $c['router']->match(array $methods, string $match, string $rewrite, $closure = null)

Girilen http istek metotlarına göre bir route yaratır, istek metotları get,post,put ve delete metotlarıdır.

##### $c['router']->get(string $match, string $rewrite, $closure = null)

Http GET isteği türünde bir route kuralı oluşturur.

##### $c['router']->post(string $match, string $rewrite, $closure = null)

Http POST isteği türünde bir route kuralı oluşturur.

##### $c['router']->put(string $match, string $rewrite, $closure = null)

Http PUT isteği türünde bir route kuralı oluşturur.

##### $c['router']->delete(string $match, string $rewrite, $closure = null)

Http DELETE isteği türünde bir route kuralı oluşturur.

##### $c['router']->group(array $options, $closure);

Bir route grubu oluşturur.

##### $c['router']->where(array $replace);

Bir route kuralı parameterelerini girilen düzenli ifadeler ile değiştirir.

##### $c['router']->attach(string $route|$regex)

Geçerli grubun katmanlarını route grubuna tayin eder.

##### $c['router']->middleware(string|array $middlewares);

Bir route kuralına girilen katmanları tayin eder.

#### Get Metotları

------

##### $this->router->getHost();

Sunucuda çalışan host adresine geri döner. Örn: example.com

##### $this->router->getDomain();

App/routes.php dosyası içerisinde domain metodu ile tanımlanmış alan adına geri döner.

##### $this->router->getModule();

Eğer bir modül çağrıldıysa modül ismine aksi durumda boş bir string '' değerine geri döner.

##### $this->router->getDirectory();

Çağırılan dizin adına geri döner.

##### $this->router->getClass();

Çağırılan sınıf adına geri döner.

##### $this->router->getMethod();

Çağırılan metot adına geri döner.