@extends('base')

@section('title', $Net->name)

@push('scripts')
  <script src="/openlayers.github.io@master/en/v6.4.3/build/ol.js"></script>
  <link rel="stylesheet" href="/openlayers.github.io@master/en/v6.4.3/css/ol.css" />

  <style>
    #map {
      width: 800px;
      height: 600px;
    }

    #description {
      margin-top: 2em;
    }

    @counter-style primary-secondary {
      system: fixed;
      symbols: "Primary" "Secondary";
      suffix: ": ";
    }

    ol.frequencies {
      list-style-type: primary-secondary;
      margin-left: 5em;
    }

.calendar {
  padding: 16px;
  margin-bottom: 24px;
  width: 21em;
}
.calendar .header {
  display: flex;
  justify-content: space-between;
}
.calendar .title {
  color: #222741;
  font-size: 20px;
  font-weight: 700;
}

.calendar table {
  margin-top: 12px;
  width: 100%;
  background: lightgrey;
  border-radius: 5%;
}
.calendar tbody td {
  border: 2px solid transparent;
  border-radius: 50%;
}
.calendar-table__row {
  display: flex;
  justify-content: center;
}
.calendar table thead {
  border-bottom: 2px solid #F2F6F8;
  margin-bottom: 4px;
}
.calendar table thead th {
  font-size: 1.1em;
  text-align: center;
  text-transform: uppercase;
}
.calendar table tbody tr:nth-child(even) {
  background: lightgrey;
}

.calendar table tbody td {
  font-size: 0.8em;
  text-align: center;
  text-transform: uppercase;
  font-weight: bold;
}

.calendar table tbody td.today {
  border-color: #FEFEFE !important;
  background-color: grey !important;
}
.calendar table tbody td.event {
  background-color: #66DCEC;
  border-color: #FEFEFE;
  -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  color: #fff;
}
.calendar table tbody td.long {
  overflow-x: hidden;
}
.calendar table tbody td.event.long {
  border-radius: 0;
  border-width: 2px 0;
}
.calendar table tbody td.event.long.start {
  border-left: 2px solid #fff;
  border-radius: 50% 0 0 50%;
}
.calendar table tbody td:last-child.eventr.long.start {
  border-width: 2px;
}
.calendar table tbody td.event.long.end {
  border-right: 2px solid #fff;
  border-radius: 0 50% 50% 0;
}
.calendar table tbody td:first-child.event.long.end {
  border-width: 2px;
}
.calendar table tbody td.inactive {
  color: slategrey;
  cursor: default;
}
.calendar table tbody td.event:hover {
  background: #f8fafa;
  -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  transition: 0.2s all ease-in;
}
.calendar table tbody td.event:hover {
  background: transparent;
  box-shadow: none;
}
.calendar table tbody td.inactive.event {
  color: #fff;
  opacity: 0.25;
}
.calendar table tbody td.inactive.event:hover {
  background: #66DCEC;
  -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
  box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
}

.calendars {
  clear: both;
}

.calendars div:last-child {
  clear: both;
}

.calendars .calendar {
  float: left;
}

.operating-days {
  display: inline;
  list-style: none;
}

.operating-days li {
  display: inline;
}

.operating-days li:after {
  content: ", ";
}

.operating-days li:last-child:after {
  content: "";
}

  </style>
@endpush

@section('content')
  <div id="net">
    <h1>{{$Net->name}}</h1>

    <ol class="frequencies">
      <li><span class="frequency">{{$Net->format_primary_frequency()}}</span> Hz ({{$Net->primary_band}})
        @if (!empty($Net->primary_frequency_repeaterbook_url()))
          (<a href="{{$Net->primary_frequency_repeaterbook_url()}}"><abbr title="RepeaterBook">RB</abbr></a>)
        @endif
      </li>
      @if (!empty($Net->secondary_frequency))
        <li><span class="frequency">{{$Net->format_secondary_frequency()}}</span> Hz ({{$Net->secondary_band}})
          @if (!empty($Net->secondary_frequency_repeaterbook_url()))
            (<a href="{{$Net->secondary_frequency_repeaterbook_url()}}"><abbr title="RepeaterBook">RB</abbr></a>)
          @endif
        </li>
      @endif
    </ol>

    @if (!empty($Net->url))
      <div><a href="{{$Net->url}}">{{$Net->url}}</a></div>
    @endif


    @if (!empty($Net->start_time))
      <div>When: {{date('H:i', strtotime($Net->start_time))}}
        @if (!empty($Net->end_time))
          to {{date('H:i', strtotime($Net->end_time))}}
        @endif
        {{$Net->timezone}}</div>
      <div>
        <ul class="operating-days">
          @if ($Net->sunday)
            <li>{{trans('common.sunday')}}</li>
          @endif
          @if ($Net->monday)
            <li>{{trans('common.monday')}}</li>
          @endif
          @if ($Net->tuesday)
            <li>{{trans('common.tuesday')}}</li>
          @endif
          @if ($Net->wednesday)
            <li>{{trans('common.wednesday')}}</li>
          @endif
          @if ($Net->thursday)
            <li>{{trans('common.thursday')}}</li>
          @endif
          @if ($Net->friday)
            <li>{{trans('common.friday')}}</li>
          @endif
          @if ($Net->saturday)
            <li>{{trans('common.saturday')}}</li>
          @endif
        </ul>
      @endif

      <div class="calendars">
        <x-calendar :events="$Net->schedule($timezone)" :showInactive=false/>
        <x-calendar :events="$Net->schedule($timezone)" incMonth=1 :showInactive=false />
        <div></div>
      </div>


      @if (!empty($Net->description))
        <div id="description">{{$Net->description}}</div>
      @endif

      <hr>
      @if (!empty($Net->arrl_net_id))
        <div>ARRL Net Id: <a href="http://www.arrl.org/webroot/resources/nets/client/netdetail.html?mfind={{$Net->arrl_net_id}}">{{$Net->arrl_net_id}}</a></div>
      @endif
      @if (!empty($Net->arrl_state))
        <div>ARRL State: {{$Net->arrl_state}}</div>
      @endif
      @if (!empty($Net->arrl_area))
        <div>ARRL Area: {{$Net->arrl_area}}</div>
      @endif
      @if (!empty($Net->arrl_region))
        <div>ARRL Region: {{$Net->arrl_region}}</div>
      @endif
      @if (!empty($Net->arrl_section))
        <div>ARRL Section: {{$Net->arrl_section}}</div>
      @endif
      @if (!empty($Net->arrl_coverage))
        <div>ARRL Coverage: {{$Net->arrl_coverage}}</div>
      @endif
      @if (!empty($Net->arrl_traffic_handling))
        <div>ARRL Traffic Handling: {{$Net->arrl_traffic_handling}}</div>
      @endif
      @if (!empty($Net->arrl_national_traffic_affiliated))
        <div>ARRL National Traffic Affiliated: {{$Net->arrl_national_traffic_affiliated}}</div>
      @endif


      @if (0 != $Net->Coverage()->count())
        <h2>{{trans('common.intended_coverage')}}</h2>
        <ul>
          @foreach ($Net->Coverage()->get() as $Gadm)
            <li>{{$Gadm->name_0}} > {{$Gadm->name_1}}</li>
          @endforeach
        </ul>
      @endif
      </div>
      @if ($Net->hasCoverage())
      <div id='map'></div>
      <div><small>{{trans('common.coverage_disclaimer')}}</small></div>
      @endif
      <div><a href="mailto:info@hamnets.org?subject=Problem Report for #{{$Net->net_id}}: {{$Net->name}}">{{trans('common.report_inaccuracy')}}</a></div>
      <script>
        var map = new ol.Map({
          target: 'map',
          layers: [
            new ol.layer.Tile({
              source: new ol.source.OSM()
            }),
            new ol.layer.VectorTile({
              source: new ol.source.VectorTile({
                format: new ol.format.MVT(),
                url: "/net/{{$Net->net_id}}/tiles/{z}/{x}/{y}",
              }),
              style: function(feature, res) {
                return new ol.style.Style({
                  fill: new ol.style.Fill({
                    color: 'rgba(0, 102, 204, 0.2)'
                  }),
                  stroke: new ol.style.Stroke({
                    width: 2,
                    color: 'rgba(0, 102, 204)'
                  })
                });
              },
            })
          ],
          view: new ol.View({
            center: ol.proj.fromLonLat([-90, 40]),
            zoom: 4
          })
        });
      </script>
  </div>
@endsection
