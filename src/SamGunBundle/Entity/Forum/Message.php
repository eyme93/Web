<?php

namespace SamGunBundle\Entity\Forum;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="Message")
 * @ORM\Entity(repositoryClass="SamGunBundle\Repository\Forum\MessageRepository")
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_topic", type="integer")
     */
    private $idTopic;

    /**
     * @var int
     *
     * @ORM\Column(name="id_posteur", type="integer")
     */
    private $idPosteur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_heure_post", type="datetime")
     */
    private $dateHeurePost;


    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idTopic
     *
     * @param integer $idTopic
     *
     * @return Message
     */
    public function setIdTopic($idTopic)
    {
        $this->idTopic = $idTopic;

        return $this;
    }

    /**
     * Get idTopic
     *
     * @return int
     */
    public function getIdTopic()
    {
        return $this->idTopic;
    }

    /**
     * Set idPosteur
     *
     * @param integer $idPosteur
     *
     * @return Message
     */
    public function setIdPosteur($idPosteur)
    {
        $this->idPosteur = $idPosteur;

        return $this;
    }

    /**
     * Get idPosteur
     *
     * @return int
     */
    public function getIdPosteur()
    {
        return $this->idPosteur;
    }

    /**
     * Set dateHeurePost
     *
     * @param \DateTime $dateHeurePost
     *
     * @return Message
     */
    public function setDateHeurePost($dateHeurePost)
    {
        $this->dateHeurePost = $dateHeurePost;

        return $this;
    }

    /**
     * Get dateHeurePost
     *
     * @return \DateTime
     */
    public function getDateHeurePost()
    {
        return $this->dateHeurePost;
    }

    /**
     * Set dateHeureEdition
     *
     * @param \DateTime $dateHeureEdition
     *
     * @return Message
     */



    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Message
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }
}
