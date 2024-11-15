<?php

namespace Pixie\Todo4u\Database;

class Connect
{
    public function connect(): \mysqli
    {
        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'todo4u';

        $connect = new \mysqli($servername,$username,$password,$database);

        if ($connect->connect_error)
        {
            throw new \RuntimeException("Database connection Failed Exception");
        }

        return $connect;
   }
}
