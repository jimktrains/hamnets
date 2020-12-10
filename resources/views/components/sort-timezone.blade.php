  <label for="timezone">{{trans('common.sort_timezone')}}</label>
  <select id="timezone" name="timezone">
    @foreach($timezones as $tz)
      <option value="{{$tz}}" {{($tz==$selectedTz) ? "selected=selected" : ""}}>{{$tz}}</option>
    @endforeach
  </select>
