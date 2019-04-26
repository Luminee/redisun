<?php

namespace Luminee\Redisun;

class ExampleModel extends BaseModel
{
    protected $key = 'user_account:{id}:user:{user_id}:type:{user_type_id}:hash';

    protected $hidden = ['password', 'reset_password_token', 'reset_password_token_create_time'];

    protected $fillable = ['abc'];

    public function type()
    {
        return $this->belongsTo('App\Models\User\Type', 'user_type_id', 'id');
    }

    /**
     * Set Attribute
     */
    public function getAllowBillAttribute($value)
    {
        return (int)$value;
    }

    public function setNicknameAttribute($value)
    {
        $this->attributes['nickname'] = e($value);
    }
   
}
