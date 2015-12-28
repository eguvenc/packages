
### Guest Katmanı

> Oturum açmamış ( yetkinlendirilmemiş ) kullanıcılara ait bir katman oluşturur. Bu katman auth paketini çağırarak kullanıcının sisteme yetkisi olup olmadığını kontrol eder ve yetkisi olmayan kullanıcıları sistem dışına yönlendirir. Route yapısında Auth katmanı ile birlikte kullanılır.

#### Konfigürasyon

Eğer tanımlı değilse <kbd>config/$env/domain.php</kbd> dosyası içerisinden uygulamanıza ait domainleri ve bu domainlere ait regex ( düzenli ) ifadeleri belirleyin.
<kbd>app/classes/Service/User.php</kbd> dosyası auth servis parametrelerini ihtiyaçlarınıza göre konfigüre edin.

```php
class User implements ServiceInterface
{
    public function register(ContainerInterface $c)
    {
        $c['user'] = function () use ($c) {

            $parameters = [
                'cache.key' => 'Auth',
                'db.adapter'=> '\Obullo\Authentication\Adapter\Database',
                'db.model'  => '\Obullo\Authentication\Model\Pdo\User',       // User model, you can replace it with your own.
                'db.provider' => [
                    'name' => 'database',
                    'params' => [
                        'connection' => 'default'
                    ]
                ],
                'db.tablename' => 'users',
                'db.id' => 'id',
                'db.identifier' => 'username',
                'db.password' => 'password',
                'db.rememberToken' => 'remember_token',
                'db.select' => [
                    'date',
                ]
            ];
            $manager = new AuthManager($c);
            $manager->setParameters($parameters);
            return $manager;
        };
    }
}
/* Location: .app/classes/Service/User.php */
```

Guest katmanına bir örnek.


```php
namespace Http\Middlewares;

use Obullo\Application\Middleware;
use Obullo\Authentication\AuthConfig;

class Guest extends Middleware
{
    public function call()
    {
        if ($this->user->identity->guest()) {

            $this->flash->info('Your session has been expired.');
            $this->url->redirect(AuthConfig::get('url.login'));
        }
        $this->next->call();
    }
    
}
/* Location: .app/classes/Http/Middlewares/Guest.php */
```
