<?php

namespace SamGunBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demande
 *
 * @ORM\Table(name="demande")
 * @ORM\Entity(repositoryClass="SamGunBundle\Repository\DemandeRepository")
 */
class Demande
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
     * @ORM\Column(name="id_salarie", type="integer")
     */
    private $idSalarie;

    /**
     * @var int
     *
     * @ORM\Column(name="id_formation", type="integer")
     */
    private $idFormation;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;


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
     * Set idSalarie
     *
     * @param integer $idSalarie
     *
     * @return Demande
     */
    public function setIdSalarie($idSalarie)
    {
        $this->idSalarie = $idSalarie;

        return $this;
    }

    /**
     * Get idSalarie
     *
     * @return int
     */
    public function getIdSalarie()
    {
        return $this->idSalarie;
    }

    /**
     * Set idFormation
     *
     * @param integer $idFormation
     *
     * @return Demande
     */
    public function setIdFormation($idFormation)
    {
        $this->idFormation = $idFormation;

        return $this;
    }

    /**
     * Get idFormation
     *
     * @return int
     */
    public function getIdFormation()
    {
        return $this->idFormation;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Demande
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
