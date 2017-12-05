<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations as JAD;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="customers")
 * @JAD\Header(type="customers", paginate=true)
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
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * @ORM\Column(name="LastName", type="string", length=20)
     * @Assert\NotBlank()
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
     * @Assert\Email(message="Invalid email")
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Employees")
     * @ORM\JoinColumn(name="SupportRepId", referencedColumnName="EmployeeId")
     */
    protected $supportRep;

    /**
     * @ORM\OneToMany(targetEntity="Invoices", mappedBy="customers")
     */
    protected $invoices;
}