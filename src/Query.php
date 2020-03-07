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
