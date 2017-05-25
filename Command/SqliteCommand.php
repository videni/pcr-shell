<?php

namespace Pcr\Command;

use Doctrine\DBAL\Connection;
use Pcr\Dbal\Schema;
use Pcr\Importer\PintushiImporter;
use Pcr\Importer\SqliteImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Vidy Videni <videni@foxmail.com>
 */
class SqliteCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pcr:sqlite')
            ->addOption('dir', 'd', InputOption::VALUE_OPTIONAL, '输出目录')
            ->setDescription('dump sql structure and data to sqlite');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = __DIR__ . '/../vendor/videni/pcr/2017-5-24.xls';
        if (!file_exists($path)) {
            throw  new \RuntimeException("请执行 composer install下载最新省市区数据");
        }

        $dir = $input->getOption('dir');
        if ($dir && (!is_dir($dir) || !is_writable($dir))) {
            throw  new \RuntimeException(sprintf('%s不存在或无写权限', $dir));
        }
        $dir = $dir ?? __DIR__ . '/../var/';

        $fromConn = \Doctrine\DBAL\DriverManager::getConnection(array(
            'url' => sprintf('sqlite:///%spcr.sqlite', $dir),
        ));

        $outputStyle = new SymfonyStyle($input, $output);

        $this->createSqliteDbStructure($fromConn, $outputStyle, $input, $output);

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        if ($questionHelper->ask($input, $output, new ConfirmationQuestion('是否从Excel导入数据到缓存? (y/N) ', true))) {
            (new SqliteImporter($fromConn, $path))->cacheToSqliteDb();
        }

        $outputStyle->writeln(sprintf("数据已导入目录%s",$dir) );
    }

    /**
     * @param Connection $conn
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function createSqliteDbStructure(Connection $conn, SymfonyStyle $outputStyle, InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $outputStyle->writeln('创建表结构');

        $sm = $conn->getSchemaManager();
        if ($sm->tablesExist(['pcr'])) {
            $outputStyle->writeln('<error>警告! 表结构已存在。</error>');
        }

        if ($questionHelper->ask($input, $output, new ConfirmationQuestion('Continue? (y/N) ', true))) {
            $outputStyle->writeln('');
            if ($sm->tablesExist(['pcr'])) {
                $sm->dropTable('pcr');
            }
            (new Schema($conn))->createSchema();
        }
    }
}