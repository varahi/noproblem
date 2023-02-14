<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\District;
use App\Repository\DistrictRepository;
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

class ImportDistrictsCommand extends Command
{
    protected static $defaultName = 'import:district:start';

    /**
     * @var string
     */
    private $defailtDomain;

    /**
     * @var string
     */
    private $defailtScheme;

    /**
     * @param string $defailtDomain
     * @param string $defailtScheme
     * @param CityRepository $cityRepository
     * @param DistrictRepository $districtRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        string $defailtDomain,
        string $defailtScheme,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        EntityManagerInterface $em
    ) {
        $this->defailtDomain = $defailtDomain;
        $this->defailtScheme = $defailtScheme;
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Import dictricts from CSV file')
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

        $csvFile = file($this->defailtScheme .'://'. $this->defailtDomain . '/data/districts.csv');

        foreach ($csvFile as $line) {
            $data = str_getcsv($line);
            $district = $this->districtRepository->findOneBy(['name' => $data['1']]);
            $city = $this->cityRepository->findOneBy(['id' => $data['0']]);
            if (!$district) {
                $district = new District();
                $district->setName($data['1']);
                $district->setCity($city);
                //$district->isHidden((bool)0);
                $this->em->persist($district);
                $this->em->flush();
            }
        }

        return 0;
    }
}
