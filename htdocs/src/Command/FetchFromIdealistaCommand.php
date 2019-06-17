<?php

namespace App\Command;

use App\Entity\Search;
use App\Entity\SearchResult;
use Doctrine\ORM\EntityManager;
use App\Repository\SearchRepository;
use App\Repository\SearchResultRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Service\Idealista;

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
     * @param Idealista        $idealista
     * @param EntityManager    $entityManager
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

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('idealista:fetch')
            ->setDescription('Fetches stored searches from the Idealista API.')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the record in the search table.');
    }

    /**
     * Queries the Search table and returns the search configuration in array form.
     *
     * @param int $id
     *
     * @return array
     */
    protected function getSearchConfigurationArrayFromSearchId($id): array
    {
        /** @var Search $search */
        $search = $this->searchRepository->findOneBy(['id' => $id]);

        return \json_decode($search->getConfiguration(), true);
    }

    /**
     * Fetches a search from the Idealista API.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var array $searchConfigurationArray */
        $searchConfigurationArray = $this->getSearchConfigurationArrayFromSearchId($input->getArgument('id'));

        $result = $this->idealista->search($searchConfigurationArray);

        $searchResult = new SearchResult();
        $searchResult->setDate(new \DateTimeImmutable())
            ->setSearch(Idealista::getSearchParametersString($searchConfigurationArray))
            ->setJson($result);

        $this->entityManager->persist($searchResult);
        $this->entityManager->flush();
    }
}
