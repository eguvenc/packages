
## Anotasyonlar

Bir anotasyon aslında bir metadata yı (örneğin yorum,  açıklama, tanıtım biçimini) yazıya, resime veya diğer veri türlerine tutturmaktır. Uygulama içinde anotasyonlar sınıflar yada sınıf metotları üzerine açıklama biçiminde yazılarak belirli işlevleri kontrol ederler.

> **Not:** Anotasyonlar herhangi bir kurulum yapmayı gerektirmez ve uygulamanıza performans açısından ek bir yük getirmez. Php ReflectionClass sınıfı ile okunan anotasyonlar çekirdekte herhangi bir düzenli ifade işlemi kullanılmadan kolayca çözümlenir.

Şu anki sürümde biz anotasyonları sadece <kbd>Http Katmanlarını</kbd> atamak ve <kbd>Event</kbd> sınıfına tayin edilen <b>Olayları Dinlemek</b> için kullanıyoruz.

<ul>
    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li><a href="#enabling-annotations">Anotasyonları aktif etmek</a></li>
            <li><a href="#available-annotations">Mevcut Anotasyonlar</a></li>
            <li><a href="#middleware">Middleware</a></li>
            <li><a href="#event">Event</a></li>
            <li><a href="#loader-annotations">Yükleyici Anotasyonları</a></li>
        </ul>
    </li>
</ul>

<a name="running"></a>

### Çalıştırma

<a name="enabling-annotations"></a>

#### Anotasyonları aktif etmek

Config.php konfigürasyon dosyasını açın ve <b>annotations > enabled</b> anahtarının değerini <b>true</b> olarak güncelleyin.

```php
'controller' => [
    'annotations' => true,
],
```

Artık kontrolör sınıfı metotları üzerinde anotasyonları aşağıdaki gibi kullanabilirsiniz.

```php
/**
 * Index
 *
 * @middleware->when("get", "post")->add("Example")
 * 
 * @return void
 */
public function index()
{
    // ..
}

/* Location: .modules/welcome/welcome.php */
```

<a name="available-annotations"></a>

#### Mevcut Olan Anotasyonlar

<table>
    <thead>
        <tr>
            <th>Anotasyon</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>@middleware->queue();</b></td>
            <td>Bir middleware katmanını uygulamaya ekler. Virgül ile birden fazla katman ismi gönderebilirsiniz.</td>
        </tr>
        <tr>
            <td><b>@middleware->unqueue();</b></td>
            <td>Varolan bir middleware katmanını uygulamadan çıkarır. Virgül ile birden fazla katman ismi gönderebilirsiniz.</td>
        </tr>
        <tr>
            <td><b>@middleware->method();</b></td>
            <td>Http protokolü tarafından gönderilen istek metodu belirlenen metotlardan biri ile eşleşmez ise sayfaya erişime izin verilmez. Virgül ile birden fazla katman ismi gönderebilirsiniz.</td>
        </tr>
         <tr>
            <td><b>@middleware->when()->queue()</b></td>
            <td>Katmanı koşullu olarak uygulamaya ekler. Eğer http protokolü tarafından gönderilen istek metodu when metodu içerisine yazılan metotlardan biri ile eşleşmez ise bu anotasyonun kullanıldığı katman uygulumaya eklenmez.</td>
        </tr>
        <tr>
            <td><b>@event->subscribe();</b></td>
            <td>Event sınıfını çağırarak subscribe metodu ile varsayılan controller için bir dinleyici atamanızı sağlar.</td>
        </tr>
    </tbody>
</table>

<a name="middleware"></a>

#### Middleware

@middleware komutu ile bir kontrolör sınıfı içerisinden uygulamaya bir katman eklenebilir yada bir katman uygulamadan kaldırılabilir.

```php
/**
 * Index
 *
 * @middleware->queue("Example");
 * @middleware->method("get", "post");
 *
 * @return void
 */
```

Yukarıdaki örnek Controller sınıfı index ( middleware call ) metodundan önce uygulamaya <b>Example</b> katmanını ekler ve sadece <b>get</b> ve <b>post</b> isteklerinde erişime izin verir.

```php
/**
 * Index
 *
 * @middleware->when("post")->queue("Xss");
 * 
 * @return void
 */
```

Yukarıdaki örnek sadece http <b>post</b> isteklerinde ve index() metodunun çalışmasından önce tanımlamış olduğunuz <b>XssFilter</b> gibi örnek bir katman çalıştırır.


```php
/**
 * Index
 *
 * @middleware->when("post")->unqueue("Csrf");
 *
 * @return void
 */
```

Yukarıdaki örnek sadece http <b>post</b> ve <b>get</b> isteklerinde index() metodunun çalışmasından önce varolan <b>Csrf</b> katmanını uygulamadan siler.

<a name="event"></a>

#### Event

```php
/**
 * Index
 *
 * @event->when("post")->subscribe('Event\Login\Attempt');
 *
 * @return void
 */
```

Bu örnekte index metodu çalıştığında <kbd>@event->subscribe</kbd> anotasyonu arkaplanda <kbd>\Obullo\Event->subscribe()</kbd> metodunu çalıştırır ve uygulama  <kbd>app/classes/Event/Login/Attemp.php</kbd> sınıfı içerisine tanımlanmış olayları dinlemeye başlar.

> **Not:** Olaylar hakkında daha detaylı bilgiye [Event.md](Event.md) dökümentasyonundan ulaşabilirsiniz.

<a name="loader-annotations"></a>

#### Yükleyici Anotasyonları

Bazı durumlarda yüklenen kontrolör sınıfının tüm metodlarında geçerli olabilecek bir filtreye ihtiyaç duyulabilir. Bu durumda filtreleri <b>__construct</b> metodu üzerine yazmanız yeterli olacaktır.

```php
/**
 * Loader
 *
 * @middleware->method("post","get");
 * 
 * @return void
 */
public function __construct()
{
    // ..
}
```