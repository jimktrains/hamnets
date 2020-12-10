  <input type="hidden" id="band_all" name="bands[]" value="☃">
  @foreach($bands as $band)
    <input type="checkbox" id="band_{{$band}}" name="bands[]" value="{{$band}}" {{in_array($band, $selectedBands) ? "checked=checked" : ""}}>
    <label for="band_{{$band}}">{{$band}}</label>&nbsp;
  @endforeach
