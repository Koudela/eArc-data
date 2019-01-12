<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\User\events\components\earc_data;

use eArc\EventTree\Event;
use eArc\EventTree\Interfaces\EventListenerInterface;

/**
 * Defines parameter used by earc/data.
 */
class Parameter implements EventListenerInterface
{
    const EARC_LISTENER_PATIENCE = -10;

    const EARC_LISTENER_COMPONENT_DEPENDENCIES = [];

    /**
     * @inheritdoc
     */
    public function process(Event $event)
    {
        return [
            'path.var.data' => dirname(__DIR__, 4) . '/var/data',
        ];
    }
}
