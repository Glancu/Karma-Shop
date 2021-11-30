<?php
declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestAPIPageIsSuccessfulTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', '/api'.$url);

        self::assertResponseIsSuccessful();
    }

    public function urlProvider()
    {
        yield ['/blog/posts/list'];
        yield ['/blog/posts/popular'];
        yield ['/blog/categories/latest'];
        yield ['/blog/categories/list'];
        yield ['/blog/tags/list'];
        yield ['/search/list?query=em'];
        yield ['/shop/brands/list'];
        yield ['/shop/categories/list'];
        yield ['/shop/colors/list'];
        yield ['/shop/products/list'];
        yield ['/shop/products/latest'];
    }
}
