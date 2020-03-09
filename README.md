<p align="center">
    <a href="https://hyperf.io/" target="_blank">
        <img src="https://hyperf.oss-cn-hangzhou.aliyuncs.com/hyperf.png" height="100px">
    </a>
    <h1 align="center">Hyperf Yii Db</h1>
    <br>
</p>

If You Like This Please Give Me Star

Install
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require lengbin/hyperf-yii-db
```

or add

```
lengbin/hyperf-yii-db": "*"
```
to the require section of your `composer.json` file.



Publish
-------
```php
      
php ./bin/hyperf.php vendor:publish lengbin/hyperf-yii-db

```

DI
-------

```php
      
//此步已经在 ConfigProvider.php 实现， 
\Lengbin\YiiDb\ConnectionInterface::class => \Lengbin\Hyperf\YiiDb\Connection::class

```

ActiveRecord
------------
```php
      
    Query 
    use Lengbin\Hyperf\YiiDb\Query;

    Model 可以继承 3 中 ActiveRecord
    //基于hy 将添加后，更新后，删除后的事件
    //基于hy 雪花算法 生成id 复写
    \Lengbin\Hyperf\YiiDb\ActiveRecord  
    // 一些 自己常用的 基于 ActiveRecord 的 复写
    Lengbin\YiiDb\ActiveRecord\AbstractActiveRecord
    // Yii 的 ActiveRecord
    Lengbin\YiiDb\ActiveRecord\ActiveRecord

```


Usage
-----
```php
// 重新 query
<?php

namespace Lengbin\Hyperf\YiiDb;

use Lengbin\YiiDb\ConnectionInterface;

class Query extends \Lengbin\YiiDb\Query
{

    public function __construct(ConnectionInterface $connection = null, array $config = [])
    {
        if ($connection === null) {
            $connection = make(ConnectionInterface::class);
        }
        parent::__construct($connection, $config);
    }

}





// 重写 connection， 已实现
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
```
