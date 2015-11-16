
## Hata Yönetimi ve Uygulama Hataları

------

Hata kontrolü ( hata raporlama ) uygulama ile tümleşik gelir ve <kbd>config/env.$env/config.php</kbd> dosyasından kontrol edilir.


<ul>
<li>
    <a href="#error-management">Hata Yönetimi</a>
    
    <ul>
        <li>
            <a href="#global-errors">Evrensel Hata Yönetimi</a>
            <ul>
                <li><a href="#php-errors-and-exceptions">Php Hataları ve İstisnai Hataları Yakalamak</a></li>
                <li><a href="#php-exception-hierarchy">İstisnai Hatalar Hiyerarşisi</a></li>
                <li><a href="#database-and-runtime-exceptions">Veritabanı ve Genel İstisnai Hataları Yakalamak</a></li>
                <li><a href="#fatal-errors">Ölümcül Hataları Yakalamak</a></li>
            </ul>
        </li>

        <li>
            <a href="#catching-exceptions-by-manually">Özel İstisnai Hataları Yakalamak</a>
            <ul>
                <li><a href="#catching-exception-problem">Özel İstisnai Hataları Yakalama Problemi</a></li>
            </ul>
        </li>
    </ul>

</li>

<li>
    <a href="#sending-custom-http-errors">Özel Http Hataları Göndermek</a>
    <ul>
        <li><a href="#showError">$this->response->error()</a></li>
        <li><a href="#show404">$this->response->error404()</a></li>
        <li><a href="#error-message-customization">Hata Şablonlarını Özelleştirmek</a></li>
    </ul>
</li>

</ul>

### Hata Yönetimi

Local çevre ortamı konfigürasyon dosyasında <b>error > debug</b> değeri true olduğunda tüm php hataları <dfn>set_exception_handler()</dfn> fonksiyonu ile Obullo\Error\Exception sınıfı içerisinden exception hatalarına dönüştürülür. Çevre ortamı <b>production</b> olarak ayarlandığında <b>log > enabled</b> anahtarı aktif ise log servisi tarafından hatalar log sürücülerine yazılır ve hatalar gösterilmez eğer <b>log > enabled</b> anahtarı konfigürasyon dosyasından aktif değilse doğal php hataları uygulamada görüntülenmeye müsait olur.

```php
return array(
      
    'error' => [
        'debug' => true,
    ],

```

Uygulamada <b>error > debug</b> değeri true olduğunda her arayüz ( Http istekleri, Ajax ve Cli istekleri ) için farklı türde hata çıktıları oluşturulur. Aşağıda Http İstekleri için oluşmuş örnek bir hata çıktısı görüyorsunuz.

![Http Errors](images/error-debug.png?raw=true "Http Errors")

<a name="global-errors"></a>

#### Evrensel Hata Yönetimi

Uygulamada evrensel hata yönetimi <kbd>app/errors.php</kbd> dosyasından kontrol edilir. Hata durumunda ne yapılacağı bir isimsiz fonksiyon tarafından belirlenerek uygulama tarafında php error handler fonksiyonlarına kayıt edilir. İsimsiz fonksiyon parametresi önüne istisnai hata tipine ait sınıf ismi yazılarak filtreleme yapılmalıdır. Aksi durumda her bir istisnai hata için bütün error fonksiyonları çalışacaktır.

<a name="php-errors-and-exceptions"></a>

##### Php Hataları ve İstisnai Hataları Yakalamak

Aşağıdaki örnekte <b>istisnai hatalara</b> dönüştürülmüş <b>doğal php hataları</b> yakalanıp log olarak kaydediliyor.

```php
/*
|--------------------------------------------------------------------------
| Php Native Errors
|--------------------------------------------------------------------------
*/
$c['app']->error(
    function (ErrorException $e) use ($c) {
        $c['logger']->error($e);
        return ! $continue = false;   // Whether to continue native errors
    }
);
```

Error metodu içerisine girilen isimsiz fonksiyonu kendi ihtiyaçlarınıza göre özelleştirebilirsiniz. İsimsiz fonksiyonlar uygulama çalıştığında fonksiyon parametresi önüne yazılan istisnai hata tipine göre filtrelenir ve application sınıfı içerisinde <dfn>set_exception_handler()</dfn> fonksiyonu içerisine bir defalığına kayıt edilir. 

Bu örnekte fonksiyon sonucu <kbd>$continue</kbd> değişkenine döner ve bu değişken php hatalarının devam edilerek gösterilip gösterilmeyeceğine karar verir. Değişken değeri <b>true</b> olması durumunda hatalar gösterilmeye devam eder <b>false</b> durumda ise <b>fatal error</b> hataları hariç diğer hatalar gösterilmez.

```php
/*
|--------------------------------------------------------------------------
| Logic Exceptions
|--------------------------------------------------------------------------
*/
$c['app']->error(
    function (LogicException $e) use ($c) {
        $c['logger']->error($e);
    }
);
```

Eğer fonksiyon içerisindeki hatalar log sınıfı herhangi bir metodunun içerisine exception nesnesi olarak gönderilirse log sınıfı tarafından istisnai hata çözümlenerek log dosyalarına kayıt edilir.

<a name="php-exception-hierarchy"></a>

##### İstisnai Hatalar Hiyerarşisi

Hataları yakalarken uygulamaya tüm exception isimleri yazmanıza <b>gerek yoktur</b>. Sadece en üst hiyerarşideki istisnai hata isimlerini girerek aynı kategorideki hataların hepsini yakalayabilirsiniz.


```php
- Exception
    - ErrorException
    - LogicException
        - BadFunctionCallException
            - BadMethodCallException
        - DomainException
        - InvalidArgumentException
        - LengthException
        - OutOfRangeException
    - RuntimeException
        - PDOException
        - OutOfBoundsException
        - OverflowException
        - RangeException
        - UnderflowException
        - UnexpectedValueException
```

İstisnai hatalar ile ilgili bu kaynağa bir gözatın. <a href="http://nitschinger.at/A-primer-on-PHP-exceptions">Php Exceptions</a>

<a name="database-and-runtime-exceptions"></a>

##### Veritabanı ve Genel İstisnai Hataları Yakalamak

Uygulama hataları varsayılan olarak log sürücülerine kaydedilirler.

```php
/*
|--------------------------------------------------------------------------
| Database and Other Runtime Exceptions
|--------------------------------------------------------------------------
*/
$c['app']->error(
    function (RuntimeException $e) use ($c) {
        $c['logger']->error($e);
    }
);
```

Bununla beraber <a href="http://php.net/manual/tr/internals2.opcodes.instanceof.php" target="_blank">instanceof</a> yöntemi ile <b>exception</b> ( $e ) nesnesine  sınıf kontrolü yapılarak yönetilebilirler. Örneğin uygulamadan dönen veritabanı hatalarını yönetmek istiyorsanız aşağıdaki kod bloğu size yardımcı olabilir.


```php
$c['app']->error(
    function (RuntimeException $e) use ($c) {

        if ($e instanceof PDOException) {

            $this->c['translator']->load('database');

            echo $this->c['response']->withStatus(200)->error(
                $this->c['translator']['OBULLO:TRANSACTION:ERROR'],
                'System Unavailable'
            );
        }
        $c['logger']->error($e);
    }
);
```

<a name="fatal-errors"></a>

##### Ölümcül Hataları Yakalamak

Aşağıdaki örnekte ise php fatal error türündeki hatalar kontrol altına alınarak log sınıfına gönderiliyor.

```php
/*
|--------------------------------------------------------------------------
| Php Fatal Errors
|--------------------------------------------------------------------------
*/
$c['app']->fatal(
    function (ErrorException $e) use ($c) {
        $c['logger']->error($e);
    }
);
```

Fatal error örneğinde ölümcül hata türündeki hatalar fatal metodu ile php <a href="http://php.net/manual/en/function.register-shutdown-function.php" target="_blank">register_shutdown</a> fonksiyonuna gönderilerek kontrol edilirler. Bir ölümcül hata oluşması durumunda isimsiz fonksiyon çalışarak fonksiyon içerisindeki görevleri yerine getirir. Fatal error metodu uygulamanın en alt seviyesinde çalışır.


> **Not:** İstisnai hatalardan faklı olarak $c['app']->fatal() metodu errors.php dosyası içerisinde yalnızca <b>bir kere</b> tanımlanabilir.

<a name="catching-exceptions-by-manually"></a>

### Özel İstisnai Hataları Yakalamak

------

Uygulamanıza özgü istisnai hataları yakalamak için <kbd>try/catch</kbd> bloğu kullanılır.

```php
try
{
	$this->db->beginTransaction();
	$this->db->query("INSERT INTO users (name) VALUES('John')");
	$this->db->commit();

} catch(\Exception $e)
{
	$this->db->rollBack();
    echo $e->getMessage();
}
```

<a name="catching-exception-problem"></a>

#### Özel İstisnai Hataları Yakalama Problemi

Eğer istisnai hataları alamıyorsanız ve  ekrana boş beyaz bir sayfa geliyorsa büyük ihtimalle bir namespace içerisindesiniz ve <b>fatal error</b> alıyorsunuz. Namespace içerisinde iken kullandığınız sayfa içerisinde en üstte <b>use Exception</b> yada catch kısmında  backslash <b>\</b> ile <b>\Exception</b> kullanmayı deneyin.

```php
} catch(\Exception $e)
{
    $this->db->rollBack();
    echo $e->getMessage();
}
```

<a name="sending-custom-http-errors"></a>

### Özel Http Hataları Göndermek

Kimi durumlarda uygulamaya özgü http hataları göstermeniz gerekebilir bu durumda Http paketi içerisindeki response sınıfına ait metotları uygulamanızda kullanabilirsiniz.

<a name="showError"></a>

##### $this->response->error('message')

```php
$this->response->withStatus(500)->error('There is an error occured');
```

Opsiyonal parametre <kbd>$status</kbd> ise hata ile birlikte hangi HTTP durum kodunun gönderileceğini belirler varsayılan değer <b>500 iç sunucu hatası</b> dır.

<a name="show404"></a>

##### $this->response->error404('message')

```php
$this->response->show404('Page not found')
```
404 http durum kodu ile birlikte sayfa bulunamadı hatası gösterir.

<a name="error-message-customization"></a>

##### Hata Şablonlarını Özelleştirmek

Ugyulama içinde gönderdiğiniz yukarıda bahsedilen hata metotlarına ait hata şablonlarını ihtiyaçlarınıza göre özelleştirebilirsiniz. <kbd>error()</kbd> türündeki hataları düzenlemek için <kbd>resources/templates/errors/general.php</kbd> dosyasını, <kbd>error404()</kbd> türündeki hataları düzenlemek içinse <kbd>resources/templates/errors/404.php</kbd> dosyasını kullanabilirsiniz.