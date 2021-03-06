@extends('panel.layouts.app')

{{-- Descripción sobre esta página --}}
@section('title', 'Viendo usuarios')
@section('description', 'Vista de todos los usuarios')

{{-- Marca el elemento del menú que se encuentra activo --}}
@section('active-index', 'active')

@section('content')
    @include('panel.layouts.breadcrumbs', [
        'breadcrumbs' => [
            [
                'title' => 'Usuarios',
                'url' => route('panel.users.index'),
                'icon' => 'fas fa-users'
            ],
            [
                'title' => $user_id ? 'Editando Usuario' : 'Crear Usuario',
                'icon' => 'fas fa-plus'
            ],
        ]
    ])

    {{-- Botones de Acción General --}}
    <div class="row mt-3 mb-4">
        <div class="col-12">
            <button type="submit"
                    class="btn btn-success"
                    form="form-add-user">
                <i class="fa fa-user-plus"></i>
                Guardar
            </button>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="row">
        <div class="col-12">
            @include('panel.users.forms.addedit.index')
        </div>
    </div>
@endsection

@section('css')
    <link href="{{ mix('assets/css/bootstrap-select.css') }}" rel="stylesheet" />
@endsection

@section('js')
    {{-- Bootstrap-select --}}
    <script src="{{ mix('assets/js/bootstrap-select.js') }}"></script>
    <script src="{{ mix('admin-panel/users/js/user_add.js') }}"></script>
@endsection
