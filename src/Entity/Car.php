<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        normalizationContext: ['groups' => ['car:read']],
        denormalizationContext: ['groups' => ['car:write']],
    ),
    ApiResource(
        uriTemplate: '/manufacturers/{manufacturer}/cars',
        operations: [new Get()],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'cars',
                fromClass: Manufacturer::class
            )
        ]
    ),
    ApiResource(
        uriTemplate: '/models/{model}/cars',
        operations: [new Get()],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'cars',
                fromClass: Model::class
            )
        ]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'name' => 'partial',
            'manufacturer' => 'exact',
            'model' => 'exact',
            'year' => 'exact',
        ]
    ),
    ApiFilter(
        OrderFilter::class,
        properties: [
            'name',
            'year',
        ]
    ),

    ORM\Table(name: 'cars'),
    ORM\Entity(repositoryClass: CarRepository::class),
]
class Car
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column,

        Groups(['car:read'])
    ]
    private int $id;

    #[
        ORM\Column(length: 255),
        Assert\NotBlank(message: 'Name cannot be blank.'),

        Groups(['car:read','car:write'])
    ]
    private string $name;

    #[
        ORM\Column,
        Assert\NotBlank(message: 'Year cannot be blank.'),
        Assert\Range(
            notInRangeMessage: 'Year must be between {{ min }} and {{ max }}.',
            min: 1900,
            max: 2023, //TODO max should be dynamic
        ),

        Groups(['car:read','car:write'])
    ]
    private int $year;

    #[
        ORM\ManyToOne(inversedBy: 'cars'),
        ORM\JoinColumn(nullable: false),
        Assert\NotBlank(message: 'Manufacturer cannot be blank.'),
        Assert\Valid,

        Groups(['car:read', 'car:write', 'manufacturer:read'])
    ]
    private Manufacturer $manufacturer;

    #[
        ORM\ManyToOne(inversedBy: 'cars'),
        ORM\JoinColumn(nullable: false),
        Assert\NotBlank(message: 'Model cannot be blank.'),
        Assert\Valid,

        Groups(['car:read', 'car:write'])
    ]
    private Model $model;

    #[
        ORM\ManyToOne(inversedBy: 'cars'),
        ORM\JoinColumn(nullable: false),
        Assert\NotBlank(message: 'User cannot be blank.'),
        Assert\Valid,

        Groups(['car:read'])
    ]
    private User $creator;

    #[
        ORM\OneToMany(mappedBy: 'car', targetEntity: Review::class, orphanRemoval: true),
        ORM\OrderBy(['createdAt' => 'DESC']),

        Groups(['car:read'])
    ]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
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

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(User $model): static
    {
        $this->creator = $model;

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCar($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCar() === $this) {
                $review->setCar(null);
            }
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
