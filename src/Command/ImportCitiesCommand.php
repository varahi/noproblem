<?php

namespace App\Command;

use App\Entity\City;
use App\Service\ChatMessenger;
use Doctrine\Persistence\ManagerRegistry;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\Repository\CityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

//Ratchet libraries

class ImportCitiesCommand extends Command
{
    // php bin/console import:cities:start
    // Check running command ps -ax | grep pts
    protected static $defaultName = 'import:cities:start';

    /**
     * @var string
     */
    private $defailtDomain;

    /**
     * @var string
     */
    private $defailtScheme;

    /*
     * @var CityRepository
     */
    private $cityRepository;


    /**
     * @param string $defailtDomain
     * @param string $defailtScheme
     * @param CityRepository $cityRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        string $defailtDomain,
        string $defailtScheme,
        CityRepository $cityRepository,
        EntityManagerInterface $em
    ) {
        $this->defailtDomain = $defailtDomain;
        $this->defailtScheme = $defailtScheme;
        $this->cityRepository = $cityRepository;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Import cities from json file')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $jsonFile = $this->defailtScheme .'://'. $this->defailtDomain . '/data/russian-cities.json';
        $content = file_get_contents($jsonFile);
        $cities = \json_decode($content);

        if (is_array($cities)) {
            foreach ($cities as $item) {
                $cityData = [
                    'name' => $item->name,
                    'lat' => $item->coords->lat,
                    'lng' => $item->coords->lon
                ];
                if ($cityData['name'] !=='') {
                    $city = $this->cityRepository->findOneBy(['name' => $cityData['name']]);
                    if (!$city) {
                        $count = count($cities);
                        $city = new City();
                        $city->setIsHidden((bool)0);
                        $city->setName($cityData['name']);
                        $city->setLatitude((string)$cityData['lat']);
                        $city->setLongitude((string)$cityData['lng']);
                        $this->em->persist($city);
                        $this->em->flush();
                    }
                }
                // Test
                //file_put_contents('data.txt', $cityData['name'] . PHP_EOL, FILE_APPEND|LOCK_EX);
            }
        }

        $io->success(sprintf('Import "%d" cities', $count));

        return 0;
    }
}
