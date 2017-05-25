<?php

namespace Pcr\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema as DbaclSchema;

/**
 * @author Vidy Videni <videni@foxmail.com>
 */
class Schema
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function createSchema()
    {
        $sm = $this->connection->getSchemaManager();

        $schema = new DbaclSchema();

        $pcrTable = $schema->createTable("pcr");
        $pcrTable->addColumn("id", "integer", array("unsigned" => true, 'columnDefinition' => 'INTEGER PRIMARY KEY AUTOINCREMENT'));
        $pcrTable->addColumn("code", "string", array("length" => 32));
        $pcrTable->addColumn("name", "string", array("length" => 32));
        $pcrTable->addColumn("abbr", "string", array("length" => 32));
        $pcrTable->addColumn("parent_code", "string", array("length" => 32));
        $pcrTable->addColumn("level", "integer");
        $pcrTable->addColumn("area_code", "string");
        $pcrTable->addColumn("post_code", "string");
        $pcrTable->addColumn("pinyin", "string");

        $sm->createTable($pcrTable);
    }
}