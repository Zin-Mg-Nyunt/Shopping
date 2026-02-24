<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressBook extends Model
{
    protected $guarded = ['id'];

    public function matches($newAddress){
        return empty(array_diff($this->only('phone','contact_name','street_address','quarter_or_village','township','state_or_region'),$newAddress));
    }
}
