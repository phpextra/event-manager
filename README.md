#Event Manager
[![Latest Stable Version](https://poser.pugx.org/phpextra/event-manager/v/stable.svg)](https://packagist.org/packages/phpextra/event-manager)
[![Total Downloads](https://poser.pugx.org/phpextra/event-manager/downloads.svg)](https://packagist.org/packages/phpextra/event-manager)
[![License](https://poser.pugx.org/phpextra/event-manager/license.svg)](https://packagist.org/packages/phpextra/event-manager)
[![Build Status](http://img.shields.io/travis/phpextra/event-manager.svg)](https://travis-ci.org/phpextra/event-manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpextra/event-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpextra/event-manager/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phpextra/event-manager/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phpextra/event-manager/?branch=master)
[![GitTip](http://img.shields.io/gittip/jkobus.svg)](https://www.gittip.com/jkobus)

Library is under active development and it aims to be **simple** and **fast**. All pull requests and bug reports are welcome.

## How it works ?

Both event and listener are interfaces.
Events support inheritance. It means that listener can listen for event parents.
If priority of workers is equal, **LIFO** order applies.


## Examples

```php
class UserLoginEvent implements EventInterface
{
    protected $user;

    public function __construct($user){ ... }

    (...)
}

class UserListener implements Listener
{
    /**
     * Listen only UserLoginEvent
     *
     * @priority HIGH
     */
    public function onUserLogin(UserLoginEvent $event)
    {
        $event->user ...
        echo "User listener 1";
    }

    /**
     * Listen for ANY event
     *
     * @priority NORMAL
     */
    public function onAnyEvent(EventInterface $event)
    {
        if($event instanceof UserLoginEvent){
            $event->user ...
            echo "User listener 2";
        }
    }
}

$manager = new EventManager();
$manager->addListener(new UserListener());
$manager->trigger(new UserLoginEvent($user));

```
Result:

```
> User listener 1
> User listener 2
```

Anonymous function can be a listener too. Priority in this case can be specified as a second constructor param in
AnonymousListener class.

```php
$listener = new AnonymousListener(function(UserLoginEvent $event){
    $event->user ...
}), Priority::LOWEST);
```

## Installation (Composer)

```json
{
    "require": {
        "phpextra/event-manager":"~1.0"
    }
}
```

##Running tests

```
// Windows
composer install & call ./vendor/bin/phpunit.bat ./tests
```

##Contributing

All code contributions must go through a pull request.
Fork the project, create a feature branch, and send me a pull request.
To ensure a consistent code base, you should make sure the code follows
the [coding standards](http://symfony.com/doc/2.0/contributing/code/standards.html).
If you would like to help take a look at the [list of issues](https://github.com/phpextra/event-manager/issues).

##Requirements

See **composer.json** for a full list of dependencies.

##Authors

Jacek Kobus - <kobus.jacek@gmail.com>

## License information

    See the file LICENSE.md for copying permission.

