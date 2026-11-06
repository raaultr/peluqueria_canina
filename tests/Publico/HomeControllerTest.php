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
        self::assertSelectorTextContains('#servicios', 'Baño e hidratación');
        self::assertSelectorTextContains('#home-method-title', 'El Método PeluCan en 4 pasos');
        self::assertSelectorCount(4, '.home-price-card');
        self::assertSelectorExists('img[src*="golden-sinfondo-transparent.png"]');
    }
}