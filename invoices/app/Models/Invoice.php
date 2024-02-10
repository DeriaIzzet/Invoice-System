<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $fillable = ['company_name', 'date', 'customer_name', 'customer_email', 'total_amount'];
    use HasFactory;
    
    public function lineItems()
    {
        return $this->hasMany(LineItem::class);
    }

}
