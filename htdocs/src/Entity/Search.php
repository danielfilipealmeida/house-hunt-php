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

    public function __construct()
    {
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

}