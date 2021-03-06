<?php

namespace App\Models;

use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ElasticquentTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'surname',
        'phone_number',
        'address',
        'email',
    ];

    function getIndexName()
    {
        return 'users_index';
    }
}
