<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations;
use Doctrine\Common\Collections\ArrayCollection;
use Jad\Database\Entities\Tracks;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\PlaylistsRepository")
 * @ORM\Table(name="media_types")
 * @Jad\Map\Annotations(type="media-type")
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