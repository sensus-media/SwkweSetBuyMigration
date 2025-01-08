<?php declare(strict_types=1);

use SwagMigrationConnector\Controllers\SwagMigrationApiControllerBase;
use SwkweSetBuyMigration\MigrationConnector\Helper\ProductSetPriceCalculationMappingHelper;

class Shopware_Controllers_Api_SwkwebProductSetPriceCalculationMigration extends SwagMigrationApiControllerBase
{
    public function indexAction()
    {
        $mappingHelper = $this->container->get(ProductSetPriceCalculationMappingHelper::class);

        if (!$mappingHelper instanceof ProductSetPriceCalculationMappingHelper) {
            throw new \RuntimeException('Required service not found');
        }

        $this->View()->assign([
            'data' => $mappingHelper->getDistinctPriceCalculations(),
        ]);
    }
}
