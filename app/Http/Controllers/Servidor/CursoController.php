<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class CursoController extends Controller
{

    public function Index() //Exibe os cursos na aba "Cursos" do layout principal
    {
    $cursos = Curso::where('status', 'aberto')->paginate(3);


    return Inertia::render('Cursos', [
        'cursos' => $cursos,
    ]);
    }

    public function Show(Curso $curso)//Exibe os detalhes do curso para a o usuário fazer matrícula
    {
        return Inertia::render('CursoDetalhe', [
            'curso' => $curso,
        ]);
    }

}