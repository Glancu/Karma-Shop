<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Service\RedisCacheService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;

class ClearRedisCacheEventListener implements EventSubscriberInterface
{
    private RedisCacheService $redisCacheService;

    public function __construct(RedisCacheService $redisCacheService) {
        $this->redisCacheService = $redisCacheService;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate
        ];
    }

    public function postPersist(): void
    {
        $this->clearRedisCache();
    }

    public function postRemove(): void
    {
        $this->clearRedisCache();
    }

    public function postUpdate(): void
    {
        $this->clearRedisCache();
    }

    private function clearRedisCache(): void
    {
//        $this->redisCacheService->flushAll();
    }
}
