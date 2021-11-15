<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\MoneyService;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MoneyServiceTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_property_convert_int_to_float(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        $moneyService = $container->get(MoneyService::class);

        self::assertEquals(50.00, $moneyService->convertIntToFloat(5000));
    }

    /**
     * @test
     */
    public function it_property_convert_float_to_int(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        $moneyService = $container->get(MoneyService::class);

        self::assertEquals(5000, $moneyService->convertFloatToInt(50));
    }
}
