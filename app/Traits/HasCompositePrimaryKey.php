<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;

trait HasCompositePrimaryKey
{
    public function getIncrementing()
    {
        return false;
    }

    public function getKey()
    {
        $attributes = [];

        foreach ($this->getKeyName() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;
    }

    protected function setKeysForSaveQuery(Builder $query)
    {
        foreach ($this->getKeyName() as $key) {
            if (isset($this->$key))
                $query->where($key, '=', $this->$key);
            else
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
        }

        return $query;
    }

    public static function find($ids, $columns = ['*'])
    {
        $me = new self;
        $query = $me->newQuery();

        foreach ($me->getKeyName() as $key) {
            $query->where($key, '=', $ids[$key]);
        }

        return $query->first($columns);
    }

    public static function findOrFail($ids, $columns = ['*'])
    {
        $result = self::find($ids, $columns);

        if (!is_null($result)) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(
            __CLASS__, $ids
        );
    }

    public function refresh()
    {
        if (!$this->exists) {
            return $this;
        }

        $this->setRawAttributes(
            static::findOrFail($this->getKey())->attributes
        );

        $this->load(collect($this->relations)->except('pivot')->keys()->toArray());

        return $this;
    }
}
