<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataTests\env;

use Behat\Behat\Context\Context;
use eArc\Data\DataRepository;
use eArc\ComponentDI\ComponentContainer;
use eArc\Data\Interfaces\Application\DataInterface;
use eArc\Data\Interfaces\Exceptions\DataExistsExceptionInterface;
use eArc\Data\Interfaces\Exceptions\NoDataExceptionInterface;
use eArc\DI\Interfaces\ContainerInterface;
use eArc\ObserverTree\ObserverTreeFactory;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var ContainerInterface */
    protected $container;

    /** @var DataRepository */
    protected $dataRepository;

    /**
     * @Given data is bootstraped
     */
    public function dataIsBootstraped()
    {
        require_once __DIR__.'/../../vendor/autoload.php';

        $OTF = new ObserverTreeFactory(
            __DIR__.'/events',
            'eArc\\DataTests\\env\\events',
            [[__DIR__.'/../../src/events', 'eArc\\Data\\events']]
        );

        $componentsRootKey = 'components';
        $componentsEventTree = $OTF->get($componentsRootKey);

        $components = new ComponentContainer($componentsEventTree);

        $this->container = $components->get('earc_data');
        $this->dataRepository = $this->container->get(DataRepository::class);
    }

    /**
     * @Given no data is persisted
     */
    public function noDataIsPersisted()
    {
        chdir($this->container->get('path.var.data'));
        foreach (glob('*.data') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @Then find data :identifier should throw an NoDataExceptionInterface
     *
     * @param string $identifier
     */
    public function findDataShouldThrowAnNodataExceptionInterface(string $identifier)
    {
        try {
            $this->dataRepository->find($identifier);
            throw new \RuntimeException();
        } catch (\Exception $exception) {
            if (!$exception instanceof NoDataExceptionInterface) {
                throw new \RuntimeException();
            }
        }

    }

    /**
     * @Then find all data should return an array of size :size
     *
     * @param string $size
     */
    public function findAllDataShouldReturnAnArrayOfSize(string $size)
    {
        if (count($this->dataRepository->findAll()) !== intval($size)) {
            throw new \RuntimeException();
        }
    }

    /**
     * @Then delete :identifier via delete should throw no Exception
     *
     * @param string $identifier
     */
    public function deleteViaDeleteShouldThrowNoException(string $identifier)
    {
        $this->dataRepository->delete($identifier);
    }

    /**
     * @Then delete :identifier via batch delete should throw no Exception
     *
     * @param string $identifier
     */
    public function deleteViaBatchDeleteShouldThrowNoException(string $identifier)
    {
        $this->dataRepository->batchDelete([$identifier]);
    }

    /**
     * @Then create :identifier should return an DataInterface
     *
     * @param string $identifier
     */
    public function createShouldReturnAnDataInterface(string $identifier)
    {
        if (!$this->dataRepository->create($identifier) instanceof DataInterface) {
            throw new \RuntimeException();
        }
    }

    /**
     * @Given a data is created with identifier :identifier
     *
     * @param string $identifier
     */
    public function aDataIsCreatedWithIdentifier(string $identifier)
    {
        $this->dataRepository->create($identifier);
    }

    /**
     * @Given the :identifier data is set with :text
     *
     * @param string $identifier
     * @param string $text
     */
    public function theDataIsSetWith(string $identifier, string $text)
    {
        $this->dataRepository->find($identifier)->set($text);
    }

    /**
     * @Then find :identifier should return a DataInterface with the same identifier and get returns :text
     *
     * @param string $identifier
     * @param string $text
     */
    public function findShouldReturnAnDataInterfaceWithTheSameIdentifierAndGetReturns(string $identifier, string $text)
    {
        if ('null' === $text) {
            $text = null;
        }

        $data = $this->dataRepository->find($identifier);

        if (!$data instanceof DataInterface
            || $data->getIdentifier() !== $identifier
            || $data->get() !== $text
        ) {
            throw new \RuntimeException();
        }

    }

    /**
     * @Then find all data should contain an DataInterface with identifier :identifier and get returns :text
     *
     * @param string $identifier
     * @param string $text
     */
    public function findAllDataShouldContainAnDataInterfaceWithIdentifierAndGetReturnsNull(string $identifier, string $text)
    {
        if ('null' === $text) {
            $text = null;
        }

        foreach ($this->dataRepository->findAll() as $data) {
            if ($data instanceof DataInterface
                && $data->getIdentifier() === $identifier
                && $data->get() === $text
            ) {
                return;
            }

        }

        throw new \RuntimeException();
    }

    /**
     * @Then create :identifier should throw an DataExistsExceptionInterface
     *
     * @param string $identifier
     */
    public function createShouldReturnAnDataExistsExceptionInterface(string $identifier)
    {
        try {
            $this->dataRepository->create($identifier);
        } catch (\Exception $exception) {
            if ($exception instanceof DataExistsExceptionInterface) {
                return;
            }
        }

        throw new \RuntimeException();
    }

    /**
     * @When delete :identifier
     *
     * @param string $identifier
     */
    public function delete(string $identifier)
    {
        $this->dataRepository->delete($identifier);
    }

    /**
     * @When batch delete :identifier1 and :identifier2
     *
     * @param string $identifier1
     * @param string $identifier2
     */
    public function batchDeleteAnd(string $identifier1, string $identifier2)
    {
        $this->dataRepository->batchDelete([$identifier1, $identifier2]);
    }

    /**
     * @Given the :identifier data is updated
     *
     * @param $identifier
     */
    public function theDataIsUpdated(string $identifier)
    {
        $this->dataRepository->update(
            $this->dataRepository->find($identifier)
        );
    }

    /**
     * @Given the :identifier data is batch updated
     *
     * @param string $identifier
     */
    public function theDataIsBatchUpdated(string $identifier)
    {
        $this->dataRepository->batchUpdate([
            $this->dataRepository->find($identifier)
        ]);
    }
}
