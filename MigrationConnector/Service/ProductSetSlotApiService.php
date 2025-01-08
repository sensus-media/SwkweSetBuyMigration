<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\MigrationConnector\Service;

class ProductSetSlotApiService extends AbstractProductSetApiService
{
    protected function mapRows(array $data): array
    {
        return $this->mapData($data, [], ['setSlot']);
    }
}
