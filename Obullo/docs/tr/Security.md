
## Güvenlik Sınıfı

Şifre sınıfı uygulamanıza kaydettiğiniz kullanıcılar için Bcrypt algoritması ile güvenli şifreler üretir.

<ul>
    <li>
        <a href="#information">Önbilgi</a>
        <ul>
            <li><a href="#why-bcrypt">Neden Bcrypt Kullanmalıyım</a></li>
            <li><a href="#scheme-identifiers">Şema Tanımlayıcıları</a></li>
            <li><a href="#structure">Yapı</a></li>
            <li><a href="#diagram">Diagram</a></li>
        </ul>
    </li>

    <li>
        <a href="#running">Çalıştırma</a>
        <ul>
            <li><a href="#loading-component">Bileşeni Yüklemek</a></li>
            <li><a href="#secure-passwords">Güvenli Şifre Yaratmak</a></li>
            <li><a href="#verify">Güvenli Şifreyi Doğrulamak</a></li>
            <li><a href="#rehash">Güvenli Şifreyi Yeniden Şifreleme</a></li>
            <li><a href="#getinfo">Şifre Bilgileri</a></li>
        </ul>
    </li>

    <li><a href="#method-reference">Fonksiyon Referansı</a></li>
</ul>

<a name="information"></a>

### Önbilgi


## Csrf Katmanı

```php
$this->match(['get', 'post'], 'widgets/tutorials/hello_form')->middleware('Csrf');
```

#### İstisnai Durumlarda Csrf Katmanını Kapatmak

Csrf katmanı global olarak tanımlandığında tüm http POST isteklerinde çalışır.

```php
/**
 * Index
 *
 * @middleware->remove("Csrf");
 * 
 * @return void
 */
public function index()
{
    $this->view->load(
        'welcome',
        [
            'title' => 'Welcome to Obullo !',
        ]
    );
}
```

http://shiflett.org/articles/cross-site-request-forgeries

```html
<form action="buy.php" method="post">
<input type="hidden" name="<?php echo $this->c['csrf']->getTokenName() ?>" value="<?php echo $this->c['csrf']->getToken(); ?>" />
<p>
Symbol: <input type="text" name="symbol" /><br />
Shares: <input type="text" name="shares" /><br />
<input type="submit" value="Buy" />
</p>
</form>
```