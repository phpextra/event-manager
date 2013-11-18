<?php

/**
 * You can extend the concrete or abstract class, or just implement the interface
 * Using both is NOT necessary and is shown here for educational purposes
 *
 * Event is just a simple class that holds the entity we are working on (the car)
 * It extends the cancellable event so we can actually cancel the whole process
 */
use Skajdo\EventManager\CancellableEvent;
use Skajdo\EventManager\EventInterface;

class CarStartEvent extends CancellableEvent implements EventInterface
{
    /**
     * @var Car
     */
    protected $car;

    /**
     * @param Car $car
     */
    function __construct(Car $car)
    {
        $this->car = $car;
    }

    /**
     * @return \Car
     */
    public function getCar()
    {
        return $this->car;
    }
}