<?php

use Skajdo\EventManager\EventManager;
use Skajdo\EventManager\Listener\AnonymousListener;

if (file_exists($a = __DIR__.'/../../../../autoload.php')) {
    require_once $a;
} else {
    require_once __DIR__.'/../../vendor/autoload.php';
}

require_once(__DIR__ . '/Car.php');
require_once(__DIR__ . '/CarHeadlightsSensorListener.php');
require_once(__DIR__ . '/CarStartEvent.php');

// inject event manager into our car
$car = new Car(new EventManager());


if(true){

    // Standard listener
    $listener = new CarHeadlightsSensorListener();

}else{

    // Anonymous function as listener - it always take the event as its first and only param
    $listener = new AnonymousListener(function(CarStartEvent $event){
        if(!$event->isCancelled()){
            if($event->getCar()->hasHeadlightsTurnedOn() != true){
                $event->getCar()->turnHeadlightsOn();
            }
        }
    });

}

// add our sensor for headlights
$car->addSensorListener($listener);

// make sure that our headlights are OFF
echo ($car->hasHeadlightsTurnedOn() ? 'Headlights are ON' : 'Headlights are OFF') . PHP_EOL; // yells Headlights are OFF

// start the engine
$car->startEngine();

// make sure that our headlights are ON
echo ($car->hasHeadlightsTurnedOn() ? 'Headlights are ON' : 'Headlights are OFF') . PHP_EOL; // yells Headlights are ON


