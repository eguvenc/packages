
## Database Sorgularını Özelleştirmek

O2 yetki doğrulama paketi kullanıcıya ait database fonksiyonlarını servis içerisinden <kbd>Obullo\Authentication\Model\User</kbd> sınıfından çağırmaktadır. Eğer mevcut database sorgularında değişlik yapmak istiyorsanız bu sınıfa genişlemek için önce auth konfigürasyon dosyasından db.model anahtarını <kbd>\Auth\Model\User</kbd> olarak değiştirmeniz gerekmektedir.

Daha sonra <kdb>app/classes/Auth/Model</kbd> klasörünü içerisine <b>User.php</b> dosyasını yaratarak aşağıdaki gibi User model sınıfı içerisinden <kbd>Obullo\Authentication\Model\User</kbd> sınıfına genişlemeniz gerekmektedir. Bunu yaparken <b>UserInterface</b> içerisindeki yazım kurallarına bir göz atın.

Aşağıda O2 yetki doğrulama paketi içerisindeki <kbd>\Obullo\Authentication\Model\UserInterface</kbd> sınıfı görülüyor.

```php
namespace Obullo\Authentication\Model;

use Auth\Identities\GenericUser;
use Obullo\Container\ContainerInterface;
use Obullo\Container\ServiceProviderInterface;

interface UserInterface
{
    public function __construct(ContainerInterface $c, ServiceProviderInterface $provider);
    public function execQuery(GenericUser $user);
    public function execRecallerQuery($token);
    public function updateRememberToken($token, GenericUser $user);
}
```

Önce User.php service dosyasından <b>db.model</b> anahtarını <kbd>\Auth\Model\User</kbd> olarak değiştirin.

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
Yukarıda gösterilen auth servis konfigürasyonundaki <b>db.model</b> anahtarını <kbd>\Auth\Model\User</kbd> olarak güncellediyseniz, aşağıda sizin için bir model örneği yaptık bu örneği değiştererek ihtiyaçlarınıza göre kullanabilirsiniz. Bunun için <kbd>Obullo\Authentication\Model\User</kbd> sınıfına bakın ve ezmek ( override ) istediğiniz method yada değişkenleri sınıfınız içerisine dahil edin.

```php
namespace Auth\Model;

use Auth\Identities\GenericUser;
use Auth\Identities\AuthorizedUser;
use Obullo\Authentication\Model\UserInterface;
use Obullo\Authentication\Model\User as AuthModel;

class User extends AuthModel implements UserInterface
{
    /**
     * Execute sql query
     *
     * @param object $user GenericUser object to get user's identifier
     * 
     * @return mixed boolean|array
     */
    public function execQuery(GenericUser $user)
    {
        return $this->db->prepare(sprintf(
            'SELECT * FROM %s WHERE %s = ?', $this->tablename, $this->columnIdentifier
        ))
            ->bindValue(1, $user->getIdentifier(), PDO::PARAM_STR)
            ->execute()
            ->rowArray();
    }

}

/* Location: .app/classes/Auth/Model/User.php */
```