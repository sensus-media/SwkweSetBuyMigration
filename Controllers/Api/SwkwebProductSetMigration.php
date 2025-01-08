<?php declare(strict_types=1);

use SwkweSetBuyMigration\Controllers\AbstractProductSetMigrationController;
use SwkweSetBuyMigration\MigrationConnector\Service\ProductSetApiService;

class Shopware_Controllers_Api_SwkwebProductSetMigration extends AbstractProductSetMigrationController
{
    protected function getApiServiceClass(): string
    {
        return ProductSetApiService::class;
    }
}
