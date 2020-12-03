<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Net extends Model
{
  protected $table = "netwithband";
    use HasFactory;

  public function scopeOrderInTz($query, $timezone)
  {
    return $query->orderByRaw("extract(hour from (current_date + start_time) at time zone timezone at time zone 'America/New_York')");
  }

  public function format_primary_frequency()
  {
    return static::format_frequency($this->primary_frequency);
  }

  public function format_secondary_frequency()
  {
    return static::format_frequency($this->secondary_frequency);
  }

  public static function format_frequency($frequency)
  {
    $s = (string)$frequency;
    $s = strrev($s);
    $s = chunk_split($s, 3, " ");
    $s = strrev($s);

    return $s;
  }
}
