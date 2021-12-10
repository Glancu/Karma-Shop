<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\RedisCacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CheckRedisConnectionCommand extends Command
{
    protected static $defaultName = 'app:redis:check-connection';

    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private ValidatorInterface $validator;
    private RedisCacheService $redisCacheService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ValidatorInterface $validator,
        RedisCacheService $redisCacheService,
        string $name = null
    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->validator = $validator;
        $this->redisCacheService = $redisCacheService;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Check redis connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln($this->redisCacheService->isConnected() ? 'Jest connection' : 'Nie ma connection');
    }
}
