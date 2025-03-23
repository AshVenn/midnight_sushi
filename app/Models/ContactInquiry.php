<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInquiry extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
    ];

    public function getStatusTextAttribute(): String{

        return match($this->status){
            'open'=>'Ouverte',
            'resolved'=>'Résolue',
            default=> 'inconnu',
        };
    }
    public function getStatusColorAttribute(): string
        {
            return match($this->status) {
               
                'open'=>'badge-error',
                'resolved'=>'badge-success',
                default => 'badge-neutral',
            };
        }
}
