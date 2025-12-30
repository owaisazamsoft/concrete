<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSequence extends Model
{
    protected $table = 'invoice_sequences';

    protected $fillable = [
        'type',
        'last_number',
        'year',
    ];

    /**
     * Increment the sequence and return the next number
     *
     * @return int
     */
    public function incrementSequence(): int
    {
        $this->last_number += 1;
        $this->updated_at = now();
        $this->save();
        return $this->last_number;
    }

  
}
