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
// 重写 connection， 已实现
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
```
