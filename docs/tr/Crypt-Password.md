
## Şifre Sınıfı

Şifre sınıfı uygulamanıza kaydettiğiniz kullanıcılar için Bcrypt algoritması ile güvenli şifreler üretir.

<ul>
    <li>
        <a href="#information">Önbilgi</a>
        <ul>
            <li><a href="#why-bcrypt">Neden Bcrypt Kullanmalıyım</a></li>
            <li><a href="#scheme-identifiers">Şema Tanımlayıcıları</a></li>
            <li><a href="#structure">Yapı</a></li>
            <li><a href="#diagram">Diagram</a></li>
        </ul>
    </li>

    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li><a href="#loading-component">Bileşeni Yüklemek</a></li>
            <li><a href="#secure-passwords">Güvenli Şifre Yaratmak</a></li>
            <li><a href="#verify">Güvenli Şifreyi Doğrulamak</a></li>
            <li><a href="#rehash">Güvenli Şifreyi Yeniden Şifreleme</a></li>
            <li><a href="#getinfo">Şifre Bilgileri</a></li>
        </ul>
    </li>

    <li><a href="#method-reference">Fonksiyon Referansı</a></li>
</ul>

<a name="information"></a>

### Önbilgi

Güvenli şifreler üretebilmek bilinmesi gerekenleri aşağıda sizin için sıraladık.

<a name="why-bcrypt"></a>

#### Neden Bcrypt Kullanmalıyım ?

Bcrypt blowfish algoritmasını kullanarak güvenli şifreler üretir. Neden bcrypt kullanılması gerektiğini bir kaç madde ile özetlersek;

- Yüksek güvenlik sağlar bu tipteki şifreleri çözebilmek için saldırganın çok yüksek donanımlı bir bilgisayar kullanması gerekir.
- Bcrypt tek yönlü bir algoritmadır bu da şifrenin tekrar plain-text formatına geri çevrilemeyeceği anlamına gelir.
- Bcrypt sınıfı herbir şifreyi farklı bir tuz (salt) ile şifreler.

<b>MD5</b> şifreleme yöntemi ise hızlı bir metot olduğundan dolayı belirsiz hassas olmayan verilerde çok sık kullanılır. Bu özelliğine karşın [rainbow tabloları](http://en.wikipedia.org/wiki/Rainbow_table) ` ndan anlaşılabileceği gibi bir güvenli şifre operasyonunda kolayca çözülebilmeleri dezavantajdır.

İşte bu nedenle Bcrypt algoritması önem kazanır. Çalışma faktörü ( cost opsiyonu ) "12" belirlendiğinde Bcrypt bir şifreyi *0.3 saniyede* MD5 ise mikrosaniyeden az bir zamanda şifreler.

> Daha fazla bilgi için bu makaleye gözatabilirsiniz. <a href="http://phpmaster.com/why-you-should-use-bcrypt-to-hash-stored-passwords/" target="_blank">Why you should use Bcrypt</a>.

<a name="scheme-identifiers"></a>

#### Şema Tanımlayıcıları

Bcrypt şifreleme yönteminde algoritma şema tanımlayıcıları şifrelenmiş güvenli şifre hakkında bilgiler verir.

- `$2a$` - Potansiyel olarak demode (buggy) olmuş bir algorithma ile yaratılmış şifre.
- `$2x$` - Geriye dönük uyumluluk için Bcrypt uyumluluk seçeneği implementasyonu.
- `$2y$` - Varolan en yeni şema ile yaratılmış versiyon *(crypt_blowfish 1.1 ve yeni sürümlerde)*.

> **Not:** Varsayılan şema en yeni şema olan `$2y$` değeridir. Diğer şemalar eski versiyonlar da üretilen şifreler için kullanılır.

<a name="structure"></a>

#### Yapı

Şifrelenmiş bir güvenli şifre aşağıdaki yapıda gibi gözükür.

```php
$2a$12$Some22CharacterSaltXXO6NC3ydPIrirIzk1NdnTz0L/aCaHnlBa
```

- `$2a$` php yorumlayıcısına hangi şemayı kullanması gerektiğini anlatır. *(Bcrypt tabanlı)*
- `12$` şifreleme mekanizmasının çalışma faktörü yani "cost" değeridir.
- `Some22CharacterSaltXXO` rastgele oluşturulan bir tuzlama değeridir *(OpenSSL tarafından oluşturulur)*
- `6NC3ydPIrirIzk1NdnTz0L/aCaHnlBa` güvenli şifre değeridir 31 karakterden oluşur.

<a name="diagram"></a>

#### Diagram

```php
$2a$12$Some22CharacterSaltXXO6NC3ydPIrirIzk1NdnTz0L/aCaHnlBa
\___________________________/\_____________________________/
  \                            \
   \                            \ Actual Hash (31 chars)
    \
     \  $2a$   12$   Some22CharacterSaltXXO
        \__/    \    \____________________/
          \      \              \
           \      \              \ Salt (22 chars)
            \      \
             \      \ Number of Rounds (work factor)
              \
               \ Hash Header
```

> Diagram bu kaynaktan alınmıştır [Andrew Moore's structure](http://stackoverflow.com/a/5343655).

<a name="running"></a>

### Çalıştırma

Şifre sınıfının çalışabilmesi için <kbd>app/components.php</kbd> dosyasından bileşen olarak konfigüre edilmesi gerekir.

```php
$c['app']->component(
    [
        'password' => 'Obullo\Crypt\Password\Bcrypt',
        ..
    ]
);
```

<a name="loading-service"></a>

#### Bileşeni Yüklemek

Şifre bileşeni metotlarına aşağıdaki gibi erişilebilir.

```php
$this->c['password']->method();
```

<a name="secure-passwords"></a>

#### Güvenli Şifre Yaratmak

Güvenli şifreler yaratmak için <b>hash</b> metodu kullanılır.

```php
echo $this->password->hash('obulloFramework', ['cost' => 10])

// Gives
// $2y$10$g6KqDmd.qZPQMaBnzhOeW.tYq03iqBe/.f3flea2zlzwyHWKBJVnm
```

Eğer işlem başarısız olur metot <b>false</b> değerine başarılı ise güvenli şifre değerine geri döner.

> **Not:** Cost değeri uygulamanın güvenliği ve donanımınızın kuvvetine göre ayarlanmalıdır. Zira sunucunuzun donanınımı zayıfsa yada şifre doğrulama aşamasında performans sorunları yaşıyorsanız bu değeri 6 olarak ayarlamanız önerilir. 8 veya 10 değerleri orta donanımlı bilgisayarlar için 12 ise güçlü donanımlı ( çekirdek sayısı fazla ) bilgisayarlar için tavsiye edilir.

<a name="verify"></a>

#### Güvenli Şifreyi Doğrulamak

Kullanıcıya ait bir şifreyi doğrulamak için kayıt edilen şifrenin <b>plain-text</b> formatındaki gerçek değerine ve <b>şifrelenmiş</b> değerine ihtiyaç duyulur. Bu iki değer karşılaştırılarak şifre doğruluğu kontrol edilir.

```php
$hash  = '$2y$10$g6KqDmd.qZPQMaBnzhOeW.tYq03iqBe/.f3flea2zlzwyHWKBJVnm';
$value = 'obulloFramework'

if ($this->password->verify($value, $hash)) {
    echo 'Şifre doğru !';
} else {
    echo 'Şifre yanlış.';
}
```

Eğer doğrulama başarılı ise metot <b>true</b> değerine aksi durumda <b>false</b> değerine döner.

<a name="rehash"></a>

#### Güvenli Şifreyi Yeniden Şifreleme

Güvenli şifre doğrulandıktan sonra eğer api tarafında güvenlik için şifrenin periyodik yenilenme zamanı gelmişse needsRehash() metodu <b>true</b> değerine döner. Böylece sisteme giriş yapan kullanıcılara ait şifreler periyodik olarak veritabanında yenilenmiş olurlar.

```php
$hash    = '$2y$10$g6KqDmd.qZPQMaBnzhOeW.tYq03iqBe/.f3flea2zlzwyHWKBJVnm';
$options = ['cost' => 12];
$value   = 'obulloFramework'

if ($this->password->verify($value, $hash)) {

    echo 'Şifre doğru !';

	if ($this->password->needsRehash($hash, $options)) {

		$hash = $this->password->hash($value, $options);

		// Yenilenen şifreyi veritabanında güncelleme kodu buraya gelmeli ..
	}

} else {
    echo 'Yanlış şifre.';
}
```

Şifre ilk şifrelenirken verilen opsiyonlar ile yeniden şifreleme işlemi için verilen opsiyonlar aynı olmalıdır.

> **Not:** Bu metot php 5.5.0 ve üzeri sürümlerde çalışır. Diğer sürümlerde <b>false</b> değerine geri dönecektir.

<a name="getinfo"></a>

#### Şifre Bilgileri

Eğer şifrenlenmiş şifre hakkında detaylı bilgiler isteniyorsa bunun için getInfo() metodu kullanılır.

```php
$hash = '$2y$10$g6KqDmd.qZPQMaBnzhOeW.tYq03iqBe/.f3flea2zlzwyHWKBJVnm';

var_dump($this->password->getInfo($hash));

// Gives
array(3) {
  ["algo"]=>
  int(1)
  ["algoName"]=>
  string(6) "bcrypt"
  ["options"]=>
  array(1) {
    ["cost"]=>
    int(10)
  }
}
```

> **Not:** getInfo() metodu php 5.5.0 ve üzeri sürümlerde çalışır. Diğer sürümlerde <b>null</b> değerine geri dönecektir.

<a name="method-reference"></a>

#### Fonksiyon Referansı

-----

##### $this->password->hash(string $value, array $options = array());

Şifrelenmiş bir güvenli şifre yaratır.

##### $this->password->verify(string $value, string $hashedValue);

Şifrelenmiş güvenli şifreyi dışarıdan gelen değerlere göre doğrular. Doğrulama için kayıt edilen şifrenin <b>plain-text</b> formatındaki gerçek değerine ve <b>şifrelenmiş</b> değerine ihtiyaç duyulur. Bu iki değer karşılaştırılarak şifre doğruluğu kontrol edilir. Karşılaştırma sonucu doğrulama başarılı ise metot <b>true</b> değerine aksi durumda <b>false</b> değerine döner.

##### $this->password->needsRehash(string $hashedValue, array $options = array());

Güvenli şifre doğrulandıktan sonra eğer şifrenin periyodik yenilenme zamanı gelmişse <b>true</b> değerine döner aksi durumda <b>false</b> değerine döner. Php 5.5.0 dan küçük sürümlerde desteklenmediğinden bu sürümlerde varsayılan olarak <b>false</b> değerine döner. Şifre ilk şifrelenirken verilen opsiyonlar ile yeniden şifreleme işlemi için verilen opsiyonlar aynı olmalıdır.

##### $this->password->getInfo(string $hash);

Şifrelenmiş güvenli şifre hakkında detaylı bilgilere bir dizi içerisinde geri döner. Php 5.5.0 ve üzeri sürümlerde desteklenir.

##### $this->password->setIdentifier($id = '2y');

Php 5.5.0 altındaki sürümler için algoritmanın varsayılan şema tanımlayıcısını değiştirir.