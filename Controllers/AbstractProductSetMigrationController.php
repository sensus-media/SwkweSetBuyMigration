<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\Controllers;

use SwagMigrationConnector\Controllers\SwagMigrationApiControllerBase;
use SwagMigrationConnector\Service\ControllerReturnStruct;
use SwkweSetBuyMigration\MigrationConnector\Service\AbstractProductSetApiService;

abstract class AbstractProductSetMigrationController extends SwagMigrationApiControllerBase
{
    public function indexAction()
    {
        $apiService = $this->container->get($this->getApiServiceClass());

        if (!$apiService instanceof AbstractProductSetApiService) {
            throw new \RuntimeException('Invalid API service');
        }

        $offset = (int) $this->Request()->getParam('offset', 0);
        $limit = (int) $this->Request()->getParam('limit', 250);

        $data = $apiService->getList($offset, $limit);
        $response = new ControllerReturnStruct($data, empty($data));

        $this->View()->assign($response->jsonSerialize());
    }

    /**
     * @return class-string
     */
    abstract protected function getApiServiceClass();
}
