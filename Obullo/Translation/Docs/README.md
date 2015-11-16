
## Translator Class

The Translator Class provides functions to retrieve language files and lines of text for purposes of internationalization.

In your app folder you'll find one called translator containing sets of language files. You can create your own language files as needed in order to display error and other messages in other languages.

**Note:** Each language file must be stored in its own folder. For example, the English files are located at: <kbd>app/translations/en</kbd>

### Initializing the Translator Class ( Array Access )

------

```php
$this->c['translator'];

$this->translator['item'];
$this->translator->method();
```

Controller sınıfının mevcut olmadığı bir yükleme seviyesinde iseniz translator sınfına konteyner içerisinden aşağıdaki gibi erişebilirsiniz.

```php
$this->c['translator']['item'];
$this->c['translator']->method();
```

### Create Your Translation File

------

It's a good practice to use ":" as key for all messages in a given file to avoid collisions with similarly named items in other files. 

We define keys an array. An example 

```php
<?php

return array(

    /**
     * Label
     */
    'GAMES:MANAGEMENT:LABEL:ID'              => 'Id',
    'GAMES:MANAGEMENT:LABEL:NAME'            => 'Game Name',
    'GAMES:MANAGEMENT:LABEL:CATEGORIES'      => 'Categories',
    'GAMES:MANAGEMENT:LABEL:PROVIDERS'       => 'Providers',
    'GAMES:MANAGEMENT:LABEL:STATUS'          => 'Status',
    'GAMES:MANAGEMENT:LABEL:ORDER'           => 'Order',
    'GAMES:MANAGEMENT:LABEL:GAME_URL'        => 'Game URL',
    'GAMES:MANAGEMENT:LABEL:GAME_IP'         => 'Game IP',
    'GAMES:MANAGEMENT:LABEL:GAME_RESOLUTION' => 'Game Resolution',
    'GAMES:MANAGEMENT:LABEL:IMAGE'           => 'Image',
    'GAMES:MANAGEMENT:LABEL:DESCRIPTION'     => 'Description',
    'GAMES:MANAGEMENT:LABEL:ACTIVE'          => 'Active',
    'GAMES:MANAGEMENT:LABEL:PASSIVE'         => 'Passive',
    'GAMES:MANAGEMENT:LABEL:FILTER_ALL'      => 'All',
    'GAMES:MANAGEMENT:LABEL:EDIT'            => 'Edit',

    /**
     * Link
     */
    'GAMES:MANAGEMENT:LINK:EDIT'    => 'Edit Game',
    'GAMES:MANAGEMENT:LINK:ADD_NEW' => 'Add New Games',

    /**
     * Error
     */
    'GAMES:MANAGEMENT:ERROR:NOTVALIDRESOLUTION' => 'Games Resolution is not valid. Ex: 800x600',

    /**
     * Button
     */
    'GAMES:MANAGEMENT:BUTTON:FILTER' => 'Filter',
    'GAMES:MANAGEMENT:BUTTON:SUBMIT' => 'Submit',

    /**
     * Notice
     */
    'GAMES:MANAGEMENT:NOTICE:CREATE' => 'Game successfully added.',
    'GAMES:MANAGEMENT:NOTICE:UPDATE' => 'Game successfully updated.',
);
```

### Loading a Translate File

------

If you want load language files from your <b>app</b> folder create your language files to there ..

```php
-  app
    + config
    - translations
        - en
            games.php
```

This function loads a language file from your <kbd>app/translator</kbd> folder.


```php
$this->translator->load('filename');  // load translator file
```

Where <samp>filename</samp> is the name of the file you wish to load (without the file extension), and language is the language set containing it (ie, en_US).

```php
$c->load('translator');

$this->translator->load('welcome');
$this->translator['SITE:TITLE:WELCOME_TO_OUR_SITE'];
```

### Loading the Framework Files

Some of the packages use framework language file which is located in your <kbd>app/translations</kbd> folder. You can change the default language. ( look at <kbd>config/debug/config.php</kbd> ) 

Core packages will load framework language files which are located in <kbd>app/translations/$language</kbd> folder.

------

```php
-  app
    + config
    - translations
        - en
             date.php
             validator.php
            ...
        - es
             date.php
             validator.php
            ...
```

This function loads the <b>date</b> language file from your <kbd>app/translator/es_ES</kbd> folder.

```php
$this->translator->load('date'); 
```

### Fetching a Line of Text

------

Once your desired language file is loaded you can access any line of text using this function:

```php
$this->translator['LANGUAGE:KEY'];
```
$this->translator class array access function returns the translated line if language line exists in your file, otherwise it returns to default text that you are provide.

### Checking a Translate Key of Text

Tramslate class allow to you array access and it returns to default key if translate key not exists.

```php
echo $this->translator['LANGUAGE:KEY']; // gives translated text of 'language_key'
```

Checking none exist key.

```php
<?php
var_dump($this->translator->exists('asdasdas'));  // gives "true" or "false"
```

Printing none exist key echo same string.

```php
<?php
echo $this->translator['asdasdas'];     // gives a notice to you 'asdasdas' if translate notice disabled from your config file.
```

If translation notice enabled from your config printing none exist key echo same string with translate notice.

```php
<?php
echo $this->translator['asdasdas'];     // gives a notice to you 'translate:asdasdas'
```

### Using $this->translator->get($key, $arguments , , , ... );

Translator class has a <b>sprintf</b> which has provide the same functionality of php sprintf.

```php
<?php
echo $this->translator->get('There are %d monkeys in the %s.', 5, 'tree');

// Gives There are *5* monkeys in the *tree*.
```

### Translation Middleware

Translator middleware setLocale method sets locale cookie value like below.

```php
http://example.com/en/home
```

But it should be loaded in middleware.php

```php
$c['app']->middleware(new Http\Middlewares\Translation);
```

Translator config should be like this

```php
<?php
// Uri Settings
'uri' => array(
    'segment'       => true, // Uri segment number e.g. http://example.com/en/home
    'segmentNumber' => 0       
),
// Cookies
'cookie' => array(
    'name'   =>'locale',
    'expire' => (365 * 24 * 60 * 60),  // 365 day; //  @see  Cookie expire time.   http://us.php.net/strtotime
    'secure' => false,    // Cookies will only be set if a secure HTTPS connection exists.
),
```

if URI Segment and Http GET not provided  It sets locale code reading http COOKIE['name'].

It sets cookie using <b>locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE'])</b> function if language code not provided with any of above the methods : 

**Note:** locale_accept_from_http() function requires php <b>intl</b> extension.

### Updating your routes

This route rewrite your url to http://example.com/en/welcomde/index First segment (0) gives language code

```php
<?php
$c['router']->route('get', '(en|es|de)/(.+)', '$2');        
```

Below the route sets your default controller for http://example.com/en/.

```php
<?php
$c['router']->route('get', '(en|es|de)', 'home/index');
```

### Fallback Translations

Mevcut yüklü dil dosyanızda bir çeviri metni bulunamazsa fallback dil dosyanız devreye girer ve fallback dosyası yüklenerek mevcut olmayan çeviri bu dosya içerisinden çağrılır.
Bu özelliği kullanabilmek translator.php config dosyasınızdaki fallback değerinin <b>true</b> olması gereklidir.

```php
'fallback' => array(
    'enabled' => false,
    'locale' => 'es',
),
```


### Function Reference

------

#### $this->translator->load(string $filename);

Loads translate file from app/translations folder.

#### $this->translator['key'];

Print translation value.

#### $this->translator->exists(string $line);

Checks a translation key of text.

#### $this->translator->get(string $line, args ... );

Offers same functionality of php sprintf.

#### $this->translator->setDefault(string $locale);


#### $this->translator->getDefault(string $locale);

#### $this->translator->setLocale(string $locale);

Set default locale value. ( en, es, de .. )

#### $this->translator->getLocale();

Get current locale value. ( en, es, de .. )

#### $this->translator->setFallback(string $locale);

Set a fallback value if locale value not found you can use fallback value.

#### $this->translator->getFallback(string $locale);

Get a fallback value if locale value not found you can use fallback value.