<?php

namespace Jad\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="invoice_items")
 * @Jad\Map\Annotations(type="invoice-items")
 */
class InvoiceItems
{
    /**
     * @ORM\Id
     * @ORM\Column(name="InvoiceLineId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="InvoiceId", type="integer", length=120)
     */
    protected $invoiceId;

    /**
     * @ORM\Column(name="TrackId", type="integer", length=120)
     */
    protected $trackId;

    /**
     * @ORM\Column(name="UnitPrice", type="decimal", precision=10, scale=2)
     */
    protected $unitPrice;

    /**
     * @ORM\Column(name="Quantity", type="integer", length=120)
     */
    protected $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="Invoices", fetch="EAGER")
     * @ORM\JoinColumn(name="InvoiceId", referencedColumnName="InvoiceId")
     */
    protected $invoice;

    /**
    FOREIGN KEY ([TrackId]) REFERENCES "tracks" ([TrackId])
    ON DELETE NO ACTION ON UPDATE NO ACTION

     */
}