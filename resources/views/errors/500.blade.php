@extends('errors::minimal')

@section('title', __('Something went wrong'))
@section('code', '500')
@section('message', __('We could not complete your request. Please try again later.'))
