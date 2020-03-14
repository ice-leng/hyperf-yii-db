<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\YiiDb;

use Lengbin\YiiDb\ActiveRecord\AbstractActiveRecord;
use Lengbin\YiiDb\ConnectionInterface;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class ActiveRecord extends AbstractActiveRecord
{

    public function __construct(ConnectionInterface $connection = null, array $config = [])
    {
        // 可以直接在这里实现，添加后，更新后，删除后的事件
        // 比如 操作日志，其他日志
        // 个人建议 使用 hy框架的事件, 分布式的时候好拆分
        $this->on(self::AFTER_INSERT, [$this, 'saveAfterInsert']);
        $this->on(self::AFTER_UPDATE, [$this, 'saveAfterUpdate']);
        $this->on(self::AFTER_DELETE, [$this, 'saveAfterDelete']);
        parent::__construct($connection, $config);
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    protected static function getContainer(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }

    protected function eventDispatcher(): EventDispatcherInterface
    {
        return $this->getContainer()->get(EventDispatcherInterface::class);
    }

    protected function saveAfterInsert($event): void
    {
        $this->eventDispatcher()->dispatch($event);
    }

    protected function saveAfterUpdate($event): void
    {
        $this->eventDispatcher()->dispatch($event);
    }

    protected function saveAfterDelete($event): void
    {
        $this->eventDispatcher()->dispatch($event);
    }

    /**
     * db
     * @return ConnectionInterface
     */
    public static function getDb(): ConnectionInterface
    {
        return static::getContainer()->get(ConnectionInterface::class);
    }

    /**
     * 使用 hy 雪花算法
     *
     * @param int $type
     * @param int $service_no
     *
     * @return int|string
     */
    public function nextId($type = 0, $service_no = 0)
    {
        $generator = $this->getContainer()->get(IdGeneratorInterface::class);
        return $generator->generate();
    }

    /**
     * @param     $model
     * @param int $pageSize
     *
     * @return array
     * @throws \Lengbin\YiiDb\Exception\Exception
     * @throws \Lengbin\YiiDb\Exception\InvalidConfigException
     * @throws \Lengbin\YiiDb\Exception\NotSupportedException
     * @throws \Throwable
     */
    public function getPage($model, $pageSize = 20): array
    {
        $params = $this->getContainer()->get(ServerRequestInterface::class)->getQueryParams();
        return parent::page($params, $model, $pageSize);
    }

}
