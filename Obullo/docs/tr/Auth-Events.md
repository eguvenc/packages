
## Olaylar

Oturum açma aşamasında login öncesi ve login sonrası <kbd>$event->fire('login.before', array($credentials))</kbd> ve <kbd>$event->fire('login.after', array($authResult))</kbd> adlı iki olay event sınıfı ile <kbd>Obullo/Authentication/User/Login</kbd> sınıfı attempt metodu içerisinde ilan edilmiştir. Olaylardan <kbd>login.before</kbd> metoduna kullanıcı giriş bilgileri parametre olarak gönderilirken <kbd>login.after</kbd> metoduna ise <kbd>Obullo/Authentication/AuthResult</kbd> sınıfı çıktıları parametre olarak gönderilir.

<a name="login-events"></a>

### Oturum Açma Olaylarını Dinlemek

Yetki doğrulama paketine ait olaylar <kbd>app/classes/Event/Login/</kbd> klasörü altında dinlenir. Aşağıdaki örnekte gösterilen Attempt sınıfı subscribe metodu <kbd>login.after</kbd> olayını dinleyerek oturum denemeleri öncesini ve bu oturumdan sonra oluşan sonuçları dinleyebilmenizi sağlar. 

Şimdi dinleyici sınıfına bir göz atalım.

<a name="login-listener"></a>

#### Dinleyici


```php
namespace Event\Login;

use Obullo\Authentication\AuthResult;
use Obullo\Event\EventListenerInterface;
use Obullo\Container\ContainerInterface;

class Attempt implements EventListenerInterface
{
    protected $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function before($credentials = array())
    {
        // ..
    }

    public function after(AuthResult $authResult)
    {
        if ( ! $authResult->isValid()) {

            // Store attemtps
            // ...
        
            // $row = $authResult->getResultRow();  // Get query results

        }
        return $authResult;
    }

    public function subscribe($event)
    {
        $event->listen('login.before', 'Event\Login\Attempt@before');
        $event->listen('login.after', 'Event\Login\Attempt@after');
    }
}

/* Location: .Event/Login/Attempt.php */
```

Yukarıdaki örnekte <b>after()</b> metodunu kullanarak oturum açma denemesinin başarılı olup olmaması durumuna göre oturum açma işlevine eklemeler yapabilir yetki doğrulama sonuçlarınına göre uygulamanızın davranışlarını özelleştirebilirsiniz.

<a name="sucscribe-to-login-event"></a>

#### Dinleyiciye Abone Olmak

Oturum açma olaylarının dinlenebilmesi için Login kontrolör dosyası içerisinde login metodu (index) üzerinde anotasyonlar yardımı ile <b>subscribe()</b> metodu içerisinden <kbd>app/classes/Event/Login/Attempt</kbd> sınıfına abone olunması gerekir.

```php
namespace Membership;

class Login extends \Controller
{
    /**
     * @event->when("post")->subscribe('Event\Login\Attempt');
     */
    public function index()
}
```

Anotasyonda event satırından sonra girilen <b>when</b> komutu dinlyeciyi sadece http post isteklerinde çalıştırır. Http Get istekleri için önceden ilan edilmiş bir olay mevcut olmadığından bu fonksiyon üzerinde sadece http post isteği için dinleyiciye abone olmak gerekir.