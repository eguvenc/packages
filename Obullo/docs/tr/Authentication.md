
## Yetki Doğrulama

Yetki doğrulama paketi yetki adaptörleri ile birlikte çeşitli ortak senaryolar için size bir API sağlar. Yetki doğrulama sorgu bellekleme özelliği ile birlikte gelir; yetkisi doğrulanmış kullanıcıları hafızada bellekler ve veritabanı sorgularının önüne geçer. Çoklu oturumları sonlandırma, yeki doğrulamayı onaylama, tarayıcı türü doğrulama ve beni hatırla gibi gelişmiş özellikleri de destekler.

<ul>
    <li><a href="#features">Özellikler</a></li>
    <li><a href="#flow-chart">Akış Şeması</a></li>
    <li>
        <a href="#configuration">Konfigürasyon</a>
        <ul>
            <li><a href="#config-table">Konfigürasyon Değerleri Tablosu</a></li>
            <li><a href="#adapters">Adaptörler</a></li>
            <li>
                <a href="#storages">Hazıfa Depoları</a>
                <ul>    
                    <li><a href="#session-storage">Session</a></li>
                    <li><a href="#redis-storage">Redis Veritabanı</a></li>
                    <li><a href="#cache-storage">Cache Sürücüleri</a> ( Memcache, Memcached )</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li><a href="#loading-service">Servisi Yüklemek</a></li>
            <li><a href="#accessing-config-variables">Konfigürasyon Değerlerine Erişmek</a></li>
        </ul>
    </li>

    <li>
        <a href="#login">Oturum Açma</a>
        <ul>
            <li><a href="#login-attempt">Oturum Açma Denemesi</a></li>
            <li><a href="#login-example">Oturum Açma Örneği</a></li>
            <li><a href="#login-results">Oturum Açma Sonuçları</a></li>
            <li><a href="#login-error-results">Oturum Açma Sonuçları Hata Tablosu</a></li>
        </ul>
    </li>

    <li>
        <a href="#identities">Kimlik Sınıfı</a>
        <ul>
            <li><a href="#identity-keys">Kimlik Anahtarları</a></li>
            <li>
                <a href="#identity-method-reference">Kimlik Sınıfı Referansı</a>
                <ul>
                    <li><a href="#identity-get-methods">Get Metotları</a></li>
                    <li><a href="#identity-set-methods">Set Metotları</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <li><a href="#login-reference">Login Sınıfı Referansı</a></li>
    <li><a href="#authResult-reference">AuthResult Sınıfı Referansı</a></li>
    <li><a href="#middleware">Auth Katmanları</a></li>
    <li><a href="#database-model">Veritabanı Sorgularını Özelleştirmek</a></li>
    <li><a href="#additional-features">Ek Özellikler</a></li>
    <li><a href="#login-event">Oturum Açma Olayı</a></li>
</ul>

<a name="features"></a>

### Özellikler

Obullo yetki doğrulama; 

* Hafıza depoları, ( Storages ) 
* Adaptörler,
* Kullanıcı kimlikleri
* Çoklu oturumları sonlandırma
* Kullanıcı kimliklerini önbelleklenme
* Kullanıcı sorgularını özelleştirebilme ( User model class )
* Yetki doğrulama onaylandırma ( Verification )
* Oturum id sini yeniden yaratma, ( Session regenerate )
* Tarayıcı türünü doğrulama ( User agent validation )
* Hatırlatma çerezi ve beni hatırla ( Remember me )

gibi özellikleri barındırır.

<a name="flow-chart"></a>

### Akış Şeması

Aşağıdaki akış şeması bir kullanıcının yetki doğrulama aşamalarından nasıl geçtiği ve servisin nasıl çalıştığı hakkında size bir ön bilgi verecektir:

![Authentication](images/auth-flowchart.png?raw=true "Authentication")

Şemada görüldüğü üzere <kbd>GenericUser</kbd> ve <kbd>AuthorizedUser</kbd> olarak iki farklı durumu olan bir kullanıcı sözkonusudur. GenericUser <kbd>yetkilendirilmemiş</kbd> AuhtorizedUser ise servis tarafından <kbd>yetkilendirilmiş</kbd> kullanıcıdır.

Akış şemasına göre GenericUser login butonuna bastığı anda ilk önce önbelleğe bir sorgu yapılır ve daha önceden kullanıcının önbellekte kalıcı bir kimliği olup olmadığında bakılır. Eğer hafıza bloğunda kalıcı yetki var ise kullanıcı kimliği buradan okunur yok ise veritabanına sorgu gönderilir ve elde edilen kimlik kartı tekrar önbelleğe yazılır.

<a name="configuration"></a>

### Konfigürasyon

Yetki doğrulama paketine ait konfigürasyon <kbd>app/$env/providers/user.php</kbd> dosyasında tutulmaktadır. Bu konfigürasyona ait bölümlerin ne anlama geldiği hakkında geniş bilgiye [Auth-Configuration.md](Auth-Configuration.md) dökümentasyonundan ulaşabilirsiniz.

<a name="adapters"></a>

#### Adaptörler

Yetki doğrulama adaptörleri uygulamaya esneklik kazandıran sorgulama arabirimleridir, yetki doğrulamanın bir veritabanı ile mi yoksa farklı bir protokol üzerinden mi yapılacağını belirleyen sınıflardır. Varsayılan arabirim türü <kbd>Database</kbd> (RDBMS or NoSQL) dir.

Farklı adaptörlerin çok farklı seçenekler ve davranışları olması muhtemeldir , ama bazı temel şeyler kimlik doğrulama adaptörleri arasında ortaktır. Örneğin, kimlik doğrulama hizmeti sorgularını gerçekleştirmek ve sorgulardan dönen sonuçlar yetki doğrulama adaptörleri için ortak kullanılır.

<a name="storages"></a>

#### Hafıza Depoları

Hazıfa deposu yetki doğrulama esnasında kullanıcı kimliğini ön belleğe alır ve tekrar tekrar oturum açıldığında database ile bağlantı kurmayarak uygulamanın performans kaybetmesini önler. Yetki doğrulama şu anda depolama için sadece <kbd>Redis</kbd> veritabanı ve <kbd>Cache</kbd> sürücüsünü desteklemektedir.

<a name="session-storage"></a>

##### Session

Session sınıfı varsayılan depodur ve depo olarak <kbd>cache</kbd> sürücüleri yerine <kbd>session</kbd> paketini kullanır.

```php
'cache' => array(

    'storage' => '\Obullo\Authentication\Storage\Session',
)
```

Session hafıza deposu kullanıldığında önbellekleme, geçici kimlik oluşturma ve sadece bir aygıttan tekil oturum açtırma gibi gelişmiş işlevler çalışmaz.

<a name="redis-storage"></a>

##### Redis Veritabanı

Yetki doğrulama sınıfı hafıza deposu için varsayılan olarak redis kullanır. Aşağıdaki resim kullanıcı kimliklerinin hafıza deposunda nasıl tutulduğunu göstermektedir.

![PhpRedisAdmin](images/auth-redis.png?raw=true "PhpRedisAdmin")

Varsayılan hafıza deposu <kbd>providers/user.php</kbd> konfigürasyonundan değiştirilebilir.

```php
'cache' => array(

    'storage' => '\Obullo\Authentication\Storage\Redis',
)
```
<a name="cache-storage"></a>

##### Cache Sürücüleri ( Memcache, Memcached )

Eğer cache sürücülerini kullanmak istiyorsanız <kbd>providers/user.php</kbd> konfigürasyon dosyasından ayarları sürücü adı ile değiştirmeniz yeterli olacaktır.

```php
'cache' => array(

    'storage' => '\Obullo\Authentication\Storage\Memcached',
)
```

Bu çözümler dışında başka bir çözüm kullanıyorsanız yazmış olduğunuz kendi hafıza depolama sınfını storage anahtarına girebilirsiniz.

<a name="running"></a>

### Çalıştırma

Mysql benzeri ilişkili bir database kullanıyorsanız aşağıdaki sql kodunu çalıştırarak demo için bir tablo yaratın.

```sql
--
-- Table structure for table `users`
--
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `remember_token` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `remember_token` (`remember_token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--
INSERT INTO `users` (`id`, `username`, `password`, `remember_token`) VALUES 
(1, 'user@example.com', '$2y$06$6k9aYbbOiVnqgvksFR4zXO.kNBTXFt3cl8xhvZLWj4Qi/IpkYXeP.', '');
```

Yukarıdaki sql kodu için kullanıcı adı <kbd>user@example.com</kbd> ve şifre <kbd>123456</kbd> dır. Yetki doğrulama <kbd>User</kbd> servisi üzerinden yönetilir <kbd>providers/user.php</kbd> konfigürasyon dosyasını açarak servisi konfigüre edebilirsiniz.

```php
return array(
    
    'params' => [

        'cache.key' => 'Auth',
        'db.adapter'=> 'Obullo\Authentication\Adapter\Database',
        'db.model'  => 'Obullo\Authentication\Model\Database',
        'db.provider' => [
            'connection' => 'default'
        ],
        'db.tablename' => 'users',
        .
)
```

**db.adapter :** Yetki doğrulama adaptörleri yetki doğrulama servisinde <kbd>Database</kbd> (RDBMS or NoSQL) veya <kbd>dosya-tabanlı</kbd> gibi farklı türde kimlik doğrulama biçimleri olarak kullanılırlar.

**db.model :** Model sınıfı yetki doğrulama sınıfına ait veritabanı işlemlerini içerir. Bu sınıfa genişleyerek bu sınıfı özelleştirebilirsiniz bunun için aşağıda veritabanı sorgularını özelleştirmek başlığına bakınız.

**db.provider.connection:** Veritabanı servis sağlayıcısının hangi bağlantıyı seçmesi gerektiğini tanımlar.

**db.tablename:** Veritabanı işlemleri için tablo ismini belirlemenize olanak sağlar. Bu konfigürasyon veritabanı işlemlerinde kullanılır.

<a name="loading-service"></a>

#### Servisi Yüklemek

Yetki doğrulama paketi sınıflarına erişim 

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\User');
```

yukarıda gösterilen servis sağlayıcısı ile olur. <kbd>User</kbd> servisi yetki doğrulama servisine ait olan <kbd>Login</kbd>, <kbd>Identity</kbd> ve <kbd>Activity</kbd> gibi sınıfları kontrol eder, böylece paket içerisinde kullanılan tüm sınıflara tek bir servis üzerinden erişim sağlanmış olur.

```php
$container->get('user')->class->method();
```

Kontroller dosyası içerisinden;

```php
$this->user->login->method();
$this->user->identity->method();
$this->user->storage->method();
$this->user->model->method();
```

<a name="accessing-config-variables"></a>

#### Konfigürasyon Değerlerine Erişmek

<a name="authconfig"></a>

##### $this->container->get('user.params')

Servis konfigürasyon dosyası içinde tanımlı konfigürasyon değerlerine döner.

```php
echo $this->container->get('user.params')('db.identifier');   // Çıktı username
echo $this->container->get('user.params')('db.password');     // Çıktı password
echo $this->container->get('user.params')('cache.key');       // Çıktı Auth
```

<a name="login"></a>

### Oturum Açma

Oturum açma işlemi bir uygulamanın en kritik bölümlerinden biridir. Bir oturum açma işleminde oturum açma / kapatma, mevcut kullanıcı oturumları almak gibi işlemleri login sınıfı, oturum açma sonuçlarını ise AuthResult sınıfı kontrol eder.

<a name="login-attempt"></a>

#### Oturum Açma Denemesi

Bir kullanıcıya oturum açma girişimi login sınıfı attempt metodu üzerinden gerçekleşir bu metot çalıştıktan sonra oturum açma sonuçlarını kontrol eden <kbd>AuthResult</kbd> nesnesi elde edilmiş olur.

```php
$auhtResult = $this->user->login->attempt(
    'users',
    [
        'db.identifier' => $this->request->post('email'), 
        'db.password' => $this->request->post('password')
    ],
    $this->request->post('rememberMe')
);
```

Oturum açma sonucunun doğruluğu <kbd>AuthResult->isValid()</kbd> metodu ile kontrol edilir eğer oturum açma denemesi başarısız ise dönen tüm hata mesajlarına getArray() metodu ile ulaşılabilir.

```php
if ($auhtResult->isValid()) {
    
    // Success

} else {

    // Fail

    print_r($auhtResult->getArray());
}
```

> Not: <kbd>app/modules/examples/membership</kbd> klasörü içerisinde oluşturulmuş örneğe göz atmayı unutmayın.

<a name="login-error-results"></a>

#### Hata Tablosu

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
            <td>AuthResult::FAILURE</td>
            <td>Genel başarısız yetki doğrulama.</td>
        </tr>
        <tr>
            <td>-1</td>
            <td>AuthResult::FAILURE_IDENTITY_AMBIGUOUS</td>
            <td>Kimlik belirsiz olması nedeniyle başarısız yetki doğrulama.( Sorgu sonucunda 1 den fazla kimlik bulunduğunu gösterir ).</td>
        </tr>
        <tr>
            <td>-2</td>
            <td>AuthResult::FAILURE_CREDENTIAL_INVALID</td>
            <td>Geçersiz kimlik bilgileri girildiğini gösterir.</td>
        </tr>
        <tr>
            <td>-3</td>
            <td>AuthResult::TEMPORARY_AUTH</td>
            <td>Geçici kimlik bilgilerinin oluşturulduğuna dair bir bilgidir.</td>
        </tr>
        <tr>
            <td>1</td>
            <td>AuthResult::SUCCESS</td>
            <td>Yetki doğrulama başarılıdır.</td>
        </tr>

    </tbody>
</table>

<a name="identities"></a>

### Kimlik Sınıfı

Yetkilendirilmiş kimliği yönetebilmek için <kbd>app/classes/Auth</kbd> içerisindeki kimlik sınıfı kullanılır. Bu klasör içerisindeki Identity sınıfı <kbd>Obullo/Authentication/User/Identity</kbd> auth paketine genişler ve aşağıdaki gibidir.

```php
namespace Auth;

use Obullo\Authentication\AbstractIdentity;
use Obullo\Authentication\User\Identity as AuthIdentity;

class Identity extends AuthIdentity
{
    /**
     * Implement your methods.
     */
    
     public function getCountry()
     {
        return $this->attributes['user_country'];
     }

}
```

Bu sınıf yetkili kullanıcıların kimliklerine ait metotları içermelidir. Sınıf içerisindeki <kbd>get</kbd> metotları kullanıcı kimliğinden <kbd>okuma</kbd>, <kbd>set</kbd> metotları ise kimliğe <kbd>yazma</kbd> işlemlerini yürütür. Bu sınıfa metotlar ekleyerek ihtiyaçlarınıza göre düzenleme yapabilirsiniz. Kimliğe ait tüm bilgileri almak için aşağıdaki metodu kullanabilirsiniz.

```php
print_r($this->user->identity->getArray());
```

Çıktı

```php
/*
Array
(
    [__isAuthenticated] => 1
    [__isTemporary] => 0
    [__isVerified] => 1
    [__rememberMe] => 0
    [__time] => 1414244130.719945
    [id] => 1
    [password] => $2y$10$0ICQkMUZBEAUMuyRYDlXe.PaOT4LGlbj6lUWXg6w3GCOMbZLzM7bm
    [remember_token] => bqhiKfIWETlSRo7wB2UByb1Oyo2fpb86
    [username] => user@example.com
)
*/
```

<a name="identity-keys"></a>

#### Kimlik anahtarları

<table>
    <thead>
        <tr>
            <th>Anahtar</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>__isAuthenticated</td>
            <td>Eğer kullanıcı yetkisi doğrulanmış ise bu anahtar <kbd>1</kbd> aksi durumda <kbd>0</kbd> değerini içerir.</td>
        </tr>
        <tr>
            <td>__isTemporary</td>
            <td>Eğer yetki doğrulama onayı için <kbd>$this->user->identity->makeTemporary()</kbd> metodu login attempt metodu sonrasında kullanılmışsa bu anahtar <kbd>1</kbd> aksi durumda <kbd>0</kbd> değerini içerir. Eğer yetki doğrulama onayı kullanıyorsanız kullanıcıyı kendi onay yönteminiz ile onayladıktan sonra <kbd>$this->user->identity->makePermanent()</kbd> metodunu kullanarak doğrulanan kullanıcı yetkisini kalıcı hale getirmeniz gerekir.</td>
        </tr>
        <tr>
            <td>__isVerified</td>
            <td>Yetki doğrulama onayı kullanıyorsanız kullanıcıyı onayladığınızda bu anahtarın değeri <kbd>1</kbd> aksi durumda <kbd>0</kbd> olur.</td>
        </tr>
        <tr>
            <td>__rememberMe</td>
            <td>Kullanıcı giriş yaparken beni hatırla özelliğini kullandıysa bu değer <kbd>1</kbd> değerini aksi durumda <kbd>0</kbd> değerini içerir.</td>
        </tr>
        <tr>
            <td>__time</td>
            <td>Kimliğin ilk oluşturulma zamanıdır. Microtime olarak oluşturulur ve unix time formatında kaydedilir.</td>
        </tr>

    </tbody>
</table>

<a name="identity-method-reference"></a>

#### Kimlik Sınıfı Referansı

------

##### $this->user->identity->check();

Kullanıcının yetkisinin olup olmadığını kontrol eder. Yetkili ise <kbd>true</kbd> değilse <kbd>false</kbd> değerine döner.

##### $this->user->identity->guest();

Kullanıcının yetkisi olmayan kullanıcı yani ziyaretçi olup olmadığını kontrol eder. Ziyaretçi ise <kbd>true</kbd> değilse <kbd>false</kbd> değerine döner.

##### $this->user->identity->exists();

Kimliğin önbellekte olup olmadığını kotrol eder. Varsa <kbd>true</kbd> yoksa <kbd>false</kbd>değerine döner.

##### $this->user->identity->expire($ttl);

Kullanıcı kimliğinin girilen süre göre geçtikten sonra yok olmasını sağlar.

##### $this->user->identity->makeTemporary();

Başarılı giriş yapmış kullanıcıya ait kimliği konfigurasyon dosyasından belirlenmiş sona erme ( expire ) süresine göre geçici hale getirir. Süre sona erdiğinde kimlik hafıza deposundan silinir.

##### $this->user->identity->makePermanent();

Başarılı giriş yapmış kullanıcıya ait geçici kimliği konfigurasyon dosyasından belirlenmiş kalıcı süreye ( lifetime ) göre kalıcı hale getirir. Süre sona erdiğinde veritabanına tekrar sql sorgusu yapılarak kimlik tekrar hafızaya yazılır.

##### $this->user->identity->isVerified();

Onaya tabi olan yetki doğrulamada başarılı oturum açma işleminden sonra kullanıcının onaylanıp onaylanmadığını gösterir. Kullanıcı onaylı ise <kbd>1</kbd> değerine değilse <kbd>0</kbd> değerine döner.

##### $this->user->identity->isTemporary();

Kullanıcının kimliğinin geçici olup olmadığını gösterir. <kbd>1</kbd> yada </kbd>0</kbd> değerine döner.

##### $this->user->identity->updateTemporary(string $key, mixed $val);

Geçici olarak oluşturulmuş kimlik bilgilerini güncellemenize olanak tanır.

##### $this->user->identity->logout();

Oturumu kapatır ve __isAuthenticated anahtarı önbellekte <kbd>0</kbd> değeri ile günceller. Bu method önbellekteki kullanıcı kimliğini bütünü ile silmez sadece kullanıcıyı oturumu kapattı olarak kaydeder.

##### $this->user->identity->destroy();

Önbellekteki kimliği bütünüyle yok eder.

##### $this->user->identity->forgetMe();

Beni hatırla çerezinin bütünüyle tarayıcıdan siler.

##### $this->user->identity->refreshRememberToken(array $credentials);

Beni hatırla çerezini yenileyerek veritabanı ve çerezlere kaydeder.

##### $this->user->identity->validate(array $credentials);

Yetkilili kullanıcı kimliğine sahip kullanıcı bilgilerini dışarıdan gelen yeni bilgiler ile karşılaştırarak doğrulama yapar.


<a name="identity-get-methods"></a>

#### Identity "Get" Metotları

------

##### $this->user->identity->getIdentifier();

Kullanıcın tekil tanımlayıcı sına geri döner. Tanımlayıcı genellikle kullanıcı adı yada id değeridir.

##### $this->user->identity->getPassword();

Kullanıcın hash edilmiş şifresine geri döner.

##### $this->user->identity->getRememberMe();

Eğer kullanıcı beni hatırla özelliğini kullanıyorsa <kbd>1</kbd> değerine aksi durumda <kbd>0</kbd> değerine döner.

##### $this->user->identity->getTime();

Kimliğin ilk yaratılma zamanını verir. ( Php Unix microtime ).

##### $this->user->identity->getRememberMe();

Kullanıcı beni hatırla özelliğini kullandı ise <kbd>1</kbd> değerine kullanmadı ise <kbd>0</kbd> değerine döner.

##### $this->user->identity->getPasswordNeedsReHash();

Kullanıcı giriş yaptıktan sonra eğer şifresi yenilenmesi gerekiyorsa hash edilmiş <kbd>yeni şifreye</kbd> gerekmiyorsa <kbd>false</kbd> değerine döner.

##### $this->user->identity->getRememberToken();

Beni hatırla çerezine döner.

##### $this->user->identity->getArray()

Kullanıcının tüm kimlik değerlerine bir dizi içerisinde geri döner.

<a name="identity-set-methods"></a>

#### Identity "Set" Metotları

------

##### $this->user->identity->variable = 'value'

Kimlik dizisine yeni bir değer ekler.

##### unset($this->user->identity->variable)

Kimlik dizisinde varolan değeri siler.

<a name="login-reference"></a>

#### Login Sınıfı Referansı

------

##### $this->user->login->attempt(array $credentials, $rememberMe = false);

Bu fonksiyon kullanıcı oturumunu açmayı dener ve AuthResult nesnesine döner.

##### $this->user->login->validate(array $credentials);

Guest kimliği bilgilerine doğrulama işlemi yapar.Bilgiler doğruysa true değerine yanlış ise false değerine döner.

##### $this->user->login->getUserSessions();

Geçerli kullanıcının önbelleğe kaydedilmiş oturumlarına bir dizi içerisinde geri döner. Her açılan oturuma bir login id verilir ve kullanıcılar farklı tarayıcılarda veya aygıtlarda birden fazla oturum açmış olabilirler.

<a name="authResult-reference"></a>

#### AuthResult Sınıfı Referansı

------

##### $result->isValid();

Login attempt methodundan geri dönen hata kodu <kbd>0</kbd> değerinden büyük ise <kbd>true</kbd> küçük ise <kbd>false</kbd> değerine döner. Başarılı oturum açma işlermlerinde hata kodu <kbd>1</kbd> değerine döner diğer durumlarda negatif değerlere döner.

##### $result->getCode();

Login denemesinden sonra geçerli hata koduna geri döner.

##### $result->getIdentifier();

Login denemesinden sonra geçerli kullanıcı kimliğine göre döner. ( id, username, email gibi. )

##### $result->getMessages();

Login denemesinden sonra hata mesajlarına geri döner.

##### $result->setCode(int $code);

Login denemesinden varsayılan sonuca hata kodu ekler.

##### $result->setMessage(string $message);

Login denemesinden sonra sonuçlara bir hata mesajı ekler.

##### $result->getArray();

Login denemesinden sonra tüm sonuçları bir dizi içerisinde verir.

##### $result->getResultRow();

Login denemesinden sonra geçerli veritabanı sorgu sonucu yada önbellek verilerine geri döner.

<a name="middlewares"></a>

#### Auth Katmanları

Auth katmanları uygulamanız içerisinde <kbd>app/classes/Http/Middlewares/</kbd> klasörü altında bulunan <kbd>Auth.php</kbd> ve <kbd>Guest.php</kbd> dosyalarıdır. Auth dosyası uygulamaya giriş yapmış olan kullanıcıları kontrol ederken Guest katmanı ise uygulamaya giriş yetkisi olmayan kullanıcıları kontrol eder. Auth ve Guest katmanlarının çalışabilmesi için route yapınızda middleware anahtarına ilgili modül için birkez tutturulmaları gerekir.

Auth katmanları hakkında daha fazla bilgi için [Auth Middleware](https://github.com/obullo/http-middlewares/blob/master/docs/tr/Auth.md) dökümentasyonunu inceleyebilirsiniz.

<a name="database-model"></a>

#### Veritabanı Sorgularını Özelleştirmek

Eğer mevcut database sorgularında değişiklik yapmak yada bir NoSQL çözümü kullanmak istiyorsanız [Auth-DatabaseModel.md](Auth-DatabaseModel.md) dökümentasyonuna gözatın.

<a name="additional-features"></a>

#### Ek Özellikler

Auth paketi yetki doğrulama onayı bazı ek özellikler ile gelir. Bu türden özelliklere ihtiyacınız varsa [Auth-AdditionalFeatures.md](Auth-AdditionalFeatures.md) dökümentasyonuna gözatın.

<a name="login-event"></a>

#### Oturum Açma Olayı

Oturum açma işlemi olayına ilişkin bilgi için [Auth-Login-Event.md](Auth-Login-Event.md) dökümentasyonunu inceleyebilirsiniz.