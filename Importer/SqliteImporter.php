<?php

namespace Pcr\Importer;

use Doctrine\DBAL\Connection;

/**
 * cache data from excel to sqlite for quick access
 *
 * @author Vidy Videni <videni@foxmail.com>
 */
class SqliteImporter
{
    /**
     * @var Connection
     */
    protected $connection;

    protected $filePath;

    public function __construct(Connection $connection, $filePath)
    {
        $this->connection= $connection;
        $this->filePath = $filePath;
    }

    public function cacheToSqliteDb()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert('pcr');

        $excel = \PHPExcel_IOFactory::load($this->filePath);
        $worksheet = $excel->getActiveSheet();
        $rowIndex = $worksheet->getHighestRow();

        for ($row = 2; $row <= $rowIndex; $row++) {
            $values = [];
            for ($col = 0; $col < 8; $col++) {
                $values[] = $this->connection->quote($worksheet->getCellByColumnAndRow($col, $row)->getValue());
            }
            $data = array_combine(
                [
                    'code',
                    'name',
                    'parent_code',
                    'abbr',
                    'level',
                    'area_code',
                    'post_code',
                    'pinyin',
                ],
                $values
            );
            $queryBuilder->values($data);
            $queryBuilder->execute();
        }
    }
}