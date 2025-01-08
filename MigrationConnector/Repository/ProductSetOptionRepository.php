<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\MigrationConnector\Repository;

use Doctrine\DBAL\Connection;
use SwagMigrationConnector\Repository\AbstractRepository;
use SwagMigrationConnector\Util\TotalStruct;

class ProductSetOptionRepository extends AbstractRepository
{
    private const ENTITY = 'swkweb_product_set_option';
    private const SOURCE_TABLE = 'swkwe_set_buy_article_set_option';

    public function getTotal()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(*)')
            ->from(self::SOURCE_TABLE, 'setOption')
            ->where($qb->expr()->eq('setOption.active', 1))
        ;

        $total = $qb->execute()->fetchOne();
        assert(is_scalar($total));

        return new TotalStruct(self::ENTITY, (int) $total);
    }

    public function requiredForCount(array $entities)
    {
        return !in_array(self::ENTITY, $entities, true);
    }

    public function fetch($offset = 0, $limit = 250)
    {
        $ids = $this->fetchIdentifiers(
            self::SOURCE_TABLE,
            $offset,
            $limit,
            ['id'],
            ['active = 1'],
        );

        $positionQb = $this->connection->createQueryBuilder();
        $positionQb
            ->select('COUNT(*)')
            ->from(self::SOURCE_TABLE, 'setOptionPosition')
            ->where(
                $positionQb->expr()->and(
                    $positionQb->expr()->or(
                        $positionQb->expr()->lt('setOptionPosition.position', 'setOption.position'),
                        $positionQb->expr()->and(
                            $positionQb->expr()->eq('setOptionPosition.position', 'setOption.position'),
                            $positionQb->expr()->lt('setOptionPosition.id', 'setOption.id'),
                        ),
                    ),
                    $positionQb->expr()->eq('setOptionPosition.setID', 'setOption.setID'),
                ),
            )
        ;

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->addSelect('productDetail.ordernumber as `setOption.ordernumber`')
            ->addSelect("({$positionQb->getSQL()}) as `setOption.calculated_position`")
            ->from(self::SOURCE_TABLE, 'setOption')
            ->leftJoin(
                'setOption',
                's_articles_details',
                'productDetail',
                $qb->expr()->eq('setOption.articledetailsID', 'productDetail.id'),
            )
            ->where($qb->expr()->in(
                'setOption.id',
                $qb->createPositionalParameter($ids, Connection::PARAM_INT_ARRAY),
            ))
        ;
        $this->addTableSelection($qb, self::SOURCE_TABLE, 'setOption');

        return $qb->execute()->fetchAllAssociative();
    }
}
