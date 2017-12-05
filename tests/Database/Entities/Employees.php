<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations as JAD;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\AlbumRepository")
 * @ORM\Table(name="employees")
 * @JAD\Header(type="employees")
 */
class Employees
{
    /**
     * @ORM\Id
     * @ORM\Column(name="EmployeeId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="LastName", type="string", length=20)
     */
    protected $lastName;

    /**
     * @ORM\Column(name="FirstName", type="string", length=20)
     */
    protected $firstName;

    /**
     * @ORM\Column(name="Title", type="string", length=20)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Employees")
     * @ORM\JoinColumn(name="ReportsTo", referencedColumnName="EmployeeId")
     */
    protected $reportsTo;

    /**
     * @ORM\Column(name="BirthDate", type="datetime")
     */
    protected $birthDate;

    /**
     * @ORM\Column(name="HireDate", type="datetime")
     */
    protected $hireDate;

    /**
     * @ORM\Column(name="Address", type="string", length=70)
     */
    protected $address;

    /**
     * @ORM\Column(name="City", type="string", length=40)
     */
    protected $city;

    /**
     * @ORM\Column(name="State", type="string", length=40)
     */
    protected $state;

    /**
     * @ORM\Column(name="Country", type="string", length=40)
     */
    protected $country;

    /**
     * @ORM\Column(name="PostalCode", type="string", length=10)
     */
    protected $postalCode;

    /**
     * @ORM\Column(name="Phone", type="string", length=24)
     */
    protected $phone;

    /**
     * @ORM\Column(name="Fax", type="string", length=24)
     * @JAD\Attribute(visible=false)
     */
    protected $fax;

    /**
     * @ORM\Column(name="Email", type="string", length=24)
     */
    protected $email;
}
