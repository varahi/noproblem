<?php

namespace App\Command;

use App\Repository\ChatRoomRepository;
use App\Repository\UserRepository;
use App\Service\ChatMessenger;
use App\Service\DataHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

//Ratchet libraries
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ChatStartCommand extends Command
{
    // php bin/console chat:start > /dev/null 2>&1 &
    // Check running command ps -ax | grep pts
    protected static $defaultName = 'chat:start';

    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Starts the websocket chat')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('Chat has started successfully');

        $output->writeln([
            'Websocket chat',
            '============',
            'Starting chat, open your browser.',
        ]);

        //$server = IoServer::factory(new HttpServer(new WsServer(new ChatMessenger($this->doctrine))), 9000);
        $server = IoServer::factory(new HttpServer(new WsServer(new DataHandler($this->doctrine))), 8080);

        $server->run();
    }
}
