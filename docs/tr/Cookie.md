
## Çerez Sınıfı

Çerez, herhangi bir internet sitesi tarafından son kullanıcının bilgisayarına bırakılan bir tür tanımlama dosyasıdır. Çerez dosyalarında oturum bilgileri ve benzeri veriler saklanır. Çerez kullanan bir siteyi ziyaret ettiğinizde, bu site tarayıcınıza bir ya da birden fazla çerez bırakma konusunda talep gönderebilir.
> **Not:** Bir çereze kayıt edilebilecek maksimum veri 4KB tır.

<ul>
    <li><a href="#loading-class">Sınıfı Yüklemek</a></li>
    <li>
        <a href="#setcookie">Bir Çereze Veri Kaydetmek</a>
        <ul>
            <li><a href="#arrays">Array Yöntemi</a></li>
            <li><a href="#method-chaining">Zincirleme Method Yöntemi</a></li>
        </ul>
    </li>
    <li><a href="#parameters">Parametre Açıklamaları</a></li>
    <li><a href="#readcookie">Bir Çerez Verisini Okumak</a></li>
    <li><a href="#removecookie">Bir Çerezi Silmek</a></li>
    <li><a href="#queue">Çerezleri Kuyruğa Göndermek</a></li>
    <li><a href="#method-reference">Fonksiyon Referansı</a></li>
</ul>

<a name="loading-class"></a>

#### Sınıfı Yüklemek

```php
$this->c['cookie']->method();
```

<a name="setcookie"></a>

#### Bir Çereze Veri Kaydetmek

Çerez sınıfını kullandığınızda bir çereze iki tür yöntemle veri kaydedebilirsiniz. Birinci yöntem array türü ile kayıt ikinci yöntem ise parametre göndererek kaydetmektir.

<a name="method-chaining"></a>

##### Zincirleme Method Yöntemi

```php
$this->cookie->expire(0)->set('name', 'value'); 
```

Bu yöntemi kullanarak konfigürasyon dosyasından gelen varsayılan değerleri devre dışı bırakarak girilen değerleri çereze kaydedebilirsiniz. Yukarıdaki örnekte çereze ait domain, path gibi bilgilerin girilmediği görülüyor bu ve bunun gibi sağlanmayan diğer bilgiler <kbd>config/$env/config.php</kbd> konfigürasyon dosyasından okunarak varsayılan değerler olarak kabul edilirler.

Zincirleme method yöntemine tam bir örnek:

```php
$this->cookie->name('hello')->value('world')->expire(86400)->domain('')->path('/')->set(); 
```
<a name="arrays"></a>

##### Array ile Kayıt Yöntemi

Bu yöntemde kayıt set metodu içerisine array türünden parametre gönderilerek yapılır.

Yukarıdaki örnekte çereze ait domain, path gibi bilgilerin girilmediği görülüyor bu ve bunun gibi sağlanmayan diğer bilgiler <kbd>config/env.$env/config.php</kbd> konfigürasyon dosyasından okunur. Eğer konfigürasyon dosyasını ezerek bir çerez kaydetmek istiyorak aşağıdaki gibi tüm parametreleri göndermelisiniz.

```php
$cookie = array(
                   'name'   => 'cookieName',
                   'value'  => 'cookieValue',
                   'expire' => 86500,
                   'domain' => '.some-domain.com',
                   'path'   => '/',
                   'secure' => false,
                   'httpOnly' => false,
                   'prefix' => 'myprefix_',
               );

$this->cookie->set($cookie); 
```

<a name="parameters"></a>

#### Parametre Açıklamaları

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>name</td>
            <td>Çerezin kaydedileceği isim.</td>
        </tr>
        <tr>
            <td>value</td>
            <td>Çereze kayıt edilecek değer.</td>
        </tr>
        <tr>
            <td>expire</td>
            <td>Son erme süresi ( expire ) parametresi saniye türünden girilir ve girilen saniye değeri şu anki zaman üzerine eklenir. Şu anki zaman otomatik olarak eklendiğinden bu süreyi kendiniz eklememeniz gerekir. Eğer sona erme süresi girilmez ise konfigürasyon dosyasındaki değer varsayılan olarak kabul edilir. Eğer sona erme süresi <b>0</b> olarak girilirse çerez tarayıcı kapandığında kendiliğinden yok olur.</td>
        </tr>
        <tr>
            <td>domain</td>
            <td>Çerezin geçerli olacağı alan adıdır. Site-wide çerezler ( tüm alt domainlerde geçerli çerezler ) kaydetmek için domain parametresini <b>.your-domain.com</b> gibi girmeniz gereklidir.</td>
        </tr>
        <tr>
            <td>path</td>
            <td>Çerezin geçerli olacağı dizin, genel olarak çerezin tüm url adresinlerine ait alt dizinlerde kabul edilmesi istendiğinden bölü işareti "/" ( forward slash ) varsayılan değer olarak kullanılır.</td>
        </tr>
        <tr>
            <td>secure</td>
            <td>Eğer çerez güvenli bir <b>https://</b> protokolü üzerinden okunuyorsa bu değerin true olması gerekir. Protokol güvenli olmadığında çereze erişilemez.</td>
        </tr>
        <tr>
            <td>httpOnly</td>
            <td>Eğer http only parameteresi true gönderilirse çerez sadece http protokolü üzerinden okunabilir hale gelir javascript gibi diller ile çerezin okunması engellenmiş olur. Çerez güvenliği ile ilgili daha fazla bilgi için <a href="http://resources.infosecinstitute.com/securing-cookies-httponly-secure-flags/" target="_blank">bu makaleden</a> faydalanabilirsiniz.</td>
        </tr>
        <tr>
            <td>prefix</td>
            <td>Önad ihtiyaç olursa sadece çerezlerinizin diğer çerezler ile karışmasını engellemek için kullanılır. Bir değer girilmezse varsayılan değer konfigürasyon dosyasından okunur.</td>
        </tr>
        </tbody>
</table>

<a name="readcookie"></a>

#### Bir Çerez Verisini Okumak

Bir çerezi okumak için get metodu kullanılır.

```php
if ($value = $this->cookie->get('name')) {
	echo $value;
}
```

Eğer çereze kayıtlı bir değer yoksa fonksiyon <b>false</b> değerine döner. Eğer çerezler için önceden konfigürasyondan bir önad ( prefix ) belirlenmişse get metodu içerisinden çerezin önadı kullanılarak çerez verileri okunur. Eğer sadece belirli çerezlerde özel bir önad kullanılmışsa bu durumda aşağıdaki gibi ikinci parametereden önadı göndermeniz gerekir.

```php
if ($value = $this->cookie->get('name', 'prefix')) {
	echo $value;
}
```
<a name="removecookie"></a>

#### Bir Çerezi Silmek

Bir çerezi silmek için çerez ismi girmeniz yeterlidir.

```php
$this->cookie->delete("name");
```
Bu fonksiyon <kbd>$this->cookie->set()</kbd> fonksiyonunu kullanır sadece içeriden <b>expire()</b> metodunu kullanarak sona erme süresini <b>-1</b> olarak gönderir.

```php
$this->cookie->delete($name = "name", $prefix = null)
```

Domain ve path metotları ile bir örnek.

```php
$this->cookie->domain('my.subdomain.com')->path('/')->delete("name");
```

Veya

```php
$this->cookie->name('name')->prefix('prf_')->domain('my.subdomain.com')->path('/')->delete();
```

<a name="queue"></a>

#### Çerezleri Kuyruğa Göndermek

Uygulamada bir http çıktısı yaratılmış olsa bile çerezleri queue komutu ile tarayıcıya sonradan kaydedebilirsiniz. Böyle bir durumda kuyruğa atılan çerezler bir konteyner içerisinde toplanırlar ve en son http çıktısından sonra <b>Application/Http</b> sınıfı tarafından http başlıklarına kaydedilirler ve bir sonraki http isteğinde mevcut olurlar.

```php
$this->cookie->queue("name", "value");
```

Queue komutu parametreleri set metodu parametreleri ile eşdeğerdir. Kuyruktan bir çerezi silmek için ise <b>unqueue</b> komutu kullanılır.

```php
$this->cookie->unqueue("name");
```

<a name="method-reference"></a>

#### Fonksiyon Referansı

-------

##### $this->cookie->name(string $name);

Kaydedilmek üzere olan bir çereze isim atar.

##### $this->cookie->value(mixed $value = '');

Kaydedilmek üzere olan bir çerez ismine değer atar.

##### $this->cookie->expire(int $expire = 0);

Kaydedilmek üzere olan bir çerezin sona erme süresini belirler.

##### $this->cookie->domain(string $domain = '');

Kaydedilmek üzere olan bir çereze ait alanadı parametresini belirler.

##### $this->cookie->path(string $path = '/');

Kaydedilmek üzere olan bir çereze ait path parametresini tanımlar.

##### $this->cookie->secure(boolean $bool = false);

Kaydedilmek üzere olan bir çereze ait secure parametresini tanımlar.

##### $this->cookie->httpOnly(boolean $bool = false);

Kaydedilmek üzere olan bir çereze ait httpOnly parametresini tanımlar.

##### $this->cookie->prefix(string $prefix = '');

Kaydedilmek üzere olan bir çereze ait bir önad tanımlar.

##### $this->cookie->set(mixed $name, string $value);

Gönderilen parametrelere göre bir çereze veri kaydeder. En son çalıştırılmalıdır. Kayıt işleminden sonra daha önce kullanılan çereze ait veriler başa döndürülür.

##### $this->cookie->get(string $name, string $prefix = '');

Kayıtlı bir çerezi okur eğer çerez mevcut değilese <b>false</b> değerine döner. Konfigürasyonda yada parametrede bir önad belirtilmişse çerez bu önad kullanılarak okunur. Parametreden bir değer gönderilirse konfigürasyon dosyasındaki varsayılan değer pas geçilir.

##### $this->cookie->delete(string $name, string $prefix = '');

Gönderilen parametrelere göre bir çerezi tarayıcıdan siler.

##### $this->cookie->queue(string $name = null, mixed $value = null);

Gönderilen parametrelere göre bir çerezi bir sonraki http isteğinde mevcut olmak üzere kuyruğa gönderir.

##### $this->cookie->queued(string $name, string $prefix = '');

Kuyruğa gönderilmiş olan çerezin değerine döner.

##### $this->cookie->unqueue(string $name, string $prefix = '');

Kuyruğa gönderilen çerezi kuyruktan siler.

##### $this->cookie->getId();

Çerez sınıfı tarafından rastgele üretilen geçerli çerezin kimlik değerine geri döner.

##### $this->cookie->getQueuedCookies();

Kuyruktaki tüm çerezlerin listesine bir dizi içerisinde geri döner.