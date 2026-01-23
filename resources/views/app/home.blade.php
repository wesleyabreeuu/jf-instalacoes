@extends('layouts.adminlte')

@section('title', 'Serviços - JF Instalações')
@section('page-title', 'Serviços')

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Bem-vindo, <b>{{ auth()->user()->name }}</b> ✅</p>
            <p>Aqui vai entrar a lista de serviços (Meus Serviços).</p>
        </div>
    </div>
@endsection
