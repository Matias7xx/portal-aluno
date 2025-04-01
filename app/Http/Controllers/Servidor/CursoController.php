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
        
        // Buscar todas as matrículas do usuário com os cursos relacionados
        $matriculas = Matricula::with('curso')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($matricula) {
                $curso = $matricula->curso;
                
                // Formatar os dados para o frontend
                return [
                    'id' => $curso->id,
                    'nome' => $curso->nome,
                    'descricao' => $curso->descricao,
                    'data_inicio' => $curso->data_inicio->format('Y-m-d'),
                    'data_fim' => $curso->data_fim->format('Y-m-d'),
                    'carga_horaria' => $curso->carga_horaria,
                    'localizacao' => $curso->localizacao,
                    'modalidade' => $curso->modalidade,
                    'status' => $curso->status, // Status do curso
                    // Dados da matrícula
                    'status_matricula' => $matricula->status,
                    'motivo_rejeicao' => $matricula->motivo_rejeicao,
                    'data_inscricao' => $matricula->created_at->format('Y-m-d'),
                    'matricula_id' => $matricula->id
                ];
            });
        
        // Todas as matrículas do usuário para o componente atualizado
        // No componente é feita a filtragem das matrículas por status
        $minhasMatriculas = $matriculas->values();
        
        // Cursos concluídos com matrícula aprovada
        $cursosConcluidos = $matriculas->filter(function ($curso) {
            return $curso['status'] === 'concluído' && $curso['status_matricula'] === 'aprovada';
        })->values();
        
        // Matrículas que foram rejeitadas
        $matriculasRejeitadas = $matriculas->filter(function ($curso) {
            return $curso['status_matricula'] === 'rejeitada';
        })->values();
        
        return Inertia::render('Servidor/Home', [
            'route' => 'historico',
            'minhasMatriculas' => $minhasMatriculas,
            'cursosConcluidos' => $cursosConcluidos,
            'matriculasRejeitadas' => $matriculasRejeitadas
        ]);
    }
    
    /**
     * Gera um certificado para um curso concluído
     * (Funcionalidade a ser implementada)
     */
    public function certificado(Curso $curso)
    {
        // Verificar se o usuário tem permissão para emitir o certificado
        $matricula = Matricula::where('curso_id', $curso->id)
            ->where('user_id', Auth::id())
            ->where('status', 'aprovada')
            ->first();
            
        if (!$matricula || $curso->status !== 'concluído') {
            abort(403, 'Você não tem permissão para emitir este certificado ou o curso ainda não foi concluído');
        }
        
        // Aqui será implementada a lógica para gerar o certificado
        // Por exemplo, renderizar um PDF com os dados do curso e do aluno
        
        // Por enquanto, apenas retorna uma mensagem de sucesso
        return Inertia::render('Servidor/Home', [
            'route' => 'certificado',
            'curso' => $curso,
            'matricula' => $matricula,
            'message' => 'Função de emissão de certificado será implementada em breve.'
        ]);
    }
}