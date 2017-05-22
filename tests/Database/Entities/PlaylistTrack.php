<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\PlaylistTrackRepository")
 * @ORM\Table(name="playlist_track")
 * @Jad\Map\Annotations(type="playlist_track")
 */
class PlaylistTrack
{
    /**
     * @ORM\Id
     * @ORM\Column(name="PlaylistId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $playlist;

    /**
     * @ORM\ManyToMany(targetEntity="Tracks")
     * @ORM\JoinTable(name="tracks",
     *      joinColumns={@ORM\JoinColumn(name="TrackId", referencedColumnName="tracks")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tracks", referencedColumnName="moo")}
     *      )
     */
    protected $tracks;

    public function __construct()
    {
        $this->tracks =  new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTracks()
    {
        return $this->tracks;
    }
}
