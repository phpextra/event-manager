<?php

namespace PHPExtra\EventManager\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * The SilexProvider class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManagerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['dispatcher_class'] = 'PHPExtra\\EventManager\\Silex\\CustomEventDispatcher';

        $app['event_manager'] = $app->share(function (Application $app) {

            $em = new ProfilableEventManager();

            if($app['debug']){

                if ($app['logger'] !== null) {
                    $em->setLogger($app['logger']);
                }

                $em
                    ->setStopwatch($app['stopwatch'])
                    ->setThrowExceptions(true)
                ;
            }

            return $em;
        });

        $app->extend('dispatcher', function (CustomEventDispatcher $dispatcher, Application $app) {
            $dispatcher->setEventManager($app['event_manager']);
            return $dispatcher;
        });

        if(!isset($app['stopwatch'])){
            $app['stopwatch'] = $app->share(function () {
                return new Stopwatch();
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}