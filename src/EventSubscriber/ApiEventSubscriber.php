<?php

namespace PI\EventSubscriber;

use PI\Controller\ApiController;
use PI\Employee\Exception\AccessDeniedException;
use PI\Employee\Guard\ApiGuardInterface;
use PI\Employee\Response\CORSResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiEventSubscriber implements EventSubscriberInterface
{

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
            KernelEvents::CONTROLLER    => "onKernelController",
            KernelEvents::EXCEPTION     => "onKernelException"
        ];
    }

    /**
     * @param ControllerEvent $event
     * @throws AccessDeniedException
     */
    public function onKernelController(ControllerEvent $event) : void
    {
//        jakby trzeba bylo zabezpieczyc API to wystarczy odkomentowac

//        $controller = $event->getController();
//        if (is_array($controller)) {
//            $controller = $controller[0];
//        }
//
//        if ($controller instanceof ApiGuardInterface) {
//            $input = array_merge($event->getRequest()->request->all(), $event->getRequest()->query->all());
//
//            if (empty($input["apiKey"]) || $input["apiKey"] != ApiController::API_KEY) {
//                jakby trzeba bylo
//                throw new AccessDeniedException("Błędny klucz API!");
//            }
//        }
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event) : void
    {
        if ($event->getException() instanceof AccessDeniedException) {
            $corsResponse = new CORSResponse([
                "status"    => ApiController::STATUS_ERROR,
                "message"   => $event->getException()->getMessage()
            ]);
            $event->setResponse($corsResponse);
        }
    }

}
