#Event Manager
[![Latest Stable Version](https://poser.pugx.org/phpextra/event-manager/v/stable.svg)](https://packagist.org/packages/phpextra/event-manager)
[![Total Downloads](https://poser.pugx.org/phpextra/event-manager/downloads.svg)](https://packagist.org/packages/phpextra/event-manager)
[![License](https://poser.pugx.org/phpextra/event-manager/license.svg)](https://packagist.org/packages/phpextra/event-manager)
[![Build Status](http://img.shields.io/travis/phpextra/event-manager.svg)](https://travis-ci.org/phpextra/event-manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpextra/event-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpextra/event-manager/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phpextra/event-manager/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phpextra/event-manager/?branch=master)

Library is under active development and it aims to be **simple** and **fast**. All pull requests and bug reports are welcome.

## How it works ?

Both event and listener are interfaces. Events support inheritance.
If priority of listeners is equal, **LIFO** order applies.

## Examples

```php
class UserLoginEvent implements Event
{
    public $user;

    public function __construct(User $user){ ... }

    (...)
}

class UserListener implements Listener
{
    /**
     * Act on UserLoginEvent or its children with high priority
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
    public function onAnyEvent(Event $event)
    {
        if($event instanceof UserLoginEvent){
            $event->user ...
            echo "User listener 2";
        }
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

Anonymous function can also be a listener. Priority in this case can be specified as a constructor param:

```php
$listener = new AnonymousListener(function(UserLoginEvent $event){
    $event->user ...
}), Priority::LOWEST);
```

## Integration with [Silex](http://silex.sensiolabs.org/)

Service provider automatically extends the event dispatcher by replacing the ```$app['dispatcher_class']```.

In the debug mode (```$app['debug'] = true```) the provider will:
- automatically inject the ```$app['logger']``` into ```EventManager```'s instance,
- grab a ```$app['stopwatch']``` instance (will use ```NullStopwatch``` if web profiler is not present),
- set the ```EventManager::throwExceptions()``` flag to ```true```

### Examples:

```php

$app = new Application();
$app->register(new EventManagerServiceProvider());

$app['event_manager']->add(new HomePageRequestListener());

$app->get('/', function(Application $app){
    $app['event_manager']->emit(new HomePageVisitedEvent());
    return 'ok';
});

```

Handling Symfony's events:

```php

use PHPExtra\EventManager\Silex\SilexEvent; // this class wraps the symfony events

$listener = new AnonymousListener(function(SilexEvent $event){
    $symfonyEvent = $event->getSymfonyEvent();

    if($symfonyEvent instanceof GetResponseEvent){
        $sfEvent->setResponse(new Response('Response from event listener !'));
    }
    
    // or 
    
    if($symfonyEvent->getName() == 'kernel.request'){
        if(!$symfonyEvent->isCancelled()){ // uses Symfony's "isPropagationStopped()"
            $symfonyEvent->cancel(); // executes Symfony's "stopPropagation()"
        }
    }
    
});

```


## Installation (Composer)

```json
{
    "require": {
        "phpextra/event-manager":"^4.0"
    }
}
```

##Running tests

```
phpunit ./tests
```


##Contributing

All code contributions must go through a pull request.
Fork the project, create a feature branch, and send me a pull request.
To ensure a consistent code base, you should make sure the code follows
the [coding standards](http://www.php-fig.org/psr/).
If you would like to help, take a look at the [list of issues](https://github.com/phpextra/event-manager/issues).

##Requirements

    PHP >=5.3.0

##Authors

Jacek Kobus - <kobus.jacek@gmail.com>

