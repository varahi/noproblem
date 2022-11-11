<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use App\Controller\Chat;

class ChatCommand extends Command
{
    //composer require wrep/daemonizable-command
    // php bin/console sockets:start-chat
    protected function configure()
    {
        $this
            ->setName('sockets:start-chat')
            ->setHelp("Starts  the chat socket demo")
            ->setDescription('Starts the socket chat demo')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Chat socket',// A line
            '============',// Another line
            'Starting chat, open your browser.',// Empty line
        ]);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            10000
        );

        $server->run();
    }
}
