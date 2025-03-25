<?php

namespace App\Http\Controllers\Servidor;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Matricula;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CursoController extends Controller
{
    /**
     * Exibe o calendário de cursos com inscrições abertas
     */
    public function calendario()
    {
        // Filtrar apenas cursos com status 'aberto'
        $cursos = Curso::where('status', 'aberto')->get();
        
        // Formatar os cursos para o componente CalendarioCursos
        $cursosFormatados = $cursos->map(function ($curso) {
            // Calcular vagas disponíveis
            $vagasDisponiveis = $curso->capacidade_maxima - $curso->alunos()->count();
            
            return [
                'id' => $curso->id,
                'nome' => $curso->nome,
                'descricao' => $curso->descricao,
                'data_inicio' => $curso->data_inicio->format('Y-m-d'),
                'data_fim' => $curso->data_fim->format('Y-m-d'),
                'carga_horaria' => $curso->carga_horaria,
                'localizacao' => $curso->localizacao,
                'capacidade_maxima' => $curso->capacidade_maxima,
                'vagas_disponiveis' => $vagasDisponiveis,
                'modalidade' => $curso->modalidade,
                'pre_requisitos' => $curso->pre_requisitos ?? [],
                'enxoval' => $curso->enxoval ?? [],
                'status' => $curso->status,
                'imagem' => $curso->imagem ?? null
            ];
        });
        
        // Agrupar cursos por mês
        $meses = $cursos->groupBy(function ($curso) {
            return Carbon::parse($curso->data_inicio)->format('Y-m');
        })->keys();
        
        return Inertia::render('Servidor/Home', [
            'route' => 'calendario',
            'cursos' => $cursosFormatados,
            'meses' => $meses
        ]);
    }
    
    /**
     * Exibe os detalhes do curso em uma página completa
     */
    public function detalhes(Curso $curso)
    {
        // Verificar se o curso existe
        if (!$curso) {
            abort(404, 'Curso não encontrado');
        }
        
        // Calcular vagas disponíveis
        $vagasDisponiveis = $curso->capacidade_maxima - $curso->alunos()->count();
        
        // Verificar se o usuário atual já está inscrito
        $usuarioInscrito = $curso->alunos()->where('user_id', Auth::id())->exists();
        
        $cursoDetalhes = [
            'id' => $curso->id,
            'nome' => $curso->nome,
            'descricao' => $curso->descricao,
            'data_inicio' => $curso->data_inicio->format('Y-m-d'),
            'data_fim' => $curso->data_fim->format('Y-m-d'),
            'carga_horaria' => $curso->carga_horaria,
            'localizacao' => $curso->localizacao,
            'capacidade_maxima' => $curso->capacidade_maxima,
            'vagas_disponiveis' => $vagasDisponiveis,
            'modalidade' => $curso->modalidade,
            'pre_requisitos' => $curso->pre_requisitos ?? [],
            'enxoval' => $curso->enxoval ?? [],
            'status' => $curso->status,
            'imagem' => $curso->imagem ?? null,
            'usuario_inscrito' => $usuarioInscrito
        ];
        
        return Inertia::render('Servidor/Home', [
            'curso' => $cursoDetalhes,
            'route' => 'curso.detalhes',
        ]);
    }
    
    /**
     * Exibe o formulário de inscrição para um curso
     */
    public function formulario(Curso $curso)
    {
        // Verificar se o curso existe e tem status 'aberto'
        if (!$curso || $curso->status !== 'aberto') {
            abort(404, 'Curso não encontrado ou não disponível para inscrição');
        }

        // Verificar se há vagas disponíveis
        $vagasDisponiveis = $curso->capacidade_maxima - $curso->alunos()->count();
        if ($vagasDisponiveis <= 0) {
            return redirect()->route('servidor.calendario')
                ->with('erro', 'Não há vagas disponíveis para este curso');
        }
        
        // Verificar se o usuário já está inscrito
        if ($curso->alunos()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('servidor.calendario')
                ->with('erro', 'Você já está inscrito neste curso');
        }
        
        return Inertia::render('Servidor/Home', [
            'route' => 'formulario',
            'curso' => $curso,
            'user' => Auth::user()
        ]);
    }
    
    /**
     * Processa a solicitação de inscrição em um curso
     */
    public function inscrever(Request $request, Curso $curso)
    {
        // Validar a solicitação
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'dados_adicionais' => 'required|array'
        ]);
        
        // Verificar se o curso existe e tem status 'aberto'
        if (!$curso || $curso->status !== 'aberto') {
            return response()->json([
                'success' => false,
                'message' => 'Curso não encontrado ou não disponível para inscrição'
            ], 404);
        }
        
        // Verificar se há vagas disponíveis
        $vagasDisponiveis = $curso->capacidade_maxima - $curso->alunos()->count();
        if ($vagasDisponiveis <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não há vagas disponíveis para este curso'
            ], 400);
        }
        
        // Verificar se o usuário já está inscrito
        if ($curso->alunos()->where('user_id', Auth::id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Você já está inscrito neste curso'
            ], 400);
        }
        
        // Criar a matrícula com dados adicionais do formulário
        try {
            $matricula = new Matricula();
            $matricula->curso_id = $curso->id;
            $matricula->user_id = Auth::id();
            $matricula->dados_adicionais = $validated['dados_adicionais'];
            $matricula->status = 'pendente';
            $matricula->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitação de inscrição enviada com sucesso! Aguarde a aprovação.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar inscrição. Por favor, tente novamente.'
            ], 500);
        }
    }
    
    /**
     * Retorna o histórico de cursos do usuário atual
     */
    public function historico()
    {
        $user = Auth::user();
        
        $cursosRealizados = $user->cursos()
            ->wherePivot('status', 'aprovada')
            ->get()
            ->map(function ($curso) {
                return [
                    'id' => $curso->id,
                    'nome' => $curso->nome,
                    'descricao' => $curso->descricao,
                    'data_inicio' => $curso->data_inicio->format('Y-m-d'),
                    'data_fim' => $curso->data_fim->format('Y-m-d'),
                    'carga_horaria' => $curso->carga_horaria,
                    'status_matricula' => $curso->pivot->status
                ];
            });
            
        $cursosEmAndamento = $user->cursos()
            ->wherePivot('status', 'pendente')
            ->get()
            ->map(function ($curso) {
                return [
                    'id' => $curso->id,
                    'nome' => $curso->nome,
                    'descricao' => $curso->descricao,
                    'data_inicio' => $curso->data_inicio->format('Y-m-d'),
                    'data_fim' => $curso->data_fim->format('Y-m-d'),
                    'carga_horaria' => $curso->carga_horaria,
                    'status_matricula' => $curso->pivot->status
                ];
            });
        
        return Inertia::render('Servidor/Home', [
            'route' => 'historico',
            'cursosRealizados' => $cursosRealizados,
            'cursosEmAndamento' => $cursosEmAndamento
        ]);
    }
}