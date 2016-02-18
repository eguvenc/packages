
## Anotasyonlar

Bir anotasyon aslında bir metadata yı (örneğin yorum,  açıklama, tanıtım biçimini) yazıya, resime veya diğer veri türlerine tutturmaktır. Uygulama içinde anotasyonlar sınıflar yada sınıf metotları üzerine açıklama biçiminde yazılarak belirli işlevleri kontrol ederler.

> Anotasyonlar herhangi bir kurulum yapmayı gerektirmez ve uygulamanıza performans açısından ek bir yük getirmez. Php ReflectionClass sınıfı ile okunan anotasyonlar uygulamada kolayca çözümlenirler.

<ul>
    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li><a href="#enabling-annotations">Anotasyonları aktif etmek</a></li>
            <li><a href="#available-annotations">Mevcut Anotasyonlar</a></li>
            <li><a href="#middleware">Middleware Komutları</a></li>
            <li><a href="#loader-annotations">Yükleyici Anotasyonları</a></li>
        </ul>
    </li>
</ul>

<a name="running"></a>

### Çalıştırma

<a name="enabling-annotations"></a>

#### Anotasyonları aktif etmek

Config.php konfigürasyon dosyasını açın ve <kbd>annotations > enabled</kbd> anahtarının değerini <kbd>true</kbd> olarak güncelleyin.

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
            <td><b>@middleware->add();</b></td>
            <td>Bir middleware katmanını uygulamaya ekler. Virgül ile birden fazla katman ismi gönderebilirsiniz.</td>
        </tr>
        <tr>
            <td><b>@middleware->remove();</b></td>
            <td>Varolan bir middleware katmanını uygulamadan çıkarır. Virgül ile birden fazla katman ismi gönderebilirsiniz.</td>
        </tr>
        <tr>
            <td><b>@middleware->method();</b></td>
            <td>Http protokolü tarafından gönderilen istek metodu belirlenen metotlardan biri ile eşleşmez ise sayfaya erişime izin verilmez. Virgül ile birden fazla katman ismi gönderebilirsiniz.</td>
        </tr>
         <tr>
            <td><b>@middleware->when()->add()</b></td>
            <td>Katmanı koşullu olarak uygulamaya ekler. Eğer http protokolü tarafından gönderilen istek metodu when metodu içerisine yazılan metotlardan biri ile eşleşmez ise bu anotasyonun kullanıldığı katman uygulumaya eklenmez.</td>
        </tr>
    </tbody>
</table>

<a name="middleware"></a>

#### Middleware Komutları

@middleware komutu ile bir kontrolör sınıfı içerisinden uygulamaya bir katman eklenebilir yada bir katman uygulamadan kaldırılabilir.

```php
/**
 * Index
 *
 * @middleware->add("Example");
 * @middleware->method("get", "post");
 *
 * @return void
 */
```

Yukarıdaki örnekte anotasyon kontrolör sınıfı index metodundan önce uygulamaya <kbd>Example</kbd> katmanını ekler ve sadece <kbd>get</kbd> ve <kbd>post</kbd> isteklerinde erişime izin verir.

```php
/**
 * Index
 *
 * @middleware->when("post")->add("Xss");
 * 
 * @return void
 */
```

Yukarıdaki örnek sadece http <kbd>post</kbd> isteklerinde ve index() metodunun çalışmasından önce tanımlamış olduğunuz <kbd>XssFilter</kbd> gibi örnek bir katman çalıştırır.


<a name="loader-annotations"></a>

#### Yükleyici Anotasyonları

Bazı durumlarda yüklenen kontrolör sınıfının tüm metodlarında geçerli olabilecek bir filtreye ihtiyaç duyulabilir. Bu durumda filtreleri <kbd>__construct</kbd> metodu üzerine yazmanız yeterli olacaktır.

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