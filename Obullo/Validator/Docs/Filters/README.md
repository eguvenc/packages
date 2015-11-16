
## Doğrulayıcı Giriş Filtreleri ( Validator Input Filters )

------

Doğrulayıcı giriş filtreleri, doğrulayıcı sınıfında çalışmaktadır. Form girdilerinin sadece posta ($_POST) verilerini filtreledikten sonra veri geçişlerine izin veren sınıftır. Giriş filtreleri PHP'nin <a href="http://php.net/manual/tr/function.filter-input-array.php" target="_blank">filter_input_array()</a> fonksiyonunu kullanmaktadır.

## Doğrulayıcı Sınıfını yüklemek

```php
$this->c['validator'];
$this->validator->method();
```

## Sabitler ( Constants )

### Filtreleri Doğrula ( <a href="http://php.net/manual/tr/filter.filters.flags.php" target="_blank">Validate Filters</a> )

<table style="min-width:1100px">
<caption><strong>Listing of filters for validation</strong></caption>

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Options</th>
<th>Flags</th>
<th>Description</th>
</tr>

</thead>

<tbody class="tbody">
<tr>
<td><strong><code>FILTER_VALIDATE_BOOLEAN</code></strong></td>
<td>"boolean"</td>
<td>
 <code class="parameter">default</code>
</td>
<td>
  <strong><code>FILTER_NULL_ON_FAILURE</code></strong>
</td>
<td>
 <p class="para">
  Returns <strong><code>TRUE</code></strong> for "1", "true", "on" and "yes".
  Returns <strong><code>FALSE</code></strong> otherwise.
 </p>
 <p class="para">
  If <strong><code>FILTER_NULL_ON_FAILURE</code></strong> is set, <strong><code>FALSE</code></strong> is
  returned only for "0", "false", "off", "no", and "", and
  <strong><code>NULL</code></strong> is returned for all non-boolean values.
 </p>
</td>
</tr>

<tr>
<td><strong><code>FILTER_VALIDATE_EMAIL</code></strong></td>
<td>"validate_email"</td>
<td>
 <code class="parameter">default</code>
</td>
<td class="empty">&nbsp;</td>
<td>Validates value as e-mail.</td>
</tr>

<tr>
<td><strong><code>FILTER_VALIDATE_FLOAT</code></strong></td>
<td>"float"</td>
<td>
 <code class="parameter">default</code>,
 <code class="parameter">decimal</code>
</td>
<td>
 <strong><code>FILTER_FLAG_ALLOW_THOUSAND</code></strong>
</td>
<td>Validates value as float.</td>
</tr>

<tr>
<td><strong><code>FILTER_VALIDATE_INT</code></strong></td>
<td>"int"</td>
<td>
 <code class="parameter">default</code>,
 <code class="parameter">min_range</code>,
 <code class="parameter">max_range</code>
</td>
<td>
 <strong><code>FILTER_FLAG_ALLOW_OCTAL</code></strong>,
 <strong><code>FILTER_FLAG_ALLOW_HEX</code></strong>
</td>
<td>Validates value as integer, optionally from the specified range.</td>
</tr>

<tr>
<td><strong><code>FILTER_VALIDATE_IP</code></strong></td>
<td>"validate_ip"</td>
<td>
 <code class="parameter">default</code>
</td>
<td>
 <strong><code>FILTER_FLAG_IPV4</code></strong>,
 <strong><code>FILTER_FLAG_IPV6</code></strong>,
 <strong><code>FILTER_FLAG_NO_PRIV_RANGE</code></strong>,
 <strong><code>FILTER_FLAG_NO_RES_RANGE</code></strong>
</td>
<td>
 Validates value as IP address, optionally only IPv4 or IPv6 or not
 from private or reserved ranges.
</td>
</tr>

<tr>
<td><strong><code>FILTER_VALIDATE_REGEXP</code></strong></td>
<td>"validate_regexp"</td>
<td>
 <code class="parameter">default</code>,
 <code class="parameter">regexp</code>
</td>
<td class="empty">&nbsp;</td>
<td>
 Validates value against <code class="parameter">regexp</code>, a
 <a class="link" target="_blank" href="http://php.net/manual/tr/book.pcre.php">Perl-compatible</a> regular expression.
</td>
</tr>

<tr>
<td><strong><code>FILTER_VALIDATE_URL</code></strong></td>
<td>"validate_url"</td>
<td>
 <code class="parameter">default</code>
</td>
<td>
 <strong><code>FILTER_FLAG_PATH_REQUIRED</code></strong>,
 <strong><code>FILTER_FLAG_QUERY_REQUIRED</code></strong>
</td>
<td>Validates value as URL (according to <a class="link external" href="http://www.faqs.org/rfcs/rfc2396">»&nbsp;http://www.faqs.org/rfcs/rfc2396</a>), optionally with required components. Beware a valid URL may not specify the HTTP protocol <code class="parameter">http://</code> so further validation may be required to determine the URL uses an expected protocol, e.g. <code class="parameter">ssh://</code> or <code class="parameter">mailto:</code>. Note that the function will only find ASCII URLs to be valid; internationalized domain names (containing non-ASCII characters) will fail.</td>
</tr>

</tbody>

</table>

### Filtreleri Sterilize Edin ( <a href="http://php.net/manual/tr/filter.filters.sanitize.php" target="_blank">Sanitize Filters</a> )

<table style="min-width:1100px">
<caption><strong>List of filters for sanitization</strong></caption>

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Options</th>
<th>Flags</th>
<th>Description</th>
</tr>

</thead>

<tbody class="tbody">
<tr>
<td><strong><code>FILTER_SANITIZE_EMAIL</code></strong></td>
<td>"email"</td>
<td class="empty">&nbsp;</td>
<td class="empty">&nbsp;</td>
<td>
 Remove all characters except letters, digits and
 <em>!#$%&amp;'*+-/=?^_`{|}~@.[]</em>.
</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_ENCODED</code></strong></td>
<td>"encoded"</td>
<td class="empty">&nbsp;</td>
<td>
 <strong><code>FILTER_FLAG_STRIP_LOW</code></strong>,
 <strong><code>FILTER_FLAG_STRIP_HIGH</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_LOW</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_HIGH</code></strong>
</td>
<td>URL-encode string, optionally strip or encode special characters.</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_MAGIC_QUOTES</code></strong></td>
<td>"magic_quotes"</td>
<td class="empty">&nbsp;</td>
<td class="empty">&nbsp;</td>
<td>Apply <span class="function"><a class="function" target="_blank" href="http://php.net/manual/tr/function.addslashes.php">addslashes()</a></span>.</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_NUMBER_FLOAT</code></strong></td>
<td>"number_float"</td>
<td class="empty">&nbsp;</td>
<td>
 <strong><code>FILTER_FLAG_ALLOW_FRACTION</code></strong>,
 <strong><code>FILTER_FLAG_ALLOW_THOUSAND</code></strong>,
 <strong><code>FILTER_FLAG_ALLOW_SCIENTIFIC</code></strong>
</td>
<td>
 Remove all characters except digits, <em>+-</em> and
 optionally <em>.,eE</em>.
</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_NUMBER_INT</code></strong></td>
<td>"number_int"</td>
<td class="empty">&nbsp;</td>
<td class="empty">&nbsp;</td>
<td>
 Remove all characters except digits, plus and minus sign.
</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_SPECIAL_CHARS</code></strong></td>
<td>"special_chars"</td>
<td class="empty">&nbsp;</td>
<td>
 <strong><code>FILTER_FLAG_STRIP_LOW</code></strong>,
 <strong><code>FILTER_FLAG_STRIP_HIGH</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_HIGH</code></strong>
</td>
<td>
 HTML-escape <em>'"&lt;&gt;&amp;</em> and characters with
 ASCII value less than 32, optionally strip or encode other special
 characters.
</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_FULL_SPECIAL_CHARS</code></strong></td>
<td>"full_special_chars"</td>
<td class="empty">&nbsp;</td>
<td>
 <strong><code>FILTER_FLAG_NO_ENCODE_QUOTES</code></strong>,
</td>
<td>
 Equivalent to calling <span class="function"><a class="function" target="_blank" href="http://php.net/manual/tr/function.htmlspecialchars.php">htmlspecialchars()</a></span> with <strong><code>ENT_QUOTES</code></strong> set. Encoding quotes can
 be disabled by setting <strong><code>FILTER_FLAG_NO_ENCODE_QUOTES</code></strong>. Like <span class="function"><a class="function" target="_blank" href="http://php.net/manual/tr/function.htmlspecialchars.php">htmlspecialchars()</a></span>, this
 filter is aware of the <a class="link" target="_blank" href="http://php.net/manual/tr/ini.core.php#ini.default-charset">default_charset</a> and if a sequence of bytes is detected that
 makes up an invalid character in the current character set then the entire string is rejected resulting in a 0-length string.
 When using this filter as a default filter, see the warning below about setting the default flags to 0.
</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_STRING</code></strong></td>
<td>"string"</td>
<td class="empty">&nbsp;</td>
<td>
 <strong><code>FILTER_FLAG_NO_ENCODE_QUOTES</code></strong>,
 <strong><code>FILTER_FLAG_STRIP_LOW</code></strong>,
 <strong><code>FILTER_FLAG_STRIP_HIGH</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_LOW</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_HIGH</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_AMP</code></strong>
</td>
<td>Strip tags, optionally strip or encode special characters.</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_STRIPPED</code></strong></td>
<td>"stripped"</td>
<td class="empty">&nbsp;</td>
<td class="empty">&nbsp;</td>
<td>Alias of "string" filter.</td>
</tr>

<tr>
<td><strong><code>FILTER_SANITIZE_URL</code></strong></td>
<td>"url"</td>
<td class="empty">&nbsp;</td>
<td class="empty">&nbsp;</td>
<td>
 Remove all characters except letters, digits and
 <em>$-_.+!*'(),{}|\\^~[]`&lt;&gt;#%";/?:@&amp;=</em>.
</td>
</tr>

<tr>
<td><strong><code>FILTER_UNSAFE_RAW</code></strong></td>
<td>"unsafe_raw"</td>
<td class="empty">&nbsp;</td>
<td>
 <strong><code>FILTER_FLAG_STRIP_LOW</code></strong>,
 <strong><code>FILTER_FLAG_STRIP_HIGH</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_LOW</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_HIGH</code></strong>,
 <strong><code>FILTER_FLAG_ENCODE_AMP</code></strong>
</td>
<td>
 Do nothing, optionally strip or encode special characters. This
 filter is also aliased to <strong><code>FILTER_DEFAULT</code></strong>.
</td>
</tr>

</tbody>

</table>

### Diğer Bayraklar ( <a href="http://php.net/manual/tr/filter.filters.misc.php" target="_blank">Other Flags</a> )

<table style="min-width:1100px">
<caption><strong>List of miscellaneous filters</strong></caption>

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Options</th>
<th>Flags</th>
<th>Description</th>
</tr>

</thead>

<tbody class="tbody">
<tr>
<td><strong><code>FILTER_CALLBACK</code></strong></td>
<td>"callback"</td>
<td><span class="type"><a class="type callable" target="_blank" href="http://php.net/manual/tr/language.types.callable.php">callable</a></span> function or method</td>
<td class="empty">&nbsp;</td>
<td>Call user-defined function to filter data.</td>
</tr>

</tbody>

</table>

### Filtre Bayrakları ( <a href="http://php.net/manual/tr/filter.filters.flags.php" target="_blank">Filter Flags</a> )

<table style="min-width:1100px">
<caption><strong>List of filter flags</strong></caption>

 <thead>
  <tr>
   <th>ID</th>
   <th>Used with</th>
   <th>Description</th>
  </tr>

 </thead>

 <tbody class="tbody">
  <tr>
   <td><strong><code>FILTER_FLAG_STRIP_LOW</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_ENCODED</code></strong>,
    <strong><code>FILTER_SANITIZE_SPECIAL_CHARS</code></strong>,
    <strong><code>FILTER_SANITIZE_STRING</code></strong>,
    <strong><code>FILTER_UNSAFE_RAW</code></strong>
   </td>
   <td>
    Strips characters that has a numerical value &lt;32.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_STRIP_HIGH</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_ENCODED</code></strong>,
    <strong><code>FILTER_SANITIZE_SPECIAL_CHARS</code></strong>,
    <strong><code>FILTER_SANITIZE_STRING</code></strong>,
    <strong><code>FILTER_UNSAFE_RAW</code></strong>
   </td>
   <td>
    Strips characters that has a numerical value &gt;127.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ALLOW_FRACTION</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_NUMBER_FLOAT</code></strong>
   </td>
   <td>
    Allows a period (<em>.</em>) as a fractional separator in
    numbers.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ALLOW_THOUSAND</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_NUMBER_FLOAT</code></strong>,
    <strong><code>FILTER_VALIDATE_FLOAT</code></strong>
   </td>
   <td>
    Allows a comma (<em>,</em>) as a thousands separator in
    numbers.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ALLOW_SCIENTIFIC</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_NUMBER_FLOAT</code></strong>
   </td>
   <td>
    Allows an <em>e</em> or <em>E</em> for scientific
    notation in numbers.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_NO_ENCODE_QUOTES</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_STRING</code></strong>
   </td>
   <td>
    If this flag is present, single (<em>'</em>) and double
    (<em>"</em>) quotes will not be encoded.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ENCODE_LOW</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_ENCODED</code></strong>,
    <strong><code>FILTER_SANITIZE_STRING</code></strong>,
    <strong><code>FILTER_SANITIZE_RAW</code></strong>
   </td>
   <td>
    Encodes all characters with a numerical value &lt;32.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ENCODE_HIGH</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_ENCODED</code></strong>,
    <strong><code>FILTER_SANITIZE_SPECIAL_CHARS</code></strong>,
    <strong><code>FILTER_SANITIZE_STRING</code></strong>,
    <strong><code>FILTER_SANITIZE_RAW</code></strong>
   </td>
   <td>
    Encodes all characters with a numerical value &gt;127.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ENCODE_AMP</code></strong></td>
   <td>
    <strong><code>FILTER_SANITIZE_STRING</code></strong>,
    <strong><code>FILTER_SANITIZE_RAW</code></strong>
   </td>
   <td>
    Encodes ampersands (<em>&amp;</em>).
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_NULL_ON_FAILURE</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_BOOLEAN</code></strong>
   </td>
   <td>
    Returns <strong><code>NULL</code></strong> for unrecognized boolean values.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ALLOW_OCTAL</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_INT</code></strong>
   </td>
   <td>
    Regards inputs starting with a zero (<em>0</em>) as octal
    numbers. This only allows the succeeding digits to be
    <em>0-7</em>.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_ALLOW_HEX</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_INT</code></strong>
   </td>
   <td>
    Regards inputs starting with <em>0x</em> or
    <em>0X</em> as hexadecimal numbers. This only allows
    succeeding characters to be <em>a-fA-F0-9</em>.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_IPV4</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_IP</code></strong>
   </td>
   <td>
    Allows the IP address to be in IPv4 format.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_IPV6</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_IP</code></strong>
   </td>
   <td>
    Allows the IP address to be in IPv6 format.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_NO_PRIV_RANGE</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_IP</code></strong>
   </td>
   <td>
    <p class="para">
     Fails validation for the following private IPv4 ranges:
     <em>10.0.0.0/8</em>, <em>172.16.0.0/12</em> and
     <em>192.168.0.0/16</em>.
    </p>
    <p class="para">
     Fails validation for the IPv6 addresses starting with
     <em>FD</em> or <em>FC</em>.
    </p>
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_NO_RES_RANGE</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_IP</code></strong>
   </td>
   <td>
    Fails validation for the following reserved IPv4 ranges:
    <em>0.0.0.0/8</em>, <em>169.254.0.0/16</em>,
    <em>192.0.2.0/24</em> and <em>224.0.0.0/4</em>.
    This flag does not apply to IPv6 addresses.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_PATH_REQUIRED</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_URL</code></strong>
   </td>
   <td>
    Requires the <acronym title="Uniform Resource Locator">URL</acronym> to contain a path part.
   </td>
  </tr>

  <tr>
   <td><strong><code>FILTER_FLAG_QUERY_REQUIRED</code></strong></td>
   <td>
    <strong><code>FILTER_VALIDATE_URL</code></strong>
   </td>
   <td>
    Requires the <acronym title="Uniform Resource Locator">URL</acronym> to contain a query string.
   </td>
  </tr>

 </tbody>

</table>

### Kullanım Örnekleri

<blockquote>Filtrelerin tanımlamaları için setRules() metodunun 4. parametresi kullanılmaktadır. Bu parametre üzerinden ister tek sabit değişken (constant), ister dizi olarak gönderilebilmektedir.</blockquote>

```php
<?php
// $_POST['username'] = '<script>test</script>';
// $_POST['password'] = 12345;

$this->validator->setRules('username', 'Username', 'required', FILTER_SANITIZE_STRIPPED);
$this->validator->setRules('password', 'Password', 'required|min(5)');

if ($this->validator->isValid()) { // true
    $username = $this->validator->getValue('username');
    $password = $this->validator->getValue('password');

    var_dump($username); // string 'test' (length=4)
}
```

```php
<?php
// $_POST['user_id'] = '2';

$this->validator->setRules(
    'user_id', 'User ID', 'required',
    array(
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_REQUIRE_SCALAR,
    )
);

if ($this->validator->isValid()) { // false
    // success process
} else {
    $userId = $this->validator->getValue('user_id');
    var_dump($userId); // bool(false)
}
```