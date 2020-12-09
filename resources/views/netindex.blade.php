@extends('base')

@section('title', 'Search')

@section('content')
    <div id="filters">
      <form>
        <label for="timezone">{{trans('common.sort_timezone')}}</label>
        <select id="timezone" name="timezone">
          @foreach($timezones as $tz)
            <option value="{{$tz}}" {{($tz==$timezone) ? "selected=selected" : ""}}>{{$tz}}</option>
          @endforeach
        </select>
        <hr>
        <label for="gridsquare">{{trans('common.grid_square')}}</label>
        <input type="text" id="gridsquare" name="gridsquare" value="{{$gridsquare}}" maxlength=6>
        <input type="button" onclick="getGridSquare()" value="{{trans('common.get_grid_square')}}" />
        <hr>
        <div>{{trans('common.bands')}}</div>
          <input type="checkbox" id="band_all" name="bands[]" value="all">
          <label for="band_all">{{trans('common.all')}}</label>&nbsp;
        @foreach($bands as $band)
          <input type="checkbox" id="band_{{$band}}" name="bands[]" value="{{$band}}" {{in_array($band, $selectedBands) ? "checked=checked" : ""}}>
          <label for="band_{{$band}}">{{$band}}</label>&nbsp;
        @endforeach
        <hr>
        <label for="term">{{trans('common.search_term')}}</label>
        <input id="term" name="term" value="{{$term}}" />
        <hr>
        <input type="submit" value="{{trans('common.search')}}">
      </form>
    </div>
    <div id="all-nets">
      <x-net-full-table :nets="$Nets" />
    </div>
@endsection
