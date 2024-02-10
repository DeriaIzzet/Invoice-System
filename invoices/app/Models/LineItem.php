<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{

    protected $fillable = ['invoice_id', 'description', 'quantity', 'unit_price'];

    use HasFactory;
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
