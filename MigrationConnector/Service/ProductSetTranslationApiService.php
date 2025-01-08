<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\MigrationConnector\Service;

class ProductSetTranslationApiService extends AbstractProductSetApiService
{
    protected function mapRows(array $data): array
    {
        return $this->mapData($data, [], ['translation', 'locale']);
    }

    protected function getStaticFields(): array
    {
        return [];
    }
}
