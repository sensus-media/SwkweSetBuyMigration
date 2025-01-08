<?php declare(strict_types=1);

use SwkweSetBuyMigration\Controllers\AbstractProductSetMigrationController;
use SwkweSetBuyMigration\MigrationConnector\Service\ProductSetOptionApiService;

class Shopware_Controllers_Api_SwkwebProductSetOptionMigration extends AbstractProductSetMigrationController
{
    protected function getApiServiceClass(): string
    {
        return ProductSetOptionApiService::class;
    }
}
