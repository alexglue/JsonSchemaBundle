<?php

namespace Soyuka\JsonSchemaBundle\Tests\Fixtures\TestBundle\Directory;

class Person
{
    private $id;
    private $name;
    private $email;
    private $gender;

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
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get email.
     *
     * @return email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param email the value to set.
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get gender.
     *
     * @return gender.
     */
    public function getGender(): bool
    {
        return $this->gender;
    }

    /**
     * Set gender.
     *
     * @param gender the value to set.
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }
}
