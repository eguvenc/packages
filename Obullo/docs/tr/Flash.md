
## Flaş Sınıfı

------

Flash sınıfı uygulama içerisinde son kullanıcıya gösterilen onay, hata veya bilgi mesajlarını yönetir. Bir işlemden sonra flaş sınıfı aracılığı ile <b>session</b> nesnesine kaydedilen mesaj veya mesajlar bir sonraki http isteğinde mevcut olurlar ve bir kez görüntülendikten sonra mevcut session verisinden silinirler.

**Note:** Flaş sınıfına ait mesajlar olası bir karışıklığı önlemek için session anahtarına <b>"flash_"</b> öneki ile kaydedilirler.

### Sınıfı Yüklemek

-------

```php
$this->c['flash']->method();
```

#### Konfigürasyon

Flaş sınıfına ait konfigürasyon dosyası <kbd>config/flash.php</kbd> dosyasından yönetilir. Konfigürasyon dosyası flaş mesajlarına ait html şablonu ve niteliklerini belirler. Varsayılan CSS şablonu bootstrap css çerçevesi için konfigüre edilmiştir. <a href="http://getbootstrap.com" target="_blank">http://getbootstrap.com</a>

```php
return array(

    'message' => '<div class="{class}">{icon}{message}</div>',

    'error'  => [
        'class' => 'alert alert-danger', 
        'icon' => '<span class="glyphicon glyphicon-remove-sign"></span> '
    ],
    'success' => [
        'class' => 'alert alert-success', 
        'icon' => '<span class="glyphicon glyphicon-ok-sign"></span> '
    ],
    'warning' => [
        'class' => 'alert alert-warning', 
        'icon' => '<span class="glyphicon glyphicon-exclamation-sign"></span> '
    ],
    'info' => [
        'class' => 'alert alert-info',
        'icon' => '<span class="glyphicon glyphicon-info-sign"></span> '
    ],
);

/* End of file flash.php */
/* Location: .config/flash.php */
```

#### Servis Kurulumu

Flaş sınıfı varsayılan olarak session sürücüsü kullanır ve çalışabilmesi için aşağıdaki gibi bir servis kurulumuna ihtiyaç duyar.

```php
namespace Service;

use Obullo\Flash\Session;
use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Flash implements ServiceInterface
{
    /**
     * Registry
     *
     * @param object $c container
     * 
     * @return void
     */
    public function register(ContainerInterface $c)
    {
        $c['flash'] = function () use ($c) {
            return new Session($c);
        };
    }
}

// END Flash service

/* End of file Flash.php */
/* Location: .app/classes/Service/Flash.php */
```

#### Bir Flaş Mesajı Göstermek

Bir flaş mesajı göstermek oldukça kolaydır bir durum metodu seçin ve içine mesajınızı girin.

```php
$this->flash->success('Form saved successfully.');
```

Ve aşağıdaki kodu view sayfanıza yerleştirin.


```php
$this->flash->output();  // Çıktı Form saved successfully.
```

Durum Metotları

<table>
    <thead>
        <tr>
            <th>Durum</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>success</td>
            <td>Başarılı işlemlerde kullanılır.</td>
        </tr>
        <tr>
            <td>error</td>
            <td>İşlemlerde bir hata olduğunda kullanılır.</td>
        </tr>
        <tr>
            <td>warning</td>
            <td>Uyarı amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
        <tr>
            <td>info</td>
            <td>Bilgi amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
    </tbody>
</table>


#### Birden Fazla Flaş Mesajı Göstermek

Birden fazla flaş mesajı göstermek için birden fazla metot kullanın.

```php
$this->flash->success('Form saved successfully.');
$this->flash->error('Error.');
$this->flash->warning('Something went wrong.');
$this->flash->info('Email has been sent to your mail address.');

$this->flash->output();
```

```php
/*
Çıktı

Form saved successfully.
Error.
Email has been sent to your mail address.
Something went wrong.
*/
```

#### Bir İşlemden Sonra Mesaj Göstermek

Uygulama içerisinde mesajlar göstermek için <b>if .. else</b> komutlarından yararlanabilirsiniz.

```php
$delete = $this->db->transactional(
    function () use () {
    	return $this->db->exec("DELETE FROM users WHERE id = 1");
    }
);
if ($delete) {
	$this->flash->success('User successfully deleted');
} else {
	$this->flash->error('Delete error');
}
```

#### Bir Flaş Mesajının Kalıcılığını Korumak

Eğer bir flaş mesajının bir sonraki http isteğinde kalıcı olmasını istiyorsanız keep metodu kullanmanız gerekir.

```php
$this->flash->keep('notice:warning');
$this->flash->keep('notice:success');
```

#### Geçerli Durum Değerini Almak

Bir flaş mesajına ait durum değerini <b>status</b> anahtarı ile alabilirsiniz.

```php
$this->flash->get('notice:status');  // Çıktı success
```

#### Kendi Flaş Mesajlarınızı Eklemek

Mevcut durum metotları dışında kendinize ait flaş mesajları da ekleyebilirsiniz. Bunun için set fonksiyonunu kullanmanız gerekir.

```php
$this->flash->set('anahtar', 'değer');
```

Mesajları okumak için ise get fonksiyonu kullanılır.

```php
echo $this->flash->get('anahtar', '<p class="example">', '</p>');
```

```php
// Çıktı  <p class="example">değer</p>
```

Eğer flaş mesajı boş ise $this->flash->get() fonksiyonu boş bir string değerine döner aksi durumda mesaja döncektir. Eğer $prefix ve $suffix değerleri boş değilse mesaj html şablonu ile birlikte görüntülenir.


### Flaş Sınıfı Referansı

------

##### $this->flash->success(string $message);

Bir flaş mesajını başarılı durum verisi ile kaydeder.

##### $this->flash->error(string $message);

Bir flaş mesajını hata durum verisi ile kaydeder.

##### $this->flash->warning(string $message);

Bir flaş mesajını uyarı durum verisi ile kaydeder.

##### $this->flash->info(string $message);

Bir flaş mesajını bilgi durum verisi ile kaydeder.

##### $this->flash->output();

Tüm flaş mesajlarını <b>string</b> türünde alır ve flaş verilerinin sonraki istekte silinmesi için verileri <b>old</b> değeri ile kaydeder.

##### $this->flash->outputArray();

Tüm flaş mesajlarını <b>array</b> türünde alır ve flaş verilerinin sonraki istekte silinmesi için verileri <b>old</b> değeri ile kaydeder.

##### $this->flash->keep(string $key)

Bir sonraki http isteğinde mevcut olması için girilen anahtara ait flaş verisini saklar.

##### $this->flash->set(string|array $data = '', $newval = '')

Durum metotlarında tanımlı olmayan yeni bir flaş verisi kaydeder.

##### $this->flash->get(string $key)

Girilen anahtara ait flaş mesajlanı <b>string</b> türünde alır ve flaş verisinin sonraki istekte silinmesi için veriyi <b>old</b> değeri ile kaydeder.