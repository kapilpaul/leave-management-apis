<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'contact_person', 'address', 'country', 'city', 'state', 'postal_code', 'email', 'phone', 'mobile', 'fax', 'website_url', 'photo_id', 'created_by'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
}
