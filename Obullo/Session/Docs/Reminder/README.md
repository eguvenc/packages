
## Reminder Class

This class control the session cookie lifetime. Using a "ttl" you can extend session life time of the current user.

```php
<?php
$c->load('session/reminder as reminder');
$this->reminder->method();
```
### Remember Me

```php
<?php
$this->reminder->rememberMe(604800);  // 1 week.
```

### Forget Me

Sets session cookie lifetime to "0". Session will be expired automatically when the browser window is closed.

```php
<?php
$this->reminder->forgetMe();
```

This functions mostly used in user login operations.

### Function Reference

------

#### $this->reminder->rememberMe(integer $ttl)

Control the session cookie and storage lifetime. Using a "ttl" you can extend session life time of the current user.

#### $this->reminder->forgetMe()

Sets session cookie lifetime to "0". Session will be expired automatically when the browser window is closed.