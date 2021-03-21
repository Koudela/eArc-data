<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data;

use eArc\Data\Entity\Interfaces\Events\PostLoadInterface;
use eArc\Data\Entity\Interfaces\Events\PostPersistInterface;
use eArc\Data\Entity\Interfaces\Events\PostRemoveInterface;
use eArc\Data\Entity\Interfaces\Events\PreLoadInterface;
use eArc\Data\Entity\Interfaces\Events\PrePersistInterface;
use eArc\Data\Entity\Interfaces\Events\PreRemoveInterface;
use eArc\Data\Manager\Interfaces\Events\OnLoadInterface;
use eArc\Data\Manager\Interfaces\Events\OnPersistInterface;
use eArc\Data\Manager\Interfaces\Events\OnRemoveInterface;

interface ParameterInterface
{
    const TAG_POST_LOAD = PostLoadInterface::class;
    const TAG_POST_PERSIST = PostPersistInterface::class;
    const TAG_POST_REMOVE = PostRemoveInterface::class;
    const TAG_PRE_LOAD = PreLoadInterface::class;
    const TAG_PRE_PERSIST = PrePersistInterface::class;
    const TAG_PRE_REMOVE = PreRemoveInterface::class;

    const TAG_ON_LOAD = OnLoadInterface::class;
    const TAG_ON_PERSIST = OnPersistInterface::class;
    const TAG_ON_REMOVE = OnRemoveInterface::class;
}
