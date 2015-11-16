
## Captcha Sınıfı

CAPTCHA "Carnegie Mellon School of Computer Science" tarafından geliştirilen bir projedir. Projenin amacı bilgisayar ile insanların davranışlarının ayırt edilmesidir ve daha çok bu ayrımı yapmanın en zor olduğu web ortamında kullanılmaktadır. CAPTCHA projesinin bazı uygulamalarına çoğu web sayfalarında rastlamak mümkündür. Üyelik formlarında rastgele resim gösterilerek formu dolduran kişiden bu resmin üzerinde yazan sözcüğü girmesi istenir. Buradaki basit mantık o resimde insan tarafından okunabilecek ancak bilgisayar programları tarafından okunması zor olan bir sözcük oluşturmaktır. Eğer forma girilen sözcük resimdeki ile aynı değilse ya formu dolduran kişi yanlış yapmıştır ya da formu dolduran bir programdır denebilir.

<ul>
<li>
    <a href="#setup">Kurulum</a>
    <ul>
        <li><a href="#adding-module">Modülü Kurmak</a></li>
        <li><a href="#removing-module">Modülü Kaldırmak</a></li>
        <li><a href="#configuration">Konfigürasyon</a></li>
        <li><a href="#service-configuration">Servis Konfigürasyonu</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma ve Seçenekler</a>
    <ul>
        <li><a href="#module">Captcha Modülü</a></li>
        <li><a href="#loading-service">Servisi Yüklemek</a></li>
        <li><a href="#choose-mod">Mod Seçimi</a></li>
        <li><a href="#font-options">Font Seçenekleri</a></li>
        <li><a href="#color-options">Renk Seçenekleri</a></li>
        <li><a href="#foreground-color-options">Arkaplan Renk Seçenekleri</a></li>
        <li><a href="#image-height">Imaj Yüksekliği</a></li>
        <li><a href="#font-width">Font Genişliği</a></li>
        <li><a href="#font-wave">Font Eğimi</a></li>
        <li><a href="#char-pool">Karakter Havuzu</a></li>
        <li><a href="#char-width">Karakter Genişliği</a></li>
    </ul>
</li>
    
<li>
    <a href="#create-operations">Captcha İşlemleri</a>
    <ul>
        <li>
            <a href="#creating-captcha">Captcha Oluşturma</a>
            <ul>
                <li><a href="#create">$this->captcha->create()</a></li>
                <li><a href="#printJs">$this->captcha->printJs()</a></li>
                <li><a href="#printHtml">$this->captcha->printHtml()</a></li>
                <li><a href="#printRefreshButton">$this->captcha->printRefreshButton()</a></li>
            </ul>
        </li>
        <li><a href="#validation">Captcha Doğrulama</a></li>
        <li><a href="#validation-with-validator">Validator Sınıfı İle Doğrulama</a></li>
        <li><a href="#results-table">Hata ve Sonuç Kodları Tablosu</a></li>
    </ul>
</li>

<li><a href="#method-reference">Fonksiyon Referansı</a></li>
</ul>

<a name="setup"></a>

### Kurulum

Captcha paketi uygulama içerisinde modül olarak kullanılır ve kurulduğunda modüle ait konfigürasyon dosyaları <kbd>config/captcha</kbd> klasörü altına kopyalanır.

<a name="adding-module"></a>

#### Modül Kurmak

```php
php task module add captcha
```

<a name="removing-module"></a>

#### Modülü Kaldırmak

```php
php task module remove captcha
```

<a name="configuration"></a>

#### Konfigürasyon

Konfigürasyon ayarlarını ihtiyaçlarınızı göre aşağıdaki dosyadan ayarlamanız gerekir.

```php
- app
- config
    - captcha
        image.php
```

<a name="service-configuration"></a>

#### Servis Konfigürasyonu

Servis dosyası modül eklendiğinde otomatik olarak <kbd>app/classes/Service</kbd> klasörü altına kopyalanır. Servis dosyasındaki captcha özelliklerini ihtiyaçlarınıza göre konfigüre etmeniz gerekebilir.

```php
namespace Service;

use Obullo\Captcha\Adapter\Image;
use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Captcha implements ServiceInterface
{
    public function register(Container $c)
    {
        $c['captcha'] = function () use ($c) {

            $captcha = new Image($c);            
            $captcha->setMod('secure');  // set to "cool" for no background
            $captcha->setPool('alpha');     // "random", "numbers"
            $captcha->setChar(5);
            $captcha->setFont(array('NightSkK','Almontew', 'Fordd'));
            $captcha->setFontSize(20);
            $captcha->excludeFont(['Fordd']);  // remove font
            $captcha->setHeight(36);
            $captcha->setWave(false);
            $captcha->setColor(['red', 'black']);
            $captcha->setTrueColor(false);
            $captcha->setNoiseColor(['red']);
            return $captcha;
        };
    }
}

/* Location: .app/classes/Service/Captcha.php */
```

<a name="running"></a>

### Çalıştırma ve Seçenekler

Image Captcha arayüzüne bağlanmak için captcha servisi kullanılır.

<a name="module"></a>

#### Captcha Modülü

Modül yaratıldığına örnek captcha oluşturma dosyaları <kbd>.modules/recaptcha/examples</kbd> dizini altına kopyalanır. Bu kapsamlı örnekleri incelemek için tarayıcınızdan aşğıdaki adresleri ziyaret edin.

```html
http://myproject/captcha/examples/form
http://myproject/captcha/examples/ajax
```

<a name="loading-service"></a>

#### Servisi Yüklemek

Captcha servisi aracılığı ile captcha metotlarına aşağıdaki gibi erişilebilir.

```php
$this->c['captcha']->method();
```

<a name="choosing-mod"></a>

#### Mod Seçimi

Image sürücüsü iki tür moda sahiptir: <kbd>secure</kbd> ve <kbd>cool</kbd>. Konfigürasyonda tanımlı varsayılan mod <b>cool</b> modudur. Güvenli mod <b>secure</b> modu imajları kompleks bir arkaplan seçerek oluşturur. Cool modunda ise captcha arkaplan kullanılmadan oluşturulur.

```php
$this->captcha->setMod('secure');
```
<a name="font-options"></a>

#### Font Seçenekleri

```php
$this->captcha->setFont(['Arial', 'Tahoma', 'Verdana']);
```

Fontlarınız <kbd>.assets/fonts</kbd> dizininden yüklenirler. Bu dizin konfigürasyon dosyasından değiştirilebilir.

```php
- assets
	- fonts
		My_font1.ttf
		My_font2.ttf
		My_font3.ttf
```

Özel fontlar eklemek için setFont metodunu kullanabilirsiniz.

```php
$this->captcha->setFont([
                  'AlphaSmoke',         // Default captcha font
                  'Almontew',        
				  'My_Font1.ttf',		// Your custom fonts with extension (.ttf etc ..)
				  'My_Font2.ttf',
				  'My_Font3.ttf'
				 ]);
```

Gereksiz fontları çıkarmak için excludeFont metodunu kullanabilirsiniz.

```php
$this->captcha->excludeFont(['AlphaSmoke','Anglican'});
```
<a name="color-options"></a>

#### Renk Seçenekleri

Varsayılan renkleri <b>config/captcha.php</b> dosyasından ayarlayabilirsiniz.

Mevcut renkler aşağıdaki gibidir.

<kbd>red</kbd> - <kbd>blue</kbd> - <kbd>green</kbd> - <kbd>black</kbd> - <kbd>yellow</kbd> 

```php
$this->captcha->setColor(['red','black']);
```
<a name="foreground-color-options"></a>

#### Arkaplan Desen Renkleri

Varsayılan renkleri <b>config/captcha.php</b> dosyasından ayarlayabilirsiniz. Birden fazla renk seçildiğinde captcha rastgele bir renk seçilerek yaratılır.

Mevcut renkler aşağıdaki gibidir.

<kbd>red</kbd> - <kbd>blue</kbd> - <kbd>green</kbd> - <kbd>black</kbd> - <kbd>yellow</kbd> 

```php
$this->captcha->setNoiseColor(['black','cyan']);
```
<a name="image-height"></a>

#### Imaj Yüksekliği

Eğer imaj <b>yüksekliği</b> bir kez ayarlanır ise imaj genişliği, karakter ve font genişliği değerleri otomatik olarak hesaplanır. Varsayılan değer <kbd>40</kbd> px dir.

```php
$this->captcha->setHeight(40);
```

<a name="font-width"></a>

#### Font Genişliği

Font size değerini atar, varsayılan değer <kbd>20</kbd> px dir.

```php
$this->captcha->setFontSize(20);
```

<a name="font-wave"></a>

#### Font Eğimi

Font eğimi özelliği etkin kılar.

```php
$this->captcha->setWave(false);
```

<a name="char-pool"></a>

#### Karakter Havuzu

Karakter havuzu captcha imajında kullanılacak karakterleri belirler, aşağıdaki listedeki değerler örnek olarak verilmiştir. Değerler konfigürasyon dosyanızdan değiştirilebilir.

```php
$this->captcha->setPool('numbers');
```

<table>
<thead>
<tr>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>numbers</td>
<td>23456789</td>
</tr>
<tr>
<td>alpha</td>
<td>ABCDEFGHJKLMNPRSTUVWXYZ</td>
</tr>
<tr>
<tr>
<td>random</td>
<td>23456789ABCDEFGHJKLMNPRSTUVWXYZ</td>
</tr>
</tbody>
</table>

Daha fazla okunabilirlik için <kbd>"1 I 0 O"</kbd> karakterlerini kullanmamanız tavsiye edilir.

Varsayılan değer <kbd>random</kbd> değeridir.

<a name="char-width"></a>

#### Karakter Genişliği

```php
$this->captcha->setChar(10);
```

<a name="create-operations"></a>

### Captcha İşlemleri

Captcha işlemleri captcha html ve javascript kodunu oluşturma, yenileme tuşu oluşturma ve doğrulama işlemlerini kapsar.

<a name="creating-captcha"></a>

#### Captcha Oluşturma

Captcha oluşturma metotları captcha elemetlerini oluşturur.

<a name="create"></a>

##### $this->captcha->create();

Captcha modülü eklendiğinde captcha modülü altında <kbd>/modules/captcha/Create.php</kbd> adında aşağıdaki gibi bir imaj controller yaratılır.

```php
namespace Captcha;

class Create extends \Controller
{
    public function index()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        $this->captcha->create();
    }
}
```

<a name="printJs"></a>

##### $this->captcha->printJs();

Sayfaya captcha eklemek için aşağıdaki gibi <b>head</b> tagları arasına javascript çıktısını ekrana dökmeniz gerekir.

```php
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <?php echo $this->captcha->printJs() ?>
</head>
<body>

</body>
</html>
```

<a name="printHtml"></a>

##### $this->captcha->printHtml();

Formlarınıza captcha eklemek için aşağıdaki gibi captcha çıktısını ekrana dökmeniz gerekir.

```php
<form method="POST" action="/captcha/examples/form">
	<?php echo $this->captcha->printHtml() ?>
    <input type="submit" value="Send" name="sendForm">
</form>
```

<a name="printRefreshButton"></a>

##### $this->captcha->printRefreshButton();

Eğer refresh button özelliğinin etkin olmasını istiyorsanız. Form taglarınız içierisinde bu fonksiyonu kullanın.

```php
<form method="POST" action="/captcha/examples/form">
    <?php echo $this->captcha->printHtml() ?>
    <?php echo $this->captcha->printRefreshButton() ?>
    <input type="submit" value="Send" name="sendForm">
</form>
```

<a name="validation"></a>

#### Captcha Doğrulama 

Captcha doğrulama için bütün sürücüler için ortak olarak kullanılan CaptchaResult sınıfı kullanılır. Bir captcha kodunun doğru olup olmadığı aşağıdaki gibi isValid() komutu ile anlaşılır.

```php
if ($this->c['captcha']->result()->isValid()) {

	// Doğrulama başarılı
}
```

Bir doğrulamadan dönen mesajlar aşağıdaki gibi alınır.

```php
print_r($this->c['captcha']->result()->getMessages());
```

Bir doğrulamaya ait hata kodu alma örneği


```php
echo $this->c['captcha']->result()->getCode();  // -2  ( Invalid Code )
```

<a name="validation-with-validator"></a>

#### Validator Sınıfı İle Doğrulama 

Eğer varolan formunuz içerisinde bir captcha doğrulaması yapıyorsanız ve konfigürasyon dosyasından <kbd>validation</kbd> ve <kbd>callback</kbd> anahtarları aktif ise doğrulama için aşağıdaki kodlar haricinde herhangi bir kod yazmanıza gerek kalmaz.

```php
namespace Captcha\Examples;

class Form extends \Controller
{
    public function index()
    {
        if ($this->request->isPost()) {

            if ($this->validator->isValid()) {
                $this->form->success('Form Validation Success.');
            }
        }
        $this->view->load(
            'form',
            [
                'title' => 'Hello Captcha !'
            ]
        );
    }
}
```

<a name="results-table"></a>

#### Hata ve Sonuç Kodları Tablosu

<table>
    <thead>
        <tr>
            <th>Kod</th>    
            <th>Sabit</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>0</td>
            <td>CaptchaResult::FAILURE</td>
            <td>Genel başarısız doğrulama.</td>
        </tr>
        <tr>
            <td>1</td>
            <td>CaptchaResult::SUCCESS</td>
            <td>Doğrulama başarılıdır.</td>
        </tr>
        <tr>
            <td>-1</td>
            <td>CaptchaResult::FAILURE_EXPIRED</td>
            <td>Girilen captcha kodunun zaman aşımına uğradığını gösterir.</td>
        </tr>
        <tr>
            <td>-2</td>
            <td>CaptchaResult::FAILURE_INVALID_CODE</td>
            <td>Girilen captcha kodunun yanlış olduğunu gösterir.</td>
        </tr>
        <tr>
            <td>-3</td>
            <td>CaptchaResult::FAILURE_CAPTCHA_NOT_FOUND</td>
            <td>Girilen captcha kodunun veriler içerisinde hiç bulunamadığını gösterir.</td>
        </tr>
    </tbody>
</table>

<a name="method-reference"></a>

#### Fonksiyon Referansı

------

##### $this->captcha->setMod(string $mod = 'secure');

Captcha modunu ayarlar <kbd>secure</kbd> veya <kbd>cool</kbd> seçilebilir. Cool seçildiğinde arkaplan boşaltılır.

##### $this->captcha->setNoiseColor(mixed $color = ['red']);

Arkaplan desen renklerini belirler.

##### $this->captcha->setColor(mixed $color = ['black']);

Imaj yazı rengini belirler.

##### $this->captcha->setTrueColor(boolean $bool = false);

Image true color seçeneğini etkin kılar. Mevcut renklerin bir siyah versiyonunu yaratır. Bknz. Php <a href="http://php.net/manual/en/function.imagecreatetruecolor.php" target="_blank">imagecreatetruecolor</a>

##### $this->captcha->setFontSize(integer $size);

Font genişliği belirler.

##### $this->captcha->setHeight(integer $height);

Eğer imaj <b>yüksekliği</b> bir kez ayarlanır ise imaj genişliği, karakter ve font genişliği değerleri otomatik olarak hesaplanır.

##### $this->captcha->setPool(string $pool);

Karakter havuzunu belirler. Değerler: numbers, random ve alpha dır.

##### $this->captcha->setChar(integer $char);

Imaj üzerindeki karakterlerin maximum sayısını belirler.

##### $this->captcha->setWave(true or false);

Yazı eğimi özelliğini açar veya kapatır.

##### $this->captcha->setFont(mixed ['FontName', ..]);

Mevcut fontlardan font yada fontlar seçmenize olanak tanır.

##### $this->captcha->excludeFont(mixed ['FontName', ..]);

Mevcut fontlardan font yada fontlar çıkarmanızı sağlar.

##### $this->captcha->getInputName();

Captcha input alanı adını verir.

##### $this->captcha->getImageUrl();

Captcha http image adresini verir.

##### $this->captcha->getImageId();

Rastgele üretilen captcha imajı id sini verir.

##### $this->captcha->getCode();

Geçerli captcha koduna geri döner.

##### $this->captcha->create();

Captcha kodunu yaratır. Http başlıkları ile birlikte kullanılması tavsiye edilir.

##### $this->captcha->result(string $code = null);

Parametreden gönderilen captcha kodunu doğrulama işlemini başlatarak CaptchaResult nesnesine döner. Eğer bir parametre girilmezse otomatik olarak $this->c['request']->post('capthca_input_ismi'); değeri alınır.

##### $this->captcha->printJs();

Captcha refresh javascript fonksiyonunu sayfaya yazdırır. Html head tagları arasında kullanılması önerilir.

##### $this->captcha->printHtml();

Captcha html <b>img</b> tagını yaratır dönen sonuç "echo" ile ekrana yazdırılmalıdır.

##### $this->captcha->printRefreshButton();

Captcha html refresh button tagını yaratır.