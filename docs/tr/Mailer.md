
## Mail Sınıfı

Mail sınıfı mailer servisi olarak konfigüre edilerek mail gönderme işlemleri için harici servis sağlayıcıları kullanarak ortak bir arayüz sağlar. Şu anki sürümde mail gönderme servisi <kbd>Mailgun</kbd> ve <kbd>Mandrill</kbd> web servislerini destekleyen servis sağlayıcılarını içerir. Mail gönderme işlemine ait sonuçlar ve dönen hata mesajları <kbd>MailResult</kbd> adlı sınıf üzerinden kontrol edilir.

<ul>

<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#service-configuration">Servis Konfigürasyonu</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#loading-service">Servisi Yüklemek</a></li>
        <li><a href="#dependencies">Bağımlılıklar</a></li>
    </ul>
</li>

<li>
    <a href="#api-service-providers">Api Servis Sağlayıcıları</a>
    <ul>
        <li><a href="#mailgun">Mailgun</a></li>
        <li><a href="#mandrill">Mandrill</a></li>
    </ul>
</li>

<li>
    <a href="#send">Mail Göndermek</a>
    <ul>
        <li><a href="#result">MailResult Sınıfı</a></li>
        <li><a href="#error-table">Hata Tablosu</a></li>
        <li><a href="#custom-errors">Özel Hatalar Tanımlama</a></li>
        <li><a href="#wrapping">Dizge Katlama</a> (Wrapping)</li>
    </ul>
</li>

<li>
    <a href="#queue">Kuyruklama</a>
    <ul>
        <li><a href="#queue-configuration">Konfigürasyon</a></li>
        <li><a href="#send-to-queue">Kuyruğa Göndermek</a></li>
        <li><a href="#consume">Kuyruğu Tüketmek</a> (İşçiler)</li>
        <li><a href="#example-queue-data">Örnek Kuyruk Verileri</a></li>
        <li><a href="#debugging">Hata Ayıklama</a></li>
    </ul>
</li>

<li>
    <a href="#method-reference">Fonksiyon Referansı</a>
    <ul>
        <li><a href="#mail-set-reference">Mail Sınıfı Set Metotları</a></li>
        <li><a href="#mail-get-reference">Mail Sınıfı Get Metotları</a></li>
        <li><a href="#mailresult-reference">MailResult Sınıfı</a></li>
    </ul>
</li>

</ul>

<a name="configuration"></a>

### Konfigürasyon

Mailer servisi ana konfigürasyonu <kbd>config/$env/mailer.php</kbd> dosyasından konfigüre edilir.

```php
return array(

    /**
     * Defaults
     *
     * Enabled : On / Off mailer service
     * Useragent : Mailer agent.
     * Validate : Whether to validate the email addresses.
     */
    'default' => [
        'enabled' => true,
        'useragent' => 'Obullo Mailer',
        'validate' => false,
    ],

    /**
     * Message body
     * 
     * Charset : Character set (utf-8, iso-8859-1, etc).
     * Priority : 1, 2, 3, 4, 5   Email Priority. 1 = highest. 5 = lowest. 3 = normal.
     * Wordwrap : Enable / disabled word-wrap.
     * Wrapchars : Character count to wrap at.
     * Mailtype : Text or html Type of mail.
     * Crlf : "\r\n" or "\n" or "\r"  Newline character. (Use "\r\n" to comply with RFC 822).
     * Newline : "\r\n" or "\n" or "\r"  Newline character. (Use "\r\n" to comply with RFC 822).
     */
    'message' => [
        'charset' => 'utf-8',
        'priority' =>  3,
        'wordwrap' => true,
        'wrapchars' => 76,
        'mailtype' => 'html',
        'crlf'  => "\n",
        'newline' =>  "\n",
    ]
);

/* Location: .config/local/mailer/mailer.php */
```

<a name="service-configuration"></a>

#### Servis Konfigürasyonu

Mailer paketini kullanabilmeniz için ilk önce servis ayarlarını yapılandırmanız gerekir. Servis parametreleri konfigürasyonu için aşağıdaki örneğe bir gözatın.

```php
namespace Service;

use Obullo\Mail\MailManager;
use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface;

class Mailer implements ServiceInterface
{
    public function register(ContainerInterface $c)
    {
        $c['mailer'] = function () use ($c) {

            $parameters = [
                'queue' => [
                    'route' => 'mailer.1',
                    'delay' => 0,
                ],
                'provider' => [
                    'mandrill' => [
                        'pool' => 'Main Pool',
                        'key' => 'enter-your-api-key',
                        'class' => 'Obullo\Mail\Provider\Mandrill',
                        'async' => false,
                    ],
                    'mailgun' => [
                        'domain' => 'enter-your-api-domain',  // example news.obullo.com
                        'key' => 'enter-your-api-key',
                        'class' => '\Obullo\Mail\Provider\Mailgun',
                    ]
                ]
            ];
            $mailer = new MailManager($c);
            $mailer->setParameters($parameters);
            $mailer->setProvider('mailgun');
            $mailer->from('Admin <admin@example.com>');
            return $mailer;
        };
    }
}
```

Queue servis parametresinde eğer kuyruğa atma seçeneği kullanılıyorsa kuyruğa ait kanal, kuyruk ismi, gecikme süresi gibi ayarlar belirlenir. Provider servis parametresi ise mail gönderimi için kullanmak istediğiniz api servisine ait api key ve varsa diğer ayarlarını konfigüre etmenizi sağlar.

<a name="running"></a>

### Çalıştırma

<a name="loading-service"></a>

#### Servisi Yüklemek

Mailer servisi aracılığı ile mail metotlarına aşağıdaki gibi erişilebilir.

```php
$this->c['mailer']->metod();
```
<a name="dependencies"></a>

#### Bağımlılıklar

Mail servisi çalışabilmek için <kbd>composer</kbd> bağımlılık yöneticisine ihtiyaç duyar ayrıca <kbd>guzzle</kbd> paketinin de composer.json dosyanızda tanımlı olması gerekir.

Mail servisini çalışabilir hale getirebilmek için aşağıdaki adımları takip edebilirsiniz.

1. Composer kurulumu için [App-Composer.md](App-Composer.md) dosyasına gözatın.
2. Composer.json dosyanıza guzzle paketini ekleyin.
3. Composer.json dosyanıza kullanmak istediğiniz mail servis sağlayıcısına ait api paketini ekleyin.

Aşağıdaki composer.json örneğini kullanabilirsiniz.


```php
{
    "autoload": {
        "psr-4": {
            "Obullo\\": "o2/",
            "": "app/classes"
        }
    },
    "require": {
        "guzzlehttp/guzzle": "~6.0",
        "mailgun/mailgun-php": "~1.7.2",
        "mandrill/mandrill": "1.0.*"
    }
}
```

Yukarıdaki örnekte <kbd>mailgun</kbd> ve <kbd>mandrill</kbd> servislerine ait api lerin her ikisinin de kurulum örneği gösteriliyor. 


```php
composer update
```

Konsolunuzdan yukarıdaki gibi update komutunu çalıştırarak composer paketlerini kurabilirsiniz.


<a name="api-service-providers"></a>

### Api Servis Sağlayıcıları

<a name="mailgun"></a>

* <b>Mailgun</b> : Yaygın kullanılan bir mail gönderme servisidir. Obullo mailgun ile mail gönderimini destekler. Mailgun web sitesinden alacağınız  bir api key ile servisinizi konfigüre ettikten sonra mail gönderme işlemlerine hemen başlayabilirsiniz. Detaylı bilgi için <a href="http://www.mailgun.com/" target="_blank">http://www.mailgun.com/</a> web adresine bir gözatın.

<a name="mandrill"></a>

* <b>Mandrill</b> : Obullo mandrill ile mail gönderimini de  destekler. Mandrill web sitesinden alacağınız  bir api key ile servisinizi konfigüre ettikten sonra mail gönderme işlemlerine hemen başlayabilirsiniz. Detaylı bilgi için <a href="http://www.mandrill.com/" target="_blank">http://www.mandrill.com/</a> web adresine bir gözatın.

Yukarıdaki web servisleri belirli bir limite kadar her ay ücretsiz mail gönderimi sağlarlar.

<a name="send"></a>

### Mail Göndermek

Basit bir http isteği ile mail göndermek için <kbd>send()</kbd> metodu, mail gönderimlerini kuyruğa atmak içinse <kbd>queue</kbd> metodu kullanılır.

```php
$this->c['mailer'];

$mailResult = $this->mailer->to('someone@example.com')
    ->cc('another@another-example.com')
    ->bcc('them@their-example.com')
    ->subject('Email Test')
    ->message('Testing the email class.')
    ->send();

if ($mailResult->hasError()) {
    echo 'Failure !';
} else {
    echo 'Success !'
}

print_r($mailResult->getArray());  // Show error messsages
```

Mail sürücüleri ve diğer bazı değerler her ne kadar önceden servisten konfigüre edilmiş olsalar dahi mailer servisi üzeriden değiştirilebilirler. Mesela <kbd>from()</kbd> fonksiyonu mail gönderilirken kullanılmıyorsa servis içerisinde ilan edilen değer varsayılan olarak kabul edilir. Eğer aşağıdaki gibi from fonksiyonu kullanıysanız girilen değer geçerli olur.

```php
$mailResult = $this->mailer
    ->from('your@example.com', 'Your Name');
    ->to('obullo@yandex.com')
    ->subject('test')
    ->message("test message")
    ->send();
```

Bir başka örnek verirsek eğer varsayılan sağlayıcınız mandrill ise servis sağlayıcınızı <kbd>setMailer()</kbd> metodu ile aşağıdaki gibi değiştirebilirsiniz.


```php
$mailResult = $this->mailer
    ->setProvider('mailgun')
    ->from('Obullo\'s <noreply@news.obullo.com>')
    ->to('obullo@yandex.com')
    ->replyTo('obullo <obullo@yandex.com>')
    ->subject('test')
    ->message("test message")
    ->attach("/var/www/files/logs.jpg")
    ->send();


if ($mailResult->hasError()) {
    echo 'Failure !';
} else {
    echo 'Success !'
}
```

<a name="result"></a>

#### Mail Result Sınıfı

Mail gönderimi gerçekleştiren iki metot vardır bunlardan biri <kbd>send()</kbd> ve diğeri ise <kbd>queue()</kbd> metodudur. Mail gönderme metotları gönderim işleminden sonra MailResult nesnesine geri dönerler. Oluşan hatalar getCode() metodu ile mesajlar ise <kbd>getMessage()</kbd> metodu ile alınır. Tüm bilgilere ulaşmak isteniyorsa <kbd>getArray()</kbd> metodu kullanılır.

```php
if ($mailResult->hasError()) {
    echo 'Error code:  '.$mailResult->getCode();
    echo 'Error message:  '.$mailResult->getMessage();
    echo 'All data: '.print_r($mailResult->getArray(), true);
}
```
<a name="error-table"></a>

#### Hata Tablosu

<table>
    <thead>
        <tr>
            <th>Kod</th>    
            <th>Sabit</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>0</td>
            <td>MailResult::FAILURE</td>
            <td>Başarısız işlem.</td>
        </tr>
        <tr>
            <td>-1</td>
            <td>MailResult::NO_RECIPIENTS</td>
            <td>Mail alıcısı boş, en az bir mail alıcısı girilmeli.</td>
        </tr>
        <tr>
            <td>-2</td>
            <td>MailResult::INVALID_EMAIL</td>
            <td>Geçersiz email adresi.</td>
        </tr>
        <tr>
            <td>-3</td>
            <td>MailResult::ATTACHMENT_UNREADABLE</td>
            <td>Mail ile beraber gönderilen ekli dosya okunamıyor.</td>
        </tr>
        <tr>
            <td>-4</td>
            <td>MailResult::QUEUE_ERROR</td>
            <td>Mail kuyruğa gönderilirken bilinmeyen bir hata oluştu. (Yalnızca queue servisinin açık olduğu durumlarda)</td>
        </tr>
        <tr>
            <td>-5</td>
            <td>MailResult::API_ERROR</td>
            <td>Mail sağlayıcısına ait serviste bilinmeyen bir hata oluştu.</td>
        </tr>
        <tr>
            <td>1</td>
            <td>MailResult::SUCCESS</td>
            <td>Başarılı İşlem.</td>
        </tr>
    </tbody>
</table>

<a name="custom-errors"></a>

#### Özel Hatalar Tanımlama

```php
$mailResult = $this->mailer->getMailResult();

if ($condition) {
    $mailResult->setCode($mailResult::API_ERROR);
    $mailResult->setMessage("Custom error request failed.");
}

print_r($mailResult->getArray());
```

Set metotları ile de özel durumlarda sınıfa hatalar tayin edilebilir.

<a name="wrapping"></a>

#### Dizge Katlama (Wrapping)

Html olmayan text tabanlı bir mail gövdesinde çok uzun bir metin varsa okunmayı zorlaştırabilir yada uzun bir url adresine varsa kullanıcının bu adrese tıklaması zorlaşabilir. Mailer konfigürasyon dosyanızda <kbd>wordwrap</kbd> değeri true (açık) olduğu durumda (RFC 822 ile uyumluluk için önerilir) uzun olan metinler konfigürasyon dosyasında tanımlı olan <kbd>wrapchars</kbd> karakter sayısından (varsayılan 76) sonra aşağıdaki gibi katlanır.

```php
The text of your email that
gets wrapped normally.
```

Eğer bir metnin yada url adresinin katlanmasını istemiyorsanız metni aşağıdaki gibi <kbd>{unwrap} {/unwrap}</kbd> karakterleri arasına yazın.

```php
{unwrap}http://example.com/a_long_link_that_should_not_be_wrapped.html{/unwrap}
```

> **Not:** Bu özelliğin çalışabilmesi için gönderilen mailin $this->mailer->setMailType('text') metodu ile text tabanlı tanımlanması gerekir.

<a name="queue"></a>


### Kuyruklama

Mailer servisi <kbd>queue</kbd> servisini kullanarak mail gönderim isteklerini kuyruğa da atabilir. Queue servisini daha önce hiç kullanmadıysanız [Queue.md](Queue.md) dökümentasyonuna bir gözatın.

<a name="queue-configuration"></a>

#### Konfigürasyon

Kuyruğa atma özelliğinin çalışabilmesi için kuyruklama işlemine ait kuyruk adı ve kanal isimini mailer servis parametrelerinden belirlemeniz gerekir. Eğer mailgun web servisini kullanıyorsanız aşağıdaki gibi web servisine ait bilgilerizi girin.

```php
$parameters = [
    'queue' => [
        'route' => 'mailer.1',
        'delay' => 0,
    ],
    'provider' => [
        'mailgun' => [
            'domain' => 'news.mydomain.com',
            'key' => 'enter-your-api-key',
            'class' => '\Obullo\Mail\Provider\Mailgun',
        ]
    ]
];

/* Location: .app/classes/Service/Mailer.php */
```

<a name="send-to-queue"></a>

#### Kuyruğa Göndermek

Mail gönderme işlerini queue servisi üzerinden kuyruğa atmak uygulamanızın performansını arttırır. Mailer servisinden gönderilen maillerin kuyruğa atılabilmesi için send komutu yerine aşağıdaki gibi <kbd>queue</kbd> komutu kullanılması gerekir.

```php
$mailResult = $this->mailer
    ->setProvider('mailgun')
    ->from('Obullo\'s <noreply@news.obullo.com>')
    ->to('obullo@yandex.com')
    ->replyTo('obullo <obullo@yandex.com>')
    ->subject('test')
    ->message("test message")
    ->attach("/var/www/files/logs.jpg")
    ->queue();

if ($mailResult->hasError()) {
    echo 'Failure !';
} else {
    echo 'Success !'
}
```

<a name="consume"></a>

#### Kuyruğu Tüketmek ( İşçiler )

Mail kuyruğunu tüketmek için konsoldan <kbd>app/classes/Workers/Mailer.php</kbd> işçisini çalıştırmanız gerekir. İşciyi test modunda çalıştırıp kuyruğu tüketmek için <kbd>--output</kbd> değerini 1 olarak girin.

```php
php task queue listen --worker=Workers@Mailer --job=mailer.1 --output=1
```

Yukarıdaki komutu konsoldan çalıştırdığınızda <kbd>$data</kbd> değişkeni içerisinden Mailer işçisine kuyruktaki email verileri gönderilir. Aşağıda sizin için mailgun web servisi ile email gönderimi yapan bir örnek yaptık.


```php
namespace Workers;

use Obullo\Queue\Job;
use Obullo\Queue\JobInterface;
use Obullo\Mail\Provider\Mailgun;
use Obullo\Container\ContainerInterface;

class Mailer implements JobInterface
{
    protected $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function fire($job, array $data)
    {
        switch ($data['mailer']) { 
        case 'mailgun':
            $this->sendWithMailgun($data);
            break;
        }
        if ($job instanceof Job) {
            $job->delete(); 
        }       
    }

    protected function sendWithMailgun(array $msgEvent)
    {
        $mail = $this->c['mailer']->setProvider('mailgun');
        $mailtype = (isset($msgEvent['html'])) ? 'html' : 'text';

        $mail->from($msgEvent['from']);

        if (! empty($msgEvent['to'])) {
            foreach ($msgEvent['to'] as $email) {
                $mail->to($email);
            }
        }
        if (! empty($msgEvent['cc'])) {
            foreach ($msgEvent['cc'] as $email) {
                $mail->cc($email);
            }
        }
        if (! empty($msgEvent['bcc'])) {
            foreach ($msgEvent['bcc'] as $email) {
                $mail->bcc($email);
            }
        }
        if (! empty($headers['Reply-To'])) {
            $this->msgEvent['h:Reply-To'] = $headers['Reply-To'];
        }
        if (! empty($headers['Message-ID'])) {
            $this->msgEvent['h:Message-Id'] = $headers['Message-ID'];
        }
        $mail->subject($msgEvent['subject']);
        $mail->message($msgEvent[$mailtype]);

        if (isset($msgEvent['files'])) {
            foreach ($msgEvent['files'] as $value) {
                $mail->attach($value['fileurl'], $value['disposition']);
            }
        }
        $mail->addMessage('o:deliverytime', $mail->setDate());
        return $mail->send();
    }

    .
    .
    .
}

/* Location: .app/classes/Mailer.php */
```

Gönderilen veriler mailer türüne göre filtrelenerek ilgili web servis api sine bağlanarak mail gönderme işlemi gerçekleştirilir. Mail gönderim işleminden sonra <kbd>$job->delete()</kbd> metodu ile tamamlanan iş kuyruktan silinir.

Yukarıda görüldüğü gibi Mailgun kütüphanesi msgEvent değişkeni ile verileri web servis sağlayıcınıza gönderir. Servis sağlayıcınızın api dökümentasyonunu inceleyin eğer msgEvent değişkeni içerisine özel bir değer eklemek gerekiyorsa bu değerleri <kbd>addMessage()</kbd> metodunu kullanarak gönderebilirsiniz.


<a name="example-queue-data"></a>

#### Örnek Kuyruk Verileri

```php
print_r($msgEvent);

Array
(
    [files] => Array
        (
            [0] => Array
                (
                    [name] => logs.jpg
                    [type] => image/jpeg
                    [fileurl] => /var/www/files/logs.jpg
                    [disposition] => attachment
                )
        )

    [headers] => Array
        (
            [User-Agent] => 
            [Date] => Tue, 4 Aug 2015 11:59:20 +0100
            [To] => obullo@yandex.com
            [Cc] => obullo@gmail.com
            [Reply-To] => "obullo@yandex.com" <obullo@yandex.com>
            [Subject] => test
        )

    [from] => Obullo's <noreply@news.obullo.com>
    [subject] => test
    [to] => Array
        (
            [0] => obullo@yandex.com
        )

    [cc] => Array
        (
            [0] => obullo@gmail.com
        )

    [h:Reply-To] => "obullo@yandex.com" <obullo@yandex.com>
    [o:deliverytime] => Tue, 4 Aug 2015 11:59:20 +0100
    [html] => test message
    [mailer] => mailgun
)
```

<kbd>app/Workers/Mailer.php</kbd> parse this format and send your emails in the background.

<a name="debugging"></a>

#### Hata Ayıklama

Dinleme komutu sonunda <kbd>--output</kbd> parametresine "1" değerini verdiğinizde kuyruğa gönderilen maillere ait çıktılar ve hata çıktılarını konsoldan takip edebilirsiniz.

```php
php task queue listen --worker=Workers@Mailer --job=mailer.1 --output=1
```

Prodüksiyon çevre ortamında <kbd>--output</kbd> parametresi değeri her zaman "0" olmalıdır.

```php
Output : 
{
"job":"Workers\\Mailer",
"data":{
"headers":{
"User-Agent":"","Date":"Tue, 4 Aug 2015 11:58:31 +0100",
"To":"obullo@yandex.com",
"Cc":"eguvenc@gmail.com",
"Reply-To":"\"obullo@yandex.com\" <obullo@yandex.com>",
"Subject":"test"},
"from_email":"noreply@news.obullo.com",
"from_name":"Obullo's",
"subject":"test",
"to":[{"email":"obullo@yandex.com","name":null,"type":"to"},
{"email":"eguvenc@gmail.com","name":null,"type":"cc"}],
"send_at":"Tue, 4 Aug 2015 11:58:31 +0100","html":"test message","mailer":"mandrill"}}
```

<a name="method-reference"></a>

### Fonksiyon Referansı

<a name="mail-set-reference"></a>

#### Mailer Set Metotları

-------

##### $this->mailer->setProvider(string $mailer)

Mail gönderici web servis sağlayıcısını belirler. Birinci parametreye sağlayıcı ismi girilmelidir. Örneğin: mailgun.

##### $this->mailer->from($email, $name = null)

Göndericiyi belirler. Birinci parametre göndericinin email adresini ikinci parametre ise ismini belirler.

##### $this->mailer->replyTo(string $email, string $name = null)

Bir reply-to email adresi atar. 

##### $this->mailer->to(string|array $email)

Gönderilen kişinin email adresini belirler. Adresler virgülle ayrılmış string yada array türünde girilebilir.

##### $this->mailer->cc(string|array $email)

Gönderilecek email adreslerini karbon kopya (cc) biçiminde gönderir. Adresler virgülle ayrılmış string yada array türünde girilebilir. Cc biçiminde gönderilen email adresleri gönderilen emaile ait kaynakta diğer gönderilen adresler olarak görüntülenirler.

##### $this->mailer->bcc(string|array $email)

Gönderilecek email adreslerini kör karbon kopya (bcc) biçiminde gönderir. Adresler virgülle ayrılmış string yada array türünde girilebilir. Bcc biçiminde gönderilen email adresleri gönderilen emaile ait kaynakta diğer gönderilen adresler olarak görüntülenemezler.

##### $this->mailer->subject(string $subject)

Email mesajına ait konuyu belirler. String türünde girilmelidir.

##### $this->mailer->message(string $body)

Email adresine ait gövde metnini belirler.

##### $this->mailer->setHeader(string $header, string $value)

Web servise gönderilmek istenen email başlıklarını <kbd>$this->msgEvent['headers']</kbd> dizisi içerisinde oluşturur.

##### $this->mailer->setDate()

RFC822 formatında <kbd>$this->msgEvent['headers']</kbd> dizisi içerisine yeni bir tarih ekleyerek oluşturulan tarihe geri döner. Örnek: Wed, 27 Sep 2006 21:36:45 +0200.

##### $this->mailer->setMailType($type = 'text')

Gönderilen mail gövdesinin gönderilme biçimini belirler. Gönderim biçimi <kbd>text</kbd> yada <kbd>html</kbd> olarak girilebilir.

##### $this->mailer->setValidation($enabled = true)

Email doğrulama özeliğini dinamik olara açıp kapatır. Doğrulama açıksa yanlış formatta bir email gönderilmek istendiğinde email gönderimi gerçekleştirilmez ve hata çıktılanır.

##### $this->mailer->attach(string $fileurl, $disposition = 'attachment')

Mail ile birlikte bir ekli dosya gönderimine olanak tanır. Birinci parametre ekli dosyanın tam adresi ikinci parametre ise <kbd>attachment</kbd> yada <kbd>inline</kbd> özelliklerini belirler. Birden fazla ekli dosya gönderilmek isteniyorsa fonksiyon birden fazla kullanılmalıdır.

##### $this->mailer->addMessage($key, $value);

Web servise gönderilmek istenen ekstra özel değerleri <kbd>$this->msgEvent</kbd> dizisi içerisine <kbd>$this->msgEvent[$key] = $value</kbd> biçiminde oluşturur.

##### $this->mailer->send()

<kbd>$this->msgEvent</kbd> içerisinde oluşturulan tüm değerleri mail sağlayıcısına ait web servisine gönderir. Email gönderim işlemi başarılı olursa işlem <kbd>MailResult</kbd> nesnesine geri döner.

##### $this->mailer->queue($options = array())

<kbd>$this->msgEvent</kbd> içerisinde oluşturulan tüm değerleri queue servisine gönderir. Email gönderim işlemi başarılı olursa işlem <kbd>MailResult</kbd> nesnesine geri döner. Birinci parametreden varsa queue opsiyonları gönderilir.

##### $this->mailer->clear()

Tüm email değişkenlerini boş değerlerine geri döndürür. Bu fonksiyon bir döngü içerisinde birden fazla email gönderilmek istendiğinde döngü içerisinde kullanılarak değişken değerlerini her defasında yeni bir email gönderimine hazır olması için başa döndürür.

<a name="mail-get-reference"></a>

#### Mailer Get Metotları

-------

##### $this->mailer->getProvider();

Geçerli mail servis sağlayıcısı ismine geri döner. Örnek: mandrill.

##### $this->mailer->getHeaders();

Bir dizi içerisinde kayıtlı olan geçerli email başlıklarına geri döner.

##### $this->mailer->getParameters();

Mailer servisinde konfigüre edilmiş parametrelere geri döner.

##### $this->mailer->getFrom();

Daha önceden tayin edilmiş gönderici adresine geri döner.

##### $this->mailer->getFromEmail();

Daha önceden tayin edilmiş gönderici email adresine geri döner.

##### $this->mailer->getFromName();

Daha önceden tayin edilmiş gönderici ismine geri döner.

##### $this->mailer->getSubject();

Daha önceden tayin edilmiş mesaj konusuna geri döner.

##### $this->mailer->getMessage();

Daha önceden tayin edilmiş email gövdesine geri döner.

##### $this->mailer->getRecipients();

Tüm email alıcılarına bir dizi içerisinde geri döner.

##### $this->mailer->getMailType();

Mail gönderim türüne geri döner. Bu türler <kbd>html</kbd> yada <kbd>text</kbd> olabilir.

##### $this->mailer->getContentType();

Mail içerik biçimlerini verir. Bu biçimler <kbd>html</kbd>, <kbd>html-attach</kbd>, <kbd>plain</kbd> yada <kbd>plain-attach</kbd> olabilir.

##### $this->mailer->getUserAgent();

Mail göndericisine ait gönderme servisi adını verir. Bu ad mailer konfigürasyon dosyasından düzenlenebilir.

##### $this->mailer->hasAttachment();

Eğer gönderilen mail bir ekli dosya içeriyorsa <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine geri döner.

##### $this->mailer->getAttachments();

Mail ile birlikte gönderilmek için atanmış tüm ekli dosya ve özelliklerine bir dizi içerisinde geri döner.

##### $this->mailer->getMailResult();

Mail gönderim işlemi sonuçlarını yönetmek için ortak bir arayüz sağlayan <kbd>MailResult</kbd> adlı sınıf nesnesine geri döner.

<a name="mailresult-reference"></a>

#### MailResult Sınıfı

-------

##### $mailResult->hasError()

Eğer mail gönderimi esnasında bir hata oluştu ise <kbd>false</kbd> aksi durumda <kbd>true</kbd> değerine geri döner.

##### $mailResult->setMessage(string $message)

Mail gönderiminden sonra oluşabilecek özel bir hatayı hatalar dizisi içerisine ekler. Birden fazla hata eklenebilir.

##### $mailResult->setCode(int $code)

Girilen hataya ait hata kodunu geçerli hata kodu değeri olarak atar.

##### $mailResult->getCode()

Mevcut hata koduna geri döner.

##### $mailResult->getMessages()

Tüm hata mesajlarını almak için başarısız işlem durumunda kullanılır. İşlemin başarısız olup olmadığı <kbd>hasError()</kbd> metodu ile kontrol edilir.

##### $mailResult->getArray()

Hata kodu ve hata mesajı ile birlikte tüm bilgilere geri döner.