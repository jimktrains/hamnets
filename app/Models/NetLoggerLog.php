<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class NetLoggerLog extends Model
{
  protected $table = "netlogger_log";
  protected $primaryKey = 'netlogger_log_id';
    use HasFactory;


  public function scopeCurrent($query, $timezone, $selectedBands)
  {
    // The select is so that I can use it in the net template.
    $query = $query->selectRaw(join(',', ['*',
      '"Mode" as mode',
      '"NetName" as name',
      '"Frequency" as primary_frequency',
      '"Date" as start_timestamp',
      'to_char("Date" at time zone ?, \'HH24:MI\') as local_start_time',
      '"Band" as primary_band']), [$timezone])
       ->where('created_at', '>', DB::raw("now() - interval '15 minute'"))
       ->orderByRaw('"Date", "Frequency"');

    if (!empty($selectedBands)) {
      $query = $query->join(DB::raw('band pband'), fn ($query) =>
        $query->whereRaw('"Frequency" <@ pband.frequencies')
          ->whereIn('pband.name', $selectedBands)
       );
    }

    return $query;
  }

  public static function format_frequency($frequency)
  {
    $s = (string)$frequency;
    $s = strrev($s);
    $s = chunk_split($s, 3, " ");
    $s = strrev($s);

    return $s;
  }

  public function format_primary_frequency()
  {
    if (!empty($this->primary_frequency)) {
     return static::format_frequency($this->primary_frequency);
    } else {
      return $this->Frequency_raw;
    }
  }
}

