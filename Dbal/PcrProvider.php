<?php

namespace Pcr\Dbal;

use Doctrine\DBAL\Connection;

/**
 * @author Vidy Videni <videni@foxmail.com>
 */
class PcrProvider
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAllProvinces()
    {
        $srcQueryBuilder = $this->connection->createQueryBuilder();
        $srcQueryBuilder->from('pcr')->select('*');

        return $srcQueryBuilder->where('level = :level ')
            ->setParameter('level', 1)->execute()->fetchAll();
    }

    public function findCitiesByProviceCode($provinceCode)
    {
        return $this->findChildren($provinceCode);
    }

    public function findRegionsByCityCode($cityCode)
    {
        return $this->findChildren($cityCode);
    }

    /**
     * @param $code
     *
     * @return array
     */
    protected function findChildren($code): array
    {
        return $this->connection->createQueryBuilder()
            ->from('pcr')
            ->select('*')
            ->where('parent_code=:parent')
            ->setParameter('parent', $code)
            ->execute()
            ->fetchAll();
    }


}