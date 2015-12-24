
## View Katmanı

> Layer paketini kullanarak view şablonları oluşturmayı sağlar.

```php
class View implements MiddlewareInterface, ControllerAwareInterface
{
    public function setController(Controller $controller)
    {
        if (method_exists($controller, '__invoke')) {  // Assign layout variables
            $controller();
        }
    }

    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        $err = null;

        return $next($request, $response, $err);
    }
}
```

#### Kurulum

Eğer katman mevcut değilse aşağıdaki kaynaktan <b>View.php</b> dosyasını uygulamanızın <kbd>app/classes/Http/Middlewares/</kbd> klasörüne kopyalayın.

```php
http://github.com/obullo/http-middlewares/
```

#### Konfigürasyon

Katmanın çalışabilmesi için katmanlar içerisine eklenmesi gerekir.

```php
$c['middleware']->add(
    [
        'Router',
        'View',
    ]
);

/* Location: .app/middlewares.php */
```

#### Çalıştırma

<kbd>app/classes/View</kbd> klasörü altında ihtiyaçlarınıza göre aşağıdaki gibi bir şema oluşturun.

```php

namespace View;

Trait Base
{
    public function __invoke()
    {
        $this->view->assign(
            [
                'header' => $this->layer->get('views/header'),
                'footer' => $this->layer->get('views/footer')
            ]
        );
    }
}
```
ve oluşturduğunuz şemayı kontrolör dosyalarından çağırın.

```php

use Obullo\Http\Controller;
use View\Base;

class Welcome extends Controller
{
    use Base;

    public function index()
    {
        $this->view->load('welcome');
    }
}
```