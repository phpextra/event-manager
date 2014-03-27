#Event Manager
[![Build Status](https://travis-ci.org/phpextra/event-manager.png?branch=master)](https://travis-ci.org/phpextra/event-manager)

Library is under active development and it aims to be **simple** and **fast**. All pull requests and bug reports are welcome.

## Installation (Composer)

CLI:

```
> composer require phpextra/event-manager
```

JSON:

```json
"require": {
    "phpextra/event-manager":"~1.0.0"
}
```

##Updating to the latest version

```
> composer update skajdo/event-manager
```

##Testing

On windows open cmd window in the project directory, then type:

```
> composer install && test
```

## How it works ?

Both event and listener are interfaces.
Events support inheritance. It means that listener can listen for event parents.

## Examples


    class UserLoginEvent implements EventInterface
    {
        protected $user;

        public function __construct($user){ ... }

        (...)
    }

    class UserListener implements Listener
    {
        /**
         * @priority HIGH
         */
        public function onUserLogin(UserLoginEvent $event)
        {
            $event->user ...
            echo "User listener 1";
        }

        /**
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
    $manager->trigger(new UserLoginEvent($user));

    > User listener 1
    > User listener 2

Anonymous function can be a listener too. Priority in this case can be specified as a second constructor param in
AnonymousListener class.


    $listener = new AnonymousListener(function(UserLoginEvent $event){
        $event->user ...
    }), Priority::LOWEST);


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

    See the file LICENSE.txt for copying permission.

