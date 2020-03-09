<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\YiiDb;

use Hyperf\Contract\ConfigInterface;
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

    public function __construct(ContainerInterface $container, $configKey = 'default')
    {
        // schemaCache
        $this->enableSchemaCache = true;
        $this->schemaCache = $container->get(CacheInterface::class);

        // logger
        $logger = $container->get(LoggerFactory::class)->get();

        // dsn
        $dsn = $this->getDsn($container, $configKey);
        parent::__construct($dsn, $logger);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $configKey
     */
    protected function getDsn(ContainerInterface $container, $configKey)
    {
        $config = $container->get(ConfigInterface::class)->get('databases.' . $configKey);

        $this->db = Db::connection($configKey);
        $this->pdo = $this->getMasterPdo();
        $this->setDriverName($config['driver']);

        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->tablePrefix = $config['prefix'];

        return [
            'driver'  => $config['driver'],
            'host'    => $config['host'],
            'dbname'  => $config['database'],
            'charset' => $config['charset'],
            'port'    => $config['port'],
        ];
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
