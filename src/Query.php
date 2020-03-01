<?php

namespace Lengbin\Hyperf\YiiDb;

use Lengbin\YiiDb\ConnectionInterface;

class Query extends \Lengbin\YiiDb\Query
{

    public function __construct(array $config = [], ConnectionInterface $connection = null)
    {
        if ($connection === null) {
            $connection = make(ConnectionInterface::class);
        }
        parent::__construct($config, $connection);
    }

}
