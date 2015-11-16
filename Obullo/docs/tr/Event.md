
## Olay Sınıfı ( Events )

------

Olay sınıfı uygulamada olaylara abone olmak ve onları dinlemek için <a href="http://www.sitepoint.com/understanding-the-observer-pattern/" target="_blank">observer</a> tasarım kalıbı ile oluşturulmuş basit bir sınıftır.

<ul>
    <li><a href="#flow">İşleyiş</a></li>
    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li><a href="#loading-service">Sınıfı Yüklemek</a></li>
            <li><a href="#firing-an-event">Bir Olayı Başlatmak</a></li>
            <li><a href="#sucscribing-an-event">Bir Olaya Abone Olup Dinlemek</a></li>
            <li><a href="#subscribe-priority">Olaylara Önemlilik Derecesi İle Abone Olmak</a></li>
            <li><a href="#stopping-an-event">Bir Olaya Ait Dinlemeleri Durdurmak</a></li>
            <li><a href="#class-listeners">Sınıfları Dinleyici Olarak Kullanmak</a></li>
            <li><a href="#method-operator">Hangi Metdodun Dinleneceğini Belirlemek</a></li>
        </ul>
    </li>


    <li>
        <a href="#event-subscribers">Olay Aboneleri</a>
        <ul>
            <li><a href="#defining-subscriber">Bir Olay Abonesi Tanımlamak</a></li>
            <li><a href="#defining-global-subscriber">Olaylara Evrensel Olarak Abone Olmak</a></li>
            <li><a href="#defining-controller-subscriber">Olaylara Kontrolör Sınıfı İçerisinden Abone Olmak</a></li>
            <li><a href="#defining-route-subscriber">Olaylara Route Üzerinden Abone Olmak</a></li>
        </ul>
    </li>

    <li><a href="#method-reference">Fonksiyon Referansı</a></li>

</ul>

<a name="flow"></a>

### İşleyiş

-------

Uygulamada olaylar belirli bir zaman dilimi içerisinde anlık gerçekleşirler. Event yapısı uygulama içerisindeki olayların gerçekleşeği an için tetikleyici fonksiyonlar ve bu fonksiyonlara bağlı çalışacak programları çalıştırmamızı sağlar. Daha iyi anlaşılması için <b>bir örnek</b> vermek gerekirse mesela uygulamamız içeriside bir login modülü olsun.

Event <kbd>fire</kbd> methodu ile login nesnemiz içerisinde bir olay fırlatılır ve daha sonra listen komutu ile de bu olay gerçekleştiğinde yapılacak işler tanımlanır. Kaydedilen olay anını <kbd>listen</kbd> yada <kbd>subscribe</kbd> komutu ile fonksiyonlara atarız. Subscribe komutunu listen komutunundan ayıran en önemli özellik bu metodun dinleyicileri bir sınıf içerisinde gruplayarak kodlarınızı daha düzenli hale getirmesidir. Bir subscribe metodu listen metotları içerir.

Son olarak olay anını ne kadar çok dinleyicimiz  ( <b>listeners / subscribers</b> ) dinlerse dinlesin olay gerçekleştiğinde dinleyicilere ( aboneler ) tanımlanan fonksiyonlar önemlilik derecelerine göre çözümlenip çalıştırılırlar. Dinleyiciler isimsiz ( anonymous ) birer fonksiyon olabilecekleri gibi <kbd>subscribe</kbd>metodu ile abone edilmiş birer <b>sınıf</b> ta olabilirler.


<a name="running"></a>

### Çalıştırma

Event sınıfı <kbd>app/components.php</kbd> dosyası içerisinde tanımlıdır ve konteyner içerisinden çağrılarak çalıştırılır.

<a name="loading-service"></a>

#### Sınıfı Yüklemek

```php
$this->c['event']->method();
```

<a name="firing-an-event"></a>

#### Bir Olayı Başlatmak

```php
$this->event->fire('login.success', array($this->c, $userId));
```

Fire metodu ile bir olayı ilan edebilirsiniz, ikinci parametreden olaya ait parametreleri gönderebilirsiniz olay anı dinlenirken bu parametreler kullanılarak işlemler gerçekleştirebilir.

<a name="sucscribing-an-event"></a>

#### Bir Olaya Abone Olup Dinlemek

```php
$this->event->listen(
    'login.success',
    function ($c, $id) {
        $date = time();
        $this->c['db']->exec("UPDATE users SET date = $date WHERE id = $id")
    }
);
```

<a name="subscribe-priority"></a>

#### Olaylara Önemlilik Derecesi İle Abone Olmak

Olaylara abone olurken her olay için bir önemlilik derecesi belirleyebilirsiniz. Dinleyiciler en yüksek önemlilik değerine sahip olan olayı ilk çalıştırırlar eğer aynı önemlilik derecesine sahip birden fazla olay varsa abone edilme sıralarına göre çalıştırılırlar.

```php
$this->event->listen('login.success', 'Event\ExampleHandler', 10);
$this->event->listen('login.success', 'Event\OtherHandler', 5);
```

<a name="stopping-an-event"></a>

#### Bir Olaya Ait Dinlemeleri Durdurmak

Bazen bir olayın diğer dinleyicilere yayılmasını önlemek isteyebiliriz böyle bir durumda dinleyici içerisinden false değerine dönmek yeterlidir.

```php
$this->event->listen(
    'login.succes', 
    function ($event) {
        return false;
    }
);
```
<a name="class-listeners"></a>

#### Sınıfları Dinleyici Olarak Kullanmak

Bazı durumlarda dinleyiciler için isimsiz bir fonksiyon kullanmak yerine bir sınıf kullanmayı tercih edebilirsiniz. Bir sınıf ve ona ait metodu dinleyici olarak atamak için aşağıdaki şablon kullanılır.

```php
Namespace\Of\Class@method
```

Örnek

```php
$this->event->listen('event.name', 'Event\Login@method');
```

<a name="method-operator"></a>

#### Hangi Metodun Dinleneceğini Belirlemek

Eğer olay için "@" sembolü ile bir method tanımlanmamışsa varsayılan olarak <b>handle</b> metodu çalıştırılır.

```php
namespace Event;

class Login {

    public function handle($data)
    {
        //
    }

}
```

Eğer handle metodu yerine başka bir metot ismi kullanmayı tercih ederseniz metot ismini aşağıdaki gibi yazmanız gerekir.

```php
$this->event->listen('event.name', 'Event\Login@onLogin');
```

<a name="event-subscribers"></a>

### Olay Aboneleri

Subscribe komutunu listen komutunundan ayıran en önemli özellik bu metodun dinleyicileri bir sınıf içerisinde gruplayarak kodlarınızı düzenli hale getirmesidir. Listen komutları subscribe komutu içerisinde kullanılırlar.

<a name="defining-subscriber"></a>

#### Bir Olay Abonesi Tanımlamak

Olay aboneleri kendi içerisinde birden fazla olaya abone olabilen sınıflardır. Aboneler subscribe metodu ile tanımlanırlar subscribe metoduna ait ilk parametreye Event sınıfı nesnesi enjekte edilir.

Aşağıdaki Event/Login klasörü altında login işlemleri için tanımlanmış bir olay abonesi örneği görüyorsunuz.

```php
namespace Event\Login;

use Obullo\Authentication\AuthResult;
use Obullo\Container\ContainerInterface;
use Obullo\Event\EventListenerInterface;

class Attempt implements EventListenerInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Constructor
     *
     * @param object $c container
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    /**
     * Before login attempt
     * 
     * @param array $credentials user login credentials
     * 
     * @return void
     */
    public function before($credentials = array())
    {
        // ..
    }

    /**
     * After login attempts
     *
     * @param object $authResult AuthResult object
     * 
     * @return void
     */
    public function after(AuthResult $authResult)
    {
        if ( ! $authResult->isValid()) {

            // Store attemtps
            // ...
        
            // $row = $authResult->getResultRow();  // Get query results
        }
        return $authResult;
    }

    /**
     * Register the listeners for the subscriber.
     * 
     * @param object $event event class
     * 
     * @return void
     */
    public function subscribe($event)
    {
        $event->listen('login.before', 'Event\Login\Attempt@before');
        $event->listen('login.after', 'Event\Login\Attempt@after');
    }

}

/* Location: .Event/User.php */
```

Yukarıdaki örnekte <b>before</b> metodu tarafından dinlenilen olay <kbd>Authentication/User/Login</kbd> kütüphanesi <b>attempt</b> metodu içerisindeki aşağıdaki gibi ateşlenmiştir.

```php
$this->c['event']->fire('login.before', array($credentials)); 
```

Yine aynı kütüphanede <b>after</b> metodu tarafından dinlenilen olay ise aşağıdaki gibi ateşlenmiştir.

```php
return $this->c['event']->fire('login.after', array($authResult));
```

<a name="defining-global-subscriber"></a>

#### Olaylara Evrensel Olarak Abone Olmak

Olaylar sınıflar, servisler içerisinde <b>listen</b> yada <b>subsrcibe</b> komutları ile evrensel olarak da dinlenebilirler. Uygulamaya ait evrensel olaylar <b>app/events.php</b> içerisinde tanımlanırlar.

```php
/*
|--------------------------------------------------------------------------
| Global Events
|--------------------------------------------------------------------------
| This file specifies the your application global events.
*/

$c['event']->subscribe('Event\YourClassname');

/* Location: .app/events.php */
```

<a name="defining-controller-subscriber"></a>

#### Olaylara Kontrolör Sınıfı İçerisinden Abone Olmak

Aşağıdaki örnekte bir login kontrolörümüz var ve anotasyonlar yardımı ile <kbd>Event\Login\Attempt</kbd> sınıfına abone olarak uygulamaya yapılan login denemelerini dinliyoruz.

```php
namespace Membership;

class Login extends \Controller
{
    public function __construct()
    {
        $this->user = $this->c->get('user');
    }

    /**
     * Login Post
     *
     * @event->subscribe('Event\Login\Attempt');
     *  
     * @return void
     */
    public function post()
    {
        $result = $this->user->login->attempt(
            array(
                $this->user['db.identifier'] => $this->validator->getValue('email'), 
                $this->user['db.password']   => $this->validator->getValue('password')
            ),
            $this->request->post('rememberMe')
        );
        if ($result->isValid()) {
            $this->flash->success('You have authenticated successfully.')
            ->url->redirect('membership/restricted');
        } else {
            $this->form->setResults($result->getArray());
        }
    }
}
```

Yukarıdaki örnekte post metodu üzerindeki <kbd>@event->subscribe('Event\Login\Attempt')</kbd> notasyonu ile login formuna post isteği gelmesi durumunda <kbd>Event\Login\Attempt</kbd> sınıfına abone olunarak bu sınıf içerisindeki subscribe metoduna ait dinleyiciler dinleniyor.

<a name="defining-route-subscriber"></a>

#### Olaylara Route Üzerinden Abone Olmak

Eğer bir olay birden fazla kontrolör yada dizin içindeki bir kontrolör grubu içerisinde gerçekleşiyorsa bu durumda olaylara route yapısı üzerinden abone olabilirsiniz.

```php
$c['router']->get(
    'membership/login.*', null, 
    function () use ($c) {
        $c['event']->subscribe('Event\ExampleClass');
    }
);

/* Location: .routes.php */
```

Yukarıdaki örnekte <kbd>membership/login</kbd> dizini altındaki her bir kontrolör sınıfı çalıştığında <kbd>app/classes/Event/ExampleClass</kbd> nesnesi içerisindeki <kbd>subscribe</kbd> metoduna abone olur.

<a name="method-reference"></a>

#### Event Sınıfı Referansı

------

##### $this->event->fire(string $event, array $payload = array(), bool $halt = false);

Bir olay yaratır. İlk parametreye olay ismi, ikinci parametreye varsa olay parametreleri girilmelidir. Opsiyonel olan üçüncü parametreye true değeri gönderilirse olaydan dönen yanıt null değerine döner.

##### $this->event->listen(string|array $events, mixed $listener, int $priority = 0);

Daha önceden oluşturulmuş olayları dinler. İlk parametreye olay ismi yada isimleri, ikinci parametreye dinleyici class ismi yada isimsiz fonksiyon girilmelidir. Opsiyonel olan üçüncü parametreye ise önemlilik derecesi girilir.

##### $this->event->subscribe(string Event\$subscriber);

İçerisinde subscribe metodu bulanan bir sınıfa abone olur. İlk parametreye olaya abone edilecek sınıfın ismi veririlir.

##### $this->event->hasListeners(string $event);

Girilen olay ismine göre olayın dinlyecileri varsa <b>true</b> değerin aksi durumda <b>false</b> değerine döner.

##### $this->event->getListeners(string $event);

Girilen olay ismine göre henüz dinlenilmemiş olayın dinleyicilerine bir dizi içerisinde döner.

##### $this->event->forget(string $event);

Girilen olaya ait tüm dinleyicileri siler.