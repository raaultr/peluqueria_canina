<?php

namespace App\Tests\Publico;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testLandingPageShowsItsMainSections(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Porque ellos también');
        self::assertSelectorTextContains('#servicios', 'Baño y secado');
        self::assertSelectorExists('img[src*="golden-sinfondo-transparent.png"]');
    }
}