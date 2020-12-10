<div class="calendar">
  <div class="header">
    <h2 class="title">{{$strmonth()}} {{$year}}</h2>
  </div>
  <table>
    <thead>
      <tr>
        <th>S</th>
        <th>M</th>
        <th>T</th>
        <th>W</th>
        <th>T</th>
        <th>F</th>
        <th>S</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cells() as $cell)
        {!! $cell['sunday'] ? "<tr>" : '' !!}
        <td class="{{$cell['inactive']}} {{$cell['event']}} {{$cell['start']}} {{$cell['long']}} {{$cell['end']}} {{$cell['today']}}">{{$cell['day']}}</td>
        {!! $cell['saturday'] ? "</tr>" : '' !!}
      @endforeach
    </tbody>
  </table>
</div>
