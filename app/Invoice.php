<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id', 'total_before_discount', 'total_after_discount'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}
