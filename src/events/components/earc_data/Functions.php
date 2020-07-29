<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\events\components\earc_data;


/**
 * Creates an unique object/data identifier string.
 *
 * @param DataInterface $data
 */
function createIdentifier($data, $path): void
{
    $identifier = $data->getIdentifier();

    if (null === $identifier) {
        do {
            $identifier = randomLowerAlphaNumericalString();
        } while (isset($data[$identifier]) || is_file($path.'/'.$identifier.'.data'));

        $data->expose()->setIdentifier($identifier);
    }
}

if (!function_exists('\\eArc\\DataStore\\events\\components\\earc_data\\randomLowerAlphaNumericalString')) {
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
