<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\YiiDb;

use Lengbin\YiiDb\ActiveRecord\AbstractActiveRecord;
use Lengbin\YiiDb\ConnectionInterface;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\EventDispatcher\EventDispatcherInterface;

class ActiveRecord extends AbstractActiveRecord
{

    public function __construct(array $config = [], ConnectionInterface $connection = null)
    {
        // 可以直接在这里实现，添加后，更新后，删除后的事件
        // 比如 操作日志，其他日志
        // 个人建议 使用 hy框架的事件, 分布式的时候好拆分
        $this->on(self::AFTER_INSERT, [$this, 'saveAfterInsert']);
        $this->on(self::AFTER_UPDATE, [$this, 'saveAfterUpdate']);
        $this->on(self::AFTER_DELETE, [$this, 'saveAfterDelete']);
        parent::__construct($config, $connection);
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    protected function getContainer()
    {
        return ApplicationContext::getContainer();
    }

    protected function eventDispatcher()
    {
        return $this->getContainer()->get(EventDispatcherInterface::class);
    }

    protected function saveAfterInsert($event)
    {
        $this->eventDispatcher()->dispatch($event);
    }

    protected function saveAfterUpdate($event)
    {
        $this->eventDispatcher()->dispatch($event);
    }

    protected function saveAfterDelete($event)
    {
        $this->eventDispatcher()->dispatch($event);
    }

    /**
     * 使用 hy 雪花算法
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

}
