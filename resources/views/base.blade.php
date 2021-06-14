<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">

    <title>@yield('title') - Ham Net Database</title>

    <style>
      #gridsquare{
        width: 6em;
      }
      #solar {
        clear: both;
        margin-top: 20px;
        border-top: 1px black solid;
      }

      footer {
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

      #upcoming-nets {
        width: 49%;
        float: left
        }
      #current-nets {
        width: 49%;
        float: right;
        }
      #coverage-nets {
        clear: both;
      }
      #all-nets {
        clear: both;
      }

      span.frequency {
        font-size: 1.1em;
      }

      td.frequency {
        min-width: 10em;
      }

      td.time {
        text-align: center;
      }

      td.band {
        text-align: center;
        max-width: 4em;
      }

      header {
        border-bottom: 1px black solid;
        margin-bottom: 10px;
        padding-bottom: 10px;
      }

      nav {
        display: inline;
      }

      header form {
        display: inline;
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
        return false;
      }
      function clearGridsquare() {
        var gridsquareinput = document.getElementById("gridsquare");
        gridsquareinput.value = '';
        return false;
      }
    </script>
    @stack('scripts')
  </head>
  <body>
    <header>
      <nav>
        <a href="{{route('home')}}">{{trans('common.homepage')}}</a> | <a href="{{route('net.index')}}">{{trans('common.net_index')}}</a>
      </nav> |
        <form action="{{route('net.index')}}">
          <label for="header-term">{{trans('common.search_term')}}</label>
          <input type="text" name="term" id="header-term" />
          <input type="submit" value="{{trans('common.search')}}" />
        </form>
      <hr>
      <form>
        <div>Location and proximity are estimated.</div>
        <x-sort-timezone />
        <x-gridsquare-filter />
        <br>
        <x-band-filter />
        <input type="submit" value="{{trans('common.update')}}" />
      </form>
    </header>

    @yield('content')

    <footer>
      <div><a href="mailto:info@hamnets.org">Contact</a> - {{trans('common.reach_out')}}</div>
      <div><a href="https://github.com/jimktrains/hamnets/">GitHub</a> - {{trans('common.code_contribute')}}</div>
      <hr>
      <div>
        <h1>{{trans('common.other_resources')}}</h1>
        <ul>
          <li><a href="https://docs.google.com/spreadsheets/d/1cpaIUPJOG9Kdb0Xo-hyzhcVKcyvOr37vrGIF1mIETHs/edit#gid=906307814">N1YZ HF NET_LIST</a></li>
          <li><a href="http://www.arrl.org/resources/nets/">ARRL ONLINE NET DIRECTORY</a></li>
          <li><a href="http://repeaterbook.com">RepeaterBook</a></li>
          <li><a href="http://www.netlogger.org/">NetLogger</a></li>
        </ul>
    </footer>
  </body>
</html>

