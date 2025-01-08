<?php declare(strict_types=1);

use SwkweSetBuyMigration\Controllers\AbstractProductSetMigrationController;
use SwkweSetBuyMigration\MigrationConnector\Service\ProductSetTranslationApiService;

class Shopware_Controllers_Api_SwkwebProductSetTranslationMigration extends AbstractProductSetMigrationController
{
    protected function getApiServiceClass(): string
    {
        return ProductSetTranslationApiService::class;
    }
}
