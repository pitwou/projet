<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="bar_code", type="bigint", unique=true)
     */
    private $barCode;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_consult", type="integer")
     */
    private $nbConsult = 1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_view", type="datetime")
     */
    private $lastView;

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
     * Set barCode
     *
     * @param integer $barCode
     *
     * @return Product
     */
    public function setBarCode($barCode)
    {
        $this->barCode = $barCode;

        return $this;
    }

    /**
     * Get barCode
     *
     * @return int
     */
    public function getBarCode()
    {
        return $this->barCode;
    }

    /**
     * Set nbConsult
     *
     * @param integer $nbConsult
     *
     * @return Product
     */
    public function setNbConsult($nbConsult)
    {
        $this->nbConsult = $nbConsult;

        return $this;
    }

    /**
     * Get nbConsult
     *
     * @return int
     */
    public function getNbConsult()
    {
        return $this->nbConsult;
    }

    /**
     * Set lastView
     *
     * @param \DateTime $lastView
     *
     * @return Product
     */
    public function setLastView($lastView)
    {
        $this->lastView = $lastView;

        return $this;
    }

    /**
     * Get lastView
     *
     * @return \DateTime
     */
    public function getLastView()
    {
        return $this->lastView;
    }

}
