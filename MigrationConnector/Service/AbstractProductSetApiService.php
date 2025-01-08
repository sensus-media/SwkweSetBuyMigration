<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\MigrationConnector\Service;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use SwagMigrationConnector\Repository\AbstractRepository;
use SwagMigrationConnector\Service\AbstractApiService;

abstract class AbstractProductSetApiService extends AbstractApiService
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var AbstractRepository
     */
    protected $repository;

    public function __construct(
        ModelManager $modelManager,
        AbstractRepository $repository
    ) {
        $this->modelManager = $modelManager;
        $this->repository = $repository;
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return list<array<string, mixed>>
     */
    public function getList($offset = 0, $limit = 250)
    {
        $data = $this->mapRows($this->repository->fetch($offset, $limit));
        $staticFields = $this->getStaticFields();

        foreach ($data as &$row) {
            $this->cleanupResultSet($row);

            if ($staticFields !== null) {
                $row = array_merge($row, $staticFields);
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function mapRows(array $data)
    {
        return $data;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getStaticFields()
    {
        return [
            '_locale' => $this->getDefaultShopLocale(),
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultShopLocale()
    {
        /** @var Shop */
        $defaultShop = $this->modelManager->getRepository(Shop::class)->getDefault();

        return str_replace('_', '-', $defaultShop->getLocale()->getLocale());
    }
}
