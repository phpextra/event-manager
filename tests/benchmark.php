<?php

date_default_timezone_set('Europe/Warsaw');
require_once(__DIR__ . '/../vendor/autoload.php');

gc_disable();

// config start

$numberOfEvents = 100;
$numberOfListenersPerEvent = 10;
$timesEachEventIsTriggered = 100;

// config end

$bench = new Ubench();
$em = new \PHPExtra\EventManager\EventManager();

$addedListeners = 0;
$createdEvents = 0;
$timesTriggered = 0;

for($i = 0;$i<$numberOfEvents;$i++){

    $eventClass = 'DummyEvent' . $i;
    eval('class ' . $eventClass . ' implements \PHPExtra\EventManager\Event\EventInterface {}');
    $createdEvents++;

    for($ii = 0; $ii < $numberOfListenersPerEvent; $ii++){
        $listenerClass = 'DummyListener' . $i . '_' . $ii;
        eval('class ' . $listenerClass . ' implements \PHPExtra\EventManager\Listener\ListenerInterface {
            public function on' . $eventClass . '(' . $eventClass . ' $event){}
        }');

        $addedListeners++;
        $em->addListener(new $listenerClass);
    }
}


$bench->start();

for($i = 0; $i < $numberOfEvents; $i++){

    $eventClass = 'DummyEvent' . $i;
    $event = new $eventClass();

    for($ii = 0; $ii < $timesEachEventIsTriggered; $ii++){
        $em->trigger($event);
        $timesTriggered++;
    }
}

$bench->end();

$time = $bench->getTime(true);
$scale = 6;

$totalWorkerExecutions = $numberOfEvents * $numberOfListenersPerEvent * $timesEachEventIsTriggered;
$timePerWorker = bcdiv($time, $totalWorkerExecutions, $scale);
$timePerListener = bcdiv($time, $addedListeners, $scale);
$timePerEvent = bcdiv($time, $createdEvents, $scale);
$timePerTrigger = bcdiv($time, $timesTriggered, $scale);

echo 'Done!' . PHP_EOL;
echo 'Added listeners (total): ' . $addedListeners . PHP_EOL;
echo 'Created events (total): ' . $createdEvents . PHP_EOL;
echo 'Times triggered: ' . $timesTriggered . PHP_EOL;
echo 'Total listeners executions: ' . $totalWorkerExecutions . PHP_EOL;
echo 'Time: ' . $bench->getTime() . ' (per worker: ' . $timePerWorker . ', per listener: ' . $timePerListener . ', per event: ' . $timePerEvent . ', per trigger: ' . $timePerTrigger . ')' . PHP_EOL;
echo 'Mem. peak.: ' . $bench->getMemoryPeak() . PHP_EOL;
