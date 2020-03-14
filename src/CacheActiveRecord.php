<?php

namespace Lengbin\Hyperf\YiiDb;

use Hyperf\Utils\ApplicationContext;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;
use Lengbin\Hyperf\YiiDb\ActiveRecord;
use Psr\SimpleCache\CacheInterface;

class CacheActiveRecord extends ActiveRecord
{

    /**
     * @var string $cacheKey
     */
    protected $cacheKey;

    /**
     * @var CacheInterface $cache
     */
    protected $cache;

    /**
     * CacheActiveRecord constructor.
     *
     * @param CacheInterface|null $cache
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __construct(?CacheInterface $cache = null)
    {

        if ($this->cacheKey === null) {
            $this->cacheKey = 'YiiDb.' . get_called_class();
        }

        if ($cache === null) {
            $this->cache = $this->getContainer()->get(CacheInterface::class);
        }

        $params = $this->getCache();
        if ($params !== null) {
            $this->setAttributes($params);
        }

        parent::__construct();
    }

    protected function getCache()
    {
        return $this->cache->get($this->cacheKey);
    }

    public function attributes()
    {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    /**
     * @param array  $params
     * @param string $isDeleteName
     *
     * @return $this
     * @throws \Lengbin\YiiDb\Exception\InvalidConfigException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function updateByParams(array $params, $isDeleteName = 'is_delete')
    {
        $data = $this->filterParams($params);
        $this->setAttributes($data);
        $this->afterSave($this->getCache() === null, $data);
        $this->cache->set($this->cacheKey, $data);
        return $this;
    }

    /**
     * @param null   $id
     * @param string $isDeleteName
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteById($id = null, $isDeleteName = 'is_delete')
    {
        $id = $id ?? $this->cacheKey;
        $this->afterDelete();
        return $this->cache->delete($id);
    }

}
