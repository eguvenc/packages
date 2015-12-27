
## Router Katmanı

> Router katmanı route işlevleri gerçekleşmeden önceki aşamayı yönetir ve varsayılan olarak katmanlar içerisinde tanımlıdır.

```php
class Router implements MiddlewareInterface, ContainerAwareInterface
{
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        if ($this->c['router']->getDefaultPage() == '') {

            $error = 'Unable to determine what should be displayed.';
            $error.= 'A default route has not been specified in the router middleware.';

            $body = $this->c['template']->make('error', ['error' => $error]);

            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->withBody($body);
        }
        
        $err = null;

        return $next($request, $response, $err);
    }
}
```

#### Konfigürasyon

Router katmanı varsayılan olarak tanımlıdır.

```php
$c['middleware']->add(
    [
        'Router',
        // 'View',
    ]
);

/* Location: .app/middlewares.php */
```

#### Kurulum

Eğer katman mevcut değilse aşağıdaki kaynaktan <b>Router.php</b> dosyasını uygulamanızın <kbd>app/classes/Http/Middlewares/</kbd> klasörüne kopyalayın.

```php
http://github.com/obullo/http-middlewares/
```