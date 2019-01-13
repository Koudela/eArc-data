<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data;

use function eArc\Data\events\components\earc_data\randomLowerAlphaNumericalString;
use function eArc\Data\events\components\earc_data\getClassName;
use eArc\Data\Interfaces\Application\DataInterface;
use eArc\Data\Interfaces\Application\DataRepositoryInterface;
use eArc\Data\Interfaces\Exceptions\DataExceptionInterface;
use eArc\Data\Interfaces\Exceptions\DataExistsExceptionInterface;
use eArc\Data\Interfaces\Exceptions\NoDataExceptionInterface;
use eArc\Data\Interfaces\Persistence\DataFactoryInterface;
use eArc\Data\Interfaces\Persistence\PersistableDataInterface;

/**
 * Data repository.
 */
class DataRepository implements DataRepositoryInterface
{
    /** @var DataFactoryInterface */
    protected $dataFactory;

    /** @var $path */
    protected $path;

    /** @var DataInterface[] */
    protected $data = [];

    public function __construct(
        DataFactoryInterface $dataFactory,
        string $pathVarData)
    {
        $this->dataFactory = $dataFactory;

        if (!is_dir($pathVarData)) {
            $dataExceptionClass = getClassName(DataExceptionInterface::class);
            throw new $dataExceptionClass(sprintf(
                '`%s` has to be a valid directory.',
                $pathVarData
            ));
        }
        $this->path = $pathVarData;
    }

    /**
     * @inheritdoc
     */
    public function find(string $identifier): DataInterface
    {
        if (!is_file($this->path.'/'.$identifier.'.data')) {
            $noDataExceptionClass = getClassName(NoDataExceptionInterface::class);
            throw new $noDataExceptionClass();
        }

        if (!isset($this->data[$identifier])) {
            $data = unserialize(file_get_contents($this->path.'/'.$identifier.'.data'));
            $this->data[$identifier] = $this->dataFactory->make($data);
        }

        return $this->data[$identifier];

    }

    /**
     * @inheritdoc
     */
    public function findAll(): array
    {
        foreach(scandir($this->path) as $file) {
            if (substr($file, -5) === '.data') {
                $identifier = substr($file, 0, -5);
                if (!isset($this->data[$identifier])) {
                    $this->find($identifier);
                }
            }
        }

        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function create(?string $identifier = null): DataInterface
    {
        $persistableDataClass = getClassName(PersistableDataInterface::class);
        /** @var PersistableDataInterface $persistableData */
        $persistableData = new $persistableDataClass();

        if (null !== $identifier) {
            if (isset($this->data[$identifier]) || is_file($this->path.'/'.$identifier.'.data')) {
                $dataExistsExceptionClass = getClassName(DataExistsExceptionInterface::class);
                throw new $dataExistsExceptionClass();
            }
            $persistableData->setIdentifier($identifier);
        }

        $data = $this->dataFactory->make($persistableData);

        $this->createIdentifier($data);

        $this->data[$data->getIdentifier()] = $data;

        $this->update($data);

        return $data;

    }

    /**
     * @inheritdoc
     */
    public function update(DataInterface $data): void
    {
        if (!isset($this->data[$data->getIdentifier()])) {
            $noDataExceptionClass = getclassName(NoDataExceptionInterface::class);
            throw new $noDataExceptionClass();
        }

        file_put_contents($this->path.'/'.$data->getIdentifier().'.data', serialize($data->expose()));
    }

    /**
     * @inheritdoc
     */
    public function delete(string $identifier): void
    {
        unset($this->data[$identifier]);
        $file = $this->path.'/'.$identifier.'.data';
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @inheritdoc
     */
    public function batchUpdate(array $dataObjects): void
    {
        foreach ($dataObjects as $data) {
            $this->update($data);
        }
    }

    /**
     * @inheritdoc
     */
    public function batchDelete(array $identifiers): void
    {
        foreach ($identifiers as $identifier) {
            $this->delete($identifier);
        }
    }

    /**
     * Creates an unique object/data identifier string.
     *
     * @param DataInterface $data
     */
    protected function createIdentifier($data): void
    {
        $identifier = $data->getIdentifier();

        if (null === $identifier) {
            do {
                $identifier = randomLowerAlphaNumericalString();
            } while (isset($this->data[$identifier]) || is_file($this->path.'/'.$identifier.'.data'));

            $data->expose()->setIdentifier($identifier);
        }
    }
}
