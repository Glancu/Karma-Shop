<?php

namespace App\EventListener;

use DateTime;
use DateTimeZone;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener {
    private RequestStack $requestStack;
    private string $expireTime;

    /**
     * JWTCreatedListener constructor.
     *
     * @param RequestStack $requestStack
     * @param string $expireTime
     */
    public function __construct(RequestStack $requestStack, string $expireTime)
    {
        $this->requestStack = $requestStack;
        $this->expireTime = $expireTime;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     *
     * @throws Exception
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $expireTime = $this->expireTime;
        $minutes = $expireTime / 60;

        $expiration = new DateTime('now', new DateTimeZone('Europe/Warsaw'));
        $expiration->modify("+${minutes} minutes");

        $payload = $event->getData();
        $payload['ie'] = new DateTime('now');
        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);
    }
}
