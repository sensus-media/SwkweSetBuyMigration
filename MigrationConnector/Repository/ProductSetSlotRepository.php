<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\MigrationConnector\Repository;

use Doctrine\DBAL\Connection;
use SwagMigrationConnector\Repository\AbstractRepository;
use SwagMigrationConnector\Util\TotalStruct;

class ProductSetSlotRepository extends AbstractRepository
{
    private const ENTITY = 'swkweb_product_set_slot';
    private const SOURCE_TABLE = 'swkwe_set_buy_article_set';

    public function getTotal()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(*)')
            ->from(self::SOURCE_TABLE, 'setSlot')
            ->where($qb->expr()->eq('setSlot.active', 1))
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
            ->from(self::SOURCE_TABLE, 'setSlotPosition')
            ->where(
                $positionQb->expr()->and(
                    $positionQb->expr()->or(
                        $positionQb->expr()->lt('setSlotPosition.position', 'setSlot.position'),
                        $positionQb->expr()->and(
                            $positionQb->expr()->eq('setSlotPosition.position', 'setSlot.position'),
                            $positionQb->expr()->lt('setSlotPosition.id', 'setSlot.id'),
                        ),
                    ),
                    $positionQb->expr()->eq('setSlotPosition.articleID', 'setSlot.articleID'),
                ),
            )
        ;

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->addSelect("({$positionQb->getSQL()}) as `setSlot.calculated_position`")
            ->from(self::SOURCE_TABLE, 'setSlot')
            ->where($qb->expr()->in(
                'setSlot.id',
                $qb->createPositionalParameter($ids, Connection::PARAM_INT_ARRAY),
            ))
        ;
        $this->addTableSelection($qb, self::SOURCE_TABLE, 'setSlot');

        return $qb->execute()->fetchAllAssociative();
    }
}
