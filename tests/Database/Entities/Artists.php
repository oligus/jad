<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\ArtistsRepository")
 * @ORM\Table(name="artists")
 */
class Artists
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ArtistId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", type="string", length=120)
     */
    protected $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Artists
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Artists
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
