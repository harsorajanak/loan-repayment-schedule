<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $table = "loans";

    protected $fillable = ['id', 'user_id', 'loan_amount', 'terms_week', 'interest_amount', 'status','loan_completion_date','balance'];

    function loanEmi(){
        return $this->hasMany(LoanEmi::class,'loan_id');
    }
}
