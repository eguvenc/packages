
## Yetki Doğrulama ( User )

Yetki doğrulama paketi yetki adaptörleri ile birlikte çeşitli ortak senaryolar için size bir API sağlar. Paket sorgu bellekleme özelliği ile birlikte gelir; yetkisi doğrulanmış kullanıcıları hafızada bellekler ve veritabanı sorgularının önüne geçer. Çoklu oturumları sonlandırma, yeki doğrulamayı onaylama, tarayıcı türü doğrulama ve beni hatırla gibi gelişmiş özellikleri de destekler.

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
                    <li><a href="#null-storage">Null</a> ( Session )</li>
                    <li><a href="#redis-storage">Redis Veritabanı</a></li>
                    <li><a href="#cache-storage">Cache</a> ( File, Apc, Memcache, Memcached, Redis )</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li>
                <a href="#service">Servis Konfigürasyonu</a>
                <ul>
                    <li><a href="#loading-service">Servisi Yüklemek</a></li>
                    <li><a href="#accessing-config-variables">Konfigürasyon Değerlerine Erişmek</a></li>
                </ul>
            </li>
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
        <a href="#identities">Kimlikler</a>
        <ul>
            <li><a href="#identities-table">Kimlikler Tablosu</a></li>
            <li><a href="#identity-class">Kimlik Sınıfı İşlevleri</a></li>
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
    <li><a href="#database-model">Veritabanı Sorgularını Özelleştirmek</a></li>
    <li><a href="#additional-features">Ek Özellikler</a></li>
    <li><a href="#events">Olaylar</a></li>
    <li><a href="#middleware">Auth Katmanları</a></li>
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

Akış şemasına göre GenericUser login butonuna bastığı anda ilk önce önbelleğe bir sorgu yapılır ve daha önceden kullanıcının önbellekte kalıcı bir kimliği olup olmadığında bakılır. Eğer hafıza bloğunda kalıcı yetki var ise kullanıcı kimliği buradan okunur yok ise veritabanına sorgu gönderilir ve elde edilen kimlik kartı performans için tekrar önbelleğe yazılır.

<a name="configuration"></a>

### Konfigürasyon

Yetki doğrulama paketine ait konfigürasyon <kbd>app/$env/providers/user.php</kbd> dosyasında tutulmaktadır. Bu konfigürasyona ait bölümlerin ne anlama geldiği aşağıda geniş bir çerçevede ele alınmıştır.

<a name="config-table"></a>

#### Konfigürasyon değerleri tablosu

<table>
    <thead>
        <tr>
            <th>Anahtar</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>cache[key]</td>
            <td>Bu değer auth paketinin kayıt olacağı anahtarın önekidir. Bu değeri her proje için farklı girmeniz tavsiye edilir. Örneğin bu değer <kbd>Auth:ProjectName</kbd> olarak girilebilir.</td>
        </tr>
        <tr>
            <td>cache[storage]</td>
            <td>Hazıfa deposu yetki doğrulama esnasında kullanıcı kimliğini ön belleğe alır ve tekrar tekrar oturum açıldığında database ile bağlantı kurmayarak uygulamanın performans kaybetmesini önler.Varsayılan depo Redis tir.</td>
        </tr>
        <tr>
            <td>cache[provider][driver]</td>
            <td>Hazıfa deposu içerisinde kullanılan servis sağlayıcısının hangi servis sağlayıcısına bağlanacağını belirler. Varsayılan değer <kbd>redis</kbd> değeridir.</td>
        </tr>

        <tr>
            <td>cache[provider][connection]</td>
            <td>Hazıfa deposu içerisinde kullanılan servis sağlayıcısının hangi bağlantıyı kullanacağını belirler.</td>
        </tr>
        <tr>
            <td>cache[block][permanent][lifetime]</td>
            <td>Oturum açıldıktan sonra kullanıcı kimliği verileri <kbd>permanent</kbd> yani kalıcı hafıza bloğuna kaydedilir. Kalıcı blokta ön belleğe alınan veriler kullanıcının web sitesi üzerinde <kbd>hareketsiz</kbd> kaldığı andan itibaren varsayılan olarak <kbd>3600</kbd> saniye sonra yok olur.</td>
        </tr>
        <tr>
            <td>cache[block][temporary][lifetime]</td>
            <td>Opsiyonel olarak gümrükten pasaport ile geçiş gibi kimlik onaylama sistemi isteniyorsa,  oturum açıldıktan sonra kullanıcı kimliği verileri <kbd>$this->user->identity->makeTemporary()</kbd> komutu ile <kbd>temporary</kbd> hafıza bloğuna taşınabilirr. Geçici bloğa taşınmış veriler <kbd>300</kbd> saniye sonrasında yok olur. Kimlik onayladı ise <kbd>$this->user->identity->makePermanent()</kbd> komutu ile kimlik kalıcı hale getirilir ve kullanıcı sisteme tam giriş yapmış olur.
            </td>
        </tr>
        <tr>
            <td>security[passwordNeedsRehash][cost]</td>
            <td>Bu değer <kbd>password</kbd> kütüphanesi tarafından şifre hash işlemi için kullanılır. Varsayılan değer 6 dır fakat maximum 8 ila 12 arasında olmalıdır aksi takdirde uygulamanız yetki doğrulama aşamasında performans sorunları yaşayabilir. 8 veya 10 değerleri orta donanımlı bilgisayarlar için 12 ise güçlü donanımlı ( çekirdek sayısı fazla ) bilgisayarlar için tavsiye edilir.</td>
        </tr>
        <tr>
            <td>session[regenerateSessionId]</td>
            <td>Session id nin önceden çalınabilme ihtimaline karşı uygulanan bir güvenlik yöntemlerinden bir tanesidir. Bu opsiyon aktif durumdaysa oturum açma işleminden önce session id yeniden yaratılır ve tarayıcıda kalan eski oturum id si artık işe yaramaz hale gelir.</td>
        </tr>
        <tr>
            <td>login[rememberMe]</td>
            <td>Eğer kullanıcı beni hatırla özelliğini kullanarak giriş bilgilerini kalıcı olarak tarayıcısına kaydetmek istiyorsa  <kbd>__rm</kbd> isimli bir çerez ilk oturum açmadan sonra tarayıcısına kaydedilir. Bu çerezin sona erme süresi varsayılan olarak 6 aydır. Kullanıcı farklı zamanlarda uygulamanızı ziyaret ettiğinde eğer bu çerez ( remember token ) tarayıcısında kayıtlı ise Identity sınıfı içerisinde <kbd>Authentication\Recaller->recallUser($token)</kbd> metodu çalışmaya başlar ve beni hatırla çerezi veritabanında kayıtlı olan değer ile karşılaştırılır değerler birbiri ile aynı ise kullanıcı sisteme giriş yapmış olur. Güvenlik amacıyla her oturum açma (login) ve kapatma (logout) işlemlerinden sonra bu değer çereze ve veritabanına yeniden kaydedilir.</td>
        </tr>
        <tr>
            <td>middleware[unique.session]</td>
            <td>Tekil oturum açma opsiyonu aktif olduğunda aynı kimlik bilgileri ile farklı aygıtlardan yalnızca bir kullanıcı oturum açabilir. Auth katmanı içerisinde kullandığınız trait sınıfının davranışına göre en son açılan oturum her zaman aktif kalırken eski oturumlar otomatik olarak sonlandırılır. Fakat bu fonksiyon <kbd>app/classes/Http/Middlewares</kbd> dizinindeki auth katmanı çalıştırıldığı zaman devreye girer. Daha fazla bilgi için auth katmanı dökümentasyonunu inceleyebilirsiniz.</td> 
        </tr>
    </tbody>
</table>

<a name="adapters"></a>

#### Adaptörler

Yetki doğrulama adaptörleri uygulamaya esneklik kazandıran sorgulama arabirimleridir, yetki doğrulamanın bir veritabanı ile mi yoksa farklı bir protokol üzerinden mi yapılacağını belirleyen sınıflardır. Varsayılan arabirim türü <kbd>Database</kbd> (RDBMS or NoSQL) dir.

Farklı adaptörlerin çok farklı seçenekler ve davranışları olması muhtemeldir , ama bazı temel şeyler kimlik doğrulama adaptörleri arasında ortaktır. Örneğin, kimlik doğrulama hizmeti sorgularını gerçekleştirmek ve sorgulardan dönen sonuçlar yetki doğrulama adaptörleri için ortak kullanılır.

<a name="storages"></a>

#### Hafıza Depoları ( Storages )

Hazıfa deposu yetki doğrulama esnasında kullanıcı kimliğini ön belleğe alır ve tekrar tekrar oturum açıldığında database ile bağlantı kurmayarak uygulamanın performans kaybetmesini önler.

Yetki doğrulama şu anda depolama için sadece <kbd>Redis</kbd> veritabanı ve <kbd>Cache</kbd> sürücüsünü desteklemektedir. Cache sürücüsü seçtiğinizde <kbd>File, Memcache, Memcached, Apc</kbd> gibi sürücüleri cache konfigurasyon dosyanızdan ayarlamanız gerekmektedir.

<a name="null-storage"></a>

##### Null ( Session )

Null sınıfı varsayılan depodur ve depo olarak <kbd>cache</kbd> sınıfı yerine <kbd>session</kbd> paketini kullanır. Deponun aktif olması için auth konfigürasyon dosyasından cache deposunun Null olarak ayarlanması gerekir.

```php
'cache' => array(

    'storage' => '\Obullo\Authentication\Storage\Null',
    'provider' => array(
        'driver' => null,
        'connection' => 'second'
    ),
)
```

Null hafıza deposunda geçici kimlik oluşturma ve sadece bir aygıttan tekil oturum açtırma gibi gelişmiş işlevler çalışmaz.

<a name="redis-storage"></a>

##### Redis Veritabanı

Yetki doğrulama sınıfı hafıza deposu için varsayılan olarak redis kullanır. Aşağıdaki resim kullanıcı kimliklerinin hafıza deposunda nasıl tutulduğunu göstermektedir.

![PhpRedisAdmin](images/auth-redis.png?raw=true "PhpRedisAdmin")

Varsayılan hafıza deposu <kbd>providers/user.php</kbd> konfigürasyonundan değiştirilebilir.

```php
'cache' => array(

    'storage' => '\Obullo\Authentication\Storage\Redis',
    'provider' => array(
        'driver' => 'redis',
        'connection' => 'second'
    ),
)
```
<a name="cache-storage"></a>

##### Cache ( File, Apc, Memcache, Memcached, Redis )

Eğer cache sürücülerini kullanmak istiyorsanız <kbd>providers/user.php</kbd> konfigürasyon dosyasından ayarları aşağıdaki gibi değiştirmeniz yeterli olacaktır.

```php
'cache' => array(

    'storage' => '\Obullo\Authentication\Storage\Cache',   // Storage driver uses cache package
    'provider' => array(
        'driver' => 'memcached',
        'connection' => 'default'
    ),
)
```

Yukarıda görüldüğü gibi provider ayarlarından driver sekmesini sürücü ismi ile değiştirmeyi unutmamalısınız. Redis dışında bir çözüm kullanıyorsanız yazmış olduğunuz kendi hafıza depolama sınfınızı provider driver anahtarına girerek kullanabilirsiniz.

<a name="running"></a>

### Çalıştırma

<a name="service"></a>

#### Servis Konfigürasyonu

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
        'db.adapter'=> '\Obullo\Authentication\Adapter\Database',
        'db.model'  => '\Obullo\Authentication\Model\Pdo\User',
        'db.provider' => [
            'name' => 'database',
            'params' => [
                'connection' => 'default'
            ]
        ],
        'db.tablename' => 'users',
        'db.id' => 'id',
        'db.identifier' => 'username',
        'db.password' => 'password',
        'db.rememberToken' => 'remember_token',
        'db.select' => [
            'date',
        ],
        .
)
```

**db.adapter :** Yetki doğrulama adaptörleri yetki doğrulama servisinde <kbd>Database</kbd> (RDBMS or NoSQL) veya <kbd>dosya-tabanlı</kbd> gibi farklı türde kimlik doğrulama biçimleri olarak kullanılırlar.

**db.model :** Model sınıfı yetki doğrulama sınıfına ait veritabanı işlemlerini içerir. Bu sınıfa genişleyerek bu sınıfı özelleştirebilirsiniz bunun için aşağıda veritabanı sorgularını özelleştirmek başlığına bakınız.

**db.provider.name :** Veritabanı servis sağlayıcınızın ismidir. Veritabanı işlemlerinin hangi servis sağlayıcısının kullanması gerektiğini tanımlar.

**db.provider.params.connection:** Veritabanı servis sağlayıcısının hangi bağlantıyı seçmesi gerektiğini tanımlar.

**db.tablename:** Veritabanı işlemleri için tablo ismini belirlemenize olanak sağlar. Bu konfigürasyon veritabanı işlemlerinde kullanılır.

<a name="loading-service"></a>

#### Servisi Yüklemek

Yetki doğrulama paketi sınıflarına erişim <kbd>User</kbd> servisi yani <kbd>AuthManager</kbd> üzerinden sağlanır, user servisi önceden <kbd>app/providers.php</kbd> dosyası içerisinde aşağıdaki gibi tanımlıdır.

```php
$c['app']->service(
    [
        'user' => 'Obullo\Authentication\AuthManager',
    ]
);
```

<kbd>User</kbd> servisi yetki doğrulama servisine ait olan <kbd>Login</kbd>, <kbd>Identity</kbd> ve <kbd>Activity</kbd> gibi sınıfları kontrol eder, böylece paket içerisinde kullanılan tüm sınıflara tek bir servis üzerinden erişim sağlanmış olur.

```php
$this->user->login->method();
$this->user->identity->method();
$this->user->activity->method();
$this->user->storage->method();
```

<a name="accessing-config-variables"></a>

#### Konfigürasyon Değerlerine Erişmek

> User servisi AuthManager sınıfı içerisinden gönderilen parametreleri auth konfigürasyon dosyasındaki parametreler ile birleştirerek tüm konfigurasyonu tek bir elden yönetmeye yardımcı olur.

<a name="authconfig"></a>

##### AuthConfig::get();

<kbd>config/auth.php</kbd> konfigürasyon dosyası veya user servisi içinde tanımlı konfigürasyon değerlerine döner.

Servis parametreleri için bir örnek

```php
echo AuthConfig::get('db.identifier');   // Çıktı username
echo AuthConfig::get('db.password');     // Çıktı password
echo AuthConfig::get('cache.key');       // Çıktı Auth
```

Tüm değerleri almak için parametre girilmez.

```php
$params = AuthConfig::getParameters();

print_r($params);  // Konfigürasyon değerleri
```

<a name="login"></a>

### Oturum Açma

Oturum açma işlemi bir uygulamanın en kritik bölümlerinden biridir. Bir oturum açma işleminde oturum açma / kapatma, mevcut kullanıcı oturumları almak gibi işlemleri login sınıfı, oturum açma sonuçlarını ise AuthResult sınıfı kontrol eder. Oturum açma olaylarına abone olmak için ise [Anotasyonlar](Annotations.md) kullanılır.

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

<a name="login-example"></a>

#### Oturum Açma Örneği

Oturum açmayı bir örnekle daha iyi kavrayabiliriz, membership adlı altında bir dizin açalım ve login controller dosyamızı bu dizin içerisinde yaratalım.


```php
+ app
+ config
+ resources
- modules
    - membership
        - view
            login.php
        Login.php
```

Login kontrolör dosyamızın içeriğini inceleyelim.

```php
namespace Membership;

Class Login extends \Controller
{
    public function index()
    {
        if ($this->request->isPost()) {

            $this->validator->setRules('email', 'Email', 'required|email|trim');
            $this->validator->setRules('password', 'Password', 'required|min(6)|trim');

            if (! $this->validator->isValid()) {
                $this->form->setErrors($this->validator);
            } else {

                $authResult = $this->user->login->attempt(
                    'users',
                    [
                        'db.identifier' => $this->validator->getValue('email'), 
                        'db.password'   => $this->validator->getValue('password'),
                    ],
                    $this->request->post('rememberMe')
                );

                if ($authResult->isValid()) {
                    $this->flash->success('You have authenticated successfully.')->url->redirect('membership/restricted');
                } else {
                    $this->form->setResults($authResult->getArray());
                }
            }
        }
        $this->view->load('login');
    }
}
```

Yukarıdaki örnekte attempt fonksiyonu <kbd>AuthResult</kbd> nesnesine geri dönüyor ve Auth result sınıfı ise isValid() metodu ile yetkilendirmenin başarılı olup olmadığını anlıyor. Yetkilendirme başarılı ise kullanıcı Guest kullanıcılarının erişemeyeceği bir sayfaya yönlendiriliyor. Eğer oturum açma başarısız ise sonuçlar form sınıfına gönderiliyor.

View dosyası

```php
<h1>Login Example</h1>

<section><?php echo $this->flash->output() ?></section>

<section><?php
if ($results = $this->form->resultsArray()) {
    foreach ($results['messages'] as $message) {
        echo $this->form->getMessage($message);
    }
}
?></section>

<section>
    <form action="/membership/login" method="POST">
        <table width="100%">
            <tr>
                <td style="width:20%;">Email</td>
                <td><?php echo $this->form->getError('email'); ?>
                <input type="text" name="email" value="<?php echo $this->form->getValue('email') ?>" />
                </td>
            </tr>
            <tr>
                <td>Password</td>
                <td><?php echo $this->form->getError('password'); ?>
                <input type="password" name="password" value="" /></td>
            </tr>
            <tr>
                <td></td>
                <td><?php echo $this->form->getError('rememberMe'); ?>
                <input type="checkbox" name="rememberMe" value="1"  id="rememberMe"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="dopost" value="DoPost" /></td>
            </tr>
            </table>
        </form>
</section>
```

<a name="login-results"></a>

#### Oturum Açma Sonuçları

Oturum açma denemesi yapıldığında <kbd>AuthResult</kbd> sınıfı ile sonuçlar doğrulama filtresinden geçer ve oluşan hata kodları ve mesajlar bir dizi içerisine kaydedilir.

```php
$authResult = $this->user->login->attempt(
    'users',
    [
        'db.identifier' => $this->request->post('email'), 
        'db.password' => $this->request->post('password')
    ],
    $this->request->post('rememberMe')
);

if ($authResult->isValid()) {

    $row = $authResult->getResultRow();

    // Go ..

} else {

    print_r($authResult->getArray()); // get errors

    /* Array ( 
        [code] => -2 
        [messages] => Array ( 
            [0] => Supplied credentials invalid. 
        ) 
        [identifier] => user@example.com 
    ) 
    */
}
```
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
            <td>AuthResult::FAILURE_UNCATEGORIZED</td>
            <td>Kategorize edilemeyen bir hata oluştuğu anlamına gelir.</td>
        </tr>
        <tr>
            <td>-4</td>
            <td>AuthResult::TEMPORARY_AUTH_HAS_BEEN_CREATED</td>
            <td>Geçici kimlik bilgilerinin oluşturulduğuna dair bir bilgidir.</td>
        </tr>
        <tr>
            <td>-5</td>
            <td>AuthResult::FAILURE_UNVERIFIED</td>
            <td>Yetki doğrulama onayı aktif iken geçici kimlik bilgilerin henüz doğrulanmadığını gösterir.</td>
        </tr>
        <tr>
            <td>-6</td>
            <td>AuthResult::WARNING_ALREADY_LOGIN</td>
            <td>Kullanıcı kimliğinin zaten doğrunlanmış olduğunu gösterir.</td>
        </tr>
        <tr>
            <td>1</td>
            <td>AuthResult::SUCCESS</td>
            <td>Yetki doğrulama başarılıdır.</td>
        </tr>

    </tbody>
</table>

<a name="identities"></a>

### Kimlikler

Kimlikler içerisinde kendi fonksiyonlarınızı oluşturabilmeniz için kimlik sınıfları <kbd>app/classes/Auth</kbd> klasörü altında gruplanmıştır. Bu klasör o2 auth paketine genişler ve aşağıdaki dizindedir.

```php
- app
    - classes
        - Auth
            Identities
                - AuthorizedUser.php
                - GenericUser.php
        + Model
```

<kbd>AuthorizedUser</kbd> yetkili kullanıcıların kimliklerine ait metodları, <kbd>GenericUser</kbd> sınıfı ise yetkisiz yani Guest diye tanımladığımız kullanıcıların kimliklerine ait metodları içerir. Bu sınıflardaki <kbd>get</kbd> metotları kullanıcı kimliklerinden <kbd>okuma</kbd>, <kbd>set</kbd> metotları ise kimliklere <kbd>yazma</kbd> işlemlerini yürütürler. Bu sınıflara metodlar ekleyerek ihtiyaçlarınıza göre düzenleme yapabilirsiniz fakat <kbd>AuthorizedUserInterface</kbd> ve <kbd>GenericUserInterface</kbd> sınıfları içerisindeki tanımlı metodlardan birini bu sınıflar içerisinden silmemeniz gerekir.

<a name="identities-table"></a>

#### Kimlikler Tablosu

<table>
    <thead>
        <tr>
            <th>Class</th>    
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Auth\Identities\GenericUser</td>
            <td>Ziyaretçi (Guest) kullanıcısına ait genel kimlik profilidir.</td>
        </tr>
        <tr>
            <td>Auth\Identities\AuthorizedUser</td>
            <td>Yetkilendirilmiş kullanıcıya ait kimlik profilidir.</td>
        </tr>
        <tr>
            <td>Auth\Model\User</td>
            <td>Database sorguları üzerinde değişiklik yapabilmenizi sağlayan ara yüzdür. Seçimlik olarak yaratılır ve yaratıldığında <kbd>\Obullo\Authentication\Model\User</kbd> sınıfına genişlemesi gerekir.</td>
        </tr>
    </tbody>
</table>

<a name="identity-class"></a>

#### Kimlik Sınıfı İşlevleri

Identity sınıfı bir kulllanıcın kimliğinin olup olmadığını eğer varsa kullanıcıya ait yetkilendirilmiş kimliği yönetmenizi sağlar. Kullanıcı kimliği O2 paketi içerisindedir ve <kbd>app/Auth/Identities/AuthorizedUser</kbd> sınıfına genişler ve bu sınıf aşağıdaki kimlik işlevlerini yönetir.

* Kimlikten veri okuma ve kimliğe veri kaydetme
* Kullanıcı kimliğinin olup olmadığı kontrolü
* Kullanıcı kimliğinin kalıcı olup olmadığı
* Kullanıcı kimliğini kalıcı veya geçici hale dönüştürme. ( makeTemporary(), makePermanent() )
* Kullanıcının oturumunu sonlandırma ( logout )
* Kullanıcı kimliğini tamamen yok etme ( destroy )
* Beni hatırla özelliği kullanılmışsa kullanıcı kimliğini çerezden kalıcı olarak silme ( forgetMe )

Aşağıda örnek bir kullanıcı kimliğini nasıl görüntüleyebileceğinizi gösteriliyor.

```php
print_r($this->user->identity->getArray()); // Çıktılar
/*
Array
(
    [__activity] => Array
        (
            [last] => 1413454236
        )

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

Yetki doğrulama paketi kendi anahtarlarını oluştururup bunları hafıza deposunu kaydederken 2 adet underscore önekini kullanır. Yetki doğrulama paketine ait olan bu anahtarlar yazma işlemlerinde çakışma olmaması için bu "__" önek kullanılarak ayırt edilir. Diğer bir anahtar <kbd>__activity</kbd> ise yetkisi doğrulanmış kullanıcılar ile igili verileri kaydetmeniz için ayrılmış bir anahtardır.

<table>
    <thead>
        <tr>
            <th>Anahtar</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>__activity</td>
            <td>Online kullanıcı aktivite verilerini içerir: Son aktivite zamanı ve diğer eklemek istediğiniz veriler gibi.</td>
        </tr>
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

> Identity sınıfı bir kulllanıcın kimliğinin olup olmadığını varsa kullanıcıya ait yetkilendirilmiş kimliği yönetmenizi sağlar.

##### $this->user->identity->check();

Kullanıcının yetkisinin olup olmadığını kontrol eder. Yetkili ise <kbd>true</kbd> değilse <kbd>false</kbd> değerine döner.

##### $this->user->identity->guest();

Kullanıcının yetkisi olmayan kullanıcı yani ziyaretçi olup olmadığını kontrol eder. Ziyaretçi ise <kbd>true</kbd> değilse <kbd>false</kbd> değerine döner.

##### $this->user->identity->exists();

Kimliğin önbellekte olup olmadığını kotrol eder. Varsa <kbd>true</kbd> yoksa <kbd>false</kbd>değerine döner.

##### $this->user->identity->expire($ttl, $block = '__permanent');

Kullanıcı kimliğini girilen süreye göre geçtikten sonra yok olmasını sağlar.

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

<a name="identity-get-methods"></a>


#### Identity "Get" Metotları

------

> Identity get metotları hafıza deposu içerisinden yetkisi doğrulanmış kullanıcıya ait kimlik verilerine ulaşmanızı sağlar.

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

> Uygulamanızda ihtiyaç duyduğunuz diğer metotları <kbd>app/classes/Auth/Identities/AuthorizedUser</kbd> sınıfı içerisine eklemeniz önerilir.

<a name="identity-set-methods"></a>

#### Identity "Set" Metotları

------

>Identity set metotları hafıza deposu içerisinden yetkisi doğrulanmış kullanıcıya ait kimlik verilerine yazmanızı sağlar.

##### $this->user->identity->variable = 'value'

Kimlik dizisine yeni bir değer ekler.

##### unset($this->user->identity->variable)

Kimlik dizisinde varolan değeri siler.

##### $this->user->identity->setArray(array $attributes)

Tüm kullanıcı kimliği dizisinin üzerine girilen diziyi yazar.


<a name="login-reference"></a>

#### Login Sınıfı Referansı

------

> Login sınıfı yetkisi doğrulanmamış (GenericUser) yada doğrulanmış (AuthorizedUser) kullanıcıya ait oturum işlemlerini yönetmenizi sağlar.

##### $this->user->login->attempt(array $credentials, $rememberMe = false);

Bu fonksiyon kullanıcı oturumunu açmayı dener ve AuthResult nesnesine döner.

##### $this->user->login->validate(array $credentials);

Yetki doğrulama yapmadan kullanıcı Guest kimliği bilgilerine doğrulama işlemi yapar.Bilgiler doğruysa true değerine yanlış ise false değerine döner.

##### $this->user->login->validateCredentials(AuthorizedUser $user, array $credentials);

AuthorizedUser kimliğine sahip kullanıcı bilgilerini dışarıdan gelen yeni bilgiler ile karşılaştırarak doğrulama yapar.

##### $this->user->login->getUserSessions();

Geçerli kullanıcının önbelleğe kaydedilmiş oturumlarına bir dizi içerisinde geri döner. Her açılan oturuma bir login id verilir ve kullanıcılar farklı tarayıcılarda veya aygıtlarda birden fazla oturum açmış olabilirler.

<a name="authResult-reference"></a>

#### AuthResult Sınıfı Referansı

------

>AuthResult sınıfı login doğrulamasından sonra geri dönen sonuçları elde etmeyi ve hata kodlarını yönetmeyi sağlar.

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


<a name="database-model"></a>

#### Veritabanı Sorgularını Özelleştirmek

Eğer mevcut database sorgularında değişiklik yapmak yada bir NoSQL çözümü kullanmak istiyorsanız [Auth-DatabaseModel.md](Auth-DatabaseModel.md) dökümentasyonuna gözatın.

<a name="additional-features"></a>

#### Ek Özellikler

Auth paketi yetki doğrulama onayı ve aktivite verilerini kaydetme gibi bazı ek özellikler ile gelir. Bu türden özelliklere ihtiyacınız varsa [Auth-AdditionalFeatures.md](Auth-AdditionalFeatures.md) dökümentasyonuna gözatın.

<a name="events"></a>

#### Olaylar

Oturum açma aşamasında login öncesi ve login sonrası <kbd>$event->fire('login.before', array($credentials))</kbd> ve <kbd>$event->fire('login.after', array($authResult))</kbd> adlı iki olay event sınıfı ile <kbd>Obullo/Authentication/User/Login</kbd> sınıfı attempt metodu içerisinde ilan edilmiştir. Olaylardan <kbd>login.before</kbd> metoduna kullanıcı giriş bilgileri parametre olarak gönderilirken <kbd>login.after</kbd> metoduna ise <kbd>Obullo/Authentication/AuthResult</kbd> sınıfı çıktıları parametre olarak gönderilir.

Oturum açma olayları hakkında daha fazla bilgi için [Events.md](Events.md) dökümentasyonunu inceleyebilirsiniz.

<a name="middleware"></a>

#### Auth Katmanları

Auth katmanları uygulamanız içerisinde <kbd>app/classes/Http/Middlewares/</kbd> klasörü altında bulunan <kbd>Auth.php</kbd> ve <kbd>Guest.php</kbd> dosyalarıdır. Auth dosyası uygulamaya giriş yapmış olan kullanıcıları kontrol ederken Guest katmanı ise uygulamaya giriş yetkisi olmayan kullanıcıları kontrol eder. Auth ve Guest katmanlarının çalışabilmesi için route yapınızda middleware anahtarına ilgili modül için birkez tutturulmaları gerekir.

Auth katmanları hakkında daha fazla bilgi için [Middleware-Auth.md](Middleware-Auth.md) dökümentasyonunu inceleyebilirsiniz.