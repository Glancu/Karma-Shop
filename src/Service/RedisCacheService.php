<?php
declare(strict_types=1);

namespace App\Service;

use App\Serializer\BlogSerializeDataResponse;
use App\Serializer\ShopSerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use Predis\CommunicationException;
use Psr\Cache\InvalidArgumentException;
use RedisException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCacheService
{
    private EntityManagerInterface $entityManager;
    private AdapterInterface $adapter;
    private string $redisUrl;
    private BlogSerializeDataResponse $blogSerializeDataResponse;
    private ShopSerializeDataResponse $shopSerializeDataResponse;

    public function __construct(
        EntityManagerInterface $entityManager,
        AdapterInterface $adapter,
        string $redisUrl,
        BlogSerializeDataResponse $blogSerializeDataResponse,
        ShopSerializeDataResponse $shopSerializeDataResponse
    )
    {
        $this->entityManager = $entityManager;
        $this->adapter = $adapter;
        $this->redisUrl = $redisUrl;
        $this->blogSerializeDataResponse = $blogSerializeDataResponse;
        $this->shopSerializeDataResponse = $shopSerializeDataResponse;
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
        if($this->isConnected()) {
            $cacheRedis = new RedisAdapter(
                $this->getClientConnection()
            );

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

        return null;
    }

    /**
     * @param string $redisKeyName
     * @param string $class
     * @param $repositoryFunctionName
     * @param string $serializeName
     * @param string $serializeFunctionName
     * @param null $parameters
     * @param null $parametersForSerialize
     * @param int $expiresAfter
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getAndSaveIfNotExistWithSerializeData(
        string $redisKeyName,
        string $class,
        $repositoryFunctionName,
        string $serializeName,
        string $serializeFunctionName,
        $parameters = null,
        $parametersForSerialize = null,
        $messageIfValueNotFound = 'Object was not found.',
        $expiresAfter = 3600
    ) {
        if($this->isConnected()) {
            $cacheRedis = new RedisAdapter(
                $this->getClientConnection()
            );

            $itemsRedis = $cacheRedis->getItem($redisKeyName);
            $cacheRedisSerializeItem = $cacheRedis->getItem($redisKeyName.'.serialize');

            if (!$itemsRedis->isHit() || !$cacheRedisSerializeItem->isHit()) {
                if (!$parameters) {
                    $value = $this->entityManager->getRepository($class)->{$repositoryFunctionName}();
                } else {
                    $value = $this->entityManager->getRepository($class)->{$repositoryFunctionName}($parameters);
                }

                if(!$value) {
                    return ['error' => true, 'message' => $messageIfValueNotFound];
                }

                if(!$parametersForSerialize) {
                    $serializeData = $this->$serializeName->{$serializeFunctionName}($value);
                } else {
                    $serializeData = $this->$serializeName->{$serializeFunctionName}($value, ...$parametersForSerialize);
                }

                $cacheRedisSerializeItem->set($serializeData);
                $cacheRedisSerializeItem->expiresAfter($expiresAfter);

                $cacheRedis->save($cacheRedisSerializeItem);

                $itemsRedis->set($value);
                $itemsRedis->expiresAfter($expiresAfter);

                $cacheRedis->save($itemsRedis);
            }

            return $cacheRedisSerializeItem->get();
        }

        return null;
    }

    public function flushAll(): void
    {
        if($this->isConnected()) {
            $client = $this->getClientConnection();

            $client->flushall();
        }
    }

    public function isConnected(): bool
    {
        $client = $this->getClientConnection();

        try {
            return $client->ping()->getPayload() === 'PONG';
        } catch (CommunicationException $e) {
            return false;
        } catch (RedisException $e) {
            return false;
        }
    }

    private function getClientConnection()
    {
        return RedisAdapter::createConnection(
            $this->redisUrl,
            [
                'lazy' => false,
                'persistent' => 0,
                'persistent_id' => null,
                'tcp_keepalive' => 0,
                'timeout' => 3,
                'read_timeout' => 0,
                'retry_interval' => 0,
            ]
        );
    }
}
