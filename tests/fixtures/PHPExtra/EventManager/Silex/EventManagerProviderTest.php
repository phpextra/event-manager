<?php

namespace fixtures;

use PHPExtra\EventManager\EventManager;
use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Silex\EventManagerServiceProvider;
use PHPExtra\EventManager\Silex\SilexEvent;
use Silex\Application;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * The EventManagerProviderTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManagerProviderTest extends WebTestCase
{
    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        $app = new Application(array('debug' => true));
        $app['exception_handler']->disable();
        $app->register(new EventManagerServiceProvider());
        $app->get('/', function(Application $app){
            return 'ok';
        });
        return $app;
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->app['event_manager'];
    }

    public function testRunApplicationWithoutListenersReturnsValidResponse()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('ok', $client->getResponse()->getContent());
    }

    public function testRunApplicationWithListenersReturnsValidResponse()
    {
        $listener = new AnonymousListener(function(SilexEvent $event) use (&$queue){
            $queue[] = $event->getName();
        });

        $this->getEventManager()->add($listener);

        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('ok', $client->getResponse()->getContent());
    }

    public function testDefaultSymfonyEventsAreTriggeredInCorrectOrder()
    {
        $expected = array(
            'kernel.request',
            'kernel.controller',
            'kernel.view',
            'kernel.response',
            'kernel.finish_request',
            'kernel.terminate',
        );

        $queue = array();

        $listener = new AnonymousListener(function(SilexEvent $event) use (&$queue){
            $queue[] = $event->getName();
        });

        $this->getEventManager()->add($listener);
        $this->createClient()->request('GET', '/');

        $this->assertEquals($expected, $queue);

    }

    public function testResponseCanBeAlteredUsingEvents()
    {
        $listener = new AnonymousListener(function(SilexEvent $event) use (&$queue){
            $sfEvent = $event->getSymfonyEvent();

            if($sfEvent instanceof GetResponseEvent){
                $sfEvent->setResponse(new Response('Response from event listener !'));
            }
        });
        $this->getEventManager()->add($listener);

        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertEquals($client->getResponse()->getContent(), 'Response from event listener !');
    }
}
