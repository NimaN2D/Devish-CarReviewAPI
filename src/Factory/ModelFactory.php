<?php

namespace App\Factory;

use App\Entity\Model;
use App\Repository\ModelRepository;
use Zenstruck\Foundry\ModelFactory as BaseModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Model>
 *
 * @method        Model|Proxy create(array|callable $attributes = [])
 * @method static Model|Proxy createOne(array $attributes = [])
 * @method static Model|Proxy find(object|array|mixed $criteria)
 * @method static Model|Proxy findOrCreate(array $attributes)
 * @method static Model|Proxy first(string $sortedField = 'id')
 * @method static Model|Proxy last(string $sortedField = 'id')
 * @method static Model|Proxy random(array $attributes = [])
 * @method static Model|Proxy randomOrCreate(array $attributes = [])
 * @method static ModelRepository|RepositoryProxy repository()
 * @method static Model[]|Proxy[] all()
 * @method static Model[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Model[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Model[]|Proxy[] findBy(array $attributes)
 * @method static Model[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Model[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ModelFactory extends BaseModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'manufacturer' => ManufacturerFactory::new(),
            'name' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Model $model): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Model::class;
    }
}
