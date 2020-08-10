<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class IndexControllerTest extends WebTestCase {
    /**
     * @test
     */
    public function is_homepage_200_status_code(): void {
        $client = static::createClient();

        $client->request('GET', '/');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
