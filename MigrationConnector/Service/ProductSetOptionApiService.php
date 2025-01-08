<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\MigrationConnector\Service;

use Shopware\Components\Model\ModelManager;
use SwagMigrationConnector\Repository\AbstractRepository;
use SwkweSetBuyMigration\MigrationConnector\Helper\ProductSetPriceCalculationMappingHelper;

class ProductSetOptionApiService extends AbstractProductSetApiService
{
    /**
     * @var ProductSetPriceCalculationMappingHelper
     */
    private $mappingHelper;

    public function __construct(
        ModelManager $modelManager,
        AbstractRepository $repository,
        ProductSetPriceCalculationMappingHelper $mappingHelper
    ) {
        parent::__construct($modelManager, $repository);

        $this->mappingHelper = $mappingHelper;
    }

    public function getList($offset = 0, $limit = 250): array
    {
        $data = parent::getList($offset, $limit);

        $priceCalculationHashes = $this->mappingHelper->getOptionPriceCalculationHashes(array_column($data, 'id'));

        foreach ($data as &$row) {
            $row['price_calculation_hash'] = $priceCalculationHashes[$row['id'] ?? null] ?? null;
        }

        return $data;
    }

    protected function mapRows(array $data): array
    {
        return $this->mapData($data, [], ['setOption']);
    }
}
