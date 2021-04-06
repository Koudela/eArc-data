<?php /** @noinspection PhpIllegalPsrClassPathInspection */ declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace {

    use eArc\Data\Entity\AbstractEmbeddedEntity;

    class MyEmbeddedEntity extends AbstractEmbeddedEntity
    {
        public string $name;
        protected string $city;
        private int $age;

        public function __construct(...$args)
        {
            $this->name = $args[0];
            $this->city = $args[1];
            $this->age = $args[2];

            unset($args);
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function getCity(): string
        {
            return $this->city;
        }

        public function getAge(): int
        {
            return $this->age;
        }
    }
}


