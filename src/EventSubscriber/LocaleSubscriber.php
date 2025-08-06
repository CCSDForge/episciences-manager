<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;


class LocaleSubscriber implements EventSubscriberInterface
{
    public function onRequestEvent(RequestEvent $event): void
    {
        // Get request directly from event
        $request = $event->getRequest();

        // Only handle main requests (not sub-requests)
        if (!$event->isMainRequest()) {
            return;
        }

        // Don't process if no previous session
        if (!$request->hasPreviousSession()) {
            return;
        }

        $supportedLocales = ['en', 'fr'];
        $defaultLocale = 'en';

        // If locale is defined in URL (e.g. /zh/home)
        if ($locale = $request->attributes->get('_locale')) {
            if (in_array($locale, $supportedLocales)) {
                $request->getSession()->set('_locale', $locale);
                $request->setLocale($locale);
            }
        } else {
            // Use locale from session
            $sessionLocale = $request->getSession()->get('_locale', $defaultLocale);

            if (in_array($sessionLocale, $supportedLocales)) {
                $request->setLocale($sessionLocale);
            } else {
                $request->setLocale($defaultLocale);
                $request->getSession()->set('_locale', $defaultLocale);
            }
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onRequestEvent', 20]],
        ];
    }
}
