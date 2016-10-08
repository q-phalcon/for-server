<?php

namespace App\Models;

trait BaseModel
{
    public function getConnection()
    {
        return null;
    }

    public function setForceExists()
    {
        return null;
    }

    public function reset()
    {
        return null;
    }

    public function getConnectionService()
    {
        return null;
    }

    public function dumpResult()
    {
        return null;
    }

    public function existsId($id)
    {
        $rv_date = $this->findFirst([
            "{$this->primary_key} = ?0",
            'bind' => [$id]
        ]);
        if (empty($rv_date)) {
            return false;
        }
        return true;
    }
}
