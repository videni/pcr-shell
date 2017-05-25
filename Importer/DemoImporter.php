<?php

namespace Pcr\Importer;

use Doctrine\DBAL\Connection;
use Pcr\Dbal\PcrProvider;

/**
 * Import data to project Pintushi
 *
 * @author Vidy Videni <videni@foxmail.com>
 */
class DemoImporter
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var PcrProvider
     */
    protected $pcrProvider;

    public function __construct(Connection $from, Connection $to)
    {
        $this->connection = $to;
        $this->pcrProvider = new PcrProvider($from);
    }

    public function import()
    {
        $provinces = $this->pcrProvider->findAllProvinces();
        $this->importProvince($provinces);
    }

    /**
     * @param $provinces
     */
    protected function importProvince(array $provinces)
    {
        foreach ($provinces as $province) {
            $destQueryBuilder = $this->connection->createQueryBuilder();
            $destQueryBuilder->insert('sylius_province');
            $destQueryBuilder
                ->setValue('code', $this->connection->quote($province['code']))
                ->setValue('name', $this->connection->quote($province['name']))
                ->setValue('abbreviation', $this->connection->quote($province['abbr']))
                ->setValue('country_id', 1)
                ->execute();

            $cities = $this->pcrProvider->findCitiesByProviceCode($province['code']);

            $this->importCity($cities, $this->connection->lastInsertId());
        }
    }

    /**
     * @param $cities
     * @param $provinceInsertId
     */
    protected function importCity(array $cities, $provinceInsertId)
    {
        foreach ($cities as $city) {
            $destCityQb = $this->connection->createQueryBuilder();
            $destCityQb->insert('demo_city')
                ->setValue('province_id', $provinceInsertId)
                ->setValue('code', $this->connection->quote($city['code']))
                ->setValue('abbr', $this->connection->quote($city['abbr']))
                ->setValue('name', $this->connection->quote($city['name']))
                ->execute();

            $this->importRegion($city['code'], $this->connection->lastInsertId());
        }
    }

    /**
     * @param $cityCode
     * @param $cityInsertId
     */
    protected function importRegion($cityCode, $cityInsertId)
    {
        $regions = $this->pcrProvider->findRegionsByCityCode($cityCode);

        foreach ($regions as $region) {
            $this->connection->createQueryBuilder()
                ->insert('demo_region')
                ->setValue('city_id', $cityInsertId)
                ->setValue('code', $this->connection->quote($region['code']))
                ->setValue('abbr', $this->connection->quote($region['abbr']))
                ->setValue('name', $this->connection->quote($region['name']))
                ->execute();
        }
    }
}