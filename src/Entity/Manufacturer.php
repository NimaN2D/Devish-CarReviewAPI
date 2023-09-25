<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Repository\ManufacturerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        normalizationContext: ['groups' => ['manufacturer:read']],
        denormalizationContext: ['groups' => ['manufacturer:write']],
    ),
    ApiResource(
        uriTemplate: '/manufacturers/{manufacturer}/models',
        description: 'Get a manufacturer\'s models',
        operations: [new Get()],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'models',
                fromClass: Manufacturer::class
            )
        ]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'name' => 'partial',
        ]
    ),
    ApiFilter(
        OrderFilter::class,
        properties: [
            'name',
        ]
    ),

    ORM\Table(name: 'manufacturers'),
    ORM\Entity(repositoryClass: ManufacturerRepository::class),
]
class Manufacturer
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column,

        Groups(['manufacturer:read'])
    ]
    private int $id;

    #[
        ORM\Column(length: 255),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Name must be at least {{ limit }} characters.',
            maxMessage: 'Name cannot be longer than {{ limit }} characters.'
        ),
        Assert\NotBlank(message: 'Name cannot be blank.'),

        Groups(['manufacturer:read', 'manufacturer:write'])
    ]
    private string $name;

    #[
        ORM\OneToMany(
            mappedBy: 'manufacturer',
            targetEntity: Car::class,
            orphanRemoval: true
        ),

        Groups(['manufacturer:read'])
    ]
    private Collection $cars;

    #[
        ORM\OneToMany(
            mappedBy: 'manufacturer',
            targetEntity: Model::class,
            orphanRemoval: true
        ),

        Groups(['manufacturer:read'])
    ]
    private Collection $models;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function setCars(Collection $cars): Manufacturer
    {
        $this->cars = $cars;
        return $this;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setManufacturer($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getManufacturer() === $this) {
                $car->setManufacturer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Model>
     */
    public function getModels(): Collection
    {
        return $this->models;
    }

    public function setModels(Collection $models): Manufacturer
    {
        $this->models = $models;
        return $this;
    }

    public function addModel(Model $model): static
    {
        if (!$this->models->contains($model)) {
            $this->models->add($model);
            $model->setManufacturer($this);
        }

        return $this;
    }

    public function removeModel(Model $model): static
    {
        if ($this->models->removeElement($model)) {
            // set the owning side to null (unless already changed)
            if ($model->getManufacturer() === $this) {
                $model->setManufacturer(null);
            }
        }

        return $this;
    }
}
