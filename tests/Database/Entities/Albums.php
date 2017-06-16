<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations as JAD;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\AlbumRepository")
 * @ORM\Table(name="albums")
 * @JAD\Head(type="albums")
 */
class Albums
{
    /**
     * @ORM\Id
     * @ORM\Column(name="AlbumId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="Title", type="string", length=160)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Artists")
     * @ORM\JoinColumn(name="ArtistId", referencedColumnName="ArtistId")
     */
    protected $artist;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Albums
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Albums
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @param mixed $artist
     * @return Albums
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
        return $this;
    }

}
