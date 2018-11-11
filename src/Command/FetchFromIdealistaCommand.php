<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Idealista;

class FetchFromIdealistaCommand extends Command
{

    /** @var Idealista */
    private $idealista;

    /**
     * @param Idealista $idealista
     */
    public function __construct(Idealista $idealista)
    {
        $this->idealista = $idealista;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('idealista:fetch')
            ->setDescription('Fetches stored searches from the Idealista API.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}