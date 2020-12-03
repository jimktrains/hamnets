<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">

    <title>Ham Net Database</title>

    <style>
      .footer {
        margin-top: 20px;
        border-top: 1px black solid;
      }
      .inactive {
        color: darkslategrey;
      }
      .inactive .net_id {
        text-decoration: line-through;
      }
      .frequency {
        text-align: right;
      }

      tr:nth-child(even) {
        background-color: lightblue;
      }

      .freq-bar {
        width: 800px;
      }

      .freq-bar .freq-viz {
        width: 5px;
        background: black;
      }
      .freq-bar .freq {
        position: sticky;
        top: 0px;
        display: inline-block;
        height: 10px;
      }
      .estimated {
        color: slategrey;
      }
      .nav div {
        display: inline-block;
      }
      .nav form {
        display: inline-block;
      }
    </style>
    <script>
      // HamGridSquare.js
      // Copyright 2014 Paul Brewer KI6CQ
      // License:  MIT License http://opensource.org/licenses/MIT or CC-BY-SA
      //
      // Javascript routines to convert from lat-lon to Maidenhead Grid Squares
      // typically used in Ham Radio Satellite operations and VHF Contests
      //
      // Inspired in part by K6WRU Walter Underwood's python answer
      // http://ham.stackexchange.com/a/244
      // to this stack overflow question:
      // How Can One Convert From Lat/Long to Grid Square
      // http://ham.stackexchange.com/questions/221/how-can-one-convert-from-lat-long-to-grid-square
      //

      latLonToGridSquare = function(param1,param2){
        var lat=-100.0;
        var lon=0.0;
        var adjLat,adjLon,GLat,GLon,nLat,nLon,gLat,gLon,rLat,rLon;
        var U = 'ABCDEFGHIJKLMNOPQRSTUVWX'
        var L = U.toLowerCase();
        // support Chris Veness 2002-2012 LatLon library and
        // other objects with lat/lon properties
        // properties could be getter functions, numbers, or strings
        function toNum(x){
          if (typeof(x) === 'number') return x;
          if (typeof(x) === 'string') return parseFloat(x);
          if (typeof(x) === 'function') return parseFloat(x());
          throw "HamGridSquare -- toNum -- can not convert input: "+x;
        }
        if (typeof(param1)==='object'){
          if (param1.length === 2){
            lat = toNum(param1[0]);
            lon = toNum(param1[1]);
          } else if (('lat' in param1) && ('lon' in param1)){
            lat = toNum(param1.lat);
            lon = toNum(param1.lon);
          } else if (('latitude' in param1) && ('longitude' in param1)){
            lat = toNum(param1.latitude);
            lon = toNum(param1.longitude);
          } else {
            throw "HamGridSquare -- can not convert object -- "+param1;
          }
        } else {
          lat = toNum(param1);
          lon = toNum(param2);
        }
        if (isNaN(lat)) throw "lat is NaN";
        if (isNaN(lon)) throw "lon is NaN";
        if (Math.abs(lat) === 90.0) throw "grid squares invalid at N/S poles";
        if (Math.abs(lat) > 90) throw "invalid latitude: "+lat;
        if (Math.abs(lon) > 180) throw "invalid longitude: "+lon;
        adjLat = lat + 90;
        adjLon = lon + 180;
        GLat = U[Math.trunc(adjLat/10)];
        GLon = U[Math.trunc(adjLon/20)];
        nLat = ''+Math.trunc(adjLat % 10);
        nLon = ''+Math.trunc((adjLon/2) % 10);
        rLat = (adjLat - Math.trunc(adjLat)) * 60;
        rLon = (adjLon - 2*Math.trunc(adjLon/2)) *60;
        gLat = L[Math.trunc(rLat/2.5)];
        gLon = L[Math.trunc(rLon/5)];
        return GLon+GLat+nLon+nLat+gLon+gLat;
      }

    </script>
    <script>
      function getLocation(cb, ecb) {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(cb);
        } else {
          ecb("Geolocation is not supported by this browser.");
        }
      }

      function showGridSquare(position) {
        console.log(position);
        var gridsquare = latLonToGridSquare(position.coords.latitude, position.coords.longitude);
        console.log(gridsquare);
        var gridsquareinput = document.getElementById("gridsquare");
        gridsquareinput.value = gridsquare;
      }

      function errorGridSquare(err) {
        var gridsquareinput = document.getElementById("gridsquare");
        gridsquareinput.value = err;
      }

      function getGridSquare() {
        getLocation(showGridSquare, errorGridSquare);
      }
    </script>
  </head>
  <body>
    <div class="nav">
      <div><form>
          <select id="timezone" name="timezone">
            @foreach($timezones as $tz)
              <option value="{{$tz}}" {{($tz==$timezone) ? "selected=selected" : ""}}>{{$tz}}</option>
            @endforeach
          </select> <!-- |
          <label for="gridsquare">Grid Square</label>
          <input type="text" id="gridsquare" name="gridsquare"> -->|
          <label for="hours_ahead">Hours to Look Ahead</label>
          <select id="hours_ahead" name="hours_ahead">
            @for($h = 1; $h < 24; $h++)
              <option value="{{$h}}" {{($h==$hoursAhead) ? "selected=selected" : ""}}>{{$h}}</option>
            @endfor
          </select> <!--|
          <select id="license_class" name="license_class">
            @foreach($licenseClasses as $lc)
              <option value="{{$lc}}" {{($lc==$licenseClass) ? "selected=selected" : ""}}>{{$lc}}</option>
            @endforeach
          </select> --> |
          @foreach($bands as $band)
            <input type="checkbox" id="band_{{$band}}" name="bands[]" value="{{$band}}" {{in_array($band, $selectedBands) ? "checked=checked" : ""}}>
            <label for="band_{{$band}}">{{$band}}</label>&nbsp;
          @endforeach
          <input type="submit" value="Update">
        </form>
        <!--<button onclick="getGridSquare()">Get Grid Square</button>-->
      </div>

    </div>
    <div id="upcoming-nets">
      <h2>Upcoming</h2>
      <!--
      <div class="freq-bar">
        @foreach($NextNets as $Net)
          @if(7125000 <= $Net->primary_frequency and $Net->primary_frequency <= 7300000)
            <span class="freq" style="left: {{ (($Net->primary_frequency - 7125000)/(7300000 - 7125000))*800 }}px">{{$Net->format_primary_frequency()}}</span>
          @endif
        @endforeach
      </div>
      <div style="height:10px; border: 1px black solid;" class="freq-bar">
        @foreach($NextNets as $Net)
          @if(7125000 <= $Net->primary_frequency and $Net->primary_frequency <= 7300000)
            <span class="freq freq-viz" style="left: {{ (($Net->primary_frequency - 7125000)/(7300000 - 7125000))*800 }}px">&nbsp;</span>
          @endif
        @endforeach
      </div>
      -->

      <table>
        <thead>
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Band</th>
            <th>Frequency</th>
            <th>Start Time</th>
            <th>End Time</th>
          </tr>
        </thead>
        @foreach($NextNets as $Net)
          <tr>
            <td>{{$Net->net_id}}</td>
            <td>{{$Net->name}}</td>
            <td>{{$Net->band}}</td>
            <td class="frequency">{{$Net->format_primary_frequency()}}</td>
            <td>{{$Net->start_time}}</td>
            <td class="{{$Net->end_timestamp_is_estimated ? 'estimated' : ''}}" title="{{$Net->end_timestamp_is_estimated ? 'estimated end time' : ''}}">{{$Net->end_time}}</td>
          </tr>
        @endforeach
      </table>
    </div>

    <div id="all-nets">
      <h2>All</h2>
      <div><a href="/csv">csv</a></div>
      <table class="nets">
        <thead>
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Band</th>
            <th>Frequency</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Timezone</th>
            <th colspan=7>Operating Days</th>
          </tr>
        </thead>
        <tbody>
          @foreach($Nets as $Net)
            <tr class="{{$Net->active ? "" : "inactive"}}" title="{{$Net->active ? "" : "inactive"}}">
              <td class="net_id">{{$Net->net_id}}</td>
              <td>{{$Net->name}}
                @if(!empty($Net->description))
                  <br>
                  <small class="description">{{$Net->description}}</small>
                @endif
                @if(!empty($Net->url))
                  <br>
                  <small class="url"><a href="{{$Net->url}}">{{$Net->url}}</a></small>
                @endif
                @if($Net->national_traffic_affiliated))
                  <br>
                  <small class="national_traffic_affiliated">National Traffic Affiliated</small>
                @endif
                <td>{{$Net->band}}
                <td class="frequency">{{$Net->format_primary_frequency()}}
                  @if(!empty($Net->secondary_frequency))
                    <br>
                    {{$Net->format_secondary_frequency()}}
                  @endif </td>
                <td>{{$Net->start_time}}</td>
                <td>{{$Net->end_time}}</td>
                <td>{{$Net->timezone}}</td>
                <td>{{$Net->sunday     ? "S" : "" }} </td>
                <td>{{$Net->monday     ? "M" : "" }} </td>
                <td>{{$Net->tuesday    ? "T" : "" }} </td>
                <td>{{$Net->wednesday  ? "W" : "" }} </td>
                <td>{{$Net->thursday   ? "H" : "" }} </td>
                <td>{{$Net->friday     ? "F" : "" }} </td>
                <td>{{$Net->saturday   ? "A" : "" }} </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="footer">
      <div><a href="mailto:info@hamnets.org">Contact</a></div>
      <hr>
      <div>
        <h1>Other Resources</h1>
        <ul>
          <li><a href="https://docs.google.com/spreadsheets/d/1cpaIUPJOG9Kdb0Xo-hyzhcVKcyvOr37vrGIF1mIETHs/edit#gid=906307814">N1YZ HF NET_LIST</a></li>
          <li><a href="http://www.arrl.org/resources/nets/">ARRL ONLINE NET DIRECTORY</a></li>
        </ul>
    </div>
  </body>
</html>
