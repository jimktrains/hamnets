<label for="gridsquare">{{trans('common.grid_square')}}</label>
<input type="text" id="gridsquare" name="gridsquare" value="{{$gridsquare}}" maxlength=6>
<input type="button" onclick="getGridSquare()" value="{{trans('common.get_grid_square')}}" />
<input type="button" onclick="clearGridsquare()" value="{{trans('common.clear_grid_square')}}" />
