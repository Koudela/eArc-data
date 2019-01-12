<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\events\components\earc_data;

use function eArc\ComponentDI\events\components\earc_component_d_i\getClassName as getComponentDIClassName;
use eArc\ComponentDI\Interfaces\Exceptions\InvalidConfigurationExceptionInterface;
use eArc\ComponentDI\ComponentContainer;
use eArc\DI\Interfaces\ContainerInterface;
use eArc\EventTree\Event;
use eArc\EventTree\Interfaces\EventListenerInterface;

/**
 * Defines functions used by earc/data.
 */
class Functions implements EventListenerInterface
{
    const EARC_LISTENER_PATIENCE = -20;

    const EARC_LISTENER_COMPONENT_DEPENDENCIES = [];

    /**
     * @inheritdoc
     */
    public function process(Event $event)
    {
        global $earcDataEvent;

        $earcDataEvent = $event;

        if (!function_exists('\\eArc\\Data\\events\\components\\earc_data\\randomLowerAlphaNumericalString'))
        {
            /**
             * Get a random string composed of lower english letters and decimal
             * digits.
             *
             * @param int $length
             *
             * @return string
             */
            function randomLowerAlphaNumericalString(int $length = 64): string
            {
                $randStr = '';

                for ($i = 0; $i < $length; $i++) {
                    try {
                        $randInt = random_int(48, 83);
                    } catch (\Exception $exception) {
                        $randInt = rand(48, 83);
                    }
                    $randStr .= chr($randInt < 58 ? $randInt : $randInt + 39);
                }

                return $randStr;
            }
        }

        if (!function_exists('\\eArc\\Data\\events\\components\\earc_data\\getComponentContainer'))
        {
            /**
             * Get the components container.
             *
             * @return ContainerInterface
             */
            function getComponentContainer(): ContainerInterface
            {
                /** @var Event $earcDataEvent */
                global $earcDataEvent;

                return $earcDataEvent->get(ComponentContainer::CONTAINER_BAG)->get('earc_data');
            }
        }

        if (!function_exists('\\eArc\\Data\\events\\components\\earc_data\\getClassName'))
        {
            /**
             * Get the class implementing the interface for the earc_data
             * component.
             *
             * @param string $interfaceName
             *
             * @return string
             */
            function getClassName(string $interfaceName): string
            {
                $className = getComponentContainer()->get($interfaceName);

                if (!is_subclass_of($className, $interfaceName)) {
                    $invalidConfigurationExceptionClass = getComponentDIClassName(
                        InvalidConfigurationExceptionInterface::class
                    );
                    throw new $invalidConfigurationExceptionClass(sprintf(
                        '`%s` has to implement `%s`',
                        $className,
                        $interfaceName
                    ));
                }

                return $className;
            }
        }

        return [];
    }
}
