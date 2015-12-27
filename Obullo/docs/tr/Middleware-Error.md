
## Error Katmanı

> Error katmanı uygulamanızda gerçekleşecek istisnai hataları yada bir katman içerisinde gerçekleşebilecek hataları kontrol eder. Temel olarak MiddlewareInterface ile aynıdır. İstenirse son parametre kullanılarak $err = "" değişkeni ile $next() fonksiyonuna özel hatalar gönderilebilir. Eğer hata gönderilmez ise bir sonraki yanıt $next() fonksiyonunu çalıştırır hata gönderilir ise katman hata çıktısı ile birlikte response nesnesine döner.

bknz. <a href="https://github.com/zendframework/zend-stratigility" target="_blank">Zend/Stratigility</a> 


```php
$err = 'example error !';

return $next($request, $response, $err);
```

#### Kurulum

Aşağıdaki kaynaktan <b>Error.php</b> dosyasını uygulamanızın <kbd>app/classes/Http/Middlewares/</kbd> klasörüne kopyalayın.

```php
http://github.com/obullo/http-middlewares/
```

#### Katman Hataları

Bir katman içerisinde hatalar oluşturmak için aşağıdaki gibi <kbd>$err</kbd> değişkenini kullanabilirsiniz.

```php
public function __invoke(Request $request, Response $response, callable $next = null)
{
    $err = 'test error !';

    return $next($request, $response, $err);
}
```

Yukarıdaki örneği uyguladığınızda Error katmanı çalıştırılır ve <kbd>test error !</kbd> hatası ile karşılaşırsınız.

#### İstisnai Hatalar

Uygulama içerisinde aşağıdaki gibi bir istisnai hata oluştuğunda hata Error katmanına gönderilir.

```php
use Obullo\Http\Controller;

class Welcome extends Controller
{
    public function index()
    {
        throw new \Exception("Im a test exception error !");

        $this->view->load('welcome');
    }
}
```
#### Hata Yönetimi

Error katmanından sadece uygulama içerisindeki istisnai hatalar ve <kbd>$err</kbd> değişkeni ile gönderilen hatalar kontrol edilebilir. Uygulama evrensel hataları ise <kbd>app/errors.php</kbd> dosyasından yönetilir. Error katmanı içerisinden <kbd>$this->c['app']->exceptionError();</kbd> metodu kullanılarak istisnai hatalar <kbd>app/errors.php</kbd> dosyasına yönlendirilmiş olur.

```php
class Error implements ErrorMiddlewareInterface, ContainerAwareInterface
{
    public function __invoke($error, Request $request, Response $response, callable $out = null)
    {
        if (is_string($error)) {

            echo $error;
        }
        if (is_object($error)) {
            
            if ($this->c['app']->env() == 'local') {

                $exception = new \Obullo\Error\Exception;
                echo $exception->make($error);

                $this->c['app']->exceptionError($error);  // Forward exceptions to app/errors.php

            } else {
            
                echo $error->getMessage();

                $this->c['app']->exceptionError($error);
            }
        }
        return $response;
    }
}
```

İstisnai hataları ekrana dökmek için  <kbd>$exception->make($error)</kbd> metodu kullanılabilir.