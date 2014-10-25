<?php

namespace Shu1\SimulistBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        // Assert that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
