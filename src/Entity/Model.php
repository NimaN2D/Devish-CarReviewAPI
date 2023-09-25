<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        normalizationContext: ['groups' => ['model:read']],
        denormalizationContext: ['groups' => ['model:write']],
    ),
    ApiResource(
        uriTemplate: '/manufacturers/{manufacturer}/models',
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
            'manufacturer' => 'exact',
        ]
    ),
    ApiFilter(
        OrderFilter::class,
        properties: [
            'name',
        ]
    ),

    ORM\Table(name: 'models'),
    ORM\Entity(repositoryClass: ModelRepository::class),
]
class Model
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column,

        Groups(['model:read', 'car:read'])
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

        Groups(['model:read', 'model:write', 'car:read'])
    ]
    private string $name;

    #[
        ORM\OneToMany(
            mappedBy: 'model',
            targetEntity: Car::class,
            orphanRemoval: true
        ),

        Groups(['model:read'])
    ]
    private Collection $cars;

    #[
        ORM\OneToOne(
            inversedBy: 'model',
            cascade: [
                'persist',
                'remove'
            ]
        ),
        ORM\JoinColumn(nullable: false),
        Assert\Valid,
        Assert\NotBlank(message: 'Manufacturer cannot be blank.'),

        Groups(['model:read', 'model:write'])
    ]
    private Manufacturer $manufacturer;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setModel($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            if ($car->getModel() === $this) {
                $car->setModel(null);
            }
        }

        return $this;
    }

    public function getManufacturer(): Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }
}
