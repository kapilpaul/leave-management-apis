<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leave_requests';

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
    protected $fillable = ['leave_type_id', 'from_date', 'to_date', 'number_of_days', 'employee_id', 'leave_reason', 'remaining_days', 'status', 'created_by'];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
    
}
