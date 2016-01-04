
## Doğrulama Sınıfı

Doğrulama sınıfı yazdığınız kodu minimize ederek form girdilerini kapsamlı bir şekilde doğrulamayı sağlar. Buna ek olarak doğrulama sınıfına ait konfigürasyon dosyasından kendi kurallarınızı tanımlayabilir yada geri çağırım fonksiyonu ile geçici kurallar oluşturabilirsiniz.

<ul>
    <li><a href="#how-it-works">Nasıl Çalışır ?</a>
        <ul>
            <li><a href="#rules-config">Kural Konfigürasyonu</a></li>
        </ul>
    </li>

    <li><a href="#validation">Doğrulama</a>
        <ul>
            <li><a href="#setRules">$this->validator->setRules()</a></li>
            <li><a href="#isValid">$this->validator->isValid()</a></li>
            <li><a href="#ruleReference">Kural Referansı</a></li>
        </ul>
    </li>

    <li>
        <a href="#callback">Geri Çağırım</a>
        <ul>
            <li><a href="#func">$this->validator->callback()</a></li>
        </ul>
    </li>

    <li>
        <a href="#errors">Hatalar</a>
        <ul>
            <li><a href="#setError">$this->validator->setError()</a></li>
            <li><a href="#setErrors">$this->validator->setErrors()</a></li>
            <li><a href="#getError">$this->validator->getError()</a></li>
            <li><a href="#getErrors">$this->validator->getErrors()</a></li>
            <li><a href="#setMessage">$this->validator->setMessage()</a></li>
            <li><a href="#setMessage">$this->validator->getMessages()</a></li>
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
        </ul>
    </li>

    <li>
        <a href="#values">Form Sınıfı</a>
        <ul>
            <li><a href="#formSetError">$this->form->setError()</a></li>
            <li><a href="#formSetErrors">$this->form->setErrors()</a></li>
            <li><a href="#formGetError">$this->form->getError()</a></li>
            <li><a href="#formGetErrors">$this->form->getErrors()</a></li>
            <li><a href="#formGetValue">$this->form->getValue()</a></li>
            <li><a href="#setValue">$this->form->setValue()</a></li>
            <li><a href="#setValue">$this->form->setSelect()</a></li>
            <li><a href="#setValue">$this->form->setCheckbox()</a></li>
            <li><a href="#setValue">$this->form->setRadio()</a></li>
        </ul>
    </li>

    <li><a href="#validation-tutorial">Ek Bilgiler</a>
        <ul>
            <li><a href="#prepping-data">Veri Filtreleme</a></li>
            <li><a href="#translating-field-names">Çoklu Diller Kullanmak</a></li>
            <li><a href="#using-arrays-as-field-name">Girdi Alanlarında Array Kullanmak</a></li>
            <li><a href="#create-your-own-rules">Kendi Kurallarınızı Oluşturun</a></li>
        </ul>    
    </li>
</ul>

<a name="how-it-works"></a>

### Nasıl Çalışır

Doğrulama sınıfı <kbd>setRules</kbd> metodu içerisine girilen ilk parametre form elementine ait isim, ikinci parametre etiket ve üçüncü parametre ise doğrulama kurallarıdır. Her doğrulama kuralı bir nesnedir.

```php
$this->validator->setRules('username', 'Username', 'required|min(5)|email');
```

Örneğin min doğrulama kuralı <kbd>Obullo\Validator\Rules\Min</kbd> adlı sınıfı çağırır.

```php
class Min
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

<a name="field"></a>

#### Field Nesnesi

Her bir kural sınıfı içerisindeki <kbd>invoke</kbd> metodu içerisinden <kbd>Field $field</kbd> nesnesi gönderilir ve __invoke metodu ile kurallar çalıştırılmış olur. Field nesnesi get metotları form elementine ait özellikleri verir. Aşağıdaki örnekte <kbd>min(5)</kbd> kuralından elde edilen değerler gözüküyor.


```php
echo $field->getValue();  // username@example.com
echo $field->getName();   // username
echo $field->getLabel();  // Username
print_r($field->getParams());  // 5
```

Set metotları ile element değerleri yenilenebilir yada forma bir mesaj gönderilebilir.

```php
$field->setValue("Field post value");
$field->setError("Field error");
$field->setMessage("Field form message");
```

<a name="next"></a>

#### $next() Komutu

Eğer doğrulama başarılı ise field sınıfının $next metodu ile bir sonraki kuralı çağırması sağlanır.

```php
if ($this->isValid($value)) {
    return $next();
}
```

Örneğin eğer <kbd>min(5)</kbd> kuralı doğrulanırsa next komutu ile sonraki <kbd>email</kbd> kuralı çağırılmış olur.


```php
$this->validator->setRules('username', 'Username', 'required|min(5)|email');
```

![Validation Rules](images/validation-rules.png?raw=true "Validation Rules")



<a name="rules-config"></a>

#### Kural Konfigürasyonu

Her bir kurala ait sınıf <kbd>app/$env/validator.php</kbd> dosyası içerisinde aşağıdaki gibi tanımlıdır.

```php
return array(

    'rules' => [

        'alpha' => 'Obullo\Validator\Rules\Alpha',
        'alphadash' => 'Obullo\Validator\Rules\AlphaDash',
        'alnum' => 'Obullo\Validator\Rules\Alnum',
        'alnumdash' => 'Obullo\Validator\Rules\AlnumDash',
        .
        .
    ]
);
```

> **Not:** Bu dosya içerisinde değişiklik yaparak kendi doğrulama kurallarınızı oluşturabilirsiniz.


<a name="validation"></a>

### Doğrulama

Form doğrulama kuralları kontroller sınıfı içerisinde <kbd>setRules()</kbd> metodu ile oluşturulur ve <kbd>isValid</kbd> metodu ile tetiklenir.

<a name="setRules"></a>

#### $this->validator->setRules()

```php
if ($this->request->isPost()) {

    $this->validator->setRules('email', 'Email', 'required|email');
    $this->validator->setRules('password', 'Password', 'required|min(6)');
}
```

<a name="isValid"></a>

#### $this->validator->isValid()

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

Form <kbd>setErrors()</kbd> metodu ile validator nesnesi form sınıfına referans olarak gönderilir. Böylece view kısmında form nesnesi üzerinden validator değerlerine ulaşılmış olur.

```php
<?php echo $this->form->getMessage() ?>

 <form name="example" action="/examples/forms/form" method="POST">
    <?php echo $this->form->getError('email') ?>
    <input type="email" name="email" value="<?php echo $this->form->getValue('email') ?>">

    <?php echo $this->form->getError('password') ?>
    <input type="password" name="password" id="pwd" placeholder="Password">
  <button type="submit" class="btn btn-default">Submit</button>
</form>
```

<a name="ruleReference"></a>

#### Kural Referansı

Aşağıda mevcut olan kurallar listesi gösteriliyor.

<table>
<thead>
<tr>
<th>Kural</th>
<th>Parametre</th>
<th>Açıklama</th>
</tr>
</thead>
<tbody>

<tr>
    <td>alpha</td>
    <td>-</td>
    <td>Eğer form element değeri ( a-z A-Z 0-9 ) karakterleri haricinde bir karakter içeriyorsa false değerine döner.</td>
</tr>
<tr>
    <td>alphaDash</td>
    <td>-</td>
    <td>Eğer form element değeri ( a-z A-Z 0-9_- ) karakterleri haricinde bir karakter içeriyorsa false değerine döner.</td>
</tr>
<tr>
    <td>creditCard</td>
    <td></td>
    <td>Eğer form element değeri </td>
</tr>
<tr>
    <td>date</td>
    <td>date(Y-m-d)</td>
    <td>Eğer form element değeri girilen tarih formatı ile uyuşmuyorsa false değerine döner.</td>
</tr>
<tr>
    <td>email</td>
    <td>email(true)</td>
    <td>Eğer form elementi geçerli bir email adresi içermiyorsa false değerine döner. Kural parametresine true gönderilirse dns kontrolü de yapılır.
</td>
</tr>
<tr>
    <td>exact</td>
    <td>exact(8)</td>
    <td>Eğer form element değerinin genişliği girilen değere tam olarak eşit değilse false değerine geri döner.</td>
</tr>
<tr>
    <td>iban</td>
    <td>iban(COUNTRY_CODE) eğer SEPA ülkeleri istenmiyorsa ikinci parametre false girilir. iban(FR)(false)</td>
    <td>Eğer form element değeri girilen ülke koduna ait geçerli IBAN kodu içermiyorsa false değerine geri döner.</td>
</tr>
<tr>
    <td>isBool</td>
    <td>-</td>
    <td>Eğer form element değeri boolean ( true / false or 0 / 1 ) değerlerini içermiyorsa false değerine geri döner.</td>
</tr>
<tr>
    <td>isDecimal</td>
    <td>-</td>
    <td>Eğer form element değeri decimal (0,2, 0,10) karakterler içermiyorsa false değerine döner.</td>
</tr>
<tr>
    <td>isJson</td>
    <td>-</td>
    <td>Eğer form element json değeri decode edilemiyorsa false değerine geri döner.</td>
</tr>
<tr>
    <td>isNumeric</td>
    <td>No</td>
    <td>Eğer form element değeri sayısal karakterler içermiyorsa false değerine geri döner.</td>
    <td></td>
</tr>
<tr>
    <td>matches</td>
    <td>matches(field_name)</td>
    <td>Eğer form element girilen form element değeri ile eşleşmiyorsa false değerine döner.</td>
</tr>
<tr>
    <td>min</td>
    <td>min(n)</td>
    <td>Eğer form element değerinin genişliği girilen değerden küçük ise false değerine geri döner.</td>
</tr>
<tr>
    <td>max</td>
    <td>max(n)</td>
    <td>Eğer form element değerinin genişliği girilen değerden büyük ise false değerine geri döner.</td>
</tr>

<tr>
    <td>alphaNumeric</td>
    <td>-</td>
    <td>Returns false if the form element contains anything other than alpha-numeric characters.</td>
</tr>
<tr>
    <td>alphaDash</td>
    <td>-</td>
    <td>Returns false if the form element contains anything other than alpha-numeric characters, underscores or dashes.</td>
</tr>
<tr>
    <td>emails</td>
    <td>emails(true)</td>
    <td>Returns false if any value provided in a comma separated list is not a valid email. (If parameter true or 1 function also will do a dns query foreach emails)</td>
</tr>
<tr>
    <td>required</td>
    <td>-</td>
    <td>Eğer form elementi boş ise false değerine döner.</td>
    <td></td>
</tr>
<tr>
    <td>validIp</td>
    <td>-</td>
    <td>Returns false if the supplied IP is not valid.</td>
</tr>
<tr>
    <td>validBase64</td>
    <td>-</td>
    <td>Returns false if the supplied string contains anything other than valid Base64 characters.</td>
</tr>
<tr>
    <td>noSpace</td>
    <td>-</td>
    <td>Returns false if the supplied string contains space characters.</td>
</tr>
<tr>
    <td>callback_function(param)</td>
    <td>callback_functionname(param)</td>
    <td>You can define a custom callback function which is a class method located in your current model or just a function.</td>
</tr>
</tbody>
</table>




### Explanation <a name="explanation"></a>

------

```php
<?php echo $this->validator->getErrorString() ?>
```

This function will return any error messages sent back by the validator. If there are no messages it returns an empty string.

The <kbd>controller</kbd> (form_example.php) has one function: <kbd>index()</kbd>. This function initializes the validation class and loads the <kbd>form helper</kbd> and <kbd>URL helper</kbd> used by your view files. It also <samp>runs</samp> the validation routine. Based on the success of the validation, it either presents the form or the success page.

### Setting Validation Rules <a name="setting-validation-rules"></a>

------

Obullo lets you set as many validation rules as you need for a given field, cascading them in order, and it even lets you prep and pre-process the field data at the same time. To set validation rules you will use the <kbd>setRules()</kbd> function:

```php
$this->validator->setRules();
```

The above function takes **three** parameters as input:

1. The field name - the exact name you've given the form field.
2. A "human" name for this field, which will be inserted into the error message. For example, if your field is named "user" you might give it a human name of "Username". **Note:** If you would like the field name to be stored in a language file, please see Translating Field Names.
3. The validation rules for this form field.

Here is an example. In your <kbd>controller</kbd> (form.php), add this code just below the validation initialization function:

```php
$this->validator->setRules('username', 'Username', 'required');
$this->validator->setRules('password', 'Password', 'required');
$this->validator->setRules('passconf', 'Password Confirmation', 'required');
$this->validator->setRules('email', 'Email', 'required');
```

Now submit the form with the fields blank and you should see the error messages. If you submit the form with all the fields populated you'll see your success page.

**Note:** The form fields are not yet re-populated with the data when there is an error. We'll get to that shortly.

### Cascading Rules <a name="cascading-rules"></a>

------

Obullo lets you pipe multiple rules together. Let's try it. Change your rules in the third parameter of rule setting function, like this:

```php
$this->validator->setRules('username', 'Username', 'required|min(5)|max(12)');
$this->validator->setRules('password', 'Password', 'required|matches(passconf)');
$this->validator->setRules('passconf', 'Password Confirmation', 'required');
$this->validator->setRules('email', 'Email', 'required|email');
```

The above code sets the following rules:

1. The username field must not be shorter than 5 characters and no longer than 12.
2. The password field must match the password confirmation field.
3. The email field must contain a valid email address.

Give it a try ! Submit your form without the proper data and you'll see new error messages that correspond to your new rules. There are numerous rules available which you can read about in the validation reference.

### Prepping Data <a name="prepping-data"></a>

------

In addition to the validation functions like the ones we used above, you can also prep your data in various ways. For example, you can set up rules like this:

```php
$this->validator->setRules('username', 'Username', 'trim|required|min(5)|max(12)');
$this->validator->setRules('password', 'Password', 'trim|required|matches(passconf)|md5');
$this->validator->setRules('passconf', 'Password Confirmation', 'trim|required');
$this->validator->setRules('email', 'Email', 'trim|required|email');
```

In the above example, we are "trimming" the fields, converting the password to MD5, and running the username through the "xssClean" function, which removes malicious data.

**Any native PHP function that accepts one parameter can be used as a rule, like** <kbd>htmlspecialchars, trim, MD5,</kbd> **etc.**

**Note:** You will generally want to use the prepping functions **after** the validation rules so if there is an error, the original data will be shown in the form.


### Re-populating the form <a name="re-populating-the-form"></a>

------

Thus far we have only been dealing with errors. It's time to repopulate the form field with the submitted data. Validator offers several helper functions that permit you to do this. The one you will use most commonly is:

```php
$this->form->setValue('fieldname')
```

**Note:*** Don't forget to include each. field name in the** <kbd>setValue()</kbd> **functions!**

```php
<?php echo $this->validator->getErrorString(); ?>

<form action="/tutorials/hello_form" method="POST">
    <table width="100%">
        <tr>
            <td style="width:20%;">Email</td>
            <td><?php echo $this->form->error('email'); ?>
            <input type="text" name="email" value="<?php echo $this->form->setValue('email'); ?>" />
            </td>
        </tr>
        <tr>
            <td>Password</td>
            <td><?php echo $this->form->error('password', '<div>', '</div>'); ?>
            <input type="password" name="password" value="<?php echo $this->form->setValue('password'); ?>" /></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="dopost" value="DoPost" /></td>
        </tr>
        </table>
</form>
```

<kbd>Now reload your page and submit the form so that it triggers an error. Your form fields should now be re-populated</kbd>

**Note:** The Function Reference section below contains functions that permit you to re-populate (select) menus, radio buttons, and checkboxes.
**Important Note:** If you use an array as the name of a form field, you must supply it as an array to the function. Example:

```php
<input type="checkbox" name="colors[]" value="<?php echo $this->form->setValue('colors[]') ?>" />
```

For more info please see the [Using Arrays as Field Names](#using-arrays-as-field-name) section below.


### Callbacks: Your own Validation Functions <a name="callbacks"></a>

------

The validation system supports callbacks to your own validation functions. This permits you to extend the validation class to meet your needs. For example, if you need to run a database query to see if the user is choosing a unique username, you can create a callback function that does that. Let's create a example of this.

In your controller, change the "username" rule to this:

```php
$this->validator->setRules('username', 'Username', 'callback_username');

$this->validator->func(
            'callback_username',
            function ($username, $val) {
                if (strlen($username) < $val) {
                    $this->setMessage('callback_username', 'Username can not bigger than '.$val. 'chars.');
                    $this->logger->debug('Callback username test.', array('username' => $username));
                    return false;
                }
                return true;
            }
        );

```

Then add a new function called <kbd>username</kbd> using func() method. Here's how your controller should now look:

```php
/**
 * $app hello_form
 * 
 * @var HelloForm
 */
namespace HelloForm;

Class HelloForm extends \Controller
{
    /**
     * Loader
     * 
     * @return void
     */
    public function load()
    {
        $this->c['url'];
        $this->c['view'];
        $this->c['post'];
        $this->c['form'];
        $this->c['session'];
    }

    public function index()
    {
        if ($this->request->isPost()) {  // If we have submit post

            $this->validator->setRules('email', 'Email', 'required|email|callback_username(100)');
            $this->validator->setRules('password', 'Password', 'required|min(6)');
            $this->validator->setRules('confirm_password', 'Confirm Password', 'required|matches(password)');
            $this->validator->setRules('agreement', 'User Agreement', 'required');

            $this->validator->func(
                'callback_username',
                function ($username, $val) {
                    if (strlen($username) < $val) {
                        $this->setMessage('callback_username', 'Username can not bigger than '.$val. 'chars.');
                        $this->logger->debug('Callback username test.', array('username' => $username));
                        return false;
                    }
                    return true;
                }
            );
            if ($this->validator->isValid()) {
                $this->flash->success('Example form success message in current page !');
                $this->url->redirect('/helloform');
            }else{
                $this->form->setErrors($this->validator);
            }
        }

        $this->c['view']->load(
            'hello_form', 
            function () {
                $this->assign('name', 'Obullo');
                $this->assign('footer', $this->template('footer'));
                $this->layout('default');
            }
        );
    }
}

/* End of file hello_form.php */
/* Location: .public/tutorials/controller/hello_form.php */
```

<kbd>Reload your form and submit it with the word "test" as the username. You can see that the form field data was passed to your callback function for you to process.</kbd>

**To invoke a callback just put the function name in a rule, with "callback_" as the rule prefix.**

You can also process the form data that is passed to your callback and return it. If your callback returns anything other than a boolean true/false it is assumed that the data is your newly processed form data.

### Setting Error Messages <a name="setting-error-messages"></a>

------

All of the native error messages are located in the following language file: <kbd>app/translations/en-US/validator.php</kbd>

To set your own custom message you can either edit that file, or use the following function:

```php
<?php
$this->validator->setMessage('rule', 'Error Message');
```

Where <var>rule</var> corresponds to the name of a particular rule, and <var>Error Message</var> is the text you would like to be displayed.

If you include <kbd>%s</kbd> in your error string, it will be replaced with the "human" name you used for your field when you set your rules.

In the **"callback"** example above, the error message was set by passing the name of the function:

```php
<?php
$this->validator->setMessage('usernameCheck')
```

You can also override any error message found in the language file. For example, to change the message for the "required" rule you will do this:

```php
<?php
$this->validator->setMessage('required', 'Your custom message here');
```

### Translating Field Names <a name="translating-field-names"></a>

------

If you would like to store the "human" name you passed to the <kbd>setRules()</kbd> function in a language file, and therefore make the name able to be translated, here's how:

First, prefix your "human" name with <kbd>translate:</kbd>, as in this example:

```php
<?php
$this->validator->setRules('first_name', 'translate:FORM_LABEL:FIRSTNAME', 'required');
```

Then, store the name in one of your language file arrays (without the prefix):

```php
<?php
$translate['FORM_LABEL:FIRSTNAME'] = 'First Name';
```

**Note:** If you store your array item in a language file that is not loaded automatically by Framework, you'll need to remember to load it in your controller using:

```php
<?php
$this->translator->load('filename');
```

See the Translator package for more info regarding language files.

### Changing the Error Delimiters <a name="changing-the-error-delimiters"></a>

------

By default, the Validator Class adds a paragraph tag ```php<p>``` around each error message shown. You can change these delimiters either globally or individually.

1. **Changing delimiters Globally**

    To globally change the error delimiters, in your controller function, just after loading the Form Validation class, add this:

```php
<?php
$this->validator->setErrorDelimiters('<div class="error">', '</div>');
```
    In this example, we've switched to using div tags.

2. Changing delimiters Individually

   Each of the two error generating functions shown in this tutorial can be supplied their own delimiters as follows:

```php
<?php echo $this->validator->getError('field name', '<div class="error">', '</div>'); ?>
```

Or:

```
<?php echo $this->validator->getErrorString('<div class="error">', '</div>'); ?>
```

### Showing Errors Individually <a name="showing-errors-individually"></a>

------

If you prefer to show an error message next to each form field, rather than as a list, you can use the <kbd>$this->validator->getError()</kbd> function.

Try it! Change your form so that it looks like this:

```php
<h5>Username</h5>
<?php echo $this->validator->getError('username'); ?>
<input type="text" name="username" value="<?php echo $this->validator->setValue('username'); ?>" size="50" />

<h5>Password</h5>
<?php echo $this->validator->getError('password'); ?>
<input type="text" name="password" value="<?php echo $this->validator->setValue('password'); ?>" size="50" />

<h5>Password Confirm</h5>
<?php echo $this->validator->getError('passconf'); ?>
<input type="text" name="passconf" value="<?php echo $this->validator->setValue('passconf'); ?>" size="50" />

<h5>Email Address</h5>
<?php echo $this->validator->getError('email'); ?>
<input type="text" name="email" value="<?php echo $this->validator->setValue('email'); ?>" size="50" />
```

If there are no errors, nothing will be shown. If there is an error, the message will appear.

**Important Note:** If you use an array as the name of a form field, you must supply it as an array to the function. Example:

```php
<?php echo $this->validator->getError('options[size]'); ?>
<input type="text" name="options[size]" value="<?php echo $this->validator->setValue("options[size]"); ?>" size="50" /> 
```

For more info please see the [Using Arrays as Field Names](#using-arrays-as-field-name) section below.

### Saving Sets of Validation Rules to a Config File <a name="saving-sets-of-validation"></a>

------

A nice feature of the Form Validation class is that it permits you to store all your validation rules for your entire application in a config file. You can organize these rules into "groups". These groups can either be loaded automatically when a matching controller/function is called, or you can manually call each set as needed.


### Using Arrays as Field Names <a name="using-arrays-as-field-name"></a>

------

The Form Validator class supports the use of arrays as field names. Consider this example:

```html
<input type="text" name="options[]" value="" size="50" />
```

If you do use an array as a field name, you must use the EXACT array name in the [Helper Functions](#helper-reference) that require the field name, and as your Validation Rule field name.

For example, to set a rule for the above field you would use:

```php
<?php
$this->validator->setRules('options[]', 'Options', 'required');
```

Or, to show an error for the above field you would use:

```php
<?php echo $this->validator->getError('options[]'); ?>
```

Or to re-populate the field you would use:

```php
<input type="text" name="options[]" value="<?php echo set_value('<kbd>options[]</kbd>'); ?>" size="50" />
```

You can use multidimensional arrays as field names as well. For example:

```html
<input type="text" name="options[size]" value="" size="50" />
```

Or even:

```html
<input type="text" name="sports[nba][basketball]" value="" size="50" />
```

As with our first example, you must use the exact array name in the helper functions:

```php
<?php echo $this->validator->getError('sports[nba][basketball]'); ?>
```

If you are using checkboxes (or other fields) that have multiple options, don't forget to leave an empty bracket after each option, so that all selections will be added to the POST array:

```html
<input type="checkbox" name="options[]" value="red" />
<input type="checkbox" name="options[]" value="blue" />
<input type="checkbox" name="options[]" value="green" /> 
```

Or if you use a multidimensional array:

```html
<input type="checkbox" name="options[color][]" value="red" />
<input type="checkbox" name="options[color][]" value="blue" />
<input type="checkbox" name="options[color][]" value="green" /> 
```

When you use a helper function you'll include the bracket as well:

```php
<?php echo $this->validator->getError('options[color][]'); ?>
```

**Note:** You can also use any native PHP functions that permit one parameter.

### Prepping Reference <a name="prepping-reference"></a>

------

The following is a list of all the prepping functions that are available to use:

<table>
    <thead>
            <tr>
                <th>Name</th>
                <th>Parameter</th>
                <th>Description</th>
            </tr>
    </thead>
    <tbody>
            <tr>
                <td>xssClean</td>
                <td>No</td>
                <td>Runs the data through the XSS filtering function, described in the <kbd>Security Helper</kbd> package.</td>
            </tr>
            <tr>
                <td>prepForForm</td>
                <td>No</td>
                <td>Converts special characters so that HTML data can be shown in a form field without breaking it.</td>
            </tr>
            <tr>
                <td>prepUrl</td>
                <td>No</td>
                <td>Adds "http://" to URLs if missing.</td>
            </tr>
            <tr>
                <td>stripTags</td>
                <td>No</td>
                <td>Strips the HTML from image tags leaving the raw URL.</td>
            </tr>
            <tr>
                <td>encodePhpTags</td>
                <td>No</td>
                <td>Converts PHP tags to entities.</td>
            </tr>
    </tbody>
</table>
  
**Note:** You can also use any native PHP functions that permit one parameter, like <samp>trim, htmlspecialchars, urldecode,</samp> etc.


### Validator Function Reference <a name="function-reference"></a>

-------

The following functions are intended to be used in your controller functions.

#### $this->validator->setRules(string $rules);

Permits you to set validation rules.

#### $this->validator->isValid();

Runs the validation routines. Returns boolean true on success and false on failure.

#### $this->validator->func(string $callback_funcname, closure func);

Creates a callback functions.

#### $this->validator->setMessage(string $rule, string $message);

Permits you to set custom error messages.

#### $this->validator->getError(string $field)

Shows an individual error message associated with the field name supplied to the function.

#### $this->validator->getErrors()

Returns to all errors in array format.

#### $this->validator->isError($field)

Shows an individual error message associated with the field name supplied to the function and it returns to string if supplied an error string otherwise it returns to **booelan**

```php
<?php var_dump( $this->validator->isError('username') );  // boolean  ?>
```

#### $this->validator->setError(string $key, string $message);

Set error message as string ( field - error ). 

#### $this->validator->setErrors(array $errors);

Sets key - value errors as array. ( Field - Error message ).

#### $this->validator->setErrorDelimiters(string $prefix = '&lt;p&gt;', $suffix = '&lt;/p&gt;')

Change error delimiters globally. See the [Changing the Error Delimiters](#changing-the-error-delimiters) section above.

#### $this->validate->getErrorString()

Shows all error messages as a string.


### Form Function Reference

#### $this->form->value(string $field)

Returns to validated field value.

#### $this->form->error(string $field)

Shows an individual error message associated with the field name supplied to the function.

#### $this->form->setValue(string $field, $default = '')

Permits you to set the value of an input form or textarea. You must supply the field name via the first parameter of the function. The second (optional) parameter allows you to set a default value for the form. Example:

```php
<input type="text" name="quantity" value="<?php echo $this->validator->setValue('quantity', '0'); ?>" size="50" />
```
The above form will show "0" when loaded for the first time.

#### $this->form->setSelect()

If you use a <kbd>(select)</kbd> menu, this function permits you to display the menu item that was selected. The first parameter must contain the name of the select menu, the second parameter must contain the value of each item, and the third (optional) parameter lets you set an item as the default (use boolean true/false).

Example:

```php
<select name="myselect">
<option value="one" <?php echo $this->form->setSelect('myselect', 'one', true); ?> >One</option>
<option value="two" <?php echo $this->form->setSelect('myselect', 'two'); ?> >Two</option>
<option value="three" <?php echo $this->form->setSelect('myselect', 'three'); ?> >Three</option>
</select>
``` 

#### $this->form->setCheckbox()

Permits you to display a checkbox in the state it was submitted. The first parameter must contain the name of the checkbox, the second parameter must contain its value, and the third (optional) parameter lets you set an item as the default (use boolean true/false). Example:

```php
<input type="checkbox" name="mycheck[]" value="1" <?php echo $this->form->setCheckbox('mycheck[]', '1'); ?> />
<input type="checkbox" name="mycheck[]" value="2" <?php echo $this->form->setCheckbox('mycheck[]', '2'); ?> />
```

#### $this->form->setRadio()

Permits you to display radio buttons in the state they were submitted. This function is identical to the set_checkbox() function above.

```php
<input type="radio" name="myradio" value="1" <?php echo $this->form->setRadio('myradio', '1', true); ?> />
<input type="radio" name="myradio" value="2" <?php echo $this->form->setRadio('myradio', '2'); ?> />
```

Kendi doğrulama kurallarınızı oluşturmak.


```php
namespace Form\Validator;

use Obullo\Container\ContainerInterface as Container;

/**
 * Test
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Test
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Container
     * 
     * @param Container $container contaienr
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    /**
     * Match one field to another
     * 
     * @param string $str   string
     * @param string $field field
     * 
     * @return bool
     */    
    public function isValid($str)
    {   
        return false;
    }
}
```