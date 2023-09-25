<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Car;
use App\Factory\CarFactory;
use App\Repository\CarRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
use Zenstruck\Foundry\Test\ResetDatabase;

class CarTest extends ApiTestCase
{
    use ResetDatabase, FactoryTrait;

    public function testGetCars(): void
    {
        CarFactory::createMany(100);
        static::createClient()->request('GET', '/api/cars');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            '@context' => '/api/contexts/Car',
            '@id' => '/api/cars',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/cars?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/cars?page=1',
                'hydra:last' => '/api/cars?page=4',
                'hydra:next' => '/api/cars?page=2',
            ],
        ]);
    }

    public function testCreateCar(): void
    {
        $carRequest = [
            'brand' => 'AUDI',
            'model' => 'A4',
            'color' => 'black',
        ];

        static::createClient()->request('POST', '/api/cars', ['json' => $carRequest]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains(array_merge($carRequest, [
            '@context' => '/api/contexts/Car',
            '@type' => 'Car',
            'reviews' => [],
        ]));

        $record = $this->getRepository()->findOneBy($carRequest);
        $this->assertNotNull($record);
    }

    public function testUpdateCar(): void
    {
        $car = CarFactory::createOne();
        $updateRequest = [
            'brand' => 'AUDI',
            'model' => 'A4',
            'color' => 'black',
        ];

        static::createClient()->request('PUT', '/api/cars/' . $car->getId(), ['json' => $updateRequest]);
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains($updateRequest);

        $record = $this->getRepository()->find($car->getId());
        $this->assertEquals($updateRequest['brand'], $record->getBrand());
        $this->assertEquals($updateRequest['model'], $record->getModel());
        $this->assertEquals($updateRequest['color'], $record->getColor());
    }

    public function testDeleteCar(): void
    {
        $car = CarFactory::createOne();

        static::createClient()->request('DELETE', '/api/cars/' . $car->getId());
        $this->assertResponseIsSuccessful();

        $record = $this->getRepository()->findOneBy([
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'color' => $car->getColor(),
        ]);
        $this->assertNull($record);
    }

    private function getRepository(): CarRepository
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        return $entityManager->getRepository(Car::class);
    }
}
