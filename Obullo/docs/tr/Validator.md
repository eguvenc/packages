
## Doğrulama Sınıfı

Doğrulama sınıfı yazdığınız kodu minimize ederek form girdilerini kapsamlı bir şekilde doğrulamayı sağlar. Buna ek olarak doğrulama sınıfına ait konfigürasyon dosyasından kendi kurallarınızı tanımlayabilir yada geri çağırım fonksiyonu ile geçici kurallar oluşturabilirsiniz.

<ul>
    <li><a href="#how-it-works">Nasıl Çalışır ?</a>
        <ul>
            <li><a href="#field">Field Nesnesi</a></li>
            <li><a href="#next">Next Komutu</a></li>
            <li><a href="#rules-config">Kural Konfigürasyonu</a></li>
        </ul>
    </li>

    <li><a href="#run">Çalıştırma</a>
        <ul>
            <li><a href="#setRules">$this->validator->setRules()</a></li>
            <li><a href="#isValid">$this->validator->isValid()</a></li>
            <li><a href="#ruleReference">Kural Referansı</a></li>
            <li><a href="#funcReference">Fonksiyon Referansı</a></li>
        </ul>
    </li>

    <li>
        <a href="#errors">Hatalar</a>
        <ul>
            <li><a href="#getError">$this->validator->getError()</a></li>
            <li><a href="#setError">$this->validator->setError()</a></li>
            <li><a href="#setErrors">$this->validator->setErrors()</a></li>
            <li><a href="#getErrors">$this->validator->getErrors()</a></li>
            <li><a href="#setMessage">$this->validator->setMessage()</a></li>
            <li><a href="#getMessages">$this->validator->getMessages()</a></li>
            <li><a href="#getErrorString">$this->validator->getErrorString()</a></li>
            <li><a href="#setErrorDelimiters">$this->validator->setErrorDelimiters()</a></li>
            <li><a href="#isError">$this->validator->isError()</a></li>
        </ul>
    </li>

    <li>
        <a href="#values">Değerler</a>
        <ul>
            <li><a href="#getValue">$this->validator->getValue()</a></li>
            <li><a href="#setValue">$this->validator->setValue()</a></li>
            <li><a href="#getFieldData">$this->validator->getFieldData()</a></li>
        </ul>
    </li>

    <li>
        <a href="#formClass">Form Sınıfı</a>
        <ul>
            <li><a href="#formSetMessage">$this->form->setMessage()</a></li>
            <li><a href="#formSetErrors">$this->form->setErrors()</a></li>
            <li><a href="#formGetError">$this->form->getError()</a></li>
            <li><a href="#formIsError">$this->form->isError()</a></li>
            <li><a href="#formGetErrorClass">$this->form->getErrorClass()</a></li>
            <li><a href="#formGetErrorLabel">$this->form->getErrorLabel()</a></li>
            <li><a href="#formGetValue">$this->form->getValue()</a></li>
            <li><a href="#formSetValue">$this->form->setValue()</a></li>
            <li><a href="#formsetSelect">$this->form->setSelect()</a></li>
            <li><a href="#formSetCheckbox">$this->form->setCheckbox()</a></li>
            <li><a href="#formSetRadio">$this->form->setRadio()</a></li>
        </ul>
    </li>

    <li>
        <a href="#callbackFunc">Geri Çağırım</a>
        <ul>
            <li><a href="#callback">$this->validator->callback()</a></li>
        </ul>
    </li>

    <li><a href="#additional-info">Ek Bilgiler</a>
        <ul>
            <li><a href="#translations">Farklı Dillere Çeviri</a></li>
            <li><a href="#create-your-own-rules">Kendi Kurallarınızı Oluşturun</a></li>
        </ul>    
    </li>
</ul>

<a name="how-it-works"></a>

### Nasıl Çalışır ?

Doğrulama kuralları doğrulama sınıfı <kbd>setRules</kbd> metodu ile oluşturulur. Bu metot içerisine girilen ilk parametre form elementine ait isim, ikinci parametre etiket ve üçüncü parametre ise kurallardır. Her doğrulama kuralı bir nesnedir. Örneğin min doğrulama kuralı <kbd>Obullo\Validator\Rules\Min</kbd> adlı sınıfı çağırır. Aşağıda  örnekte bir form doğrulama kuralının oluşturuluşu gösteriliyor.

```php
$this->validator->setRules('username', 'Username', 'required|min(5)|email');
```

<a name="field"></a>

#### Field Nesnesi

Her bir kural sınıfı içerisinden <kbd>invoke</kbd> metodu içerisine <kbd>Field $field</kbd> nesnesi gönderilir ve __invoke metodu ile kurallar çalıştırılmış olur. Field nesnesi get metotları, form elementine ait özellikleri verir. Aşağıdaki örnekte <kbd>min(5)</kbd> kuralından elde edilen değerler gözüküyor.


```php
class Min
{
    public function __invoke(Field $next)
    {
        echo $field->getValue();  // username@example.com
        echo $field->getName();   // username
        echo $field->getLabel();  // Username
        print_r($field->getParams());  // 5

        $field = $next;
        $value = $field->getValue();

        if ($this->isValid($value)) {
            return $next();
        }
        return false;
    }
}
```

Set metotları ile element değerleri yenilenebilir yada forma bir mesaj gönderilebilir.

```php
$field->setValue("Field post value");
$field->setError("Field error");
$field->setMessage("Field form message");
```

<a name="next"></a>

#### $next() Komutu

Next komutu ile doğrulama kuralının kendinden bir sonraki doğrulama kuralını çağırması sağlanır.

```php
$this->validator->setRules('username', 'Username', 'required|email');
```

Aşağıdaki örneği göz önüne alırsak, required kuralı eğer doğrulamayı geçerse bu kural içerisindeki $next() fonksiyonu bir sonraki kuralı çağırır.

```php
class Required
{
    public function __invoke(Field $next)
    {
        $field = $next;
        $value = $field->getValue();

        if ($this->isValid($value)) {
            return $next();
        }
        return false;
    }
}
```

Daha iyi anlaşılması için akış şemasına gözatalım.

![Validation Rules](images/validation-rules.png?raw=true "Validation Rules")

Şemaya göre ilk kural olan <kbd>required</kbd> kuralı, doğrulandığında $next() komutu ile sonraki kural olan <kbd>email</kbd> kuralını çağırır. Eğer email kuralı <b>true</b> değerine dönerse doğrulayıcı aynı elemente ait bir sonraki kuralı çağırır. Eğer metot <b>false</b> değerine dönerse bu durumda $next() komutu çalıştırılmaz, doğrulama hataları değişkenlere atanır. Bu durum herbir element için zincirleme bir şekilde devam eder.

> **Not:** Doğrulama aşamasında bütün elementlerin sadece ilk kuralları çalışır (örn. required), birinci kuraldan sonraki diğer tüm elementlere ait kurallar isValid() metodunun cevabı true alındığında çağrılırlar. Böylece form doğrulama aşamasında tüm kuralların çağrılması önlenerek performanstan kazanılmış olur.

<a name="rules-config"></a>

#### Kural Konfigürasyonu

Her bir kurala ait sınıf <kbd>app/$env/validator.php</kbd> dosyası içerisinde aşağıdaki gibi tanımlıdır.

```php
return array(

    'rules' => [
        'alpha' => 'Obullo\Validator\Rules\Alpha',
        'alphadash' => 'Obullo\Validator\Rules\AlphaDash',
        .
    ]
);
```

> **Not:** Bu dosya içerisinde değişiklik yaparak kendi doğrulama kurallarınızı oluşturabilirsiniz.

<a name="run"></a>

### Çalıştırma

Form doğrulama kuralları kontroller sınıfı içerisinde <kbd>setRules()</kbd> metodu ile oluşturulur ve <kbd>isValid</kbd> metodu ile tetiklenir.

<a name="setRules"></a>

#### $this->validator->setRules()

Doğrulama kuralları nesne yöntemi ile aşağıdaki gibi tek tek,

```php
if ($this->request->isPost()) {

    $this->validator->setRules('username', 'Username', 'required|email');
    $this->validator->setRules('password', 'Password', 'required|min(6)');
}
```

yada aşağıdaki gibi bir dizi aracılığı ile atanabilirler.

```php
if ($this->request->isPost()) {

    $rules = array(
       array(
             'field'   => 'username',
             'label'   => 'Username',
             'rules'   => 'required'
          ),
       array(
             'field'   => 'password',
             'label'   => 'Password',
             'rules'   => 'required|min(6)'
          ),
    );
    $this->validator->setRules($rules);
}
```

<a name="isValid"></a>

#### $this->validator->isValid()

Doğrulama sınıfına tanımlanan kurallar $this->validator->isValid() metodu ile çalıştırılır.

```php
if ($this->request->isPost()) {

    if ($this->validator->isValid()) {          
        // success
    } else {
        // fail
    }
}
```

<a name="ruleReference"></a>

#### Kural Referansı

Aşağıdaki tabloda şu anki sürümde mevcut olan doğrulama kuralları gösteriliyor.

<table>
<thead>
<tr>
<th>Kural</th>
<th>Açıklama</th>
<th>Parametre</th>
</tr>
</thead>
<tbody>
<tr>
    <td>alphadash</td>
    <td>Eğer form element değeri ( a-z A-Z_- ) karakterleri haricinde bir karakter içeriyorsa false değerine döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>alnum</td>
    <td>Eğer form element değeri ( a-z A-Z 0-9 ) karakterleri haricinde bir karakter içeriyorsa false değerine döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>alnumdash</td>
    <td>Eğer form element değeri ( a-z A-Z 0-9_- ) karakterleri haricinde bir karakter içeriyorsa false değerine döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>alpha</td>
    <td>Eğer form element değeri ( a-z A-Z ) karakterleri haricinde bir karakter içeriyorsa false değerine döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>captcha</td>
    <td>Eğer form element değeri geçerli captcha yanıtını içerimiyorsa false değerine geri döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>csrf</td>
    <td>Eğer form element değeri geçerli csrf değerini içerimiyorsa false değerine geri döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>date</td>
    <td>Eğer form element değeri girilen tarih formatı ile uyuşmuyorsa false değerine döner.</td>
    <td>date(Y-m-d)</td>
</tr>
<tr>
    <td>email</td>
    <td>Eğer form elementi geçerli bir email adresi içermiyorsa false değerine döner. Kural parametresine true gönderilirse dns kontrolü de yapılabilir.
    <td>email(true)</td>
</td>
</tr>
<tr>
    <td>exact</td>
    <td>Eğer form element değerinin genişliği girilen değere tam olarak eşit değilse false değerine geri döner.</td>
    <td>exact(n)</td>
</tr>
<tr>
    <td>iban</td>
    <td>Uluslarası banka esap numarası yani IBAN değeri geçerli değilse false değerine geri döner.</td>
    <td>iban(COUNTRY_CODE) yada iban(COUNTRY_CODE)(false). Eğer ayrıca SEPA (Single Euro Payments Area) dışındaki ülkeler için doğrulama istenmiyorsa 2. parametre false girilir.</td>
</tr>
<tr>
    <td>isbool</td>
    <td>Eğer form element değeri boolean ( true / false or 0 / 1 ) değerlerini içermiyorsa false değerine geri döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>isdecimal</td>
    <td>Eğer form element değeri ondalık bir sayı değilse false değerine döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>isjson</td>
    <td>Eğer form element json değeri decode edilemiyorsa false değerine geri döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>isnumeric</td>
    <td>Eğer form element değeri sayısal karakterler içermiyorsa false değerine geri döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>matches</td>
    <td>Eğer form element girilen form element değeri ile eşleşmiyorsa false değerine döner.</td>
    <td>matches(field_name)</td>
</tr>
<tr>
    <td>max</td>
    <td>Eğer form element değerinin genişliği girilen değerden büyük ise false değerine geri döner.</td>
    <td>max(n)</td>
</tr>
<tr>
    <td>min</td>
    <td>Eğer form element değerinin genişliği girilen değerden küçük ise false değerine geri döner.</td>
    <td>min(n)</td>
</tr>
<tr>
    <td>recaptcha</td>
    <td>Eğer form element değeri google recaptcha servisinden gelen yanıt ile eşleşmiyorsa false değerine geri döner.</td>
    <td>-</td>
</tr>
<tr>
    <td>required</td>
    <td>Eğer form elementi boş ise false değerine döner.</td>
    <td>-</td>
</tr>
</tbody>
</table>

<a name="funcReference"></a>

#### Fonksiyon Referansı

Aşağıdaki yardımcı fonksiyonlar ile doğrulama değerleri filtreden geçirilebilir yada değiştirilebilir.

<table>
<thead>
<tr>
<th>Fonksiyon</th>
<th>Açıklama</th>
<th>Parametre</th>
</tr>
</thead>
<tbody>
<tr>
    <td>md5</td>
    <td>Form element değerini php md5() fonksiyonu sonucuna dönüştürür.</td>
    <td>-</td>
</tr>
<tr>
    <td>trim</td>
    <td>Form element değerini php trim() fonksiyonundan geçirir.</td>
    <td>-</td>
</tr>
</tbody>
</table>

> **Not:** Kendi sınıflarınızı yaratarak özel kurallar ve fonksiyonlar oluşturabilirsiniz bunun için kendi kurallarınızı oluşturmak bölümüne bakınız.


<a name="errors"></a>

### Hatalar

Aşağıdaki fonksiyonlar doğrulama sınıfı içerisine kaydedilen hatalara direkt ulaşabilmenizi sağlar.

<a name="getError"></a>

#### $this->validator->getError($field)

Doğrulamadan sonra form alanı hatalı ise ilgili alana ait hata mesajını ekrana yazdırır.

```php
echo $this->validator->getError('email')
```

Çıktı

```php
The Email field is required.
```

<a name="setError"></a>

#### $this->validator->setError(string $field, $error)

Girilen alana ait hata tayin eder.

```php
$this->validator->setError('email', 'Example error');
```

<a name="setErrors"></a>

#### $this->validator->setErrors(array $errors)

Dizi türünde girilen hataları hata değişkeni içerisine kaydeder.

```php
$errors = array(
    'username' => 'The Username field is required.',
    'password' => 'The Password field is required.',
    'email' = 'The Email field is required.'
);
$this->validator->setErrors($errors);
```

<a name="getErrors"></a>

#### $this->validator->getErrors()

Doğrulayıcıdan dönen hatalara bir dizi içerisinde geri döner.

<a name="setMessage"></a>

#### $this->validator->setMessage($error)

Form elementi hatalarından bağımsız genel doğrulama mesajları kaydetmek için kullanılır.

```php
$this->validator->setMessage("Please choose an option.");
```

> **Not:** Field nesnesi içerisindeki set metotları validator sınıfı metotlarını çalıştırır.

<a name="getMessages"></a>

#### $this->validator->getMessages()

Form mesajı olarak kaydedilen hatalara bir dizi içerisinde geri döner.

```php
Array
(
    [0] => Form validation failed.
    [1] => Please choose a color.
    [2] => Please choose a hobby.
)
```
<a name="getErrorString"></a>

#### $this->validator->getErrorString()

Dönen form hatalarını string biçiminde ekrana döker.

```php
echo $this->validator->getErrorString();
```

Çıktı

```php
<p>The Username field is required.</p>
<p>The Email field is required.</p>
```

<a name="setErrorDelimiters"></a>

#### $this->validator->setErrorDelimiters($prefix = '<p\>', $suffix = '<\p\>')

$this->validator->getErrorString() fonksiyonu için ön ek ve son ekleri belirler.

```php
$this->validator->setErrorDelimiters('<div>', '</div>')
```

<a name="isError"></a>

#### $this->validator->isError($field)

Form elementine ait bir hata varsa true değerine aksi durumda false değerine geri döner.

```php
if ($this->validator->isError($field)) {
    
    // field has error
}
```

<a name="values"></a>

### Değerler

Aşağıdaki fonksiyonlar doğrulama sınıfı içerisine kaydedilen form değerlerine direkt ulaşabilmenizi sağlar.

<a name="getValue"></a>

#### $this->validator->getValue($field, $default = '')

Form alanı içerisine kaydedilen ilgili alanın değerine geri döner.

```php
$this->validator->getValue('email')  // obullo@example.com
```

<a name="setValue"></a>

#### $this->validator->setValue($field, $value)

Form alanı içerisine kaydedilen ilgili alanın değerini günceller.

```php
$this->validator->setValue('email', 'john@example.com')
```

<a name="getFieldData"></a>

#### $this->validator->getFieldData();

```php
$fields = $this->validator->getFieldData();

print_r($fields);
```

Çıktı

```php
[email] => Array
(
    [field] => email
    [label] => Email
    [rules] => required|email
    [postdata] => user@example.com
    [error] => 
)
[password] => Array
(
    [field] => password
    [label] => Password
    [rules] => required|min(6)
    [postdata] => 123456
    [error] => 
)
```

<a name="formClass"></a>

### Form Sınfı

Doğrulama işlemi içerisinde form sınıfı <b>get</b> metotları, verileri view dosyalarına bağlamak yada <b>set</b> metotları ile form nesnesine veri göndermek için kullanılır.

<a name="formSetMessage"></a>

#### $this->form->setMessage($message)

Form çıktı dizisi içerisinde oluşturulan <b>messages</b> anahtarına bir form mesajı ekler. Detaylı bilgi için [Form.md](Form.md) dökümentasyonunu inceleyebilirsiniz.

<a name="formSetErrors"></a>

#### $this->form->setErrors(object $validator | array $errors)

Form sınıfına doğrulama nesnesinden dönen hata ve değerleri göndermek için bu metot kullanılır.

```php
if ($this->request->isPost()) {

    if ($this->validator->isValid()) {          
        $this->form->success('Success');
    } else {
        $this->form->error('Fail');
    }
    $this->form->setErrors($this->validator);
}
```

Form post işleminde sonra <kbd>validator</kbd> nesnesi form sınıfına referans olarak gönderilir. Böylece view kısmında form nesnesi üzerinden validator değerlerine ulaşılmış olur.

Form mesajları

```php
echo $this->form->getMessage()
```

Form hataları

```php
 <form name="example" action="/examples/forms/form" method="POST">
    <?php echo $this->form->getError('email') ?>
    <input type="email" name="email" value="<?php echo $this->form->getValue('email') ?>">

    <?php echo $this->form->getError('password') ?>
    <input type="password" name="password" id="pwd" placeholder="Password">
  <button type="submit" class="btn btn-default">Submit</button>
</form>
```

<a name="formGetErrors"></a>

#### $this->form->getError($field)

Doğrulamadan sonra form alanı hatalı ise ilgili alana ait hata mesajını ekrana yazdırır.

```php
echo $this->form->getError('email', $prefix = '<p>', $suffix = '</p>')
```

Çıktı

```php
The Email field is required.
```

Doğrulama sınıfı array türündeki alanları da destekler.

```php
<input type="text" name="options[]" value="" size="50" />
```

Bu türden bir element isminin doğrulaması için form kuralına da aynı isimle girilmesi gerekir.

```php
$this->validator->setRules('options[]', 'Options', 'required');
```

Eğer checkbox element türünde birden fazla alan isteniyorsa,

```php
<input type="checkbox" name="options[]" value="red" />
<input type="checkbox" name="options[]" value="blue" />
<input type="checkbox" name="options[]" value="green" /> 
```

Bu türden bir elemente ait hata mesajını almak için metot içinde aynı isim kullanılır.

```php
echo $this->form->getError('options[]');
```

hatta alan ismi çok boyutlu bir array içerse bile,

```php
<input type="checkbox" name="options[color][]" value="red" />
<input type="checkbox" name="options[color][]" value="blue" />
<input type="checkbox" name="options[color][]" value="green" /> 
```

yine isim aşağıdaki gibi girilir.

```php
echo $this->form->getError('options[]');
```

<a name="formIsError"></a>

#### $this->form->isError($field)

Eğer girilen alana ait bir hata varsa true aksi durumda false değerine döner.

<a name="formGetErrorClass"></a>

#### $this->form->getErrorClass($field)

Eğer girilen alana ait hata dönerse <kbd>app/$env/form.php</kbd> dosyasından

```php
'error' => [
    'class' => 'has-error has-feedback',
]
```
<kbd>error > class</kbd> konfigürasyonu

```php
echo $this->form->getErrorClass('email')
```

aşağıdaki gibi çıktılanır.


```php
has-error has-feedback    
```

<a name="formGetErrorLabel"></a>

#### $this->form->getErrorLabel($field)

Eğer girilen alana ait hata dönerse <kbd>app/$env/form.php</kbd> dosyasından

```php
'error' => [
    'label' => '<label class="control-label" for="%s">%s</label>
]
```

<kbd>error > label</kbd> konfigürasyonu

```php
echo $this->form->getErrorLabel('email')
```

aşağıdaki gibi çıktılanır.

```php
<label class="control-label" for="field">Label</label>
```

<a name="formGetValidationErrors"></a>

#### $this->form->getValidationErrors();

<kbd>$validator->getErrorString()</kbd> metoduna geri döner.

<a name="formGetValue"></a>

#### $this->form->getValue()

Doğrulanmış bir form elementinin son değerine geri döner.

<a name="formSetValue"></a>

#### $this->form->setValue($field, $value = '')

Input yada textarea türündeki bir form elementine değer girmeyi sağlar. İlk parametreye input ismi girilmek zorundadır. İkinci parametre opsiyoneldir ve input alanı için varsayılan değeri tanımlar.

```php
<input type="text" name="quantity" 
value="<?php echo $this->form->setValue('quantity', '0'); ?>" size="50" />
```

Yukarıdaki örnekte form elementi sayfa ilk yüklendiğinde 0 değerini gösterir.

<a name="formSetSelect"></a>

#### $this->form->setSelect()

Eğer bir <kbd>select</kbd> menü kullanıyorsanız, bu fonksiyon menüye ait seçilen opsiyonları göstermeyi sağlar. İlk parametre select menü ismini belirler, ikinci parametre ise her bir opsiyon değerini içermek zorundadır. Üçüncü parametre ise opsiyoneldir, opsiyon değerinin varsayılan olarak gösterilip gösterilmeyeceğini belirler ve boolean tipinde olmalıdır.

```php
<select name="myselect">
<option value="one" <?php echo $this->form->setSelect('myselect', 'one', true); ?> >One</option>
<option value="two" <?php echo $this->form->setSelect('myselect', 'two'); ?> >Two</option>
<option value="three" <?php echo $this->form->setSelect('myselect', 'three'); ?> >Three</option>
</select>
``` 

<a name="formSetCheckbox"></a>

#### $this->form->setCheckbox()

Array türünde bir element isminin doğrulanması için form kuralına da aynı ismin girilmesi gerekir.

```php
$this->validator->setRules('options[]', 'Options', 'required');
```
Form post işleminden sonra seçilen checkbox element değerini seçili hale getirmek için aşağıdaki yöntem kullanılır.

```php
echo $this->form->setCheckbox('options[]', 'red');
```

```php
<label>
<input type="checkbox" name="options[color][]" 
value="red" <?php echo $this->form->setCheckbox('options[]', 'red') ?> />
Red
</label>

<label>
<input type="checkbox" name="options[color][]" 
value="blue" <?php echo $this->form->setCheckbox('options[]', 'blue') ?> />
Blue
</label>

<label>
<input type="checkbox" name="options[color][]" 
value="green" <?php echo $this->form->setCheckbox('options[]', 'green') ?>  />
Green
</label>
```

<a name="formSetRadio"></a>

#### $this->form->setRadio()

Form post işleminden sonra seçilen radio element değerini seçili hale getirmek için aşağıdaki yöntem kullanılır, $this->form->setCheckbox() metodu ile aynı işlevselliğe sahiptir.

```php
<input type="radio" name="myradio" value="1" <?php echo $this->form->setRadio('myradio', '1', true) ?> />
<input type="radio" name="myradio" value="2" <?php echo $this->form->setRadio('myradio', '2') ?> />
```

> **Not:** Form sınıfı ile ilgili daha ayrıntılı bilgi için [Form.md](Form.md) dökümentasyonuna göz atın.


<a name="callbackFunc"></a>

### Geri Çağırım

Geri çağırım metotları özel doğrulama fonksiyonları oluşturmak yada opsiyonel olarak array türündeki alanları doğrulamak için kullanılabilir.

<a name="callback"></a>

#### $this->validator->callback(Closure $function($field))

Aşağıdaki örnekte olduğu gibi <kbd>$field</kbd> nesnesi tanımlı olan bütün callback fonksiyonlarına gönderilir ve böylece gönderilen alana ait özellikler isimsiz fonksiyon içerisinde elde edilmiş olur. 

```php
$this->validator->setRules('email', 'Email', 'required|email');
$this->validator->setRules('options[]', 'Options', 'callback_options');
$this->validator->callback(
    'callback_options',
    function ($field) {
        $value = $field->getValue();
        if (empty($value)) {
            $field->setMessage('Please choose a color.');
            $field->setError('Please choose a color.');
            return false;
        }
        return $field();
    }
);
```

Özel fonksiyonun çalışabilmesi için fonksiyon adının doğrulama kuralları içerisine de eklenmesi gerekir.

```php
$this->validator->setRules('options[]', 'Options', 'callback_options');
```

Yukarıdaki örneği dikkate alırsak isValid() metodunu çalıştırdığımızda

```php
if ($this->validator->isValid()) {          
    $this->form->success('Form validation success.');
} else {
    $this->form->error('Form validation failed.');
}
```

eğer <kbd>options</kbd> elementinin değeri boş gelirse <kbd>Please choose a color.</kbd> hataları ile karşılaşmamız gerekir.


> **Not:** Eğer birden fazla özel fonksiyon oluşturulmak isteniyorsa callback() metodu tekrar kullanılmalıdır.


<a name="additional-info"></a>

### Ek Bilgiler

<a name="translations"></a>

#### Farklı Dillere Çeviri

Doğrulama sınıfına ait geçerli çeviri verisi <kbd>resources/translations/en/validator.php</kbd> dosyası içerisindedir. Eğer yeni bir dil eklenmek isteniyorsa aşağıdaki adımları izleyin. 

Yeni dil dosyamızın ispanyolca (es) olduğunu varsayalım bunun için,

* İlk önce <b>translations/en/validator.php</b> dosyasının bir kopyasını alın ve <b>resources/translations/es/</b> dizinine kopyalayın.
* Ve bu dosya içerisindeki değerleri aşağıdaki gibi değiştirin.

```php
return array(
    
    'OBULLO:VALIDATOR:REQUIRED' => "El campo %s es obligatorio.",
    'OBULLO:VALIDATOR:EMAIL'    => "El campo %s debe contener una dirección de correo válida.",
);
```

Eğer varsayılan dil tarayıcınızdan ispanyolca (es) olarak girilirse doğrulama verileri artık bu dilde okunur. Çeviriler hakkında daha ayrıntılı bilgi için [Translation.md](Translation.md) dökümentasyonuna göz atın.

##### Girdi Etiketlerinin Çevirisi

Girdi alanlarına ait etiketlerin başlarında <kbd>translate:</kbd> öneki kullanırsanız bu anahtarlara ait çeviriler varsa çevirilirler.

```php
$this->validator->setRules('email', 'translate:Email', 'required|email');
```

Ayrıca dil dosyanıza bu çevirileri aşağıdaki gibi eklemeniz gerekir.

```php
return array(
    
    'Email' => 'Correo Electrónico',
    'OBULLO:VALIDATOR:REQUIRED' => "El campo %s es obligatorio.",
);
```

<a name="create-your-own-rules"></a>

#### Kendi Kurallarınızı Oluşturun

Kendi doğrulama kurallarınızı oluşturmak için kurala ait klasör yolunu <kbd>app/$env/validator.php</kbd> dosyası içerisinde tanımlamanız gerekir. Örneğin doğum tarihi alanını doğrulamak için <kbd>birthdate</kbd> adlı bir kuralımız olsun. Bunun için <kbd>Form\Validator\BirthDate</kbd> dosya yolunu aşağıdaki gibi tanımlamanız gerekir.

```php
return array(

    'rules' => [

        'birthdate' => 'Form\Validator\BirthDate'
        'alpha' => 'Obullo\Validator\Rules\Alpha',
        'alphadash' => 'Obullo\Validator\Rules\AlphaDash',
        .
        .
        .
        'trim' => 'Obullo\Validator\Rules\Trim'
    ]
);
```

Sonraki aşamada <kbd>app/classes/Fom/Validator/BirthDate</kbd> isimli bir sınıf oluşturun. Artık doğrulama kurallarında bu kuralı aşağıdaki gibi kullanabilirsiniz.

```php
$this->validator->setRules('email', 'Birth Date', 'required|birthdate');
```