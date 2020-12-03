<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Band extends Model
{
  protected $table = "band";
    use HasFactory;

  public function scopeHavingNets($query)
  {
    return $query->whereIn('band_id', function ($query) {
      return $query
        ->select('band_id')
        ->from('band')
        ->join('net', function ($query) {
          return $query->whereRaw('primary_frequency <@ frequencies');
        })
        ->groupby('band_id')
        ->havingRaw('count(*) > 0');
    });
  }
}
