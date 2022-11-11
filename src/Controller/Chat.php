<?php

namespace App\Controller;

use App\Manager\ChatManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

//use Ratchet\WebSocket\Version\RFC6455\Connection;

class Chat implements MessageComponentInterface
{
    /** @var ConnectionInterface[] */
    protected $clients = [];

    /** @var ChatManager */
    protected $chm;

    public function __construct(ChatManager $chm)
    {
        $this->chm = $chm;
        //$this->chm->truncateChats();
    }

    /**
     * @param ConnectionInterface conn
     * @return string
     */
    private function getRid(ConnectionInterface $conn)
    {
        return $conn->resourceId;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$this->getRid($conn)] = $conn;
    }

    public function onClose(ConnectionInterface $conn)
    {
        $rid = array_search($conn, $this->clients);
        if ($user = $this->chm->getUserByRid($rid)) {
            $chat = $user->getChat();
            $this->chm->removeUserFromChat($user, $chat);
            foreach ($chat->getUsers() as $user) {
                $this->clients[$user->getRid()]->close();
            }
        }
        unset($this->clients[$rid]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = json_decode($msg, true);
        $rid = array_search($from, $this->clients);
        switch ($msg['type']) {
            case 'request':
                $chat = $this->chm->findOrCreateChatForUser($rid);
                if ($chat->getIsCompleted()) {
                    $msg = json_encode(['type' => 'response']);
                    foreach ($chat->getUsers() as $user) {
                        $conn = $this->clients[$user->getRid()];
                        $conn->send($msg);
                    }
                }
                break;
            case 'message':
                if ($chat = $this->chm->getChatByUser($rid)) {
                    foreach ($chat->getUsers() as $user) {
                        $conn = $this->clients[$user->getRid()];
                        $msg['from'] = $conn === $from ? 'me' : 'guest';
                        $conn->send(json_encode($msg));
                    }
                }
                break;
        }
    }
}
