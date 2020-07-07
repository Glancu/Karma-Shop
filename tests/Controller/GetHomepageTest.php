<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class GetHomepageTest
 *
 * @package App\Tests\Controller
 */
class GetHomepageTest extends WebTestCase {
    public function testShowHomepage(): void {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}