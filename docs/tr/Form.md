
## Form Sınıfı

------

Form sınıfı özel form mesajlarını, validator sınıfı çıktılarını, işlem sonuçlarını, ve form hatalarını yönetir.

<ul>
<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#status-table">Durum Metotları Tablosu</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#loading-service">Sınıfı Yüklemek</a></li>
        <li><a href="#basic-usage">Bir Form Mesajı Göstermek</a></li>
        <li>
            <a href="#using-with-validator">Doğrulama Hatalarını Form Sınıfı ile Göstermek</a>
            <ul>
                <li><a href="#http-requests">Http İstekleri</a></li>
                <li><a href="#ajax-requests">Ajax İstekleri</a></li>
            </ul>
        </li>
    </ul>
</li>

<li>
    <a href="#customization">Özelleştirme</a>
    <ul>
        <li><a href="#adding-custom-data">Özel Form Verileri Eklemek</a></li>
        <li><a href="#adding-custom-errors">Özel Hataları Form Sınıfına Uyarlamak</a></li>
        <li><a href="#adding-custom-results">Özel Sonuçları Form Sınıfına Uyarlamak</a></li>
    </ul>
</li>

<li>
    <a href="#get-methods">Get Metotları</a>
    <ul>
        <li><a href="#getMessage">$this->form->getMessage()</a></li>
        <li><a href="#getValidationErrors">$this->form->getValidationErrors()</a></li>
        <li><a href="#getError">$this->form->getError()</a></li>
        <li><a href="#getValue">$this->form->getValue()</a></li>
        <li><a href="#outputArray">$this->form->outputArray()</a></li>
        <li><a href="#results">$this->form->results()</a></li>
    </ul>
</li>

<li>
    <a href="#set-methods">Set Metotları</a>
    <ul>
        <li><a href="#setValue">$this->form->setValue()</a></li>
        <li><a href="#setSelect">$this->form->setSelect()</a></li>
        <li><a href="#setCheckbox">$this->form->setCheckbox()</a></li>
        <li><a href="#setRadio">$this->form->setRadio()</a></li>
        <li><a href="#setMessage">$this->form->setMessage()</a></li>
        <li><a href="#setKey">$this->form->setKey()</a></li>
        <li><a href="#setErrors">$this->form->setErrors()</a></li>
        <li><a href="#setResults">$this->form->setResults()</a></li>
    </ul>
</li>

<li>
    <a href="#method-reference">Fonksiyon Referansı</a>
</li>



</ul>

<a name="configuration"></a>

### Konfigürasyon

Form sınıfına ait konfigürasyon dosyası <kbd>config/form.php</kbd> dosyasından yönetilir. Konfigürasyon dosyası form mesajlarına ait html şablonu ve niteliklerini belirler. Varsayılan CSS şablonu bootstrap css çerçevesi için konfigüre edilmiştir. Bu adresten <a href="http://getbootstrap.com" target="_blank">http://getbootstrap.com</a> bootstrap css projesine gözatabilirsiniz.


```php
return array(

    'message' => '<div class="{class}">{icon}{message}</div>',

    'error'  => [
        'class' => 'alert alert-danger', 
        'icon' => '<span class="glyphicon glyphicon-remove-sign"></span> '
    ],
    'success' => [
        'class' => 'alert alert-success', 
        'icon' => '<span class="glyphicon glyphicon-ok-sign"></span> '
    ],
    'warning' => [
        'class' => 'alert alert-warning', 
        'icon' => '<span class="glyphicon glyphicon-exclamation-sign"></span> '
    ],
    'info' => [
        'class' => 'alert alert-info', 
        'icon' => '<span class="glyphicon glyphicon-info-sign"></span> '
    ],
);

/* End of file form.php */
/* Location: .config/form.php */
```

Yukarıdaki form şablonuna ait bir mesaj form sınıfı ile aşağıdaki gibi üretilebilir.

```php
$this->form->success("Welcome to form class");
```
View çıktısı

```php
echo $this->form->getMessage();
```

```php
// Çıktı <span class="glyphicon glyphicon-ok-sign">Welcome to form class</span>
```
ya da

```php
$this->form->status(1);
$this->form->code(3);
$this->form->setKey('message', 'Example warning');
```

View çıktısı

```php
echo $this->form->getMessage();
```

```php
// Çıktı <span class="glyphicon glyphicon-exclamation-sign">Example warning</span> 
```

<a name="status-table"></a>

#### Durum Metotları Tablosu

<table>
    <thead>
        <tr>
            <th>Durum</th>
            <th>Kod</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>error</td>
            <td>0</td>
            <td>İşlemlerde bir hata olduğunda kullanılır.</td>
        </tr>
        <tr>
            <td>success</td>
            <td>1</td>
            <td>Başarılı işlemlerde kullanılır.</td>
        </tr>
        <tr>
            <td>warning</td>
            <td>2</td>
            <td>Uyarı amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
        <tr>
            <td>info</td>
            <td>3</td>
            <td>Bilgi amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
    </tbody>
</table>

<a name="running"></a>

### Çalıştırma

Form sınıfı <kbd>app/components.php</kbd> dosyası içerisinde tanımlıdır ve konteyner içerisinden çağrılarak çalıştırılır.

<a name="loading-service"></a>

#### Sınıfı Yüklemek

-------

```php
$this->c['form']->method();
```

<a name="basic-usage"></a>

#### Bir Form Mesajı Göstermek

Bir http post form mesajı form sınıfına aşağıdaki gibi durum metotları ile atanır.

```
$this->form->success('Form saved successfully.');
$this->form->error('Form validation failed.');
$this->form->warning('Something went wrong.');
$this->form->info('Email has been sent your address.');
```

Bir http post form mesajına getMessage fonksiyonu ile ulaşılır.

```php
echo $this->form->getMessage();
```

Form durum mesajları validator sınıfı ile birlikte kullanılırlar. Form mesajları doğrulama aşaması çalıştıktan sonra kullanıcının bulunduğu işlem durumuna göre gösterilirler. Aşağıdaki örnek kodu bir kontrolör dosyası post metodu içerisine yazın.

```php
if ($this->request->isPost()) {

    $this->c['validator'];

    $this->validator->setRules('name', 'Name', 'required');
    $this->validator->setRules('email', 'Email', 'required|email');

    if ($this->validator->isValid()) {
        $this->form->success('Form saved successfully.');
    } else {
        $this->form->error('Form validation failed.');
    }
}
```

Ve aşağıdaki kodu view sayfanızda form etiketi üzerine yerleştirin.

```html
<?php echo $this->form->getMessage() ?>

<form action="/form/post" method="POST">...</form>
```

Şimdi form gönderme butonu ile formu post edin eğer form doğrulaması başarılı ise <b>Form saved successfully</b> aksi durumda <b>Form validation failed</b> mesajı almanız gerekir.

<a name="using-with-validator"></a>

#### Doğrulama Hatalarını Form Sınıfı ile Göstermek

Bir form doğrulamasından dönen hataları view sayfasında form sınıfı aracılığı ile kullanabilmek için setErrors fonksiyonu ile hataların form sınıfına aktarılması gerekir. 

<a name="http-requests"></a>

##### Http İstekleri

Bir http post türü form doğrulama işlemi aşağıdaki gibi yapılabilir.

```php
if ($this->request->isPost()) {

    $this->c['validator'];

    $this->validator->setRules('name', 'Name', 'required|trim');
    $this->validator->setRules('email', 'Email', 'required|trim|email');

    $this->validator->isValid();

    $this->form->setErrors($this->validator->getErrors());  // Hataları form sınıfı içerisine kaydedelim.
}
```

Ve view sayfanıza aşağıdaki kodları yerleştirin.

```php
<form action="/form/post" method="POST">
    <table width="100%">
        <tr>
            <td>Name</td>
            <td><?php echo $this->form->getError('name'); ?>
            <input type="text" name="name" value="<?php echo $this->form->getValue('name') ?>" /></td>
        </tr>
        <tr>
            <td style="width:20%;">Email</td>
            <td><?php echo $this->form->getError('email'); ?>
            <input type="text" name="email" value="<?php echo $this->form->getValue('email') ?>" />
            </td>
        </tr>
    </table>
</form>
```

Yukarıdaki işlemleri yaptıysanız form alanlarını boş girdikten sonra formu çalıştırdığınızda isim ve email alanlarına ait hatalar alamanız gerekir.

<a name="ajax-requests"></a>

##### Ajax İstekleri

Bir ajax türü form doğrulama işlemi aşağıdaki gibi yapılabilir.

```php
if ($this->request->isPost()) {

    $this->c['validator'];

    $this->validator->setRules('name', 'Name', 'required|trim');
    $this->validator->setRules('email', 'Email', 'required|trim|email');

    if ( ! $this->validator->isValid()) {
        $this->form->error('Form validation failed');
        $this->form->setErrors($this->validator->getErrors());
        print_r($this->form->outputArray());
    }   
}
```

Form sınıfı http ve ajax istekleri için aşağıdaki gibi standart bir çıktı üretir.


```php
/*
Çıktı

Array
(
    [success] => 0
    [code] => 0
    [message] => Form validation failed
    [errors] => Array
        (
            [email] => The Email field is required.
            [name] => The Name field is required.
        )
)
*/
```

Son aşamada form çıktıları ajax işlemleri için http resonse sınıfı yardımıyla json formatında kodlanması gereklidir.

```php
echo $this->c['response']->json($this->form->outputArray());
```

<a name="customization"></a>

### Özelleştirme

<a name="adding-custom-data"></a>

#### Özel Form Verileri Eklemek

Setkey fonksiyonu ile mevcut array çıktıları içerisine uygulamaya özel form verileri eklenebilir.

```php
$this->form->status(1);
$this->form->code(2);
$this->form->setKey('message', "1. özel durum mesajı");
$this->form->setKey('message2', "2. özel durum mesajı");

print_r($this->form->outputArray());
```

```php
Çıktı

Array
(
    [success] => 1
    [code] => 2
    [message] => 1. özel durum mesajı
    [message2] => 2. özel durum mesajı
    [errors] => Array
        (
            [email] => The Email field is required.
            [name] => The Name field is required.
        )
)
```

<a name="adding-custom-errors"></a>

#### Özel Hataları Form Sınıfına Uyarlamak

Özel bir durum için oluşmuş hataları da form sınıfına gönderebilmek mümkündür bunun için hataları array türünde gönderin.

```php
$errors = array (
    'success' => 0,
    'code' => 99,
    'message' => 'İşlem Başarısız',
    'errors' => [
        'email' => 'The Email field is required.',
        'name' => 'The Name field is required.''
    ]
)

$this->form->setErrors($errors);
```

Doğrulama sınıfı hataları da istenirse array türünde gönderilebilir.

```php
$this->form->setErrors($this->validator->getErrors());
```

<a name="adding-custom-results"></a>

#### Özel Sonuçları Form Sınıfına Uyarlamak

Bir servis yada işlem için oluşmuş hataları da form sınıfına gönderebilmek mümkündür bunun için setResults fonksiyonu kullanılır. Aşağıda yanlış açılan bir oturum açma işlemine ait bir örnek görülüyor.

```php
$result = $this->user->login->attempt(
    array(
        $this->user['db.identifier'] => $this->validator->getValue('email'), 
        $this->user['db.password']   => $this->validator->getValue('password')
    ),
    $this->request->post('rememberMe')
);
```

```php
if ($result->isValid()) {
    $this->flash->success('You have authenticated successfully.')->url->redirect('membership/restricted');
} else {
    $this->form->setResults($result->getArray());
}
```

Ve view sayfasında alınan form sonuçlarına ait mesajlar getMessage() fonksiyonu kullanılarak form sınıfına konfigürasyonunda tanımlı olan html şablonu içerisinde gösteriliyor.

```php
if ($results = $this->form->results()) {
    foreach ($results->messages as $message) {
        echo $this->form->getMessage($message);
    }
}
```

Form sonuçlarını eğer ekrana yazdırsaydık aşağıdaki gibi bir çıktı ile karşılaşacaktık.

```php
print_r($this->form->results(true));

/*
Array (

    [code] => 0 
    [messages] => Array ( 
        [0] => Supplied credential is invalid. 
    ) 
    [identifier] => user@example.com
)
*/
```

<a name="get-methods"></a>

### Get Metotları

Form get metotları bir http form post işleminden sonra doğrulama sınıfı ile filtrelenen değerleri elde etmek veya form elementlerine atamak için kullanılırlar.

<a name="getMessage"></a>

##### $this->form->getMessage()

Form doğrulama hatalı ise forma ait genel hata mesajına geri döner.

<a name="getValidationErrors"></a>

##### $this->form->getValidationErrors();

Eğer validator sınıfı mevcutsa form post işleminden sonra girilen input alanlarına ait hatalara string formatında geri döner.

<a name="getError"></a>

##### $this->form->getError(string $field, $prefix = '', $suffix = '');

Eğer validator sınıfı mevcutsa form post işleminden sonra girilen input alanına ait hataya geri döner.

```php
<form action="/user/post/index" method="POST">
    <table width="100%">
        <tr>
            <td>Email</td>
            <td><?php echo $this->form->getError('email'); ?>
            <input type="text" name="email" value="<?php echo $this->form->getValue('email') ?>" />
            </td>
        </tr>
</form>
```
<a name="getValue"></a>

##### $this->form->getValue(string $field);

Eğer validator sınıfı mevcutsa form post işleminden sonra filtrelenen input alanına ait değere geri döner.

```php
<input type="text" name="price" value="<?php echo $this->form->getValue('price') ?>" size="20" />
```

<a name="outputArray"></a>

##### $this->form->outputArray();

Bir form doğrulamasından sonra oluşan çıktıları array formatında getirir.

<a name="results"></a>

##### $this->form->results();

Bir form doğrulamasından sonra eğer bir servis yada uygulama sonucu için <kbd>$this->form->setResults()</kbd> metodu ile girilen hata değerlerine array formatında geri döner.

<a name="set-methods"></a>

### Set Metotları

Form set metotları checbox, menü yada radio elementi kullanıyorsanız bir http form post işleminden sonra doğrulama sınıfından gelen güvenli değerlerler ile bu elementlere ait opsiyonları seçili olarak göstermek için kullanılırlar.

<a name="setValue"></a>

##### $this->form->setValue(string $field, $default = '');

Eğer validator sınıfı mevcutsa form post işleminden sonra filtrelenen input alanına ait değere geri döner. İkinci parametre eğer form post verisinde alana ait değer yoksa geri dönülecek varsayılan değeri belirler.

```php
<input type="text" name="price" value="<?php echo $this->form->setValue('price', '0.00') ?>" size="20" />
```

<a name="setSelect"></a>

##### $this->form->setSelect(string $field, $value = '', $default = false);

Eğer bir <b>select</b> menü kullanıyorsanız bu fonksiyon seçilen menü değerine ait opsiyonu seçili olarak göstermenize olanak sağlar.

```php
<select name="myselect">
        <option value="one" <?php echo $this->form->setSelect('myselect', 'one', true) ?> >One</option>
        <option value="two" <?php echo $this->form->setSelect('myselect', 'two') ?> >Two</option>
        <option value="three" <?php echo $this->form->setSelect('myselect', 'three') ?> >Three</option>
</select>
```

<a name="setCheckbox"></a>

##### $this->form->setCheckbox(string $field, $value = '', $default = false);

Eğer bir <b>checbox</b> elementi kullanıyorsanız bu fonksiyon seçilen değere ait opsiyonu seçili olarak göstermenize olanak sağlar.

```php
<input type="checkbox" name="mycheck" value="1" <?php echo $this->form->setCheckbox('mycheck', '1') ?> />
<input type="checkbox" name="mycheck" value="2" <?php echo $this->form->setCheckbox('mycheck', '2') ?> />
```

<a name="setRadio"></a>

##### $this->form->setRadio(string $field, $value = '', $default = false);

Eğer bir <b>radio</b> elementi kullanıyorsanız bu fonksiyon seçilen değere ait opsiyonu seçili olarak göstermenize olanak sağlar.

```php
<input type="radio" name="myradio" value="1" <?php echo $this->form->setRadio('myradio', '1', true) ?> />
<input type="radio" name="myradio" value="2" <?php echo $this->form->setRadio('myradio', '2') ?> />
```

<a name="setMessage"></a>

##### $this->form->setMessage(string $message = '', integer $status = 0);

Bir form doğrulaması çıktısı <kbd>message</kbd> anahtarına bir mesaj değeri atar. İkinci parametere girilirse eğer form success anahtarı <b>0</b> yada <b>1</b> olarak değiştirir.

<a name="setKey"></a>

##### $this->form->setKey(string $key, mixed $val);

Bir form doğrulamasından sonra oluşan çıktı dizisindeki anahtarlara değeri ile birlikte yeni bir anahtar ekler yada mevcut anahtarı yeni değeriyle günceller.

<a name="setErrors"></a>

##### $this->form->setErrors(array|object $errors);

Bir form doğrulamasından sonra oluşan çıktı dizisindeki input alanlarına ait anahtar olan <kbd>errors</kbd> anahtarına hatalar ekler. İlk parametre array olarak gönderilirse hatalar olduğu gibi kaydedilir. Nesne olarak sadece validator sınıfı gönderilebilir buradaki amaç validator sınıfındaki hataları kendiliğinden form sınıfına aktarmaktır.

<a name="setResults"></a>

##### $this->form->setResults(array $results);

Bir servis yada işlem için oluşmuş özel hataları form sınıfına gönderebilmek için kullanılır. Gönderilen veriler form çıktısında <kbd>results</kbd> anahtarına kaydedilir.

```php
$result = $exampleApi->exec();
$this->form->setResults($result->getArray());
```

Örnek Çıktı

```php
print_r($this->form->results());
```

```php
/*
Array
(
    [success] => 0
    [code] => 0
    [results] => Array
        (
            [messages] => Array
                (
                    [0] => Supplied credential is invalid.
                )

            [identifier] => user@example.com
        )
)
*/
```

Results anahtarına kaydedilen verilere view sayfasından nesne olarak ulaşılabilir. Aşağıdaki örnekte sonuçlardan alınan mesajlar form sınıfı html şablonuna aktarılıyor.

```php
if ($results = $this->form->results()) {
    foreach ($results->messages as $message) {
        echo $this->form->getMessage($message);  // Mesajlar form sınıfı html şablonuna aktarılıyor.
    }
}

```

<a name="method-reference"></a>

#### Form Sınıfı Referansı

------

##### $this->form->success(string $message);

Bir form doğrulaması çıktısı <kbd>message</kbd> anahtarına (girilen mesajı), <kbd>success</kbd> anahtarına (1) ve <kbd>code</kbd> anahtarına ise (1) değerini ekler.

##### $this->form->error(string $message);

Bir form doğrulaması çıktısı <kbd>message</kbd> anahtarına (girilen mesajı), <kbd>success</kbd> anahtarına (0) ve <kbd>code</kbd> anahtarına ise (0) değerini ekler.

##### $this->form->warning(string $message);

Bir form doğrulama çıktısı <kbd>message</kbd> anahtarına (girilen mesajı), <kbd>code</kbd> anahtarına ise (2) değerini ekler. Success anahtarı ( status ) varsayılan (0) dır.

##### $this->form->info(string $message);

Bir form doğrulama çıktısı <kbd>message</kbd> anahtarına (girilen mesajı), <kbd>code</kbd> anahtarına ise (3) değerini ekler. Success anahtarı ( status ) varsayılan (0) dır.

##### $this->form->code(int $code);

Bir form doğrulama çıktısı mevcut <kbd>code</kbd> anahtarının sayısal değerini günceller. Mevut kod değeri (0) dır. Kod değeri 0-4 arasındaki değerler <kbd>$this->form->getMessage()</kbd> fonksiyonunda gösterilecek olan html şablonunu belirler. (0) error , (1) success, (2) warning ve (3) info konfigürasyonu ile ilintilidir. 

##### $this->form->status(int $status = 0);

Bir form doğrulama çıktısı mevcut <kbd>success</kbd> anahtarının sayısal değerini günceller. Mevcut durum değeri (0) dır. Bu anahtara verilebilecek değerler <b>0</b> yada <b>1</b> olmalıdır. 

##### $this->form->getMessage($msg = '');

Bir form doğrulaması çıktısından dönen mesajı bir başka deyişle <kbd>message</kbd> anahtarı değerini <kbd>config/form.php</kbd> dosyasındaki konfigürasyonda tanımlı html şablonu içerisine dahil ederek bu mesaja geri döner. Eğer birinci parametreden bir mesaj gönderilirse formdan dönen mesaj yerine bu mesaj mesaj değişkeni olarak kabul edilir.

##### $this->form->getError(string $field, $prefix = '', $suffix = '');

Eğer validator sınıfı mevcutsa form post işleminden sonra girilen input alanına ait hataya geri döner. Birinci parametre input alanı ismi, ikinci parametre hataya ait varsa bir html tagı başlangıcı, üçüncü parametre varsa bir html tagı kapanış değerine ayrılmıştır.

##### $this->form->getValue(string $field);

Eğer validator sınıfı mevcutsa form post işleminden sonra filtrelenen input alanına ait değere geri döner.

##### $this->form->outputArray();

Bir form doğrulamasından sonra oluşan çıktıları array formatında getirir.

##### $this->form->results($assoc = false);

Bir form doğrulamasından sonra eğer bir servis yada uygulama sonucu için <kbd>$this->form->setResults()</kbd> metodu ile girilen hata değerlerine array formatında geri döner. İlk parametreye true değeri gönderilirse 

##### $this->form->setMessage(string $message = '', integer $status = 0);

Bir form doğrulaması çıktısı <kbd>message</kbd> anahtarına bir mesaj değeri atar. İkinci parametere girilirse eğer form success anahtarı <b>0</b> yada <b>1</b> olarak değiştirir.

##### $this->form->setKey(string $key, mixed $val);

Bir form doğrulamasından sonra oluşan çıktı dizisindeki anahtarlara değeri ile birlikte yeni bir anahtar ekler yada mevcut anahtarı yeni değeriyle günceller.

##### $this->form->setErrors(array|object $errors);

Bir form doğrulamasından sonra oluşan çıktı dizisindeki input alanlarına ait anahtar olan <kbd>errors</kbd> anahtarına hatalar ekler. İlk parametre array olarak gönderilirse hatalar olduğu gibi kaydedilir. Nesne olarak sadece validator sınıfı gönderilebilir buradaki amaç validator sınıfındaki hataları kendiliğinden form sınıfına aktarmaktır.

##### $this->form->setResults(array $results);

Bir servis yada işlem için oluşmuş özel hataları form sınıfına gönderebilmek için kullanılır.

##### $this->form->setValue(string $field, $default = '');

Eğer validator sınıfı mevcutsa form post işleminden sonra filtrelenmiş input alanına ait değere geri döner. İkinci parametre eğer form post verisinde alana ait değer yoksa geri dönülecek varsayılan değeri belirler.

##### $this->form->setSelect(string $field, $value = '', $default = false);

Eğer bir select menü kullanıyorsanız bu fonksiyon seçilen menü değerine ait opsiyonu seçili olarak göstermenize olanak sağlar.

##### $this->form->setCheckbox(string $field, $value = '', $default = false);

Eğer bir checbox elementi kullanıyorsanız bu fonksiyon seçilen değere ait opsiyonu seçili olarak göstermenize olanak sağlar.

##### $this->form->setRadio(string $field, $value = '', $default = false);

Eğer bir radio elementi kullanıyorsanız bu fonksiyon seçilen değere ait opsiyonu seçili olarak göstermenize olanak sağlar.