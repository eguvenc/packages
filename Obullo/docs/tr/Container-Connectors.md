

#### AMQP ( PECL )

Php <a href="http://php.net/manual/pl/book.amqp.php" target="_blank">AMQP</a> genişlemesi için yazılmış servis sağlayıcısıdır. Uygulamanızdaki <kbd>queue.php</kbd> konfigürasyonunu kullanarak AMQP bağlantılarını yönetir.


Varolan bir bağlantıyı almak için aşağıdaki yöntem izlenir.

```php
$AMQPConnection = $container->get('amqp')->shared(['connection' => 'default']);
```

Birkez yüklendikten sonra amqp bağlantısı aşağıdaki gibi açılır.

```php
$channel = new AMQPChannel($AMQPConnection);
```

Konfigürasyonda olmayan yeni bir bağlantı yaratmak için factory metodunu kullanılır.

```php
$AMQPConnection = $container->get('amqp')->factory( 
    [
        'host'  => 'localhost',
        'port'  => 5672,
        'username'  => 'guest',
        'password'  => 'guest',
        'vhost' => '/',
    ]
);
```

#### AMQPLib (videlalvaro/php-amqplib)

Composer <a href="https://github.com/videlalvaro/php-amqplib">Php AMQPLib</a> kütüphanesi için yazılmış servis sağlayıcısıdır. 

```php
// $container->share('amqp', 'Obullo\Container\ServiceProvider\Connector\Amqp')
//     ->withArgument($container)
//     ->withArgument($config->getParams());
```

Eğer amqplib kullanıyorsanız <kbd>app/classes/ServiceProvider/Connector/Amqp.php</kbd> dosyasını açın ve yukarıdaki gibi mevcut amqplib bağlantısını derleme içine alarak mevcut amqp sağlayıcısını derleme dışına çıkarın.

```php
// AmqpLib Replacement
//

$container->share('amqp', 'Obullo\Container\ServiceProvider\Connector\AmqpLib')
    ->withArgument($container)
    ->withArgument($config->getParams());
```

Varolan bir bağlantıyı almak için aşağıdaki yöntem izlenir.

```php
$conn = $container->get('amqp')->get(['connection' => 'default']);
```

Aşağıda mesaj gönderimine dair bir örnek gösteriliyor.

```php
$ch = $conn->channel();
$ch->queue_declare('basic_get_queue', false, true, false, false);
$ch->exchange_declare('basic_get_test', 'direct', false, true, false);
$ch->queue_bind('basic_get_queue', 'basic_get_test');

$toSend = new AMQPMessage(
    'test message',
    array(
        'content_type' => 'text/plain',
        'delivery_mode' => 2
    )
);
$ch->basic_publish($toSend, 'basic_get_test');

$msg = $ch->basic_get($queue);
$ch->basic_ack($msg->delivery_info['delivery_tag']);
var_dump($msg->body);

$ch->close();
```

Daha fazla örnek için <a href="https://github.com/videlalvaro/php-amqplib" target="_blank">https://github.com/videlalvaro/php-amqplib</a> <kbd>demo</kbd> sekmesini ziyaret edin.


#### CacheFactory

<kbd>CacheFactory</kbd> paketindeki sürücüleri tek bir arayüz üzerinden kontrol etmek için yazılmış servis sağlayıcısıdır. Uygulamanızdaki seçilen sürücüye göre <kbd>providers/sürücü.php</kbd> konfigürasyonunu kullanarak cache bağlantılarını yönetir.

Varolan bir önbellek bağlantısını almak için aşağıdaki yöntem izlenir.

```php
$cache = $container->get('cacheFactory')->shared( 
    [
        'driver' => 'redis',
        'connection' => 'default'
    ]
);
```

Konfigürasyonda olmayan yeni bir bağlantı yaratmak için factory metodu kullanılır.

```php
$cache = $container->get('cacheFactory')->factory, 
    [
        'driver' => 'redis',
        'options' => array(
            'host' => '127.0.0.1',
            'port' => 6379,
            'options' => array(
                'auth' => '123456',    // Connection password
                'timeout' => 30,
                'persistent' => 0,
                'reconnection.attemps' => 100,     // For persistent connections
                'serializer' => 'none',
                'database' => null,
                'prefix' => null,
            )
       )
    ]
);
```

Birkez yüklendikten sonra sınıf metodlarına erişilir.

```php
$cache->method();
```

Memcached sürücüsü için bir örnek.

```php
$cache = $container->get('cacheFactory')->shared( 
    [
        'driver' => 'memcached',
        'connection' => 'default'
    ]
);
```

#### Database

<kbd>Database</kbd> paketindeki bağlantı adaptörlerini tek bir arayüz üzerinden kontrol edebilmek ve bağlantı yönetimini sağlamak için yazılmış servis sağlayıcısıdır. Uygulamanızdaki database.php konfigürasyonunu kullanarak seçilen database sürücüsüne göre ilişkili database (RBDMS) nesnelerini yönetir.

```php
'connections' => array(

    'default' => array(
        'dsn'      => 'mysql:host=localhost;port=;dbname=test',
        'username' => 'root',
        'password' => '123456',
        'options'  => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]
    ),
    'failed' => array(
        'dsn'      => 'mysql:host=localhost;port=;dbname=failed',
        'username' => 'root',
        'password' => '123456',
        'options'  => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]
    ),
)
```

Tanımlı veritabanı bağlantılarını almak için aşağıdaki yöntem izlenir.

```php
$db = $container->get('database')->shared(['connection' => 'default']);
$db->method();
```

Konfigürasyonda olmayan yeni bir bağlantı yaratmak için factory metodu kullanılır.

```php
$db = $container->get('database')->factory(
    [
        'dsn'      => 'mysql:host=localhost;port=;dbname=test',
        'username' => 'root',
        'password' => '123456',
        'options' => [
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]
    ]
);
```

Bir başka veritabanı kullanmak istiyorsanız aşağıda başka bir veritabanına ait yapılandırılma gösteriliyor.

PostgreSQL

```php
pgsql:host=127.0.0.1;port=5432;dbname=anydb
```

#### DoctrineDBAL

<a href="http://www.doctrine-project.org/projects/dbal.html" target="_blank">DoctrineDBAL</a> veritabanı şema yönetimi gibi birçok özelliği destekleyen güçlü bir <a href="http://php.net/manual/en/book.pdo.php" target="_blank">PDO</a> arayüzüdür. En popüler veritabanı türleri için ortak bir arayüz sağlar. Eğer DoctrineDBAL servis sağlayıcısını kullanmak istiyorsanız,

<kbd>app/classes/ServiceProvider/Database.php</kbd> dosyası içerisindeki veritabanı sağlayıcınıza ait satırı derleme içine alarak DoctrineDBAL sağlayıcısını derleme dışına çıkarın.


```php
// $container->share('database', 'Obullo\Container\ServiceProvider\Connector\Database')
//     ->withArgument($container)
//     ->withArgument($config->getParams());
```

```php
// DoctrineDBAL Replacement
// 

$container->share('database', 'Obullo\Container\ServiceProvider\Connector\DoctrineDBAL')
    ->withArgument($container)
    ->withArgument($config->getParams());
```

Böylece <kbd>DoctrineDBAL</kbd> servis sağlayıcısına geçtiğinizde veritabanı metotlarınızı değiştirmek zorunda kalmazsınız.

#### Doctrine Query Builder

Uygulamanızdaki database servis sağlayıcısını kullanarak QueryBuilder nesnesini oluşturur. Doctrine Query Builder için oluşturulmuş servis sağlayıcısıdır. Eğer sorgu oluşturucuyu kullanmak istiyorsanız <kbd>app/providers.php</kbd> dosyasından <kbd>QueryBuilder</kbd> servis sağlayıcınızı aktif edin.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\QueryBuilder');
```

Sorgu oluşturucunun uygulama içerisinde nasıl kullanıldığına dair detaylı bilgiyi [Database-DoctrineQueryBuilder.md](Database-DoctrineQueryBuilder.md) dosyasından elde edebilirsiniz.

#### Memcached

Php <a href="http://php.net/manual/en/book.memcached.php" target="_blank">Memcached</a> genişlemesi için oluşturulmuş servis sağlayıcısıdır. Uygulamanızdaki <kbd>providers/memcached.php</kbd> konfigürasyonunu kullanarak memcached bağlantılarını yönetmenize yardımcı olur.

```php
$memcached = $container->get('memcached')->shared(['connection' => 'default']);
```

Birkez yüklendikten sonra memcached metotlarına erişilir.

```php
$memcached->set(" ... ");
$memcached->get(" ... ");
```

Konfigürasyonda olmayan yeni bir bağlantı yaratmak için factory metodunu kullanılır.

```php
$memcached = $container->get('memcached')->factory( 
    [
        'host' => '127.0.0.1',
        'port' => 11211,
        'weight' => 1,
        'options' => array(
            'persistent' => false,
            'pool' => 'connection_pool',   // http://php.net/manual/en/memcached.construct.php
            'timeout' => 30,               // Seconds
            'attempt' => 100,
            'serializer' => 'php',    // php, json, igbinary
            'prefix' => null
        )
    ]
);
```

#### Memcache

Php <a href="http://php.net/manual/en/book.memcache.php" target="_blank">Memcache</a> genişlemesi için oluşturulmuş servis sağlayıcısıdır. Uygulamanızdaki <kbd>providers/memcache.php</kbd> konfigürasyonunu kullanarak memcache bağlantılarını yönetmenize yardımcı olur.

```php
$memcache = $container->get('memcache')->shared(['connection' => 'default']);
```

Konfigürasyonda olmayan yeni bir bağlantı yaratmak için factory metodunu kullanılır.

```php
$memcached = $container->get('memcache')->factory( 
    [
        'host' => '127.0.0.1',
        'port' => 11211,
        'weight' => 1,
        'options' => array(
            'persistent' => true,
            'timeout' => 30,
            'attempt' => 100,
        )
    ]
);
```

#### Mongo

Php <a href="http://php.net/manual/en/book.mongo.php">MongoDb</a> veritabanı genişlemesi için yazılmış servis sağlayıcısıdır. Uygulamanızdaki <kbd>mongo.php</kbd> konfigürasyonunu kullanarak mongo db bağlantılarını yönetir.

```php
$mongoInstance = $container->get('mongo')->shared(['connection' => 'default']);
```

Veritabanı seçiminden sonra mongo metotlarına erişilir.

```php
$mongo = $mongoInstance->selectDb('dbname');
$mongo->find();
```

Yada method zincirleme yöntemi ile mongo metotlarına direkt erişilir.

```php
$mongo = $container->get('mongo')->shared(['connection' => 'default'])->selectDb('dbname');
$mongo->find();
```

Konfigürasyonda olmayan yeni bir bağlantı yaratmak için factory metodu kullanılır.

```php
$mongo = $container->get('mongo')->factory(
    [
        'server' => 'mongodb://localhost:27017',
        'options' => array('connect' => true)
    ]
);
```

Uygulamanızdaki database.php konfigürasyonunu kullanarak pdo bağlantılarını yönetmenize yardımcı olur.

#### Redis

Pecl <a href="https://pecl.php.net/package/redis">Redis</a> veritabanı genişlemesi için yazılmış servis sağlayıcısıdır. Uygulamanızdaki <kbd>providers/redis.php</kbd> konfigürasyonunu kullanarak redis bağlantılarını yönetmenize yardımcı olur.

```php
$redis = $container->get('redis')->shared(['connection' => 'default']);
```

Birkez yüklendikten sonra redis metotlarına erişilir.

```php
$redis->set(" ... ");
$redis->get(" ... ");
```

Konfigürasyonda olmayan yeni bir bağlantı yaratmak için factory metodu kullanılır.

```php
$redis = $container->get('redis')->factory( 
    [
        'host' => '127.0.0.1',
        'port' => 6379,
        'options' => array(
            'auth' => '123456',    // Connection password
            'timeout' => 30,
            'persistent' => 0,
            'reconnection.attemps' => 100,     // For persistent connections
            'serializer' => 'none',
            'database' => null,
            'prefix' => null,
        )
    ]
);
```