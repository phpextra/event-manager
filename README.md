#Skajdo Event Manager (v1.0.0) ![WTFPL License](http://www.wtfpl.net/wp-content/uploads/2012/12/wtfpl-badge-2.png) [![Build Status](https://travis-ci.org/jack-ks/skajdo-test-suite.png?branch=master)](https://travis-ci.org/jack-ks/skajdo-test-suite)
##Event Managment made simple

PSR-0 compliant, lightweight & tested.
Start using events under one minute - no initial setup required. It's **that** easy.

Library is under active development and it aims to be **simple** and **fast**. All pull requests and bug reports are welcome.

## Installation (Composer)

CLI:

```
> composer require skajdo/event-manager
```

JSON:

```json
"require": {
    "skajdo/event-manager":"1.0.0"
}
```


##Updating to the lastest version

```
> composer update skajdo/event-manager
```

##Testing

We are using skajdo-test-suite in our application. To install and run use:

```
> composer update skajdo/test-suite & php tests/run-tests.php run -s ./tests
```

## Usage example

We have a car and it will automatically notify all subsystems that it started.
Electronics will turn our headlights on.

```php

<?php

use \Skajdo\EventManager\EventManager;
use \Skajdo\EventManager\Event\EventInterface;
use \Skajdo\EventManager\Listener\ListenerInterface;

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

class CarElectronics implements ListenerInterface
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

##Changelog

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

## License information

    Copyright © 2013 Jacek Kobus <kobus.jacek@gmail.com>
    This work is free. You can redistribute it and/or modify it under the
    terms of the Do What The Fuck You Want To Public License, Version 2,
    as published by Sam Hocevar. See http://www.wtfpl.net/ for more details.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.