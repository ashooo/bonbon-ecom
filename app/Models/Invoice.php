<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'pdf_path',
        'print_count',
        'last_printed_at',
    ];

    protected $casts = [
        'print_count' => 'integer',
        'last_printed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function incrementPrintCount()
    {
        $this->increment('print_count');
        $this->update(['last_printed_at' => now()]);
    }
}
