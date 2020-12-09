@extends('base')

@section('title', 'Search')

@section('content')
    <div id="filters">
      <form>
        <label for="timezone">Sort Timezone</label>
        <select id="timezone" name="timezone">
          @foreach($timezones as $tz)
            <option value="{{$tz}}" {{($tz==$timezone) ? "selected=selected" : ""}}>{{$tz}}</option>
          @endforeach
        </select>
        <hr>
        <label for="gridsquare">Grid Square</label>
        <input type="text" id="gridsquare" name="gridsquare" value="{{$gridsquare}}" maxlength=6>
        <input type="button" onclick="getGridSquare()" value="Get Grid Square" />
        <hr>
        <div>Bands</div>
          <input type="checkbox" id="band_all" name="bands[]" value="all">
          <label for="band_all">All</label>&nbsp;
        @foreach($bands as $band)
          <input type="checkbox" id="band_{{$band}}" name="bands[]" value="{{$band}}" {{in_array($band, $selectedBands) ? "checked=checked" : ""}}>
          <label for="band_{{$band}}">{{$band}}</label>&nbsp;
        @endforeach
        <hr>
        <label for="term">Search Term</label>
        <input id="term" name="term" value="{{$term}}" />
        <hr>
        <input type="submit" value="Update">
      </form>
    </div>
    <div id="all-nets">
      <x-net-full-table :nets="$Nets" />
    </div>
@endsection
