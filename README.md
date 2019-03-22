# Event Manager

[![Latest Stable Version](https://poser.pugx.org/phpextra/event-manager/v/stable.svg)](https://packagist.org/packages/phpextra/event-manager)
[![Total Downloads](https://poser.pugx.org/phpextra/event-manager/downloads.svg)](https://packagist.org/packages/phpextra/event-manager)
[![License](https://poser.pugx.org/phpextra/event-manager/license.svg)](https://packagist.org/packages/phpextra/event-manager)
[![Build Status](http://img.shields.io/travis/phpextra/event-manager.svg)](https://travis-ci.org/phpextra/event-manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpextra/event-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpextra/event-manager/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phpextra/event-manager/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phpextra/event-manager/?branch=master)


## How it works ?

Both the event and listener are (marker) interfaces. Events support inheritance.

## Examples

```php
class UserLoginEvent implements Event
{
    public $userId;
}

class UserListener implements Listener
{
    /**
     * Acts on UserLoginEvent or it's descendants
     */
    public function onUserLogin(UserLoginEvent $event)
    {
        echo "User listener 1";
    }

    /**
     * Act on any event
     */
    public function onAnyEvent(Event $event)
    {
        echo "User listener 2";
    }
}

$manager = new EventManager();
$manager->add(new UserListener());
$manager->emit(new UserLoginEvent($user));

```
Result:

```
> User listener 1
> User listener 2
```

## Installation (Composer)

```
composer require phpextra/event-manager:5.*
```

## Running tests

```
composer tests
```

## Running php-cs-fixer

```
composer fix
```

## Contributing

All code contributions must go through a pull request.
Fork the project, create a feature branch, and send me a pull request.
To ensure a consistent code base, you should make sure the code follows
the [coding standards](http://www.php-fig.org/psr/).
If you would like to help, take a look at the [list of issues](https://github.com/phpextra/event-manager/issues).

## Authors

Jacek Kobus - <kobus.jacek@gmail.com>

