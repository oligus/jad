<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\TracksRepository")
 * @ORM\Table(name="tracks")
 * @Jad\Map\Annotations(type="tracks")
 */
class Tracks
{
    /**
     * @ORM\Id
     * @ORM\Column(name="TrackId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", type="string", length=200)
     */
    protected $name;

    /**
     * @ORM\Column(name="Composer", type="string", length=220)
     */
    protected $composer;

    /**
     * @ORM\Column(name="UnitPrice", type="decimal", precision=10, scale=2)
     */
    protected $price;
}
