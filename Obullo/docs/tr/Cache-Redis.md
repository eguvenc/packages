
## Redis Sürücüsü

Memcached sürücüsü sunucunuza php extension olarak kurulmayı gerektirir. Ubuntu ve benzer linux sitemleri altında redis kurulumuna dair <b>warmup</b> adı verilen dökümentasyon topluluğunun hazırladığı <a href="https://github.com/obullo/warmup/tree/master/Redis" target="_blank">bu belgeden</a> yararlanabilirsiniz.

<ul>
<li> 
  <a href="#redis-configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#redis-nodes">Çoklu Sunucular</a></li>
        <li><a href="#redis-service">Servis Kurulumu</a></li>
        <li><a href="#redis-service-provider">Servis Sağlayıcısı</a></li>
        <li><a href="#redis-service-provider-connections">Servis Sağlayıcısı Bağlantıları</a></li>
        <li>
            <a href="#redis-reference">Redis Referansı</a>
            <ul>
                <li><a href="#redis-auth">$this->cache->auth()</a></li>
                <li><a href="#redis-setSerializer">$this->cache->setSerializer()</a></li>
                <li><a href="#redis-getSerializer">$this->cache->getSerializer()</a></li>
                <li><a href="#redis-setOption">$this->cache->setOption()</a></li>
                <li><a href="#redis-getOption">$this->cache->getOption()</a></li>
                <li><a href="#redis-set">$this->cache->set()</a></li>
                <li><a href="#redis-get">$this->cache->get()</a></li>
                <li><a href="#redis-append">$this->cache->append()</a></li>
                <li><a href="#redis-exists">$this->cache->exists()</a></li>
                <li><a href="#redis-replace">$this->cache->replace()</a></li>
                <li><a href="#redis-delete">$this->cache->delete()</a></li>
                <li><a href="#redis-renameKey">$this->cache->renameKey()</a></li>
                <li><a href="#redis-setTimeout">$this->cache->setTimeout()</a></li>
                <li><a href="#redis-getAllKeys">$this->cache->getAllKeys()</a></li>
                <li><a href="#redis-getMultiple">$this->cache->getMultiple()</a></li>
                <li><a href="#redis-getLastError">$this->cache->getLastError()</a></li>
                <li><a href="#redis-type">$this->cache->type()</a></li>
                <li><a href="#redis-flushAll">$this->cache->flushAll()</a></li>
                <li><a href="#redis-getSet">$this->cache->getSet()</a></li>
                <li><a href="#redis-hSet">$this->cache->hSet()</a></li>
                <li><a href="#redis-hGet">$this->cache->hGet()</a></li>
                <li><a href="#redis-hGetAll">$this->cache->hGetAll()</a></li>
                <li><a href="#redis-hLen">$this->cache->hLen()</a></li>
                <li><a href="#redis-hDel">$this->cache->hDel()</a></li>
                <li><a href="#redis-hKeys">$this->cache->hKeys()</a></li>
                <li><a href="#redis-hVals">$this->cache->hVals()</a></li>
                <li><a href="#redis-hIncrBy">$this->cache->hIncrBy()</a></li>
                <li><a href="#redis-hIncrByFloat">$this->cache->hIncrByFloat()</a></li>
                <li><a href="#redis-hMSet">$this->cache->hMSet()</a></li>
                <li><a href="#redis-hMGet">$this->cache->hMGet()</a></li>
                <li><a href="#redis-sAdd">$this->cache->sAdd()</a></li>
                <li><a href="#redis-sort">$this->cache->sort()</a></li>
                <li><a href="#redis-sSize">$this->cache->sSize()</a></li>
                <li><a href="#redis-sInter">$this->cache->sInter()</a></li>
                <li><a href="#redis-sGetMembers">$this->cache->sGetMembers()</a></li>
            </ul>
        </li>
    </ul>
</li>
</ul>

<a name="redis-configuration"></a>

### Konfigürasyon

Redis sürücüsü bağlantı ayarlarınızı <kbd>config/env.$env/cache/redis.php</kbd> dosyasında tanımlamanız gerekir.

<a name="redis-nodes"></a>

#### Çoklu Sunucular ( Nodes )

Birden fazla redis sunucunuz varsa konfigürasyon dosyasındaki diğer sunucu adreslerini aşağıdaki gibi nodes dizisi içerisine girmeniz gerekir.

```php
  'connections' => 
  [
      'default' => [ .. ],
      'nodes' => [
          [
              'host' => '10.0.0.168',
              'port' => 6379,
          ],
          [
              'host' => '10.0.0.169',
              'port' => 6379,
          ]

      ]
  ],
```

<a name="redis-service-configuration"></a>

#### Servis Kurulumu

Eğer uygulama içerisinde cache servisinin redis kullanmasını istiyorsanız <kbd>app/Classes/Service/Cache.php</kbd> dosyasındaki <b>driver</b> anahtarını <b>redis</b> olarak değiştirin.

```php
$this->c['app']->provider('cache')->get(['driver' => 'redis', 'connection' => 'default']);
```

Redis sürücüsü seçildiğinde bazı ek özellikler ve metotlar gelir. Aşağıda şu anki sürümde tanımlı olan metotlar basitçe anlatılmıştır.

<a name="redis-service-provider"></a>

#### Servis Sağlayıcısı

Cache kütüphanesi bağımsız olarak kullanılmak istendiği durumlarda servis sağlayıcısından direkt olarak çağrılabilir. Servis sağlayıcı yüklendiği zaman kütüphaneyi bir değişkene atayıp yarattığınız bağlantıya ait metotlara ulaşabilirsiniz.

```php
$this->cache = $this->c['app']->provider('cache')->get(
      [
        'driver' => 'redis', 
        'connection' => 'default'
      ]
);
$this->cache->method();
```

<a name="redis-service-provider-connections"></a>

#### Servis Sağlayıcısı Bağlantıları

Servis sağlayıcısı <b>connection</b> anahtarındaki bağlantı değerini önceden <kbd>config/$env/cache</kbd> klasöründe tanımlı olan <b>$sürücü.php</b> dosyası connections dizisi içerisinden alır. Aşağıda redis sürücüsü <b>default</b> bağlantısına ait bir örnek görülüyor.

```php

return array(

    'connections' => 
    [
        'default' => [ 
            'host' => '10.0.0.168',
            'port' => 6379,
            'options' => [
                'persistent' => false,
                'auth' => '123456',
                'timeout' => 30,
                'attempt' => 100,
                'serializer' => 'none',
                'database' => null,
                'prefix' => null,
            ]
        ],
        
    ]
);
```

<a name="redis-reference"></a>

### Redis Referansı

-------

> Bu sınıf içerisinde tanımlı olmayan metotlar __call metodu ile php Redis sınıfından çağrılırlar.

<a name="redis-auth"></a>

##### $this->cache->auth(string $password)

Eğer yetkilendirme konfigürasyon dosyasından yapılmıyorsa bu fonksiyon ile manual olarak yetkilendirme yapabilirsiniz. Şifre plain-text biçiminde olmalıdır.

<a name="redis-setSerializer"></a>

##### $this->cache->setSerializer(string $serializer);

Encode ve decode işlemleri için serileştirici türünü seçer.

* **none**     : Serileştirici kullanılmaz veriler raw biçiminde kaydedilir.
* **php**      : Php serialize() fonksiyonunu serileştiri olarak seçer.
* **json**     : Serileştiriciyi JSON encoder fonksiyonu olarak seçer.
* **igbinary** : Serileştiriciyi igbinary olarak seçer.

<a name="redis-getSerializer"></a>

##### $this->cache->getSerializer();

Geçerli serileştirici türüne geri döner.

<a name="redis-setOption"></a>

##### $this->cache->setOption($option = 'OPT_SERIALIZER', $value = 'SERIALIZER_NONE')

Redis için bir opsiyon tanımlar. Birer sabit olan opsiyonlar parametrelerden string olarak kabul edilir. Sabitler ( Constants ) hakkında daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#setoption">Redis setOption</a> metoduna bir gözatın.

<a name="redis-getOption"></a>

##### $this->cache->getOption($option = 'OPT_SERIALIZER');

Redis e daha önceden set edilmiş opsiyonun değerine döner. Opsiyon sabitleri parametreden string olarak kabul edilir. Daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#getoption">Redis getOption</a> metoduna bir gözatın.

<a name="redis-set"></a>

##### $this->cache->set(mixed $key, mixed $data, int optional $expiration)

Önbellek deposuna veri kaydeder. Kaydetme işlemlerinde <b>string</b> ve <b>array</b> türlerini kullanabilirsiniz Eğer ilk parametreye bir dizi gönderirseniz ikinci parametreyi artık sona erme süresi olarak kullanabilirsiniz.

>**Not:** Anahtar içerisinde ":" karakterini kullanırsanız anahtarlar gruplanarak gösterilirler.

<a name="redis-get"></a>

##### $this->cache->get($key)

Önbellek deposundan veri okur. Okuma işlemlerinde string ve array türlerini kullanabilirsiniz. Anahtar içerisinde ":" karakterini kullanarak gruplanmış verilere ulaşabilirsiniz.

```php
$this->cache->get('key');           // Çıktı value
$this->cache->get('example:key');   // Çıktı value
```
<a name="redis-append"></a>

##### $this->cache->append(string $key, string or array $data)

Daha önce değer atanmış bir anahtara yeni değer ekler. Yeni atanan değer önceki değer ile string biçiminde birleştirilir.

<a name="redis-exists"></a>

##### $this->cache->exists(string $key)

Bir anahtarın var olup olmadığını kontrol eder. Anahtar mevcut ise **true** değilse **false** değerinde döner.

<a name="redis-replace"></a>

##### $this->cache->replace(string|array $key);

Girilen anahtar değerini günceller.

<a name="redis-delete"></a>

##### $this->cache->delete(string $key);

Girilen anahtarı önbellekten siler.

<a name="redis-renameKey"></a>

##### $this->cache->renameKey(string $key, string $newKey);

Mevcut bir anahtarı yeni bir anahtar ile değiştirme imkanı sağlar. Değiştirilmek istenen anahtar var ise işlem sonucu **true** yok ise **false** değerine dönecektir.

>**Not:** Yeni anahtar daha önce tanımlanmış ise yeni anahtar bir öncekinin üzerine yazılır.

<a name="redis-setTimeout"></a>

##### $this->cache->setTimeout(string $key, int $ttl)

Önceden set edilmiş bir anahtarın sona erme süresini günceller. Son parametre mili saniye formatında yazılmalıdır.

<a name="redis-getAllKeys"></a>

##### $this->cache->getAllKeys();

Bütün anahtarları dizi olarak döndürür.

<a name="redis-getMultiple"></a>

##### $this->cache->getMultiple(array $key)

Tüm belirtilen anahtarların değerini dizi olarak döndürür. Bir yada daha fazla anahtar değeri bulunamaz ise bu anahtarların değeri **false** olarak dizide var olacaklardır.

```php
$this->cache->set('key1', 'value1');
$this->cache->set('key2', 'value2');
$this->cache->set('key3', 'value3');
$this->cache->getMultiple(array('key1', 'key2', 'key3')); 
```
<a name="redis-getLastError"></a>

##### $this->cache->getLastError()

En son meydana gelen hataya string biçiminde geri döner.

<a name="redis-type"></a>

##### $this->cache->type(string $key)

Girilen anahtarın redis türünden biçimine döner bu biçimlerden bazıları şunlardır: <b>string, set, list, zset, hash</b>.

<a name="redis-flushAll"></a>

##### $this->cache->flushAll()

Geçerli veritabanından tüm anahtarları siler. Bu işlemin sonucu daima **true** döner.

<a name="redis-getSet"></a>

##### $this->cache->getSet(string $key, string $value);

Önbellek deposuna yeni veriyi kaydederken eski veriye geri dönerek eski veriyi elde etmenizi sağlar.

<a name="redis-hSet"></a>

##### $this->cache->hSet(string $key, string $hashKey, mixed $value);

Belirtilen anahtarın alt anahtarına ( hashKey ) bir değer ekler.Metot eğer anahtara ait bir veri yoksa yani insert işleminde **true** değerine anahtara ait bir veri varsa yani replace işleminde **false** değerine döner.

```php
$this->cache->hSet('h', 'key1', 'merhaba'); // Sonuç true
$this->cache->hGet('h', 'key1'); // Sonuç "merhaba"

$this->cache->hSet('h', 'key1', 'php'); // Sonuç false döner ama değer güncellenir
$this->cache->hGet('h', 'key1');  // Sonuç "php"
```
<a name="redis-hGet"></a>

##### $this->cache->hGet(string $key, string $hashKey);

Hash tablosundan bir değere ulaşmanızı sağlar. Saklanan değere erişmek için belirtilen anahtarı hash tablosunda veya diğer anahtarlar içinde arayacaktır. Bulunamaz ise sonuç **false** dönecektir. 

```php
$this->cache->hGet('h', 'key');   // key "h" tablosunda aranır
```
<a name="redis-hGetAll"></a>

##### $this->cache->hGetAll();

Hash tablosundaki tüm değerleri bir dizi içerisinde verir.

```php
$this->cache->delete('h');
$this->cache->hSet('h', 'a', 'x');
$this->cache->hSet('h', 'b', 'y');

print_r($this->cache->hGetAll('h'));  // Çıktı array("x", "y");
```
<a name="redis-hLen"></a>

##### $this->cache->hLen();

Hash tablosundaki değerlerin genişliğini rakam olarak döndürür.

```php
$this->cache->delete('h');
$this->cache->hSet('h', 'key1', 'php');
$this->cache->hSet('h', 'key2', 'obullo');
print_r($this->cache->hLen('h')); // sonuç 2
```
<a name="redis-hDel"></a>

##### $this->cache->hDel();

Hash tablosundan bir değeri siler. Hash tablosu yada belirtilen anahtar yok ise sonuç **false** dönecektir.

```php
$this->cache->hDel('h', 'key');
```

<a name="redis-hKeys"></a>

##### $this->cache->hKeys();

Bir hash deki tüm anahtarları dizi olarak döndürür.

```php
$this->cache->delete('h');
$this->cache->hSet('h', 'a', 'x');
$this->cache->hSet('h', 'b', 'y');

print_r($this->cache->hKeys('h'));  // Çıktı  array("a", "b");
```
<a name="redis-hVals"></a>

##### $this->cache->hVals();

Bir hash deki tüm değerleri dizi olarak döndürür.

```php
$this->cache->delete('h');
$this->cache->hSet('h', 'a', 'x');
$this->cache->hSet('h', 'b', 'y');

print_r($this->cache->hVals('h'));  // Çıktı array("x", "y");
```
<a name="redis-hIncrBy"></a>

##### $this->cache->hIncrBy();

Bir hash üyesinin değerini belirli bir miktarda artırır.

>**Not:** hIncrBy() metodunu kullanabilmek için serileştirme türü "none" olmalıdır.

```php
$this->cache->delete('h');
$this->cache->hIncrBy('h', 'x', 2);  // Sonuç:  2 / yeni değer: h[x] = 2
$this->cache->hIncrBy('h', 'x', 1);  // h[x] ← 2 + 1. sonuç 3
```

<a name="redis-hIncrByFloat"></a>

##### $this->cache->hIncrByFloat();

Bir hash üyesinin değerini float (ondalıklı) değer olarak artırmayı sağlar.

>**Not:** hIncrByFloat() metodunu kullanabilmek için serileştirme türü "none" olmalıdır.

```php
$this->cache->delete('h');
$this->cache->hIncrByFloat('h','x', 1.5);   // Sonuç 1.5: h[x] = 1.5 now
$this->cache->hIncrByFLoat('h', 'x', 1.5);  // Sonuç 3.0: h[x] = 3.0 now
$this->cache->hIncrByFloat('h', 'x', -3.0); // Sonuç 0.0: h[x] = 0.0 now
```

<a name="redis-hMSet"></a>

##### $this->cache->hMSet(string $key, array $members);

Tüm hash değerlerini doldurur. String olmayan değerleri string türüne çevirir, bunuda standart string e dökme işlemini kullanarak yapar. Değeri **null** olarak saklanmış veriyi boş string olarak saklar.

```php
$this->cache->delete('user:1');
$this->cache->hMset('user:1', array('ad' => 'Ali', 'maas' => 2000));
$this->cache->hIncrBy('user:1', 'maas', 100);  // Ali'nin maaşını 100 birim arttırdık.
```

<a name="redis-hMGet"></a>

##### $this->cache->hMGet(string $key, array $members);

Hash de özel tanımlanan alanların değerlerini dizi olarak getirir.

```php
$this->cache->delete('h');
$this->cache->hSet('h', 'field1', 'value1');
$this->cache->hSet('h', 'field2', 'value2');
$this->cache->hmGet('h', array('field1', 'field2')); 

// Sonuç: array('field1' => 'value1', 'field2' => 'value2')
```

<a name="redis-sAdd"></a>

##### $this->cache->sAdd(string $key, string or array $value);

Belirtilen değere bir değer ekler. Eğer değer zaten eklenmiş ise işlem sonucu **false** değerine döner.

```php
$this->cache->sAdd('key1', 'value1'); 

// 1, 'key1' => {'value1'}

$this->cache->sAdd('key1', array('value2', 'value3'));

// 2, 'key1' => {'value1', 'value2', 'value3'}

$this->cache->sAdd('key1', 'value2');

// 0, 'key1' => {'value1', 'value2', 'value3'}
```

<a name="redis-sort"></a>

##### $this->cache->sort(string $key, array $sort)

Saklanan değerleri parametreler doğrultusunda sıralar.

Değerler:

```php
$this->cache->delete('test');
$this->cache->sAdd('test', 2);
$this->cache->sAdd('test', 1);
$this->cache->sAdd('test', 3);
```

Kullanımı:

```php
print_r($this->cache->sort('test')); // 1,2,3
print_r($this->cache->sort('test', array('sort' => 'desc')));  // 5,4,3,2,1
print_r($this->cache->sort('test', array('sort' => 'desc', 'store' => 'out'))); // (int)5
```
>**Not:** **sort** methodunun kullanılabilmesi için serileştirme türünün **"none"** olarak tanımlaması gerekmektedir.

<a name="redis-sSize"></a>

##### $this->cache->sSize(string $key)

Belirtilen anahtara ait değerlerin toplamını döndürür.

```php
$this->cache->sAdd('key1' , 'test1');
$this->cache->sAdd('key1' , 'test2');
$this->cache->sAdd('key1' , 'test3'); // 'key1' => {'test1', 'test2', 'test3'}
```

```php
$this->cache->sSize('key1'); /* 3 */
$this->cache->sSize('keyX'); /* 0 */
```
<a name="redis-sInter"></a>

##### $this->cache->sInter(array $key)

Belirtilen anahtarlara ait değerlerin bir birleriyle kesişenleri döndürür.

```php
$this->cache->sAdd('key1', 'val1');
$this->cache->sAdd('key1', 'val2');
$this->cache->sAdd('key1', 'val3');
$this->cache->sAdd('key1', 'val4');

$this->cache->sAdd('key2', 'val3');
$this->cache->sAdd('key2', 'val4');

$this->cache->sAdd('key3', 'val3');
$this->cache->sAdd('key3', 'val4');
```

```php
print_r($this->cache->sInter('key1', 'key2', 'key3'));  // Çıktı array('val4', 'val3')
```

<a name="redis-sGetMembers"></a>

##### $this->cache->sGetMembers(string $key)

Belirtilen anahtarın değerini bir dizi olarak döndürür.

```php
$this->cache->delete('key');
$this->cache->sAdd('key', 'val1');
$this->cache->sAdd('key', 'val2');
$this->cache->sAdd('key', 'val1');
$this->cache->sAdd('key', 'val3');
```

```php
print_r($this->cache->sGetMembers('key'));  // Çıktı array('val3', 'val2', 'val1');
```

> **Not:** Bu dökümentasyonda tanımlı olmayan redis metotları __call metodu ile php Redis sınıfından çağrılırlar. Php Redis sınıfı hakkında daha detaylı dökümentasyona <a href="https://github.com/phpredis/phpredis" target="_blank">buradan</a> ulaşabilirsiniz.