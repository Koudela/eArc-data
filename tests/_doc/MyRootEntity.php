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

    use eArc\Data\Collection\EmbeddedCollection;
    use eArc\Data\Entity\AbstractEntity;

    class MyRootEntity extends AbstractEntity
    {
        protected EmbeddedCollection $myEmbeddedEntityCollection;

        public function __construct(...$args)
        {
            $this->myEmbeddedEntityCollection = new EmbeddedCollection($this, MyEmbeddedEntity::class);

            unset($args);
        }

        public function getMyEmbeddedEntityCollection(): EmbeddedCollection
        {
            return $this->myEmbeddedEntityCollection;
        }
    }
}


