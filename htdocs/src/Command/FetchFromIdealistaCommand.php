<?php

namespace App\Command;

use App\Service\Idealista;
use App\Entity\SearchResult;
use Doctrine\ORM\EntityManager;
use App\Repository\SearchRepository;
use App\Repository\SearchResultRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchFromIdealistaCommand extends Command
{

    /** @var Idealista */
    private $idealista;

    /** @var SearchRepository */
    private $searchRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var SearchResultRepository */
    //private $searchResultRepository;

    /**
     * @param Idealista $idealista
     * @param EntityManager $entityManager
     * @param SearchRepository $searchRepository
     */
    public function __construct(
        Idealista $idealista, 
        EntityManager $entityManager,
        SearchRepository $searchRepository
    ) {
        $this->idealista = $idealista;
        $this->entityManager = $entityManager;
        $this->searchRepository = $searchRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('idealista:fetch')
            ->setDescription('Fetches stored searches from the Idealista API.');

    }

    /**
     * Fetches a search from the Idealista API
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // need a search ID.
        // needs to get the search array
        // needs to use the service
        // needs to store the search

        $testParameterArray = [
            'country' => 'pt',
            'operation' => 'sale',
            'propertyType' => 'homes',
            'center' => '37.0160273,-7.93204812',
            'distance' => 5000,
            'chalet' => true,
            'maxPrice' => 150000,
            'order' => 'price',
            'sort' => 'asc'
        ];


        $result = $this->idealista->search($testParameterArray);

        $searchResult = new SearchResult();
        $searchResult->setDate(new \DateTime())
            ->setSearch(Idealista::getSearchParametersString($testParameterArray))
            ->setJson($result);
        
        $this->entityManager-persist($searchResult);

    }
}