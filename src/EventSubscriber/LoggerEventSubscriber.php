<?php

namespace PI\EventSubscriber;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LoggerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * LoggerEventSubscriber constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => "onKernelException",
            ConsoleEvents::ERROR    => "onConsoleError"
        ];
    }

    /**
     * @param ExceptionEvent $event
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onKernelException(ExceptionEvent $event) : void
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        $this->connection->executeQuery("
            INSERT INTO log (log_message, log_file, log_line, log_path, log_stacktrace) 
            VALUES (
                " . $this->connection->quote($exception->getMessage()) . ",
                " . $this->connection->quote($exception->getFile()) . ",
                " . $this->connection->quote($exception->getLine()) . ",
                " . $this->connection->quote($request->getPathInfo()) . ",
                " . $this->connection->quote($exception->getTraceAsString()) . "
            );
        ");
    }

    /**
     * @param ConsoleErrorEvent $event
     */
    public function onConsoleError(ConsoleErrorEvent $event) : void
    {
        // https://symfony.com/doc/current/components/console/events.html#the-consoleevents-error-event
        // https://stackoverflow.com/questions/52483483/symfony-4-console-exception-event-listener
    }

}
