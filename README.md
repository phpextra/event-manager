#Skajdo Event Manager [![Build Status](https://travis-ci.org/jack-ks/skajdo-test-suite.png?branch=master)](https://travis-ci.org/jack-ks/skajdo-test-suite)
##Event Managment made simple

PSR-0 compliant, lightweight & tested.
Start using events under one minute. It's **that** easy.

Library is under active development and it aims to be **simple** and **fast**. All pull requests and bug reports are welcome.

## Installation (Composer)

CLI:

```
> composer require skajdo/event-manager
```

JSON:

```json
"require": {
    "skajdo/event-manager":"*"
}
```


##Updating to the lastest version

```
> composer update skajdo/event-manager
```

##Testing

We are using skajdo-test-suite in our application. To install and run use:

```
> composer update skajdo/test-suite & php tests/run.php
```

##Features - why would I want to use it ?

While most available event managers are based on events that are names, and some objects that carry information
I chose different approach to get rid of those things. You do not need to define events as a list of some names.
Each event is an object that you create and it will carry __whatever you want__. You only need to extend an empty interface, that's all.
Listener is also an object (including closures); you do not need to know the name of an event while adding a listener to the stack.
Manager will handle that for you, because while you wrote that listener, you already told what and when should be triggered.

- code-completion friendly design; you dont't have to remember event & listener names. Since both are usually your project classes, simple built-in type hinting and cc will work just fine.
- flexible and extensible; events and listeners can be anything. It can be a module, a bundle, maybe a form. This event manager will not ruin your design because listener method names are not predefined - just type event class name as a method param and it will work. Both listener and event interfaces are ... empty.
- simple to use; remember only two methods: `addListener(Listener $l)` and `triggerEvent(EventInterface $event)`. You have many events and want a single listener to rule them all ? No problem. Just type class parent as expected event, for ex. `onSmthn(EventInterface $event)` and all existing events will be passed to your method.
- supports cool language features; listener can be an anonymous function. It can simplify your work as you will not need to create separate classes for listeners unless you really want to.
- log-aware; are you using monolog ? Yes, it is supported. All loggers using PSR-3 are supported;
- clean code.

## Usage example

We have a car and it will automatically notify all subsystems that it started.
Electronics will turn our headlights on.

```php

<?php

use \Skajdo\EventManager\EventManager;
use \Skajdo\EventManager\Event\EventInterface;
use \Skajdo\EventManager\Listener\Listener;

class Car {

    /**
     * @var bool
     */
    public $headlights = false;

    /**
     * @var EventManager
     */
    public $em;

    /**
     * Create new car
     */
    public function __construct()
    {
        $this->em = new EventManager();
        $this->em->addListener(new CarElectronics());
    }

    /**
     * Start the car
     */
    public function start()
    {
        $this->em->triggerEvent(new CarStartEvent($this));
    }
}

class CarStartEvent implements EventInterface
{
    /**
     * @var Car
     */
    protected $car;

    /**
     * @param Car $car
     */
    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    /**
     * @return Car
     */
    public function getCar()
    {
        return $this->car;
    }
}

class CarElectronics implements Listener
{
    /**
     * Turn the headlights on when the car starts
     *
     * @priority 200
     * @param CarStartEvent $event
     */
    public function onCarStart(CarStartEvent $event)
    {
        $event->getCar()->headlights = true;
    }
}


$car = new Car();
$car->start();
echo ($car->headlights == true) ? 'HEADLIGHTS ARE ON' : 'HEADLIGHTS ARE OFF'; // returns HEADLIGHTS ARE ON


```

## Anonymous functions usage

Above listener (`CarElectronics`) could be replaced with:

```php

<?php

    $electronics = function(CarStartEvent $event){$event->getCar()->headlights = true;}
    $car = new Car();
    $car->em->addListener($electronics, Priority::NORMAL);
    $car->start();
    echo ($car->headlights == true) ? 'HEADLIGHTS ARE ON' : 'HEADLIGHTS ARE OFF'; // returns HEADLIGHTS ARE ON

?>

```

## Adding catch-all listener

Consider the following scenario:


    CarEvent implements EventInterface
    CarStartEvent extends CarEvent
    CarEngineStartEvent extends CarStartEvent


Simple, right ? Now watch this:

```php

<?php

abstract class MyListener implements Listener
{
    public function onAnyEvent(EventInterface $ev); // catches ALL events

    public function onAnyCarEvent(CarEvent $ev); // catches CarEvent, CarStartEvent, CarEngineStartEvent

    public function onCarOrEngineStart(CarStartEvent $ev); // catches CarStartEvent, CarEngineStartEvent

    public function onEngineStart(CarEngineStartEvent $ev); // catches CarEngineStartEvent
}

?>

```

Few lines of code are worth more than a milion words :-)

##To-Do

* reformat log messages
* recurrency check and monitor
* performance test
* propagation stop (need to be reconsidered)
* more tests (or maybe use PHPUnit)
* replace ZendCode with Doctrine Annotations or just use the default Reflection API


##Changelog

1.1.0

* interface changes (ListenerInterface -> Listener, EventInterface -> Event)
* cleaned up classes
* added benchmark test (10150 listener calls < 0.8 sec)

1.0.4

* changed license to MIT
* added EventManagerAware interface
* readme update
* added MONITOR priority
* removed exception in case where listener is not listening to any known events (info-log instead)

1.0.3

* added support for event inheritance
* replaced multiple event-queues with one to make priority managment easier
* updated readme

1.0.2

* bugfix

1.0.1

* added closure support
* priority can be overriden while adding listener

1.0.0

* first relase


##Contributing

All code contributions must go through a pull request.  
Fork the project, create a feature branch, and send me a pull request.
To ensure a consistent code base, you should make sure the code follows
the [coding standards](http://symfony.com/doc/2.0/contributing/code/standards.html).
If you would like to help take a look at the [list of issues](https://github.com/jkobus/skajdo-test-suite/issues).

##Requirements

See **composer.json** for a full list of dependencies.

##Authors

Jacek Kobus - <kobus.jacek@gmail.com>

## License information (MIT)

    See the file LICENSE.txt for copying permission.



[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/jkobus/skajdo-event-manager/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

