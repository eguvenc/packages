
## Ortamlar

Uygulamanız <kbd>local</kbd>, <kbd>test</kbd>, <kbd>production</kbd> veya yeni eklenen bir çevre ortamına göre farklı davranışlar sergileyebilir. 

<a name="environments"></a>

#### Ortam Konfigürasyonu

Çevre ortamı <kbd>app/environments.php</kbd> dosyasında oluşturulan sunucu isimlerinin mevcut sunucu ismi ile karşılaştırılması sonucu elde edilir. 

```php
return array(

    'local' => [
        'john-desktop',
        'localhost.ubuntu',
    ],

    'test' => [
        'localhost.test',
    ],

    'production' => [
        'localhost.production',
    ],
);
```

Local ortamda çalışırken her geliştiricinin kendine ait bilgisayar ismini <kbd>app/environments.php</kbd> dosyası <kbd>local</kbd> dizisi içerisine bir defalığına eklemesi gereklidir. Linux benzeri işletim sistemlerinde bilgisayarınızın adını hostname komutuyla kolayca öğrenebilirsiniz.

```
root@localhost: hostname   // localhost.ubuntu
```

Prodüksiyon veya test gibi ortamlarda çalışmaya hazırlık için sunucu isimlerini yine bu konfigürasyon dosyasındaki prodüksiyon ve test dizileri altına tanımlamanız yeterli olacaktır. Sunucu isimleri geçerli sunucu ismi ile eşleşmediğinde aşağıdaki gibi bir hata ile karşılaşırsınız.

```
We could not detect your application environment, please correct your app/environments.php file.
```

<a name="env-variable"></a>

#### Ortam Değişkeni

Geçerli ortam değişkenine,

```php
echo $container->get('app')->getEnv();  // Çıktı  local
```

metodu ile ulaşılır. Varsayılan ortam türleri aşağıda listelenmiştir.

* <b>Local</b> : Yerel sunucu ortamıdır, geliştiriciler tarafından uygulama bu ortam altında geliştirilir, her bir geliştiricinin bir defalığına <kbd>app/environments.php</kbd> dosyası içerisine kendi bilgisayarına ait ismi tanımlaması gereklidir.

* <b>Test</b> : Test sunucu ortamıdır, geliştiriciler tarafından uygulama bu ortamda test edilir sonuçlar başarılı ise prodüksiyon ortamında uygulama yayına alınır, test sunucu isimlerinin bir defalığına <kbd>app/environments.php</kbd> dosyası içerisine tanımlaması gereklidir.

* <b>Production</b> : Prodüksiyon sunucu ortamıdır, geliştiriciler tarafından testleri geçmiş başarılı uygulama prodüksiyon ortamında yayına alınır, prodüksiyon sunucu isimlerinin bir defalığına <kbd>app/environments.php</kbd>  dosyası içerisine tanımlaması gereklidir.


<a name="creating-environment-config"></a>

#### Ortama Bağlı Konfigürasyonlar

Konfigürasyon paketi geçerli ortam klasöründeki konfigürasyonlara ait değişen anahtarları <kbd>local</kbd> ortam anahtarlarıyla eşleşirse değiştirir aksi durumda olduğu gibi bırakır.Prodüksiyon ortamı üzerinden örnek verecek olursak bu konfigürasyonu yaratmak için değişen anahtar değerlerini ilgili dosyaya girmeniz yeterli olur. Mesala prodüksiyon ortamı içerisine aşağıdaki gibi bir <kbd>config.php</kbd> dosyası oluşturalım,

```php
- app
    - config
        + local
        - production
            config.php
            database.php
        + test
        - my
            config.php
            database.php
```

Aşağıdaki örnekte sadece dosya içerisindeki değişime uğrayan anahtarlar gözüküyor. Uygulama çalıştığında bu anahtarlar <kbd>local</kbd> ortam anahtarları ile birleştirilirler.

```php
return array(
                    
    'log' => [
        'enabled' => true,
    ],
    'http' => [
        'webhost' => 'framework',
    ],
    'locale' => [
        'timezone' => 'gmt',
        'date' => [
            'format' => 'H:i:s d:m:Y',
        ],
    ],
    'cookie' => [
        'domain' => '',
    ],
    'security' => [
        'encryption' => [
            'key' => 'my-production-secret-key',
        ],
    ],
);

/* Location: .app/production/config.php */
```

<a name="create-a-new-env-variable"></a>

#### Yeni Bir Ortam Değişkeni Yaratmak

Yeni bir ortam yaratmak için <kbd>app/environments.php</kbd> dosyasına ortam adını küçük harflerle girin. Aşağıdaki örnekte sunucu isimleri ile birlikte <kbd>qa</kbd> adında bir ortam yaratılıyor.

```php
return array(
    .
    .
    'production' => [ ... ]
    'qa' => [
        'example.hostname'
        'example2.hostname'
    ]
);
```

Local çevre ortamından yeni yaratılan ortama konfigürasyon dosyalarını kopyalayın.

```php
- app
    - local
        config.php
        database.php
    - qa
        config.php
        database.php
```