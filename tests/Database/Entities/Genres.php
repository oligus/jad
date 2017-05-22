<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\GenresRepository")
 * @ORM\Table(name="genres")
 * @Jad\Map\Annotations(type="genres")
 */
class Genres
{
    /**
     * @ORM\Id
     * @ORM\Column(name="GenreId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", type="string", length=120)
     */
    protected $name;
}
