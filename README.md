#Skajdo Event Manager
[![Build Status](https://travis-ci.org/jkobus/skajdo-event-manager.png?branch=master)](https://travis-ci.org/jkobus/skajdo-event-manager)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/jkobus/skajdo-event-manager/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

##Event Management made simple

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


##Updating to the latest version

```
> composer update skajdo/event-manager
```

##Testing

On windows open cmd window in the project directory, then type:

```
> composer update & test
```

##Features - why would I want to use it ?

- code-completion friendly design; you dont't have to remember event & listener names. Since both are usually your project classes, simple built-in type hinting and cc will work just fine.
- flexible and extensible; events and listeners can be anything. It can be a module, a bundle, maybe a form. This event manager will not ruin your design because listener method names are not predefined - just type event class name as a method param and it will work. Both listener and event interfaces are ... empty.
- simple to use; remember only two methods: `addListener(Listener $l)` and `trigger(EventInterface $event)`. You have many events and want a single listener to rule them all ? No problem. Just type class parent as expected event, for ex. `onSmthn(EventInterface $event)` and all existing events will be passed to your method.
- supports cool language features; listener can be an anonymous function. It can simplify your work as you will not need to create separate classes for listeners unless you really want to.
- log-aware; are you using monolog ? Yes, it is supported. All loggers using PSR-3 are supported;
- clean, tested code.

## Usage example

For more complex example please see **examples** inside this project.

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

## Anonymous function as listener

```php

<?php

$listener = new AnonymousListener(function(SomeEventInterface $event){
    ...
});

?>

```


##To-Do

* update readme (70%)
* reformat log messages (70%)
* recurrency check and monitor (100%)
* performance test (n\a)
* more tests (or maybe use PHPUnit) (100%)
* replace ZendCode with Doctrine Annotations or just use the default Reflection API (n\a)


##Changelog

2.0.0

* removed deprecated methods
* updated class names
* added phpunit
* added worker factory
* updated composer and readme

**Line 1.* is deprecated and will not be maintained.**

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
If you would like to help take a look at the [list of issues](https://github.com/jkobus/skajdo-event-manager/issues).

##Requirements

See **composer.json** for a full list of dependencies.

##Authors

Jacek Kobus - <kobus.jacek@gmail.com>

## License information

    See the file LICENSE.txt for copying permission.

