<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SearchRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Search
{
    /** @var int DEFAULT_LATITUDE */
    /** @var int DEFAULT_LONGITUDE */
    private const DEFAULT_LATITUDE = 0;
    private const DEFAULT_LONGITUDE = 0;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $configuration;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SearchResult", mappedBy="search")
     */
    private $searchResults;

    /**
     * @ORM\Column(type="integer")
     */
    private $radius;

    /**
     * @ORM\Column(type="integer")
     */
    private $minPrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxPrice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PropertyType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $property_type;

    /**
     * @ORM\Column(type="array")
     */
    private $coordinates = [];

    /**
     * Search constructor.
     */
    public function __construct()
    {
        $this->coordinates = [
            'latitude' => self::DEFAULT_LATITUDE,
            'longitude' => self::DEFAULT_LONGITUDE
        ];
        $this->searchResults = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConfiguration(): ?string
    {
        return $this->configuration;
    }

    /**
     * @param string|null $configuration
     *
     * @return $this
     */
    public function setConfiguration(?string $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return Collection|SearchResult[]
     */
    public function getSearchResults(): Collection
    {
        return $this->searchResults;
    }

    /**
     * @param SearchResult $searchResult
     *
     * @return $this
     */
    public function addSearchResult(SearchResult $searchResult): self
    {
        if (!$this->searchResults->contains($searchResult)) {
            $this->searchResults[] = $searchResult;
            $searchResult->setSearch($this);
        }

        return $this;
    }

    /**
     * @param SearchResult $searchResult
     *
     * @return $this
     */
    public function removeSearchResult(SearchResult $searchResult): self
    {
        if ($this->searchResults->contains($searchResult)) {
            $this->searchResults->removeElement($searchResult);
            // set the owning side to null (unless already changed)
            if ($searchResult->getSearch() === $this) {
                $searchResult->setSearch(null);
            }
        }

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->coordinates['latitude'];
    }

    /**
     * @param float $latitude
     *
     * @return $this
     */
    public function setLatitude(float $latitude): self
    {
        $this->coordinates['latitude'] = $latitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->coordinates['longitude'];
    }

    /**
     * @param float $longitude
     *
     * @return $this
     */
    public function setLongitude(float $longitude): self
    {
        $this->coordinates['longitude'] = $longitude;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRadius(): ?int
    {
        return $this->radius;
    }

    /**
     * @param int $radius
     *
     * @return $this
     */
    public function setRadius(int $radius): self
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    /**
     * @param int $minPrice
     *
     * @return $this
     */
    public function setMinPrice(int $minPrice): self
    {
        $this->minPrice = $minPrice;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    /**
     * @param int $maxPrice
     *
     * @return $this
     */
    public function setMaxPrice(int $maxPrice): self
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    /**
     * @return PropertyType|null
     */
    public function getPropertyType(): ?PropertyType
    {
        return $this->property_type;
    }

    /**
     * @param PropertyType|null $property_type
     *
     * @return $this
     */
    public function setPropertyType(?PropertyType $property_type): self
    {
        $this->property_type = $property_type;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getCoordinates(): ?array
    {
        return $this->coordinates;
    }

    /**
     * @param array $coordinates
     *
     * @return $this
     */
    public function setCoordinates(array $coordinates): self
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateConfigurationBeforeInsert(): void
    {
        $this->setConfiguration(json_encode(self::getIdealistaConfiguration($this)));
    }

    /**
     * Converts the entity data into a json message used in the Idealista API to search for properties
     *
     * @param Search $entity
     *
     * @return array
     */
    private static function getIdealistaConfiguration(Search $entity): array
    {
        /** @var array $result */
        $result = [
            'country'      => 'pt',
            'operation'    => 'sale',
            'propertyType' => 'homes',
            'center'       => implode(', ', [$entity->getLatitude(), $entity->getLongitude()]),
            'distance'     => $entity->radius,
            'chalet'       => true,
            'maxPrice'     => $entity->getMaxPrice(),
            'minPrice'     => $entity->getMinPrice(),
            'order'        => 'price',
            'sort'         => 'asc'
        ];

        return $result;
    }
}