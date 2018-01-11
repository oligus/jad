<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations as JAD;
use Doctrine\Common\Collections\ArrayCollection;
use Jad\Database\Entities\Tracks;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\PlaylistsRepository")
 * @ORM\Table(name="media_types")
 * @JAD\Header(type="media-types", readOnly=true)
 */
class MediaTypes
{
    /**
     * @ORM\Id
     * @ORM\Column(name="MediaTypeId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", type="string", length=120)
     */
    protected $name;
}