
## Form Element Sınıfı

Form element sınıfı html formları, html form elementleri ve form etiketi ile ilgili girdileri kolayca oluşturmanıza yardımcı olur. Ayrıca form güvenliğine ilişkin verileri oluşturabilir, örneğin Csrf token form metodu kullanıldığında otomatik olarak oluşturulur.

<ul>

<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#service">Servis Kurulumu</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#loading-service">Servisi Yüklemek</a></li>
        <li><a href="#form">$this->element->form()</a>
            <ul>
                <li><a href="#adding-attributes">Nitelikler Eklemek</a> ( Attributes )</li>
                <li><a href="#adding-hidden-inputs">Gizli Girdi Alanları Oluşturmak</a></li>
            </ul>
        </li>
        <li><a href="#formMultipart">$this->element->formMultipart()</a></li>
        <li><a href="#formClose">$this->element->formClose()</a></li>
        <li><a href="#input">$this->element->input()</a></li>
        <li><a href="#password">$this->element->password()</a></li>
        <li><a href="#upload">$this->element->upload()</a></li>
        <li><a href="#textarea">$this->element->textarea()</a></li>
        <li><a href="#dropdown">$this->element->dropdown()</a></li>
        <li><a href="#checkbox">$this->element->checkbox()</a></li>
        <li><a href="#radio">$this->element->radio()</a></li>
        <li><a href="#submit">$this->element->submit()</a></li>
        <li><a href="#reset">$this->element->reset()</a></li>
        <li><a href="#button">$this->element->button()</a></li>
    </ul>
</li>

<li>
    <a href="#form-class">Form Sınıfı</a>
    <ul>
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
    </ul>
</li>

<li>
    <a href="#security">Güvenlik</a>
    <ul>
        <li><a href="#dangerous-inputs">Tehlikeli Girdilerden Kaçış</a></li>
    </ul>
</li>


</ul>

<a name="configuration"></a>

### Konfigürasyon

-------

Form element sınıfı herhangi bir konfigürasyon dosyasına ihtiyaç duymaz.

<a name="service"></a>

#### Servis Kurulumu

Form element sınıfı opsiyonel olarak kullanılır bu yüzden çalışabilmesi için aşağıdaki gibi bir servis kurulumuna ihtiyaç duyar.

```php
namespace Service;

use Obullo\Container\ServiceInterface;
use Obullo\Form\Element as FormElement;
use Obullo\Container\ContainerInterface;

class Element implements ServiceInterface
{
    public function register(ContainerInterface $c)
    {
        $c['element'] = function () use ($c) {
            return new FormElement($c);
        };
    }
}

// END Element service

/* End of file Element.php */
/* Location: .app/classes/Service/Element.php */
```

<a name="running"></a>

### Çalıştırma

Servis yüklendikten sonra aşağıdaki gibi form element sınıfı metotlarına ulaşılabilir.

<a name="loading-service"></a>

#### Servisi Yüklemek

-------

```php
$this->c['element']->method();
```

<a name="form"></a>

#### $this->element->form($action, $attributes = '', $hidden = array())

Ana konfigürasyon dosyasında tanımlı olan base > url değerine göre form tagı oluşturur. Form taglarını HTML olarak yazmak yerine bu fonksiyon kullanılarak yazılmasının ana faydası web site base url değeri değiştiğinde tüm form url adreslerinizi değiştirmek zorunda kalmamanızdır.

```php
echo $this->element->form('email/send', " method=get ");
```

```html
<form method="get" action="http:/example.com/index.php/email/send" />
```

<a name="adding-attributes"></a>

##### Nitelikler ( Attributes ) Eklemek

```php
echo $this->element->form('email/send', ['class' => 'email', 'id' => 'myform']);
```

```php
<form method="post" action="http:/example.com/index.php/email/send"  class="email"  id="myform" />
```

<a name="adding-hidden-inputs"></a>

##### Gizli Girdi Alanları Oluşturmak

```php
echo $this->element->form('email/send', '', ['username' => 'Joe', 'member_id' => '234']);
```

```html
<form method="post" action="http:/example.com/index.php/email/send">
<input type="hidden" name="username" value="Joe" />
<input type="hidden" name="member_id" value="234" />
```

#### $this->element->formMultipart($action, $attributes = array(), $hidden = array())

Bu fonksiyon <kbd>$this->element->form()</kbd> metotu ile aynı işlevleri yerine getirir form metodundan ayrılan yek yönü <kbd>upload</kbd> işlemleri için multipart özelliği eklemesidir.

```php
echo $this->element->formMultipart('file/upload');
```

```html
<form action="/file/upload" method="post" accept-charset="utf-8" enctype="multipart/form-data">
```

<a name="formClose"></a>

#### $this->element->formClose($extra = '')

```php
echo $this->element->formClose("</div></div>");
```

```html
</form>
</div></div>
```

<a name="hidden"></a>

#### $this->element->hidden($name, $value , $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="hidden"</kbd> olarak ayarlar.

```php
$this->element->hidden('username', 'johndoe',  $attr = " id='username' " );
```

```html
<input type="hidden" name="username" value="johndoe" id='username'  />
```

Array türünden veri gönderilerek de yaratılabilirler.

```php
$data = array(
              'name'  => 'John Doe',
              'email' => 'john@example.com',
              'url'   => 'http://example.com'
        );
echo $this->element->hidden($data);
```

```html
<input type="hidden" name="name" value="John Doe" />
<input type="hidden" name="email" value="john@example.com" />
<input type="hidden" name="url" value="http://example.com" />
```

<a name="input"></a>

#### $this->element->input($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="text"</kbd> olarak ayarlar.

```php
echo $this->element->input('username', 'johndoe', ' maxlength="100" size="50" style="width:50%"');
```

```html
<input type="text" name="username" id="username" 
value="johndoe" maxlength="100" size="50" style="width:50%" />
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $this->element->input('username', 'johndoe', ' onclick="someFunction()" ');
```

Array türünden veri gönderilerek de yaratılabilirler.

```php
$data = array(
    'name'      => 'username',
    'id'        => 'username',
    'value'     => 'johndoe',
    'maxlength' => '100',
    'size'      => '50',
    'style'     => 'width:50%',
);
echo $this->element->input($data);
```

<a name="password"></a>

#### $this->element->password($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="password"</kbd> olarak ayarlar ve diğer işlevleri <kbd>$this->element->input()</kbd> metodu ile aynıdır.

<a name="upload"></a>

#### $this->element->upload($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="file"</kbd> olarak ayarlar. Geri kalan diğer işlevleri <kbd>$this->element->input()</kbd> metodu ile aynıdır.

<a name="textarea"></a>

#### $this->element->textarea($name, $value, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="textarea"</kbd> olarak ayarlar. Geri kalan diğer işlevleri <kbd>$this->element->input()</kbd> metodu ile aynıdır.

```php
$data = array(
    'name'      => 'entry',
    'id'        => 'article',
    'value'     => '',
    'maxlength' => '800',
    'rows'      => '10',
    'cols'      => '5',
);
echo $this->element->textarea($data);
```

```html
<textarea name="entry" cols="40" rows="10" id="article" maxlength="800" ></textarea>
```

<a name="dropdown"></a>

#### $this->element-> dropdown($name, $options = '', $selected = '', $attributes = '')

Seçilebilir opsiyonlar girdisi oluşturur. İlk parametre girdi ismini, ikinci parametre seçme opsiyonlarını, üçüncü parametre seçili olan opsiyonları son parametre ise ekstra nitelikleri göndermenizi sağlar.

```php
$options = array(
    'small'  => 'Small Shirt',
    'med'    => 'Medium Shirt',
    'large'  => 'Large Shirt',
    'xlarge' => 'Extra Large Shirt',
);

echo $this->element->dropdown('shirts', $options, 'large');
```

```html
<select name="shirts">
<option value="small">Small Shirt</option>
<option value="med">Medium Shirt</option>
<option value="large" selected="selected">Large Shirt</option>
<option value="xlarge">Extra Large Shirt</option>
</select>
```

```php
echo $this->element->dropdown('shirts', $options, ['small', 'large']);
```

```html
<select name="shirts" multiple="multiple">
<option value="small" selected="selected">Small Shirt</option>
<option value="med">Medium Shirt</option>
<option value="large" selected="selected">Large Shirt</option>
<option value="xlarge">Extra Large Shirt</option>
</select>
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $this->element->dropdown('shirts', $options, 'large', ' id="shirts" onChange="someFunction();" ');
```

<a name="fieldset"></a>

#### $this->element->fieldset($legent_text = '', $attributes = array())

```php
echo $this->element->fieldset('Address Information');
echo "<p>fieldset content here</p>\n";
echo $this->element->fieldsetClose();
```

```html
<fieldset>
<legend>Address Information</legend>
<p>form content here</p>
</fieldset>
```

<a name="fieldsetClose"></a>

#### $this->element->fieldsetClose($extra = '')

```php
echo $this->element->fieldsetClose("</div></div>");
```

```html
</fieldset>
</div></div>
```

<a name="checkbox"></a>

#### $this->element->checkbox($data = '', $value = '', $checked = false, $attributes = '')

```php
echo $this->element->checkbox('newsletter', 'accept', true);
```

```html
<input type="checkbox" name="newsletter" value="accept" checked="checked" />
```

Üçüncü parametre true/false değeri alır ve kutunun seçili olup olmadığını belirler. Array türünden veri gönderilerek de yaratılabilirler.

```php
$data = array(
    'name'        => 'newsletter',
    'id'          => 'newsletter',
    'value'       => 'accept',
    'checked'     => true,
    'style'       => 'margin:10px',
    );

echo $this->element->checkbox($data);
```

```html
<input type="checkbox" name="newsletter" id="newsletter" 
value="accept" checked="checked" style="margin:10px" />
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $this->element->checkbox('newsletter', 'accept', true, ' onClick="someFunction()" ')
```

<a name="radio"></a>

#### $this->element->radio($data = '', $value = '', $checked = false, $attributes = '')

Bu fonksiyon girdi türünü <kbd>type="radio"</kbd> olarak ayarlar. Geri kalan diğer işlevleri <kbd>$this->element->checkbox()</kbd> metodu ile aynıdır.

<a name="submit"></a>

#### $this->element->submit()

```php
echo $this->element->submit('mysubmit', 'Submit Post!');
```

```
<input type="submit" name="mysubmit" value="Submit Post" />
```

<a name="reset"></a>

#### $this->element->reset()

```php
echo $this->element->reset('myreset', 'Reset Form');
```

```
<input type="reset" name="myreset" value="Reset Form"  />
```

<a name="button"></a>

#### $this->element->button()


```php
echo $this->element->button('name', 'Content');
```

```html
<button name="name" type="button">Content</button> 
```

Array türünden veri gönderilerek de yaratılabilirler.


```php
$data = array(
    'name'    => 'button',
    'id'      => 'button',
    'value'   => 'true',
    'type'    => 'reset',
    'content' => 'Reset'
);

echo $this->element->button($data);
```

```html
<button name="button" id="button" value="true" type="reset">Reset</button>  
```

JavaScript nitelikleri de ekleyebilirsiniz.

```php
echo $this->element->button('mybutton', 'Click Me', ' onClick="someFunction()" ');
```

<a name="form-class"></a>

## Form Sınıfı

Form sınıfı get metotları validator sınıfı yada kendi oluşturduğunuz form doğrulama hataları, genel form mesajları, form input değerlerini yönetir aynı zamanda form aksiyonundan sonra geri dönen sonuçlara göre form girdilerindeki opsiyonları seçili olarak göstermeye olanak sağlar.

Form sınıfı metotlarına ulaşabilmek için sınıfın bir kez yüklenmesi gerekir.

```php
$this->c['form'];
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

<a name="security"></a>

### Güvenlik

<a name="dangerous-inputs"></a>

#### Tehlikeli Girdilerden Kaçış

Form içerisinde Quote gibi form yapısını bozan karakterler ve HTML karakterlerini güvenli bir şekilde kullanmanıza olanak tanır.

```php
$string = 'Tehlikeli girdiler içeren <strong>"alıntılı"</strong> yazı.';

<input type="text" name="myform" value="<?php echo $string ?>" />
```

Yukarıdaki veri çift tırnak karakterleri içerir ve form girdi yapısını bozar. <kbd>$this->c['clean']->escape()</kbd> metodu ise aşağıdaki gibi HTML karakterlerini kodlayarak form içinde güvenli bir şekilde kullanılmasını sağlar.

```php
<input type="text" name="myform" value="<?php echo $this->c['clean']->escape($string); ?>" />
```

> **Not:** Eğer form element sınıfı fonksiyonlarını zaten kullanıyorsanız bu fonksiyonu kullanmaya ihtiyacınız kalmaz çünkü form değerleri otomatik olarak güvenli formata dönüştürülür. Bu fonksiyonu yalnızca kendi form elementlerinizi oluşturduğunuz zaman kullanmanız önerilir.


<kbd>Clean</kbd> ve diğer <kbd>Girdi Filtreleri</kbd> için filtre paketine ait [Filters.md](Filters.md) dökümentasyonunu gözden geçirmeyi unutmayın.