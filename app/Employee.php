<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employees';

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
    protected $fillable = ['user_id', 'employee_number', 'joining_date', 'company_id', 'supervisior_id', 'designation_id', 'photo_id', 'blood_group', 'linkedin', 'contact', 'official_number', 'fathers_name', 'fathers_number', 'mothers_name', 'mothers_number', 'spouse_name', 'spouse_number', 'current_address', 'permanent_address', 'nid', 'passport', 'driving_licence', 'date_of_birth', 'emergency_contact_name', 'emergency_contact_number', 'relation_emergency_contact', 'skills', 'education', 'experience', 'leaving_date', 'created_by', 'updated_by'];

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function supervisior() {
        return $this->belongsTo('App\User', 'supervisior_id');
    }

}
