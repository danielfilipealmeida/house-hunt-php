<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SearchResultRepository")
 */
class SearchResult
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
    private $search;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $date;

    /**
     * @ORM\Column(type="json");
     */
    private $json;


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of search
     */ 
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set the value of search
     *
     * @return  self
     */ 
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of json
     */ 
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Set the value of json
     *
     * @return  self
     */ 
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }
}