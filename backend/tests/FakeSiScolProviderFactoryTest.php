<?php

namespace App\Tests;

use App\Service\SiScol\FakeSiScolDataProvider;
use App\Service\SiScol\SiScolDataProviderFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\SiScol\AbstractSiScolDataProvider;

/**
 * Test si la classe retourner est bien FakeSiScolDataProvider
 *
 * @author leveillard
 */
class FakeSiScolProviderFactoryTest extends KernelTestCase
{
    public function testCreateReturnsFakeProviderWhenSiScolIsFake(): void
    {
        self::bootKernel();

        /** @var SiScolDataProviderFactory $factory */
        $factory = self::getContainer()->get(SiScolDataProviderFactory::class);

        $provider = $factory->create();

        $this->assertInstanceOf(FakeSiScolDataProvider::class, $provider);
        $this->assertSame('fake', $provider->getProviderId());
    }
}