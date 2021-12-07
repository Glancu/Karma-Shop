<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\RedisCacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisFlushAllCommand extends Command
{
    protected static $defaultName = 'app:redis:flushall';

    private RedisCacheService $redisCacheService;

    /**
     * RedisFlushAllCommand constructor.
     *
     * @param RedisCacheService $redisCacheService
     * @param string|null $name
     */
    public function __construct(
        RedisCacheService $redisCacheService,
        string $name = null
    ) {
        parent::__construct($name);
        $this->redisCacheService = $redisCacheService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Clear all keys and values from redis.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->redisCacheService->flushAll();

        $output->writeln("Redis has been flushed.");
    }
}
