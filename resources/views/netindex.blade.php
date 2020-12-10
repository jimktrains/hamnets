@extends('base')

@section('title', 'Search')

@section('content')
    <div id="filters">
    </div>
    <div id="all-nets">
      <x-net-full-table :nets="$Nets" />
    </div>
@endsection
