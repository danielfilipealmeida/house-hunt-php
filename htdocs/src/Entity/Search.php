<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SearchRepository")
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

    public function __construct()
    {
        $this->coordinates = [
            'latitude' => self::DEFAULT_LATITUDE,
            'longitude' => self::DEFAULT_LONGITUDE
        ];
        $this->searchResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getConfiguration(): ?string
    {
        return $this->configuration;
    }

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

    public function addSearchResult(SearchResult $searchResult): self
    {
        if (!$this->searchResults->contains($searchResult)) {
            $this->searchResults[] = $searchResult;
            $searchResult->setSearch($this);
        }

        return $this;
    }

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

    public function getLatitude(): ?float
    {
        return $this->coordinates['latitude'];
    }

    public function setLatitude(float $latitude): self
    {
        $this->coordinates['latitude'] = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->coordinates['longitude'];
    }

    public function setLongitude(float $longitude): self
    {
        $this->coordinates['longitude'] = $longitude;

        return $this;
    }

    public function getRadius(): ?int
    {
        return $this->radius;
    }

    public function setRadius(int $radius): self
    {
        $this->radius = $radius;

        return $this;
    }

    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    public function setMinPrice(int $minPrice): self
    {
        $this->minPrice = $minPrice;

        return $this;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(int $maxPrice): self
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    public function getPropertyType(): ?PropertyType
    {
        return $this->property_type;
    }

    public function setPropertyType(?PropertyType $property_type): self
    {
        $this->property_type = $property_type;

        return $this;
    }

    public function getCoordinates(): ?array
    {
        return $this->coordinates;
    }

    public function setCoordinates(array $coordinates): self
    {
        $this->coordinates = $coordinates;

        return $this;
    }

}