<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class RedisCacheService
{
    private EntityManagerInterface $entityManager;
    private AdapterInterface $adapter;

    public function __construct(EntityManagerInterface $entityManager, AdapterInterface $adapter)
    {
        $this->entityManager = $entityManager;
        $this->adapter = $adapter;
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
