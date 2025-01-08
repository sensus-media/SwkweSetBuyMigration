<?php declare(strict_types=1);

namespace SwkweSetBuyMigration\MigrationConnector\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use SwagMigrationConnector\Repository\AbstractRepository;
use SwagMigrationConnector\Util\TotalStruct;

class ProductSetTranslationRepository extends AbstractRepository
{
    private const ENTITY = 'translation_swkweb_product_set';
    private const SOURCE_TABLE = 's_core_translations';

    public function getTotal()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('COUNT(*)');

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
        $qb = $this->getQueryBuilder();
        $this->addTableSelection($qb, self::SOURCE_TABLE, 'translation');

        $qb
            ->addSelect('REPLACE(locale.locale, "_", "-") as `locale.locale`')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from(self::SOURCE_TABLE, 'translation')
            ->innerJoin(
                'translation',
                's_core_shops',
                'shop',
                $qb->expr()->eq('shop.id', 'translation.objectlanguage'),
            )
            ->innerJoin(
                'shop',
                's_core_locales',
                'locale',
                $qb->expr()->eq('locale.id', 'shop.locale_id'),
            )
            ->leftJoin(
                'translation',
                'swkwe_set_buy_article_set',
                'setSlot',
                (string) $qb->expr()->and(
                    $qb->expr()->eq('translation.objectkey', 'setSlot.id'),
                    $qb->expr()->eq('translation.objecttype', ':objectTypeSlot'),
                ),
            )
            ->leftJoin(
                'translation',
                'swkwe_set_buy_article_set_option',
                'setOption',
                (string) $qb->expr()->and(
                    $qb->expr()->eq('translation.objectkey', 'setOption.id'),
                    $qb->expr()->eq('translation.objecttype', ':objectTypeOption'),
                ),
            )
            ->where(
                $qb->expr()->or(
                    $qb->expr()->and(
                        $qb->expr()->isNotNull('setSlot.id'),
                        $qb->expr()->eq('translation.objecttype', ':objectTypeSlot'),
                    ),
                    $qb->expr()->and(
                        $qb->expr()->isNotNull('setOption.id'),
                        $qb->expr()->eq('translation.objecttype', ':objectTypeOption'),
                    ),
                ),
            )
            ->orderBy('translation.id')
            ->setParameter('objectTypeSlot', 'swkweSetBuySlot')
            ->setParameter('objectTypeOption', 'swkweSetBuyOption')
        ;

        return $qb;
    }
}
