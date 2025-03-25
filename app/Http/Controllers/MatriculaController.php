<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\Curso;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Mail\NovaMatricula;
use App\Mail\MatriculaAprovada;
use App\Mail\MatriculaRejeitada;

class MatriculaController extends Controller
{
    /**
     * Exibe o formulário de inscrição para um curso.
     *
     * @param  int  $cursoId
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */

    public function inscricao($cursoId)
    {
        // Validação de ID
        $cursoId = filter_var($cursoId, FILTER_VALIDATE_INT);
        if (!$cursoId) {
            abort(404, 'Curso não encontrado');
        }
        
        $curso = Curso::findOrFail($cursoId);
        $user = Auth::user();

        // Verifica se o usuário já está matriculado
        $matriculaExistente = Matricula::where('curso_id', $cursoId)
            ->where('user_id', $user->id)
            ->exists();

        if ($matriculaExistente) {
            return back()->withErrors([
                'enrollment' => 'Você já está matriculado neste curso.'
            ]);
        }

        // Verifica se o curso atingiu a capacidade máxima
        $matriculasAtivas = Matricula::where('curso_id', $cursoId)
            ->whereIn('status', ['aprovada', 'pendente'])
            ->count();

        if ($matriculasAtivas >= $curso->capacidade_maxima) {
            return redirect()->route('cursos')
                ->with('message', 'O curso atingiu a capacidade máxima.');
        }

        return Inertia::render('CursoDetalhesComponents/Formulario', [
            'curso' => $curso,
            'user' => $user
        ]);
    }

    /**
     * Exibe a página de confirmação após o envio da matrícula
     */
    public function confirmacao(Request $request)
    {
        // Recuperar dados da matrícula da sessão
        return Inertia::render('Components/Confirmacao', [
            'user' => Auth::user(),
            'mensagem' => 'Sua inscrição foi enviada com sucesso e está aguardando análise.',
            'detalhes' => session('detalhes_matricula'),
            'tipo' => 'matricula'
        ]);
    }

    /**
     * Processa a inscrição em um curso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        //Validação dos dados
        $validator = Validator::make($request->all(), [
            'curso_id' => ['required', 'exists:cursos,id'],
            'dados_adicionais' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $validated = $validator->validated();
    
        $user = Auth::user();
        $curso = Curso::findOrFail($validated['curso_id']);
    
        // Logs para auditoria
        Log::info('Tentativa de matrícula', [
            'user_id' => $user->id,
            'curso_id' => $curso->id, 
            'ip' => $request->ip()
        ]);
    
        // Verifica se o usuário já está matriculado
        if (Matricula::where('curso_id', $validated['curso_id'])
            ->where('user_id', $user->id)
            ->exists()) {
            return redirect()->route('cursos')
                ->with('message', 'Você já está matriculado neste curso.');
        }
    
        // Verifica a capacidade do curso
        $matriculasAtivas = Matricula::where('curso_id', $validated['curso_id'])
            ->whereIn('status', ['aprovada', 'pendente'])
            ->count();
    
        if ($matriculasAtivas >= $curso->capacidade_maxima) {
            return redirect()->route('cursos')
                ->with('message', 'O curso atingiu a capacidade máxima.');
        }
    
        try {
            // Criar a matrícula
            $matricula = Matricula::create([
                'curso_id' => $validated['curso_id'],
                'user_id' => $user->id,
                'dados_adicionais' => $validated['dados_adicionais'],
                'status' => 'pendente',
            ]);
    
            session(['detalhes_matricula' => [
                'nome' => $user->name,
                'curso' => $curso->nome,
                'data_inicio' => (new \DateTime($curso->data_inicio))->format('d/m/Y'),
                'data_fim' => (new \DateTime($curso->data_fim))->format('d/m/Y'),
                'id' => $matricula->id,
                'created_at' => now()->format('d/m/Y H:i')
            ]]);
            
            return redirect()->route('confirmacao');
        } catch (\Exception $e) {
            Log::error('Erro ao realizar matrícula', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'curso_id' => $curso->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocorreu um erro ao processar sua inscrição. Por favor, tente novamente.')
                ->withInput();
        }
    }
}