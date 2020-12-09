<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Welcome;
use App\Http\Controllers\Net;
use App\Http\Controllers\NetIndex;

/*
 |--------------------------------------------------------------------------
 | Web Routes
 |--------------------------------------------------------------------------
 |
 | Here is where you can register web routes for your application. These
 | routes are loaded by the RouteServiceProvider within a group which
 | contains the "web" middleware group. Now create something great!
 |
 */
Route::get('/csv', [NetIndex::class, 'csv'])
  ->name('csv');

Route::get('/net/{net_id}/tiles/{x}/{y}/{z}', [Net::class, 'tile'])
  ->name('net_map_tile');

Route::get('/net/{net_id}', [Net::class, 'show'])
  ->name('net');

Route::get('/net', [NetIndex::class, 'index'])
  ->name('net.index');

Route::get('/', [Welcome::class, 'index'])
  ->name('home');
