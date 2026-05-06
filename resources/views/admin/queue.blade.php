@extends('layouts.admin')

@section('title', 'Monitor Antrean')
@section('header', 'Monitor Antrean')
@section('subheader', 'Kelola panggilan pasien untuk hari ini.')

@section('content')

<livewire:queue-monitor />

@endsection