<?php
/*
 * This file is part of the Redisun package.
 *
 * (c) LI Mengxiang <limengxiang876@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Limen\Redisun;

/**
 * Class BaseModel
 * @package Limen\Redisun\Examples
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class BaseModel extends Model
{
    protected $type = 'hash';

    protected $fillable = [];

    protected $hidden = [];

    protected $timestamp = true;

    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $k => $value) {
            if ($this->isFillable($k)) $this->attributes[$k] = $value;
        }

        parent::__construct();
    }

    public function create($data, $id = null, $ttl = null, $exists = null)
    {
        foreach ($this->getFieldNeedles() as $field) {
            $fields[$field] = $data[$field];
        }

        return parent::create($fields, $data, $ttl, $exists);
    }

    public function fill($data)
    {
        foreach ($data as $k => $item) {
            if ($this->isFillable($k)) $this->attributes[$k] = $item;
        }
        return $this;
    }

    public function save()
    {
        foreach ($this->getFieldNeedles() as $field) {
            if (!isset($this->attributes[$field])) throw new Exception($field . ' is not set.');
            $keys[$field] = $this->attributes[$field];
        }

        $data = [];
        foreach ($this->attributes as $k => $value) {
            if ($this->isSaveable($k)) $data[$k] = $value;
        }

        return parent::create($keys, $data, null, null);
    }

    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set' . Str::studly($key) . 'Attribute';

            return $this->{$method}($value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    protected function isFillable($key)
    {
        return empty($this->fillable) || in_array($key, $this->fillable);
    }

    protected function isSaveable($key)
    {
        return $this->isFillable($key) || $key == 'id' || $this->isTimestamp($key);
    }

    protected function isTimestamp($key)
    {
        return in_array($key, ['created_at', 'updated_at']) && $this->timestamp;
    }

    protected function hasSetMutator($key)
    {
        return method_exists($this, 'set' . Str::studly($key) . 'Attribute');
    }

}