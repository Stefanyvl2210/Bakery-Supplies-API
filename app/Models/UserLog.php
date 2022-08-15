<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model {
    use HasFactory;

    protected $table = "userlog";

    protected $fillable = [
        'user_id',
        'action',
        'userlog_previous_id',
    ];

    /*
     * Orders
     */
    public function user() {
        return $this->belongsTo( User::class );
    }
}
