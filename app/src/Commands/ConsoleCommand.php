<?php

namespace Sainsburys\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sainsburys\Classes\ProductScraper;


class ConsoleCommand extends Command
{
    /** @var ProductScraper */
    protected $scraper;

    public function __construct() {
        /** @var ProductScraper */
        $this->scraper = new ProductScraper();
        parent::__construct();
    }
     /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('scrape:products')
             ->setDescription('A console application that scrapes the Sainsbury’s grocery site - Ripe Fruits page');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Fetching products...');
        $output->writeln($this->scraper->scrape());
        $output->writeln('Finished fetching the products.');
    }

}