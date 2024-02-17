<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_no',
        'total_before_discount',
        'total_after_discount',
        'invoice_date',
        'due_date',
        'discount_percentage',
        'billing_address',
            'realm_id',
            'terms',
            'SyncToken'
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
