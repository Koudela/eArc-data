<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\User\events\components\earc_user;

use eArc\Data\Data;
use eArc\Data\DataFactory;
use eArc\Data\DataRepository;
use eArc\Data\Exceptions\DataException;
use eArc\Data\Exceptions\NoDataException;
use eArc\Data\Interfaces\Application\DataInterface;
use eArc\Data\Interfaces\Application\DataRepositoryInterface;
use eArc\Data\Interfaces\Exceptions\DataExceptionInterface;
use eArc\Data\Interfaces\Exceptions\DataExistsException;
use eArc\Data\Interfaces\Exceptions\DataExistsExceptionInterface;
use eArc\Data\Interfaces\Exceptions\NoDataExceptionInterface;
use eArc\Data\Interfaces\Persistence\DataFactoryInterface;
use eArc\Data\Interfaces\Persistence\PersistableDataInterface;
use eArc\Data\PersistableData;
use eArc\EventTree\Event;
use eArc\EventTree\Interfaces\EventListenerInterface;

/**
 * Defines dependencies earc/data uses.
 */
class Dependencies implements EventListenerInterface
{
    const EARC_LISTENER_PATIENCE = 10;

    const EARC_LISTENER_COMPONENT_DEPENDENCIES = [];

    /**
     * @inheritdoc
     */
    public function process(Event $event)
    {
        return [
            DataFactory::class => [],

            DataRepository::class => [DataFactory::class, 'path.var.data'],

            DataInterface::class => Data::class,

            DataRepositoryInterface::class => DataRepository::class,

            DataExceptionInterface::class => DataException::class,

            DataExistsExceptionInterface::class => DataExistsException::class,

            NoDataExceptionInterface::class => NoDataException::class,

            DataFactoryInterface::class => DataFactory::class,

            PersistableDataInterface::class => PersistableData::class,
        ];
    }
}
