<?php declare(strict_types=1);

use SwkweSetBuyMigration\Controllers\AbstractProductSetMigrationController;
use SwkweSetBuyMigration\MigrationConnector\Service\ProductSetSlotApiService;

class Shopware_Controllers_Api_SwkwebProductSetSlotMigration extends AbstractProductSetMigrationController
{
    protected function getApiServiceClass(): string
    {
        return ProductSetSlotApiService::class;
    }
}
