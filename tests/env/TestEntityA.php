<?php declare(strict_types=1);

namespace eArc\DataTests\env;

use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Manager\UniqueEntityProxy;
use function eArc\Data\Manager\data_load;

class TestEntityA extends AbstractEntity
{
    public $publicVar = 1;
    protected $protectedVar = '1';
    private $privateVar = true;

    /** @var string */
    protected $testEntityB_PK;

    /** @var UniqueEntityProxy */
    protected $testEntityB;

    protected $entity;

    public function getTestEntityB(): TestEntityB
    {
//        $testEntityB = new TestEntityB();
//        return data_save($testEntityB);

        return $this->testEntityB->load(TestEntityB::class);
    }

    public function setTestEntityB(TestEntityB $testEntityB): self
    {
        $this->testEntityB = di_static(UniqueEntityProxy::class)::getInstance($testEntityB);

        return $this;
    }

    public function getEntity()
    {
        return data_load(TestEntityB::class, $this->entity);
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity->getPrimaryKey();
    }

    public function getPublicVar(): int
    {
        return $this->publicVar;
    }

    public function setPublicVar(int $publicVar): self
    {
        $this->publicVar = $publicVar;

        return $this;
    }

    public function getProtectedVar(): string
    {
        return $this->protectedVar;
    }

    public function setProtectedVar(string $protectedVar): self
    {
        $this->protectedVar = $protectedVar;

        return $this;
    }

    protected function getPrivateVar(): bool
    {
        return $this->privateVar;
    }

    public function setPrivateVar(bool $privateVar): self
    {
        $this->privateVar = $privateVar;

        return $this;
    }
}
