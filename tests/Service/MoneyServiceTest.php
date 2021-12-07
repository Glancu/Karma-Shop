<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\MoneyService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class MoneyServiceTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_property_convert_int_to_float(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        self::assertEquals(50.00, MoneyService::convertIntToFloat(5000));
    }

    /**
     * @test
     */
    public function it_property_convert_float_to_int(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        self::assertEquals(5000, MoneyService::convertFloatToInt(50));
    }
}
