<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Net extends Model
{
  protected $table = "netwithband";
  protected $primaryKey = 'net_id';
    use HasFactory;

  public function scopeOrderInTz($query, $timezone)
  {
    return $query->orderByRaw("extract(hour from (current_date + start_time) at time zone timezone at time zone ?)", $timezone);
  }

  public function scopeWhereGridSquare($query, $gridsquare)
  {
    return $query->selectRaw("distinct on (netwithband.net_id) netwithband.*")
        ->leftJoin('coverage', 'coverage.net_id', '=', 'netwithband.net_id')
        ->leftJoin('gadm36', function ($join) use ($gridsquare) {
          $join->on('gadm36.gid', '=', 'coverage.gid')->whereRaw("gadm36.geom && maidenhead2bbox(?)", $gridsquare);
        })
        ->OrWhereNotNull("gadm36.gid")
        ->OrWhereRaw("st_buffer(maidenhead2centroid(primary_repeater_gridsquare), 1.0, 16) && maidenhead2bbox(?)", $gridsquare)
        ->OrWhereRaw("st_buffer(maidenhead2centroid(secondary_repeater_gridsquare), 1.0, 16) && maidenhead2bbox(?)", $gridsquare)
        ->orderBy('netwithband.net_id');
  }

  public function getTile($zoom, $x, $y, $gridsquare)
  {
    $res = DB::select("
    WITH mvtgeom AS
(
  SELECT st_setsrid(ST_AsMVTGeom(ST_Transform(geom, 3857), ST_TileEnvelope(?, ?, ?)), 0) AS geom,
         name_0,
         name_1,
         engtype_1
  FROM gadm36
  join coverage on gadm36.gid = coverage.gid
  WHERE net_id = ?
    and ST_Transform(geom, 3857) && ST_TileEnvelope(?, ?, ?)
  union all
  SELECT st_setsrid(ST_AsMVTGeom(ST_Transform(st_buffer(maidenhead2centroid(primary_repeater_gridsquare), 1.0, 16), 3857), ST_TileEnvelope(?, ?, ?)), 0) AS geom,
         primary_repeater_gridsquare,
         null,
         'gridsquare-buffer'
  FROM net
  WHERE net_id = ?
    and st_transform(st_buffer(maidenhead2centroid(primary_repeater_gridsquare), 1.0, 16), 3875) && ST_TileEnvelope(?, ?, ?)
  union all
  SELECT st_setsrid(ST_AsMVTGeom(ST_Transform(st_buffer(maidenhead2centroid(secondary_repeater_gridsquare), 0.75, 16), 3857), ST_TileEnvelope(?, ?, ?)), 0) AS geom,
         secondary_repeater_gridsquare,
         null,
         'gridsquare-buffer'
  FROM net
  WHERE net_id = ?
    and st_transform(st_buffer(maidenhead2centroid(secondary_repeater_gridsquare), 0.75, 16), 3857) && ST_TileEnvelope(?, ?, ?)
)
SELECT ST_AsMVT(mvtgeom.*)
FROM mvtgeom;
", [$zoom, $x,$y, $this->net_id, $zoom, $x, $y, $zoom, $x, $y, $this->net_id, $zoom, $x, $y, $zoom, $x, $y, $this->net_id, $zoom, $x, $y,]);
    $mvtgeom = stream_get_contents($res[0]->st_asmvt);
    return $mvtgeom;
  }

  public function primary_frequency_repeaterbook_url()
  {
    if ($this->primary_frequency_is_repeater) {
      return "https://www.repeaterbook.com/repeaters/details.php?state_id={$this->primary_repeaterbook_state_id}&ID={$this->primary_repeaterbook_repeater_id}";
    }
  }

  public function secondary_frequency_repeaterbook_url()
  {
    if ($this->secondary_frequency_is_repeater) {
      return "https://www.repeaterbook.com/repeaters/details.php?state_id={$this->secondary_repeaterbook_state_id}&ID={$this->secondary_repeaterbook_repeater_id}";
    }
  }

  public function Coverage()
  {
    return $this->belongsToMany(Gadm36::class, 'coverage', 'net_id', 'gid');
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
