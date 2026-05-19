@extends('layouts.app')

@section('title', 'IDDS Starter - Vanilla JS')

@section('content')
  @include('pages.dashboard')
  @include('pages.articles')
  @include('pages.form')
@endsection
