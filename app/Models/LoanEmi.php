<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanEmi extends Model
{
    use HasFactory;

    protected $table = "loan_emis";

    protected $fillable = ['loan_id', 'emi_amount', 'interest_amount', 'emi_date', 'emi_paid_date'];
}
