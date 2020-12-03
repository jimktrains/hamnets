<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NextNet extends Net
{
  protected $table = "nextnet";

  public function scopeUpcoming($query, $hours = 1)
  {
    return $query->whereRaw("(current_timestamp between start_timestamp and end_timestamp or start_timestamp between current_timestamp and current_timestamp + make_interval(hours := ?::integer))", $hours);
  }

  public function scopeForTz($query, $timezone)
  {
    return $query->selectRaw("net_id,
name,
band,
primary_frequency,
to_char(start_timestamp  at time zone ?, 'HH24:MI') as start_time,
to_char(end_timestamp at time zone ?, 'HH24:MI') as end_time,
start_timestamp at time zone ?  as start_timestamp,
end_timestamp at time zone ?    as end_timestamp,
end_timestamp_is_estimated
", [$timezone, $timezone, $timezone, $timezone]);
  }
}
