<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class ChatUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     *
     * @var int
     */
    private $rid;

    /**
     * @ORM\ManyToOne(targetEntity="Chat", inversedBy="users")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     * @var Chat
     */
    private $Chat;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rid
     *
     * @param integer $rid
     * @return ChatUser
     */
    public function setRid($rid)
    {
        $this->rid = $rid;

        return $this;
    }

    /**
     * Get rid
     *
     * @return string
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set Chat
     *
     * @param Chat $chat
     * @return ChatUser
     */
    public function setChat(Chat $chat = null)
    {
        $this->Chat = $chat;
        $chat->addUser($this);

        return $this;
    }

    /**
     * Get Chat
     *
     * @return Chat
     */
    public function getChat()
    {
        return $this->Chat;
    }
}
