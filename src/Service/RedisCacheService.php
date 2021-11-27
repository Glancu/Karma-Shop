<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;

class RedisCacheService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $redisKeyName
     * @param string $class
     * @param $repositoryFunctionName
     * @param null $parameters
     * @param int $expiresAfter
     *
     * @return array|string|object
     *
     * @throws InvalidArgumentException
     */
    public function getAndSaveIfNotExist(
        string $redisKeyName,
        string $class,
        $repositoryFunctionName,
        $parameters = null,
        $expiresAfter = 3600
    ) {
        $clientRedis = RedisAdapter::createConnection(getenv('REDIS_URL'));

        $cacheRedis = new RedisAdapter($clientRedis);

        /**
         * @var CacheItem $itemsRedis
         */
        $itemsRedis = $cacheRedis->getItem($redisKeyName);
        if (!$itemsRedis->isHit()) {
            if(!$parameters) {
                $value = $this->entityManager->getRepository($class)->{$repositoryFunctionName}();
            } else {
                $value = $this->entityManager->getRepository($class)->{$repositoryFunctionName}($parameters);
            }

            $itemsRedis->set($value);
            $itemsRedis->expiresAfter($expiresAfter);

            $cacheRedis->save($itemsRedis);
        }

        return $itemsRedis->get();
    }
}
