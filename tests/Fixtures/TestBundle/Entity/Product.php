<?php

namespace Soyuka\JsonSchemaBundle\Tests\Fixtures\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Soyuka\JsonSchemaBundle\Constraints as JsonSchemaAssert;

/**
 * @ORM\Entity(repositoryClass="Soyuka\JsonSchemaBundle\Tests\Fixtures\TestBundle\Entity\ProductRepository")
 * @JsonSchemaAssert\JsonSchema(schema = "validators/product.json")
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column
     */
    private $name;

    /**
     * @ORM\Column(nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

    /**
     * Get id.
     *
     * @return id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param name the value to set.
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get description.
     *
     * @return description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param description the value to set.
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get price.
     *
     * @return price.
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Set price.
     *
     * @param price the value to set.
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }
}
