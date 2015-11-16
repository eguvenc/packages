
## Validator Class

This class provides a comprehensive form validation and data prepping class that helps minimize the amount of code you'll write.

<ul>
<li><a href="#overview">Overview</a></li>
<li><a href="#validation-tutorial">Validation Tutorial</a>
    <ul>
        <li><a href="#the-controller">The Controller</a></li>
        <li><a href="#setting-validation-rules">Setting Validation Rules</a></li>
        <li><a href="#cascading-rules">Cascading Rules</a></li>
        <li><a href="#prepping-data">Prepping Data</a></li>
        <li><a href="#re-populating-the-form">Re-populating the Form</a></li>
        <li><a href="#callbacks">Callbacks</a></li>
        <li><a href="#setting-error-messages">Setting Error Messages</a></li>
        <li><a href="#changing-the-error-delimiters">Changing the Error Delimiters</a></li>
        <li><a href="#translating-field-names">Translating Field Names</a></li>
        <li><a href="#showing-errors-individually">Showing Errors Individually</a></li>
        <li><a href="#saving-sets-of-validation">Saving Sets of Validation Rules to a Config File</a></li>
        <li><a href="#using-arrays-as-field-name">Using Arrays as Field Name</a></li>
    </ul>    
</li>
<li><a href="#rule-reference">Rule Reference</a></li>    
<li><a href="#prepping-reference">Prepping Reference</a></li>    
<li><a href="#function-reference">Function Reference</a></li>    
<li><a href="#helper-reference">Helper Reference</a></li>    
</ul>

### Overview <a name="overview"></a>

------

Before explaining traditional approach to data validation, let's describe the ideal scenario:

1. A form is displayed.
2. You fill and submit it.
3. If you submit something invalid, or perhaps miss a required item, the form is redisplayed containing your data along with an error message describing the problem.
4. This process continues until you submit a valid form.

On the receiving end, the script must:

1. Check for the required data.
2. Verify that the data is of the correct type, and meets the correct criteria. For example, if a username is submitted it must be validated to contain only permitted characters. It must be of a minimum length, and not exceed a maximum length. The username can't be someone else's existing username, or perhaps even a reserved word. Etc.
3. Sanitize the data for security.
4. Pre-format the data if needed (Does the data need to be trimmed? HTML encoded? Etc.)
5. Prep the data for insertion into a database.

Although there is nothing terribly complex about the above process, it usually requires a significant amount of code, and to display error messages, various control structures are usually placed within the form HTML. Form validation, while simple to create, is generally very messy and tedious to implement.

### Validation Tutorial <a name="validation-tutorial"></a>

------

What follows is a "hands on" tutorial for implementing Form Validation.

In order to implement form validation you'll need three things:

1. A view file containing a form.
2. A view file containing a "success" message to be displayed upon successful submission.
3. A controller function to receive and process the submitted data.

Let's create those three things, using a member sign-up form as the example.


### Controller <a name="the-controller"></a>

------

An example form validation controller:

```php
<?php

/**
 * $app hello_form
 * 
 * @var Controller
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
        $this->c['html'];
        $this->c['view'];
        $this->c['post'];
        $this->c['form'];
        $this->c['request'];
        $this->c['flash as flash'];
    }

    public function index()
    {
        if ($this->request->isPost()) {  // If we have submit post

            $this->c['validator'];

            $this->validator->setRules('email', 'Email', 'required|email');
            $this->validator->setRules('password', 'Password', 'required|min(6)');
            $this->validator->setRules('confirm_password', 'Confirm Password', 'required|matches(password)');
            $this->validator->setRules('agreement', 'User Agreement', 'required');

            if ($this->validator->isValid()) {
                $this->flash->success('Example form success message in current page !');  // 1 = success, 0 = fail
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

### View <a name="the-view"></a>


```php
<section><?php echo $this->form->message() ?></section>
<section><?php echo $this->flash->output() ?></section>

<form action="/tutorials/hello_form" method="POST">
    <table width="100%">
        <tr>
            <td style="width:20%;">Email</td>
            <td><?php echo $this->form->error('email'); ?>
            <input type="text" name="email" value="<?php echo $this->form->value('email') ?>" />
            </td>
        </tr>
        <tr>
            <td>Password</td>
            <td><?php echo $this->form->error('password'); ?>
            <input type="password" name="password" value="" /></td>
        </tr>
        <tr>
            <td>Confirm</td>
            <td><?php echo $this->form->error('confirm_password'); ?>
            <input type="password" name="confirm_password" value="" /></td>
        </tr>
        <tr>
            <td></td>
            <td><?php echo $this->form->error('agreement'); ?>
            <input type="checkbox" name="agreement" value="1"  id="agreement"><label for="agreement"> I agree terms and conditions</label></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="submit" value="DoPost" /></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        </table>
</form>
```

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
<?php
$this->validator->setRules();
```

The above function takes **three** parameters as input:

1. The field name - the exact name you've given the form field.
2. A "human" name for this field, which will be inserted into the error message. For example, if your field is named "user" you might give it a human name of "Username". **Note:** If you would like the field name to be stored in a language file, please see Translating Field Names.
3. The validation rules for this form field.

Here is an example. In your <kbd>controller</kbd> (form.php), add this code just below the validation initialization function:

```php
<?php
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
<?php
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
<?php
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
<?php
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
<?php
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
<?php

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

### Rule Reference <a name="rule-reference"></a>

------

The following is a list of all the native rules that are available to use:

<table>
<thead>
<tr>
<th>Rule</th>
<th>Parameter</th>
<th>Description</th>
<th>Example</th>
</tr>
</thead>
<tbody>
<tr>
<td>required</td>
<td>No</td>
<td>Returns false if the form element is empty.</td>
<td></td>
</tr>
<tr>
<td>contains</td>
<td>Yes</td>
<td>Returns false if the form element has unaccepted values.</td>
<td>contains(1) or contains(foo,bar), contains(1,3,9)</td>
</tr>
<tr>
<td>matches</td>
<td>Yes</td>
<td>Returns false if the form element does not match the one in the parameter.</td>
<td>matches(form_item)</td>
</tr>
<tr>
<td>min</td>
<td>Yes</td>
<td>Returns false if the form element is shorter then the parameter value.</td>
<td>min(6)</td>
</tr>
<tr>
<td>max</td>
<td>Yes</td>
<td>Returns false if the form element is longer then the parameter value.</td>
<td>max(12)</td>
</tr>
<tr>
<td>exact</td>
<td>Yes</td>
<td>Returns false if the form element is not exactly the parameter value.</td>
<td>exact(8)</td>
</tr>
<tr>
<td>alpha</td>
<td>No</td>
<td>Returns false if the form element contains anything other than alphabetical characters.</td>
<td></td>
</tr>
<tr>
<td>alphaNumeric</td>
<td>No</td>
<td>Returns false if the form element contains anything other than alpha-numeric characters.</td>
<td></td>
</tr>
<tr>
<td>alphaDash</td>
<td>No</td>
<td>Returns false if the form element contains anything other than alpha-numeric characters, underscores or dashes.</td>
<td></td>
</tr>
<tr>
<td>isDecimal</td>
<td>No</td>
<td>Returns false if the form element contains anything other than decimal characters.</td>
<td></td>
</tr>
<tr>
<td>isNumeric</td>
<td>No</td>
<td>Returns false if the form element contains anything other than numeric characters.</td>
<td></td>
</tr>
<tr>
<td>isInteger</td>
<td>No</td>
<td>Returns false if the form element contains anything other than an integer.</td>
<td></td>
</tr>
<tr>
<td>isNatural</td>
<td>No</td>
<td>Returns false if the form element contains anything other than a natural number: 0, 1, 2, 3, etc.</td>
<td></td>
</tr>
<tr>
<td>isNaturalNoZero</td>
<td>No</td>
<td>Returns false if the form element contains anything other than a natural number, but not zero: 1, 2, 3, etc.</td>
<td></td>
</tr>
<tr>
<td>email</td>
<td>No</td>
<td>Returns false if the form element does not contain a valid email address if true parameter provided it also do dns check.</td>
<td>email(true)</td>
</tr>
<tr>
<td>emails</td>
<td>Yes</td>
<td>Returns false if any value provided in a comma separated list is not a valid email. (If parameter true or 1 function also will do a dns query foreach emails)</td>
<td>emails(true)</td>
</tr>
<tr>
<td>validIp</td>
<td>No</td>
<td>Returns false if the supplied IP is not valid.</td>
<td></td>
</tr>
<tr>
<td>validBase64</td>
<td>No</td>
<td>Returns false if the supplied string contains anything other than valid Base64 characters.</td>
<td></td>
</tr>
<tr>
<td>noSpace</td>
<td>No</td>
<td>Returns false if the supplied string contains space characters.</td>
<td></td>
</tr>
<tr>
<td>callback_function(param)</td>
<td>Yes</td>
<td>You can define a custom callback function which is a class method located in your current model or just a function.</td>
<td>callback_functionname(param)</td>
</tr>
<tr>
<td>date</td>
<td>Yes</td>
<td>Returns false if the supplied date is not valid in current format. Enter your date format, default is mm-dd-yyyy.</td>
<td>date(yyyy-mm-dd)</td>
</tr>

</tbody>
</table>

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