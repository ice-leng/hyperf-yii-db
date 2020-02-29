<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\YiiDb;

use Hyperf\DbConnection\Db;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

class Connection extends \Lengbin\YiiDb\Connection
{
    /**
     * @var  $db
     */
    public $db;

    public function __construct(ContainerInterface $container)
    {
        // schemaCache
        $this->enableSchemaCache = true;
        $this->schemaCache = $container->get(CacheInterface::class);

        // logger
        $logger = $container->get(LoggerFactory::class)->get();
        parent::__construct(null, $logger);
    }

    public function init()
    {
        $pool = 'default';
        $this->db = Db::connection($pool);
        $this->username = $pool;
        $this->pdo = $this->getMasterPdo();
        $this->setDriverName('mysql');
    }

    /**
     * 写库
     * @return mixed
     */
    public function getMasterPdo()
    {
        return $this->db->getPdo();
    }

    /**
     * 读库
     *
     * @param
     *
     * @return mixed
     */
    public function getSlavePdo($fallbackToMaster = true)
    {
        return $this->db->getReadPdo();
    }

}
