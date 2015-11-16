

## Model nedir ?

------

Modeller veritabanı ile haberleşmeyi sağlayan ve veritabanı fonksiyonları için tasarlanmış php sınıflarıdır. Örnek verecek olursak bir blog uygulaması yaptığımızı düşünelim bu uygulamada yer alan model sınıflarınıza <b>insert, update, delete</b> metotlarını ve veritabanı <b>get</b> metotları koymanız beklenir. Model sınıfı size uygulamada ayrı bir katman sağlar ve veritabanı kodlarınızı bu katmanda geliştirmeniz kodlarınızın sürekliliğine, esnekliğine ve test edilebilirliğine yardımcı olur.

Uygulamanızda model katmanı kullandığınızda <b>sorgu önbellekme</b>, <b>testler</b>, <b>veritabanı kodlarının bakımı</b> gibi problemler kolaylıkla çözülür.

### Modelleri Yüklemek

------


```php
$this->modelBar = new \Model\Foo\Bar;
$this->modelBar->method();
```

### Model Klasörü Yaratmak

Obullo model sınıflarını <b>app/classes/Model</b> klasöründen yükler. Aşağıdaki örnek, modellerin nasıl kullanılabileceği hakkında size bir fikir verebilir.

```php
+ app
 - classes
    - Model
		  Entry.php
```

Önce classes klasörü altında Model adında bir klasörünüz yoksa bu isimde bir klasör yaratın ve içerisine aşağıdaki gibi <b>Entry.php</b> adında bir dosya oluşturun. Model sınıflarını yaratırken aynı sınıf yapılarında olduğu gibi dosya adı ve klasör adları büyük harfle yazılmalıdır.

Lütfen aşağıdaki örneğe göz gezdirmeden önce ona ait sql kodunu veritabanınızda çalıştırın.

```php
--
-- Table structure for table `entries`
--

CREATE TABLE IF NOT EXISTS `entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

```

#### Entry.php

```php
namespace Model;

class Entry extends \Obullo\Database\Model
{
    public $title;
    public $content;
    public $date;

    /**
     * Get one entry
     *
     * @param integer $id user id
     * 
     * @return array
     */
    public function findOne($id = 1)
    {
        return $this->db->prepare("SELECT * FROM entries WHERE id = ?")
            ->bindParam(1, $id, \PDO::PARAM_INT)
            ->execute()->rowArray();
    }

    /**
     * Get all entries
     *
     * @param integer $limit number
     * 
     * @return array
     */
    public function findAll($limit = 10)
    {
        return $this->db->prepare("SELECT * FROM users LIMIT ?")
            ->bindParam(1, $limit, \PDO::PARAM_INT)
            ->execute()->resultArray();
    }

    /**
     * Insert entry
     * 
     * @return void
     */
    public function insert()
    {
        return $this->db->insert(
            'entries', 
            [
                'title' => $this->title, 
                'content' => $this->content,
                'date' => $this->date
            ], 
            [
                'title' => \PDO::PARAM_STR,
                'content' => \PDO::PARAM_STR,
                'date' => \PDO::PARAM_INT,
            ]
        );
    }

    /**
     * Update entry ( Example transaction )
     * 
     * @param integer $id id
     * 
     * @return void
     */
    public function update($id)
    {
        return $this->db->transactional(
            function () 
            {
                return $this->db->update(
                    'entries', 
                    [
                        'title' => $this->title, 
                        'content' => $this->content,
                        'date' => $this->date
                    ], 
                    ['id' => 1], 
                    [
                        'id' => \PDO::PARAM_INT,
                        'title' => \PDO::PARAM_STR,
                        'content' => \PDO::PARAM_STR,
                        'date' => \PDO::PARAM_INT,
                    ]
                );
            }
        );
    }

    /**
     * Delete entry
     * 
     * @param integer $id id
     * 
     * @return void
     */
    public function delete($id)
    {
        return $this->db->delete('entries', ['id' => $id], ['id' => \PDO::PARAM_INT]);
    }

    /**
     * Loader
     * 
     * @return void
     */
    public function load()
    {
        $this->c['db'];
    }

}

/* Location: .model/Entry.php */
```

Yukarıdaki örnekteki uygulamaya özgü <b>findAll()</b> ve <b>findOne()</b> metotları veritabanından <b>okuma</b> işlemleri yaparken diğer metotlar veritabanına <b>yazma</b> işlemi yaparlar. Eğer veritabanına veri kaybı olmadan ( transactions ) yazma işlemleri yapmak istiyorsak kodlarımızı aşağıdaki gibi <b>transaction()</b> metodu içerisinde kullanmamız gerekir.

```php
namespace Model;

class Entry extends \Obullo\Database\Model
{
    /**
     * Insert entry
     * 
     * @return void
     */
    public function insert()
    {
        $data = [
                    'title' => $this->title, 
                    'content' => $this->content,
                    'date' => $this->date
        ];

        return $this->db->transactional(
            function () use ($data) {

                return $this->db->insert(
                    'entries', 
                    $data, 
                    [
                        'title' => \PDO::PARAM_STR,
                        'content' => \PDO::PARAM_STR,
                        'date' => \PDO::PARAM_INT,
                    ]
                );
            }
        );
    }

}
```

Transaction metodu, içerisine konulan isimsiz fonksiyonları çalıştırır ve çalışma aşamasında <b>commit</b> işlemi başarılı ise işlemi veritabanına kaydeder. Kaydetme işlemi başarılı olduğunda metot içerisindeki isimsiz fonksiyonun sonucuna geri dönülür aksi durumda uygulama bir <b>PDOException</b> hatası fırlatır ve hata mesajı görüntülenirken yapılan yazma işlemleri içeriden <b>rollBack</b> metodu ile geri alınır. Eğer veritabanına kaydettiğiniz veriler kritik düzeyde önemli veriler ise veri kaybı olmadan kayıt işlemleri için mutlaka yazma işlemlerinde transaction metodu kullanmanız tavsiye edilir.

> **Not:** PDOException ve diğer RuntimeException hataları <b>app/errors.php</b> dosyasından kontrol edilirler.


Şimdi de entry modelini kontrolör sınıfı içerisinde nasıl kullanacağımıza dair bir örnek yapalım.


```php
namespace Welcome;

class Welcome extends \Controller
{
    public function load()
    {
        $this->entry = new \Model\Entry;
    }

    public function index()
    {
    	$rowArray = $this->entry->findOne(1);
		print_r($rowArray);
    }

    public function insert()
    {
        $this->entry->title = 'Insert Test';
        $this->entry->content = 'Hello World';
        $this->entry->date = time();
        $this->entry->insert();

        echo 'New entry added.';
    }

    public function update($id)
    {
        $this->entry->title = 'Update Test';
        $this->entry->content = 'Welcome to my world';
        $this->entry->date = time();
        $this->entry->update($id);  // Transaction example
                                    // Globally catch the PDOException Errors using app/errors.php
                                    // or use try catch
        echo 'Entry updated.';    
    }

    public function delete($id)
    {
        $this->entry->delete($id);
    }
}

/* Location: .modules/welcome/welcome.php */
```