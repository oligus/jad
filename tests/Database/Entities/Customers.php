<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations as JAD;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="customers")
 * @JAD\Head(type="customers")
 */
class Customers
{
    /**
     * @ORM\Id
     * @ORM\Column(name="CustomerId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="FirstName", type="string", length=40)
     */
    protected $firstName;

    /**
     * @ORM\Column(name="LastName", type="string", length=20)
     */
    protected $lastName;

    /**
     * @ORM\Column(name="Company", type="string", length=80)
     */
    protected $company;

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
     */
    protected $fax;

    /**
     * @ORM\Column(name="Email", type="string", length=60, nullable=false)
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Employees", fetch="EAGER")
     * @ORM\JoinColumn(name="SupportRepId", referencedColumnName="EmployeeId")
     */
    protected $supportRep;

    /**
     * @ORM\OneToMany(targetEntity="Invoices", mappedBy="customer")
     */
    protected $invoices;
}