@extends('layouts.error')

@section('body')
    Error {{ $exception->getStatusCode() }}: {{ $exception->getMessage() }}
@endsection
