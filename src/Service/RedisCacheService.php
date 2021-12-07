<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCacheService
{
    private EntityManagerInterface $entityManager;
    private AdapterInterface $adapter;
    private string $redisUrl;

    public function __construct(EntityManagerInterface $entityManager, AdapterInterface $adapter, string $redisUrl)
    {
        $this->entityManager = $entityManager;
        $this->adapter = $adapter;
        $this->redisUrl = $redisUrl;
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
        $cacheRedis = $this->adapter;

        $itemsRedis = $cacheRedis->getItem($redisKeyName);
        if (!$itemsRedis->isHit()) {
            if (!$parameters) {
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

    public function flushAll(): void
    {
        $client = RedisAdapter::createConnection(
            $this->redisUrl,
            [
                'lazy' => false,
                'persistent' => 0,
                'persistent_id' => null,
                'tcp_keepalive' => 0,
                'timeout' => 30,
                'read_timeout' => 0,
                'retry_interval' => 0,
            ]
        );

        $client->flushall();
    }
}
