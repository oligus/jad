<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use Jad\Map\Annotations as JAD;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="invoices")
 * @JAD\Header(type="invoices")
 */
class Invoices
{
    /**
     * @ORM\Id
     * @ORM\Column(name="InvoiceId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Customers")
     * @ORM\JoinColumn(name="CustomerId", referencedColumnName="CustomerId")
     */
    protected $customers;

    /**
     * @ORM\Column(name="InvoiceDate", type="datetime")
     */
    protected $invoiceDate;

    /**
     * @ORM\Column(name="BillingAddress", type="string", length=70)
     */
    protected $billingAddress;

    /**
     * @ORM\Column(name="BillingCity", type="string", length=40)
     */
    protected $billingCity;

    /**
     * @ORM\Column(name="BillingState", type="string", length=40)
     */
    protected $billingState;

    /**
     * @ORM\Column(name="BillingCountry", type="string", length=40)
     */
    protected $billingCountry;

    /**
     * @ORM\Column(name="BillingPostalCode", type="string", length=10)
     */
    protected $billingPostalCode;

    /**
     * @ORM\Column(name="Total", type="decimal", precision=10, scale=2)
     */
    protected $total;

    /**
     * @ORM\OneToMany(targetEntity="InvoiceItems", mappedBy="invoice")
     */
    protected $invoiceItems;

}