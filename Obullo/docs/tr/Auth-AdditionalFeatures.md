
## Ek Özellikler

Auth paketinde yetki doğrulama onayı ve aktivite verilerini kaydetme gibi bazı ek özellikler de kullanılabilir.

<ul>
    <li>
        <a href="#authentication-verify">Yetki Doğrulama Onay Özelliği</a>
        <ul>
            <li><a href="#temporary-identities">Geçici Kimlikler</a></li>
            <li><a href="#making-temporary-identity">Geçici Kimlik Oluşturmak</a></li>
            <li><a href="#making-permanent-identity">Kalıcı Kimlik Oluşturmak</a></li>
            <li><a href="#temporary-identity-example">Geçici Oturum Açma Örneği</a></li>
        </ul>
    </li>
    <li><a href="#saving-user-activity-data">Kullanıcı Aktivite Verilerini Kaydetmek</a></li>
</ul>

<a name="authentication-verify"></a>

### Yetki Doğrulama Onay Özelliği

Yetki doğrulama onayı kullanıcının kimliğini sisteme giriş yapmadan önce <b>email</b>, <b>sms</b> yada <b>mobil çağrı</b> gibi yöntemlerle onay işleminden geçirmek için kullanılan ekstra bir özelliktir.

Kullanıcı başarılı olarak giriş yaptıktan sonra kimliği kalıcı olarak ( varsayılan 3600 saniye ) önbelleklenir. Eğer kullanıcı onay adımından geçirilmek isteniyorsa kalıcı kimlikler <kbd>$this->user->identity->makeTemporary()</kbd> metodu ile geçici hale ( varsayılan 300 saniye ) getirilir. Geçici olan bir kimlik 300 saniye içerisinde kendiliğinden yokolur. 

Bu özelliği kullanmak istiyorsanız aşağıda daha detaylı bilgiler bulabilirsiniz.


<a name="temporary-identities"></a>

#### Geçiçi Kimlikler

Geçici kimlikler genellikle yetki doğrulama onaylaması için kulanılırlar.

Kullanıcının geçici kimliğini onaylaması sizin ona <b>email</b>, <b>sms</b> yada <b>mobil çağrı</b> gibi yöntemlerinden herhangi biriyle göndermiş olacağınız onay kodu ile gerçekleşir. Eğer kullanıcı 300 saniye içerisinde ( bu konfigürasyon dosyasından ayarlanabilir bir değişkendir ) kullanıcı kendisine gönderilen onay kodunu onaylayamaz ise geçiçi kimlik kendiliğinden yok olur.

Eğer kullanıcı onay işlemini başarılı bir şekilde gerçekleştirir ise <kbd>$this->user->identity->makePermanent()</kbd> metodu ile kimliği kalıcı hale getirmeniz gereklidir.
Bir kimlik kalıcı yapıldığında kullanıcı tam olarak yetkilendirilmiş olur.

<a name="making-temporary-identity"></a>

#### Geçici Kimlik Oluşturmak

```php
$this->user->identity->makeTemporary();
```
Bu fonksiyonun oturum denemesi fonksiyonundan sonra kullanılması gerekmektedir. Bu fonksiyon kullanıldığında eğer oturum açma başarılı ise kalıcı olarak kaydedilen kimlik hafıza bloğunda geçici hale getirilir. Fonksiyonun kullanılmadığı durumlarda ise varsayılan olarak tüm kullanıcılar sistemde kalıcı oturum açmış olurlar.

Bu aşamadan sonra onaya düşen kullanıcı için bir onay kodu oluşturup ona göndermeniz gerekmektedir. Onay kodu onaylanırsa bu onaydan sonra aşağıdaki method ile kullanıcıyı kalıcı olarak yetkilendirebilirsiniz.

<a name="making-permanent-identity"></a>

#### Kalıcı Kimlik Oluşturmak

```php
$this->user->identity->makePermanent();
```

Yukarıdaki method geçici kimliği olan kullanıcıyı kalıcı kimlikli bir kullanıcı haline dönüştürür. Kalıcı kimliğine kavuşan kullanıcı artık sistemde tam yetkili konuma gelir. Kalıcılık kullanıcı kimliğinin önbelleklenmesi (cache) lenmesi demektir. Önbelleklenen kullanıcının kimliği tekrar oturum açıldığında database sorgusuna gidilmeden elde edilmiş olur. Kalıcı kimliğin önbelleklenme süresi konfigürasyon dosyasından ayarlanabilir bir değişkendir. Geçici veya kalıcı kimlik oluşturma fonksiyonları kullanılmamışsa sistem <b>varsayılan</b> olarak her kimliği <b>kalıcı</b> olarak kaydedecektir.

<a name="temporary-identity-example"></a>

#### Geçici Oturum Açma Örneği

Geçici oturumun kalıcı oturumdan farkı <kbd>$this->user->identity->makeTemporary();</kbd> metodu ile oturum açıldıktan sonra kimliğin geçici hale getirilmesidir.

Örnek

```php
$authResult = $this->user->login->attempt(
    [
        'db.identifier' => $this->request->post('email'), 
        'db.password' => $this->request->post('password')
    ],
    $this->request->post('rememberMe')
);
```

```php
if ($authResult->isValid()) {

    $this->user->identity->makeTemporary();

    $this->flash->success('Verification code has been sent.');
    $this->url->redirect('membership/confirm_code');
} 
```

Yukarıdaki kod bloğuna login kontrolör içerisine entegre edip çalıştırdığınıza login denemesi başarılı ise geçici kimlik oluşturulur. Sonraki adım için bir <b>membership/confirm_code</b> sayfası oluşturun ve bu sayfada oluşturacağınız formda kullanıcı onay kodunu doğru girdi ise <kbd>$this->user->identity->makePermanent();</kbd> metodunu kullanarak kullanıcıyı yetkilendirin.


```php
+ app
+ assets
- modules
    - membership
        + view
        Login.php
        Confirm_Code.php
```
<a name="saving-user-activity-data"></a>

### Kullanıcı Aktivite Verilerini Kaydetmek

Kullanıcı aktivite sınıfı yetkilendirilmiş kullancılara ait meta verilerini kaydeder. Son aktivite zamanı ve diğer eklemek istediğiniz harici veriler bu sınıf aracılığıyla activity key içerisinde tutulur. Her sayfa yenilenmesinde bu veriler güncellenir.

```php
$this->user->activity->set('sid', $this->session->get('session_id'));
$this->user->activity->set('date', time());

// __activity a:3:{s:3:"sid";s:26:"f0usdabogp203n5df4srf9qrg1";s:4:"date";i:1413539421;}
```

#### Activity Sınıfı Referansı

------

>Aktivite verileri, son aktivite zamanı gibi anlık değişen kullanıcı verilerini önbellekte tutabilmeyi sağlayan sınıftır.

##### $this->user->activity->set($key, $val);

Aktivite dizinine bir anahtar ve değerini ekler.

##### $this->user->activity->get($key);

Aktivite dizininde anahtarla eşleşen değere geri döner.

##### $this->user->activity->remove($key);

Daha önce set edilen değeri temizler.

##### $this->user->activity->destroy();

Tüm aktivite verilerini önbellekten temizler.