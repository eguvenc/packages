
## View Class

A view is simply a web page, or a page fragment, like a header, footer, sidebar, etc.

Views can flexibly be embedded within other views using <b>Nested Layers</b>. ( See Obullo Layer package docs. ). if you need this type of hierarchy you need create a <b>View Controller</b> in your <kbd>public/views</kbd> folder.

### Initializing the Class

------

```php
<?php
$this->c['view'];
$this->view->method();
```
Once loaded, the view object will be available using: <dfn>$this->view->method()</dfn>


### Views

------

To load a view file from your <kbd>public/directory/view</kbd> folder call following function:

```php
<?php
$this->view->load('filename');
```

**Tip**: This function normally include a view file. If you want to load file as string use <b>false</b> parameter.

```php
<?php
echo $this->view->load('filename', false);
```

### Templates

------

To load a template file as string from <kbd>resources/templates</kbd> folder you need use the following function:

```php
<?php
echo $this->view->template('filename');
```

To include it as file use <b>true</b> parameter.

```php
<?php
$this->view->template('filename', false);
```

### Dynamic Variables <a name="dynamic-variables"></a>

------

To create view variables shown as below:

```php
<?php
$this->view->load('hello_world', function() {
    $this->assign('name', 'Obullo');
    $this->assign('footer', $this->template('footer'));
});
```

Getting variable values

```php
<?php
echo $name // gives Obullo
```

### Static Variables <a name="static-variables"></a>

------

To create view static variables shown as below:

```php
<?php
$this->view->assign('@VARIABLE', 'value');
```

Getting static variable values

```php
<?php
echo '@VARIABLE' // gives my "value"
```

**Note:** All static variables must be UPPERCASE LETTERS. e.g. ( @VARIABLE, @FOO, @BAR )


### Predefined Static Variables

Some variables pre defined in view file and automatically replaced when you use them in your views files.


<table class="span9">

<thead>
<tr>
<th>Variable</th>
<th>Code Provision</th>
<th>Value</th>
</tr>
</thead>

<tbody>



<tr>
<td>@ASSETS</td>
<td><b>echo $this->config['url']['assets'];</b></td>
<td>Resource url default value "/assets"</td>
</tr>

</tbody>
</table>


### Layouts

Layout method help to design your application layout using php anonymous functions.

```php
<?php
'view' => array(
 'layouts' => 
    array(
        'default' => function () {
            $this->assign('header',  '@layer.views/header');   // creates a Layer request to "public/views/controller/header.php"
            $this->assign('sidebar', '@layer.views/sidebar');  // creates a Layer request to "public/views/controller/sidebar.php"
            $this->assign('footer',  '@layer.views/footer');  // no need controller
        },
        'welcome' => function () {
            $this->assign('footer', $this->template('footer'));
        },
    )
);

/* End of file view.php */
/* Location: ./config/view.php */
```
Then in your controller file you can call your layout using $this->layout() function.

```php
<?php

$this->view->load(
  'hello_world',
    function () {
      $this->assign('title', 'Hello World !');
      $this->assign('@VAR', 'My static variable !');  // it "@" means assign variable as static
      $this->layout();
    }
);
```

If you are not provide layout name in $this->layout(); function it will fetch default configured layout otherwise you need to provide a name.


**Note:** All static variables must be UPPERCASE LETTERS. e.g. ( @VARIABLE, @FOO, @BAR )


```php
<?php
$this->layout('welcome');  // run welcome layout
```

Example view file

```php
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $title ?></title>
    </head>
    <body>
        <section>
            <p><?php echo $header ?></p>
        </section>

        @VAR;

        <?php echo $content ?>

        <section>
            <?php echo $footer ?>
        </section>
    </body>
</html>
```

Header File

```php
<div id="header"> 
  <h1 class="logo">Home</h1>

  <div id="menu">
    <ul>
      <?php echo $li ?>
    </ul>
  </div>
  
</div>
```

## Calling header controller with Ajax

If you keep your user data in your header sometimes you need to refresh it with a ajax request.

You can do a ajax request to <kbd>public/views/contoller/header/</kbd>

An example ajax get method.

```php
<?php
$.ajax({
        url: "/views/header",
        type: "get",
    });
```


The <b>hello_world</b> view file should be located in your <kbd>public/$yourfolder/view</kbd> path.


### Array Data

------

Data is passed from the controller to the view by an <strong>array</strong> in the second parameter of the view loading function. Here is an example using an array:

```php
<?php

$this->view->load(
  'welcome', 
  function () {
    $data = array(
                 'numbers' => array('1','2','3','4','5'),
                 'message' => 'My Message'
            );
    $this->assign('mydata', $data);
  }
);
```

### Reaching Global Data

You pass global data using <b>use()</b> function.

```php
<?php

$data = array(
    'numbers' => array('1','2','3','4','5'),
    'message' => 'My Message'
    );
$anotherData = array(
                  'title' => 'Hello World !';
                );

$this->view->load(
   'hello_world', 
    function () use ($data, $anotherData) {
        $this->assign('mydata', $data);
        $this->assign('another_data', $anotherData);
    }
);
```

Let's try it with your controller file. Open it add this code:

```php
<?php

/**
 * $c hello_world
 * 
 * @var Controller
 */
$app = new Controller(
    function ($c) {
        $c->load('view');
    }
);

$app->func(
    'index',
    function () {

        $data = array(
            'numbers' => array('1','2','3','4','5'),
            'message' => 'My Message'
          );

        $this->view->load(
            'hello_world',
            function () use ($data) {
                $this->assign('title', 'Hello World !');
                $this->assign('data', $data);
            }
        );
    }
);   
```

Now open your blog.php view file and change the text to variables that correspond to the array keys in your data:

```php
<h1><?php echo $title ?></h1>

Numbers:  <?php print_r($data['numbers']); ?>
Message:  <?php echo $data['message']; ?>
```

Then load the page at the URL you've been using and you should see the variables replaced.

### Creating Loops

------

The data array you pass to your view files is not limited to simple variables. You can pass multi dimensional arrays, which can be looped to generate multiple rows. For example, if you pull data from your database it will typically be in the form of a multi-dimensional array.

Now open your local view file and create a loop:

```php
<h1><?php echo $title ?></h1>

Numbers:

<ul>
<?php foreach($numbers as $item):?>

    <li><?php echo $item;?></li>

<?php endforeach;?>
</ul>

Message:  <?php echo $message; ?>
```

### Loading views

------

There is a second optional parameter that lets you change the behavior of the function so that it loads file as include instead of return to string. This can be useful if you want to process the data in some way. If you set the parameter to false (boolean) it will load file as string.

### String Views

```php
<?php
echo $this->view->load('myfile', false);  
```
### Loading view as File

```php
<?php
$this->view->load('myfile');  // default behaviour
```

### Templates

------

```php
<?php
echo $this->view->template('header');
echo $this->view->template('footer');
```

Then in your controller file you can call your layouts using last parameter.

```php
<?php

$this->view->load(
  'hello_world',
  [
      'title' => 'Hello World'
  ]
  'welcome'
);
```

### Subfolders

------

You can create unlimited subfolders.

```php
echo $this->view->template('subfolder/sub/filename');
```

### View Controllers ( Nested Layers )

View controlers simply control the view parts like a header, footer, sidebar, banners, etc. Let's create a header controller to control navigations.

```php
<?php

/**
 * $c Header Navigation Controller ( Nested Layer )
 *
 * @var View Controller
 */
$app = new Controller(
    function ($c) {
        $c->load('url');
        $c->load('request');
    }
);

$app->func(
    'index',
    function () {
        $firstSegment   = $this->request->global->uri->segment(0);     // Get first segment from request global
        $currentSegment = (empty($firstSegment)) ? 'home' : $firstSegment;  // Set current segment as "home" if its empty

        $li = '';
        $navigation = array(
            'home'    => 'Home',
            'about'   => 'About', 
            'contact' => 'Contact',
            'membership/login'   => 'Login',
            'membership/signup'  => 'Signup',
        )
        foreach ($navigation as $key => $value){
            $active = ($currentSegment == $key) ? ' id="active" ' : '';
            $li.= '<li>'.$this->url->anchor($key, $value, " $active ").'</li>';
        }

        echo $this->view->->get(   // View Controller output must be string
            'header',
            [
              'li' => $li
            ]
        );
    }
);
```

Header View file

```php
<div id="header"> 
  <h1 class="logo"><?php echo $this->url->anchor('/home', 'Blog Demo') ?></h1>
  <div id="menu">
    <ul>
      <?php echo $li ?>
    </ul>
  </div>
</div>
```

Finally calling Header View Controller using <b>"Layers"</b> gives below the output.

```php
<?php
$this->c['layer'];
echo $this->layer->get('views/header');
```
Gives 

```php
<div id="header"> 
  <h1 class="logo"><a href="/">Home</a></h1>
  <div id="menu">
    <ul>
      <li id="active"><a href="/">Home</a></li>
      <li><a href="/about">About</a></li>
      <li><a href="/contact">Contact</a></li>
      <li><a href="/login">Login</a></li>
      <li><a href="/signup">Signup</a></li>
    </ul>
  </div>
</div>
```

## Troubleshooting with Nested Layers

------

### Why Uri use $this->request->global->uri->segment() instead of $this->uri->segment(0);

Normally <b>$this->request->segment(0);</b> method gives us to first segment.

However when we use View Controllers ( Nested Layers ), <b>Global Uri</b> and <b>Global Router</b> object variables will be <b>changed</b> when the <b>Layer</b> request is done. To protect these variables Layer class creates a request object and do backup <b>Uri</b> and <b>Router</b> class variables into controller <b>$this->request</b> variable;

So in <kbd>View Controllers</kbd> we access <b>Global Router</b> and <b>Global Uri</b> using below

```php
<?php
$this->request->global->uri->method();
$this->request->global->router->method();
```

Accessing <b>local</b> router and uri variables is same like below.

```php
<?php
$this->request->uri->method();
$this->request->router->method();
```

### Function Reference

------

#### $this->view->load('filename', $data = array(), $layout = null);

Include the file from local directory e.g. <kbd>public/welcome/view</kbd>

#### $this->view->get('filename', $data = array());

Returns to the view file as string.

#### $this->view->template('filename', $data = array(), $include = true);

Gets the file from templates directory e.g. <kbd>app/templates</kbd>

#### $this->view->assign('key', $val = '');

Assign a view variable ( Variable types can be String, Array or Object ), this method <kbd>automatically detects</kbd> the variable types.

#### $this->view->assign('@VARIABLE', 'value');

Uses the layout configuration that is defined in your <kbd>config/env/view.php</kbd>.