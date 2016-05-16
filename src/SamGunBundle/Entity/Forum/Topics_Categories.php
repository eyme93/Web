<?php

namespace SamGunBundle\Entity\Forum;

use Doctrine\ORM\Mapping as ORM;

/**
 * Topics_Categories
 *
 * @ORM\Table(name="Topics__categories")
 * @ORM\Entity(repositoryClass="SamGunBundle\Repository\Forum\Topics_CategoriesRepository")
 */
class Topics_Categories
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
     * @ORM\Column(name="id_categorie", type="integer")
     */
    private $idCategorie;

    /**
     * @var int
     *
     * @ORM\Column(name="id_souscategorie", type="integer")
     */
    private $idSouscategorie;


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
     * @return Topics_Categories
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
     * Set idCategorie
     *
     * @param integer $idCategorie
     *
     * @return Topics_Categories
     */
    public function setIdCategorie($idCategorie)
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }

    /**
     * Get idCategorie
     *
     * @return int
     */
    public function getIdCategorie()
    {
        return $this->idCategorie;
    }

    /**
     * Set idSouscategorie
     *
     * @param integer $idSouscategorie
     *
     * @return Topics_Categories
     */
    public function setIdSouscategorie($idSouscategorie)
    {
        $this->idSouscategorie = $idSouscategorie;

        return $this;
    }

    /**
     * Get idSouscategorie
     *
     * @return int
     */
    public function getIdSouscategorie()
    {
        return $this->idSouscategorie;
    }
}
