
## Http Uri Sınıfı

Uri sınıfı url adresinden gelen string türündeki verileri almayı sağlar. Eğer URI route yapısı kullanıldıysa yeniden route edilmiş segmentleri de almaya yardımcı olur. Http paketi içinde yer alan uri sınıfı <a href="http://www.php-fig.org/psr/psr-7/" target="_blank">Psr7</a> Standartlarını destekler ve <a href="https://github.com/zendframework/zend-diactoros" target="_blank">Zend-Diactoros</a> ailesinin üyelerinden biridir.

<ul>
    <li><a href="#url-and-uri">Url ve Uri Nedir ?</a></li>
    <li>
        <a href="#how-it-works">Nasıl Çalışıyor ?</a>
        <ul>
            <li><a href="#grabbing-uri">Sınıfı Çağırmak</a></li>
            <li><a href="#resolving-url">Url Çözümleme</a></li>
        </ul>
    </li>
    <li><a href="#special-methods">Özel Metotlar</a></li>
    <li><a href="#with-methods">With Metotları</a></li>
    <li>
        <a href="#get-methods">Get metotları</a>
        <ul>
            <li><a href="#getScheme">$uri->getScheme()</a></li>
            <li><a href="#getAuthority">$uri->getAuthority()</a></li>
            <li><a href="#getUserInfo">$uri->getUserInfo()</a></li>
            <li><a href="#getHost">$uri->getHost()</a></li>
            <li><a href="#getPort">$uri->getPort()</a></li>
            <li><a href="#getPath">$uri->getPath()</a></li>
            <li><a href="#getQuery">$uri->getQuery()</a></li>
            <li><a href="#getFragment">$uri->getFragment()</a></li>
            <li><a href="#getRequestUri">$uri->getRequestUri()</a></li>
            <li><a href="#getSegments">$uri->getSegments()</a></li>
            <li><a href="#getRoutedSegments">$uri->getRoutedSegments()</a></li>
            <li><a href="#segment">$uri->segment()</a></li>
            <li><a href="#rsegment">$uri->rsegment()</a></li>
        </ul>
    </li>
</ul>


<a name="url-and-uri"></a>

### Url ve Uri Nedir ? 

URL (Uniform Resource Locator) web üzerindeki bir kaynağın konumu gösterir, URI (Uniform Resource Identifier) ise diğer kaynaklardan ayıran tanımlayıcı ismini belirtir. 
Her URL , URI'dir ancak her URI , URL değildir. Bazı URI'ler bir adres olmasına rağmen gerçek bir kaynağı göstermeyebilir, sadece tanımlayıcıdır. Url bu nedenle daha genel bir terimdir.

<a name="how-it-works"></a>

### Nasıl Çalışıyor ?

Dışarıdan gelen bir http isteği ServerRequestFactory sınıfı tarafından dinlenerek çözümlenir ve elde edilen değişkenler ile Uri sınıfı yaratılır.

<a name="grabbing-uri"></a>

#### Sınıfı Çağırmak

Uri sınıfı request sınıfı içerisinden çağırılır.

```php
$uri = $request->getUri() 
```

<a name="resolving-url"></a>

#### Url Çözümleme

Uri sınıfı dışarıdan gelen bir http isteğini,

```php
http://example.com/welcome/index?a=1&y=2
```

aşağıdaki gibi çözümler.

```php
echo $uri : http://example.com/welcome/index?a=1&y=2
echo $uri->getScheme() : http
echo $uri->getAuthority() : example.com
echo $uri->getHost() : example.com
echo $uri->getPort() : 
echo $uri->getPath() : /welcome/index
echo $uri->getQuery() : a=1&y=2
echo $uri->getFragment() : 
```

<a name="special-methods"></a>

### Özel Metotlar

Çerçeveye özgü metotlar size yardımcı olarak url adresinin tümünü yada belirli parçalarını alabilmenize olanak sağlar. Psr7 standartı <kbd>$uri->getPath()</kbd> metodu protokol,host,port ve sorgu değişkenleri olmadan bir url adresinin bütününü verir.

```php
echo $uri->getPath() : /welcome/index
```

Fakat url adresi ile birlikte varsa sorgu değişkenlerinin tümünü alabilmek <kbd>$uri->getRequestUri()</kbd> adlı psr7 standartı olmayan özel bir metot ile mümkün olur.

```php
echo $uri->getRequestUri() : /welcome/index?a=1&y=2
```

Parçalanan url bir dizi içerisinde toplanır çerçeveye özgü <kbd>$uri->getSegments()</kbd> metodu ile numaranlandırılmış parçaların tümüne ulaşılabilir.

```php
print_r($uri->getSegments()) : Array
(
    [0] => welcome
    [1] => index
)
```

Parçalara tek tek ulaşmak için <kbd>$uri->segment(n)</kbd> metodu kullanılır.

```php
echo $uri->segment(0) : welcome
```

<a name="with-methods"></a>

### With Metotları

Uri sınıfında with öneki ile başlayan metotları kullanarak uri nesnesine etki etmek mümkündür. Aşağıdaki gibi bir web url adresimizin olduğunu varsayarsak.

```php
echo $uri : http://example.com/welcome/index
```

Aşağıdaki metotlar ile istediğimiz türde uri'ler elde edebiliriz.

```php
$uri->withScheme("https") : https://example.com/welcome/index
$uri->withUserInfo("test", "123456") : http://test:123456@example.com/welcome/index
$uri->withHost("example") : http://example.com/welcome/index
$uri->withPort("9898") : http://example.com:9898/welcome/index
$uri->withPath("/example.php") : http://example.com/example.php
$uri->withQuery("a=1&b=2") : http://example.com/welcome/index?a=1&b=2
$uri->withFragment("anchor") : http://example.com/welcome/index#anchor
```

<a name="get-methods"></a>

### Get Metotları

<a name="getScheme"></a> 

#### $uri->getScheme()

Uri protokolüne geri döner. Örneğin <kbd>https, ftp, http</kbd>.

<a name="getAuthority"></a> 

#### $uri->getAuthority()

Yetki alanına geri döner. Örneğin <kbd>example.com</kbd>.

<a name="getUserInfo"></a> 

#### $uri->getUserInfo()

Eğer uri <kbd>test:123456@yetkiAlanı</kbd> gibi bir kullanıcı bilgisi içerisiyorsa "username[:password]" biçimini içeren string türüne döner. Eğer bir kullanıcı verisi yoksa metot boş string türüne döner.

<a name="getHost"></a> 

#### $uri->getHost()

Host adresine geri döner. Örneğin <kbd>example.com</kbd>.

<a name="getPort"></a> 

#### $uri->getPort()

Eğer url sayısal bir port değeri içerisiyorsa bu değere aksi durumda <b>null</b> değerine geri döner.

<a name="getPath"></a> 

#### $uri->getPath()

Uri path bileşeni varsa <kbd>/welcome/index</kbd> gibi örnek bir değer aksi durumda <kbd>/</kbd> karakteri elde edilir.

<a name="getQuery"></a> 

#### $uri->getQuery()

Uri içerisinde sorgu değişkenlerine geri döner. Örneğin <kbd>x=1&y=2</kbd>

<a name="getFragment"></a> 

#### $uri->getFragment()

Uri içerisinde <kbd>#</kbd> karakteri önüne gelen değeri verir.

<a name="getRequestUri"></a> 

#### $uri->getRequestUri()

Url <kbd>$uri->getPath()</kbd> ve <kbd>$uri->getQuery()</kbd> metotlarının birleşimini verir. Örneğin <kbd>/welcome/index?a=1&y=2</kbd>

<a name="getSegments"></a> 

#### $uri->getSegments()

Tüm uri segmentlerine geri döner. Örnek:

```php
$segments = $uri->getSegments();

foreach ($segments as $segment)
{
    echo $segment;
    echo '<br />';
}
```

Çıktı

```php
welcome
index
```

<a name="getRoutedSegments"></a> 

#### $uri->getRoutedSegments()

Bu method işlev olarak bir önceki metodun aynısıdır, tek farkı route işlemlerine duyarlı segmentlerin elde edilmesidir.

<a name="segment"></a> 

#### $uri->segment(n, $noResult = null)

Spesifik bir segment değerine geri döner. Metod içerisine elde edilmek istenen segmentin numarası (n) girilir. Segmentler soldan sağa doğru numaralandırılır. Örneğin aşağıdaki gibi bir URL adresimiz varsa:

```php
http://example.com/sports/basketball/nba/score_history
```

Segment numaralandırılması aşağıdaki gibi olur.

* (0) sports
* (1) basketball
* (2) nba
* (3) score_history

Eğer olmayan bir numara girilirse method <kbd>null</kbd> değerine geri döner. İkinci parametre ise opsiyoneldir. Eğer girilen segment numarası mevcut değilse method bu durumda ikinci parametrede belirtilen değere döner.

```php
http://example.com/sports/basketball/team/
```

```php
$id = $uri->segment(3, 0); // 0
```

Yukarıdaki kod aşağıdaki yazımdan kaçmak için kullanılır.

```php
http://example.com/sports/basketball/team/5
```

```php
if ($uri->segment(3) === null)
{
    $id = 0;
}
else
{
    $id = $uri->segment(3);  // 5
}
```

<a name="rsegment"></a> 

#### $uri->rsegment(n)

Bu method işlev olarak bir önceki metodun aynısıdır, tek farkı route işlemlerine duyarlı segmentlerin elde edilmesidir.