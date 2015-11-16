
## Session Class

The Session Class permits you to maintain a user's "state" and track their activity while they browse your site. The Sess Class stores session meta data information for each user as json encoded data in your storage.

### Initializing the Class

------

Sessions will typically run each page load, **so you need to call session class** for each page load if you need.
For the most part the session class will run unattended in the background, so simply initializing the sess class will cause it to read & update sessions.

```php
<?php
$this->c['session'];
$this->session->method();
```

Once loaded, the Session object will be available using: <dfn>$this->session->method()</dfn>

#### Sesssion class has three type of handlers

<table>
    <thead>
        <tr>
            <th>Handler</th>
            <th>Drivers</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Cache</td>
            <td>Redis, Memcache, Memcached, Apc, File, Local Memory</td>
        </tr>
        <tr>
            <td>NoSQL</td>
            <td>Mongo</td>
        </tr>
        <tr>
            <td>Pdo</td>
            <td>Mysql, PostgreSQL</td>
        </tr>
    </tbody>
</table>

Mostly recommended <b>cache handler</b> which has many type of drivers. Local Memory Storage defined as a default handler.

#### Session Meta Data

Metadata is simply an array containing the following information:

<table>
    <thead>
        <tr>
            <th>Identifier</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>sid</td>
            <td>The user's unique Session ID.</td>
        </tr>
        <tr>
            <td>ip</td>
            <td>The user's IP Address</td>
        </tr>
        <tr>
            <td>ua</td>
            <td>The user's User Agent data (the first 50 characters of the browser data string)</td>
        </tr>
        <tr>
            <td>uid</td>
            <td>If user's "user_id" key available in session data we read your $_SESSION['user_id'] and copy it into meta data.</td>
        </tr>
        <tr>
            <td>uname</td>
            <td>If user's "username" key available in session data we read your $_SESSION['username'] and copy it into meta data.</td>
        </tr> 
        <tr>
            <td>la</td>
            <td>The "last activity" time stamp.</td>
        </tr>
    </tbody>
</table>

The above data is stored in a key as a <b>json_encoded</b> array with this prototype:

```php
<?php
array
(
     'sid'   => session_id()
     'ip'    => 'string - user IP address',
     'ua'    => 'string - user agent data',
     'uid'   => 'int - user id if its available in session data',
     'uname' => 'string - username if its available in session data',
     'la'    => 'int timestamp'
)
```

An example php session with json_encoded metadata:

```php
'username|s:10:"helloworld";_o2_meta|s:255:"{
    "sid":"3s08c265ao7lmk3hsqhhke1mn0","ip":"127.0.0.1",
    "ua":"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0",
    "la":1404743275}"';
```


#### Session Validator

Session class has a function called <b>isValid()</b>, it is used to validate user's session information with info stored in "metadata". The function compares session_id, ip address, and user_agent info.

Match data functionality has configurable items in your config.php

### Session Configuration

------

Edit your <kbd>config/local/config.php</kbd> file.

```php
<?php

'session' => array(
    'cookie' => array(
        'name'     => 'session',       // The name you want for the cookie
        'domain'   => '',              // Set to .your-domain.com for site-wide cookies
        'path'     => '/',             // Typically will be a forward slash
        'secure'   => false,           // When set to true, the cookie will only be set if a secure connection exists.
        'httpOnly' => false,           // When true the cookie will be made accessible only through the HTTP protocol
        'prefix'   => '',              // Set a prefix to your cookie
    ),
    'storageKey' => 'o2_sessions:',    // Your cache handler keeps session data in /o2_sessions folder using ":" colons.
    'lifetime'       => 7200,          // The number of SECONDS you want the session to last. By default " 2 hours ". "0" is no expiration.
    'expireOnClose'  => true,          // Whether to cause the session to expire automatically when the browser window is closed 
    'timeToUpdate'   => 1,             // How many seconds between framework refreshing "Session" meta data Information"
    'rememberMeSeconds' => 604800,     // Remember me ttl for session reminder class. By default " 1 Week ".
    'metaData' => array(
        'enabled' => true,
        'matchIp' => false,         // Whether to match the user's IP address when reading the session data
        'matchUserAgent' => true    // Whether to match the User Agent when reading the session data
    )
),

/* End of file config.php */
/* Location: .app/env/local/config.php */
```
### Session Cookie Preferences

------

You'll find the following Session Cookie related preferences in your <kbd>config.php</kbd> file:

<table>
    <thead>
        <tr>
            <th>Preference</th>
            <th>Default</th>
            <th>Options</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>cookie[name]</td>
            <td>session</td>
            <td>None</td>
            <td>The name you want the session cookie saved as.</td>
        </tr>
        <tr>
            <td>cookie[domain]</td>
            <td></td>
            <td>None</td>
            <td>Set to .your-domain.com for site-wide cookies</td>
        </tr>
        <tr>
            <td>cookie[path]</td>
            <td></td>
            <td>None</td>
            <td>Typically will be a forward slash</td>
        </tr>
        <tr>
            <td>cookie[secure]</td>
            <td>false</td>
            <td>true/false (boolean)</td>
            <td>When set to true, the cookie will only be set if a secure connection exists</td>
        </tr>
        <tr>
            <td>cookie[httpOnly]</td>
            <td>false</td>
            <td>true/false (boolean)</td>
            <td>When true the cookie will be made accessible only through the HTTP protocol</td>
        </tr>
        <tr>
            <td>cookie[prefix]</td>
            <td>None</td>
            <td></td>
            <td>Set a prefix to your cookie</td>
        </tr>
    </tbody>
</table>

### Session Preferences

You'll find the following Session related preferences in your <kbd>config.php</kbd> file:

<table>
    <thead>
        <tr>
            <th>Preference</th>
            <th>Default</th>
            <th>Options</th>
            <th>Description</th>
        </tr>
    </thead>
        <tr>
            <td>lifetime</td>
            <td>7200</td>
            <td>None</td>
            <td>The number of SECONDS you want the session to last. By default " 2 hours ". "0" is no expiration.</td>
        </tr>
        <tr>
            <td>expireOnclose</td>
            <td>false</td>
            <td>None</td>
            <td>Whether to cause the session to expire automatically when the browser window is closed</td>
        </tr>
        <tr>
            <td>timeToUpdate</td>
            <td>300</td>
            <td>Time in seconds</td>
            <td>This options controls how often the sess class will update session meta data.</td>
        </tr>
        <tr>
            <td>matchIp</td>
            <td>false</td>
            <td>true/false (boolean)</td>
            <td>Whether to match the user's IP address when reading the session data. Note that some ISPs dynamically changes the IP, so if you want a non-expiring session you will likely set this to false.</td>
        </tr>
        <tr>
            <td>matchUseragent</td>
            <td>true</td>
            <td>true/false (boolean)</td>
            <td>Whether to match the User Agent when reading the session data.</td>
        </tr>
        <tr>
            <td>rememberMeSeconds</td>
            <td>604800</td>
            <td></td>
            <td>Remember me ttl for session rememberMe() method. By default " 1 Week ".</td>
        </tr>
    </tbody>
</table>

### Retrieving Session Data

------

Any piece of information from the session array is available using the following function:

```php
<?php
$this->session->get('item');
```

Where <kbd>item</kbd> is the array index corresponding to the item you wish to fetch. For example, to fetch the session ID you will do this:

```php
<?php
$sessionID = $this->session->get('session_id');
```

**Note:** The function returns false (boolean) if the item you are trying to access does not exist.

### Adding Custom Session Data

------

A useful aspect of the session array is that you can add your own data to it and it will be stored in the user's cookie. Why would you want to do this? Here's one example:

Let's say a particular user logs into your site. Once authenticated, you could add their username and email address to the session cookie, making that data globally available to you without having to run a database query when you need it.

To add your data to the session array involves passing an array containing your new data to this function:

Where <samp>$array</samp> is an associative array containing your new data. Here's an example:

```php
<?php
$data = array(
                'username'  => 'johndoe',
                'email'     => 'johndoe@some-site.com',
                'logged_in' => true
            );

$this->session->set($data);
```

```php
<?php
$this->session->set('some_name', 'some_value');
```

### Removing Session Data

------

Just as $this->session->set() can be used to add information into a session, $this->session->remove() can be used to remove it, by passing the session key. For example, if you want to remove 'some_name' from your session information:

```php
<?php
$this->session->remove('some_name');
```

This function can also be passed an associative array of items to unset.

```php
<?php
$array = array('username' => '', 'email' => '');

$this->session->remove($array);
```

### Destroying a Session

------

To clear the current session:

```php
<?php
$this->session->destroy();
```

**Note:** This function should be the last one called, and even flash variables will no longer be available. If you want only some items to be destroyed and instead of all, use <kbd>$this->session->remove()</kbd>.


**Note:** Session meta data are only updated every "5" seconds by default to reduce processor load. If you repeatedly reload a page you'll notice that the "last activity" time only updates if "5" seconds or more has passed since the last time the cookie was written. This time is configurable by changing the <kbd>timeToUpdate</kbd> line in your <kbd>config.php</kbd> file.


### Regenerating Session Id

Regenerate the session ID and renew current session meta data, generation can safely be called in the middle of a session. if you use first parameter as <b>false</b> it will not delete the current session data and migrate your data to new session id contents.

```php
<?php
$this->session->reqenerateId(true);          // Regenerates session id and "deletes" old session
$this->session->reqenerateId(false);         // Regenerates session id and "migrates" old session data to new session_id.
$this->session->reqenerateId(false, 86400);  // Sets current session lifetime to 1 day.
```

Second parameter is max lifetime of current session.

### Function Reference

------

#### $this->session->set(mixed $data, $val = '', $prefix = '')

Stores a new session data to session container. You can send array data for first parameter.

#### $this->session->get(string $key)

Gets stored session from session container.

#### $this->session->get('session_id');

Returns to current session id.

#### $this->session->getName(string $key)

Returns to session name.

#### $this->session->exists()

Returns to "true" if session exist, session id not empty and its currently active otherwise "false".

#### $this->session->remove($data = mixed, $prefix = '')

Unsets a stored session data from session container. You can send array data for first parameter.

#### $this->session->regenerateId(boolean $deleteOldSession = true, int $lifetime)

Regenerate the session ID and renew current session meta data, generation can safely be called in the middle of a session.

#### $this->session->destroy()

Destroys the current session.