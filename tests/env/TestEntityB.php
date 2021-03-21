<?php declare(strict_types=1);

namespace eArc\DataTests\env;

use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Entity\Interfaces\Index\IsIndexedInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\AutoincrementPrimaryKeyInterface;

class TestEntityB extends AbstractEntity implements AutoincrementPrimaryKeyInterface, IsIndexedInterface
{
    protected $someValue;

    /**
     * @return mixed
     */
    public function getSomeValue()
    {
        return $this->someValue;
    }

    /**
     * @param mixed $someValue
     *
     * @return self
     */
    public function setSomeValue($someValue): self
    {
        $this->someValue = $someValue;

        return $this;
    }

    public static function getIndexedProperties(): array
    {
        return ['someValue' => IsIndexedInterface::TYPE_UNIQUE];
    }
}
