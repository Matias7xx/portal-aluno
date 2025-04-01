<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const carregando = ref(false);

// Estado para diferentes categorias de cursos
const cursosAbertos = ref([]);
const cursosEmAndamento = ref([]);
const cursosConcluidos = ref([]);

// Estado para modal de detalhes
const showModal = ref(false);
const cursoSelecionado = ref(null);

// Estado para matrículas rejeitadas
const matriculasRejeitadas = ref([]);

onMounted(() => {
  carregando.value = true;
  
  // Obter todas as matrículas do usuário
  const minhasMatriculas = page.props.minhasMatriculas || [];
  
  // Filtrar matrículas rejeitadas primeiro (independente do status do curso)
  matriculasRejeitadas.value = minhasMatriculas.filter(curso => 
    curso.status_matricula === 'rejeitada'
  );
  
  // Filtrar as demais matrículas por status do curso
  // Excluindo as rejeitadas para não aparecerem em múltiplas seções
  const matriculasValidas = minhasMatriculas.filter(curso => 
    curso.status_matricula !== 'rejeitada'
  );
  
  // Separar por status do curso
  cursosAbertos.value = matriculasValidas.filter(curso => 
    curso.status === 'aberto'
  );
  
  cursosEmAndamento.value = matriculasValidas.filter(curso => 
    curso.status === 'em andamento'
  );
  
  // Para cursos concluídos, usamos os dados fornecidos diretamente ou filtramos
  if (page.props.cursosConcluidos) {
    cursosConcluidos.value = page.props.cursosConcluidos;
  } else {
    cursosConcluidos.value = matriculasValidas.filter(curso => 
      curso.status === 'concluído' && curso.status_matricula === 'aprovada'
    );
  }
  
  carregando.value = false;
});

// Formatar data para exibição
const formatarData = (dataString) => {
  if (!dataString) return '';
  const data = new Date(dataString);
  return data.toLocaleDateString('pt-BR');
};

// Abrir modal de detalhes do curso
const verDetalhes = (curso) => {
  cursoSelecionado.value = curso;
  showModal.value = true;
};

// Fechar modal
const fecharModal = () => {
  showModal.value = false;
  setTimeout(() => {
    cursoSelecionado.value = null;
  }, 300);
};

// Verifica se tem motivo de rejeição
const temMotivoRejeicao = computed(() => {
  return cursoSelecionado.value && 
         cursoSelecionado.value.motivo_rejeicao && 
         cursoSelecionado.value.motivo_rejeicao.trim() !== '';
});

// Formatar o status da matrícula para exibição
const formatarStatusMatricula = (status) => {
  if (!status) return '';
  
  const statusMap = {
    'pendente': {
      texto: 'Pendente',
      classe: 'bg-yellow-100 text-yellow-800'
    },
    'aprovada': {
      texto: 'Aprovada',
      classe: 'bg-green-100 text-green-800'
    },
    'rejeitada': {
      texto: 'Rejeitada',
      classe: 'bg-red-100 text-red-800'
    }
  };
  
  return statusMap[status] || { texto: status.charAt(0).toUpperCase() + status.slice(1), classe: 'bg-gray-100 text-gray-800' };
};

// Formatar o status do curso para exibição
const formatarStatusCurso = (status) => {
  if (!status) return '';
  
  const statusMap = {
    'aberto': {
      texto: 'Aberto',
      classe: 'bg-blue-100 text-blue-800'
    },
    'em andamento': {
      texto: 'Em andamento',
      classe: 'bg-indigo-100 text-indigo-800'
    },
    'concluído': {
      texto: 'Concluído',
      classe: 'bg-green-100 text-green-800'
    },
    'cancelado': {
      texto: 'Cancelado',
      classe: 'bg-red-100 text-red-800'
    }
  };
  
  return statusMap[status] || { texto: status.charAt(0).toUpperCase() + status.slice(1), classe: 'bg-gray-100 text-gray-800' };
};

// Formatar a modalidade do curso para exibição
const formatarModalidade = (modalidade) => {
  if (!modalidade) return '';
  
  const modalidadeMap = {
    'presencial': {
      texto: 'Presencial',
      classe: 'bg-emerald-100 text-emerald-800'
    },
    'online': {
      texto: 'Online',
      classe: 'bg-purple-100 text-purple-800'
    },
    'híbrido': {
      texto: 'Híbrido',
      classe: 'bg-amber-100 text-amber-800'
    }
  };
  
  return modalidadeMap[modalidade] || { texto: modalidade.charAt(0).toUpperCase() + modalidade.slice(1), classe: 'bg-gray-100 text-gray-800' };
};

// Navegar para a página de detalhes do curso
const navegarParaDetalhes = (cursoId) => {
  if (!cursoId) return;
  router.visit(route('servidor.curso.detalhes', cursoId));
};

// Emitir certificado (funcionalidade a ser implementada)
const emitirCertificado = (cursoId) => {
  alert('Funcionalidade de emissão de certificado a ser implementada.');
  // Aqui seria a implementação para emitir o certificado
  // router.visit(route('servidor.curso.certificado', cursoId));
};
</script>

<template>
  <div class="historico-cursos">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">Histórico de Cursos</h1>
      <p class="text-gray-600">Acompanhe seus cursos realizados e em andamento</p>
    </div>
    
    <!-- Estado de carregando -->
    <div v-if="carregando" class="flex justify-center my-12">
      <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <div v-else>
      <!-- 1. Cursos com Inscrição Aberta -->
      <div v-if="cursosAbertos.length > 0" class="mb-10">
        <h2 class="text-xl font-semibold text-blue-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Cursos com Inscrições Abertas
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="curso in cursosAbertos" 
            :key="curso.id"
            class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300"
          >
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
              <h3 class="font-medium text-gray-900 truncate">{{ curso.nome }}</h3>
              <div class="flex gap-1">
                <span 
                  :class="[formatarStatusMatricula(curso.status_matricula).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarStatusMatricula(curso.status_matricula).texto }}
                </span>
              </div>
            </div>
            <div class="p-4">
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ curso.descricao }}</p>
              
              <!-- Mostrar status do curso e modalidade -->
              <div class="flex flex-wrap gap-2 mb-3">
                <span 
                  v-if="curso.status"
                  :class="[formatarStatusCurso(curso.status).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarStatusCurso(curso.status).texto }}
                </span>
                <span 
                  v-if="curso.modalidade"
                  :class="[formatarModalidade(curso.modalidade).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarModalidade(curso.modalidade).texto }}
                </span>
              </div>
              
              <!-- Motivo de rejeição (se houver) -->
              <div 
                v-if="curso.status_matricula === 'rejeitada' && curso.motivo_rejeicao" 
                class="mb-3 p-2 bg-red-50 border border-red-100 rounded-md"
              >
                <p class="text-sm text-red-800">
                  <span class="font-medium">Motivo da rejeição:</span> {{ curso.motivo_rejeicao }}
                </p>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>{{ formatarData(curso.data_inicio) }} a {{ formatarData(curso.data_fim) }}</span>
                </div>
                
                <div class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ curso.carga_horaria }} horas</span>
                </div>
                
                <div v-if="curso.localizacao" class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span>{{ curso.localizacao }}</span>
                </div>
              </div>
              
              <div class="flex justify-between pt-2 border-t border-gray-100">
                <button 
                  @click="verDetalhes(curso)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Detalhes
                </button>
                
                <button 
                  @click="navegarParaDetalhes(curso.id)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                  Ver Curso
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 2. Cursos em Andamento -->
      <div class="mb-10">
        <h2 class="text-xl font-semibold text-indigo-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          Cursos em Andamento
        </h2>
        
        <div v-if="cursosEmAndamento.length === 0" class="bg-gray-50 rounded-lg p-6 text-center">
          <p class="text-gray-500">Você não possui cursos em andamento no momento.</p>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="curso in cursosEmAndamento" 
            :key="curso.id"
            class="bg-white rounded-lg shadow border border-indigo-100 overflow-hidden hover:shadow-md transition-shadow duration-300"
          >
            <div class="bg-indigo-50 px-4 py-3 border-b border-indigo-100 flex justify-between items-center">
              <h3 class="font-medium text-indigo-900 truncate">{{ curso.nome }}</h3>
              <div class="flex gap-1">
                <span 
                  :class="[formatarStatusMatricula(curso.status_matricula).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarStatusMatricula(curso.status_matricula).texto }}
                </span>
              </div>
            </div>
            <div class="p-4">
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ curso.descricao }}</p>
              
              <!-- Mostrar status do curso e modalidade -->
              <div class="flex flex-wrap gap-2 mb-3">
                <span 
                  v-if="curso.status"
                  :class="[formatarStatusCurso(curso.status).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarStatusCurso(curso.status).texto }}
                </span>
                <span 
                  v-if="curso.modalidade"
                  :class="[formatarModalidade(curso.modalidade).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarModalidade(curso.modalidade).texto }}
                </span>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>{{ formatarData(curso.data_inicio) }} a {{ formatarData(curso.data_fim) }}</span>
                </div>
                
                <div class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ curso.carga_horaria }} horas</span>
                </div>
                
                <div v-if="curso.localizacao" class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span>{{ curso.localizacao }}</span>
                </div>
              </div>
              
              <div class="flex justify-between pt-2 border-t border-gray-100">
                <button 
                  @click="verDetalhes(curso)"
                  class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Detalhes
                </button>
                
                <button 
                  @click="navegarParaDetalhes(curso.id)"
                  class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                  Ver Curso
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 3. Cursos Concluídos -->
      <div class="mb-10">
        <h2 class="text-xl font-semibold text-green-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Cursos Realizados
        </h2>
        
        <div v-if="cursosConcluidos.length === 0" class="bg-gray-50 rounded-lg p-6 text-center">
          <p class="text-gray-500">Você ainda não concluiu nenhum curso.</p>
        </div>
        
        <div v-else class="overflow-hidden shadow-sm border border-gray-200 rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modalidade</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carga Horária</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="curso in cursosConcluidos" :key="curso.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ curso.nome }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatarData(curso.data_inicio) }} a {{ formatarData(curso.data_fim) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="[formatarModalidade(curso.modalidade).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                  >
                    {{ formatarModalidade(curso.modalidade).texto }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ curso.carga_horaria }} horas</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-2">
                  <button 
                    @click="verDetalhes(curso)" 
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Detalhes
                  </button>
                  <button 
                    @click="emitirCertificado(curso.id)" 
                    class="text-green-600 hover:text-green-900"
                  >
                    Emitir Certificado
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 4. Matrículas Rejeitadas -->
      <div v-if="matriculasRejeitadas.length > 0" class="mb-10">
        <h2 class="text-xl font-semibold text-red-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Inscrições Não Aprovadas
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="curso in matriculasRejeitadas" 
            :key="curso.id"
            class="bg-white rounded-lg shadow border border-red-100 overflow-hidden hover:shadow-md transition-shadow duration-300"
          >
            <div class="bg-red-50 px-4 py-3 border-b border-red-100 flex justify-between items-center">
              <h3 class="font-medium text-red-900 truncate">{{ curso.nome }}</h3>
              <div class="flex gap-1">
                <span 
                  :class="[formatarStatusMatricula(curso.status_matricula).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarStatusMatricula(curso.status_matricula).texto }}
                </span>
              </div>
            </div>
            <div class="p-4">
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ curso.descricao }}</p>
              
              <!-- Mostrar status do curso e modalidade -->
              <div class="flex flex-wrap gap-2 mb-3">
                <span 
                  v-if="curso.status"
                  :class="[formatarStatusCurso(curso.status).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarStatusCurso(curso.status).texto }}
                </span>
                <span 
                  v-if="curso.modalidade"
                  :class="[formatarModalidade(curso.modalidade).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarModalidade(curso.modalidade).texto }}
                </span>
              </div>
              
              <!-- Motivo de rejeição (sempre presente nesta seção) -->
              <div class="mb-3 p-2 bg-red-50 border border-red-100 rounded-md">
                <p class="text-sm text-red-800">
                  <span class="font-medium">Motivo da não aprovação:</span> 
                  {{ curso.motivo_rejeicao || "Não especificado" }}
                </p>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>{{ formatarData(curso.data_inicio) }} a {{ formatarData(curso.data_fim) }}</span>
                </div>
                
                <div class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ curso.carga_horaria }} horas</span>
                </div>
                
                <div v-if="curso.data_inscricao" class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Inscrito em: {{ formatarData(curso.data_inscricao) }}</span>
                </div>
              </div>
              
              <div class="flex justify-between pt-2 border-t border-gray-100">
                <button 
                  @click="verDetalhes(curso)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Detalhes
                </button>
                
                <button 
                  @click="navegarParaDetalhes(curso.id)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                  Ver Curso
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal de detalhes do curso -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="fecharModal"
    >
      <div 
        class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ cursoSelecionado?.nome }}
          </h3>
          
          <button 
            @click="fecharModal"
            class="text-gray-400 hover:text-gray-500"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="p-6">
          <!-- Informações do curso -->
          <div class="mb-6">
            <h4 class="text-md font-medium text-gray-700 mb-2">Informações do Curso</h4>
            
            <div class="bg-gray-50 p-4 rounded-md space-y-3">
              <div>
                <span class="text-sm font-medium text-gray-500">Descrição:</span>
                <p class="text-sm text-gray-700 mt-1">{{ cursoSelecionado?.descricao }}</p>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <span class="text-sm font-medium text-gray-500">Período:</span>
                  <p class="text-sm text-gray-700">{{ formatarData(cursoSelecionado?.data_inicio) }} a {{ formatarData(cursoSelecionado?.data_fim) }}</p>
                </div>
                
                <div>
                  <span class="text-sm font-medium text-gray-500">Carga Horária:</span>
                  <p class="text-sm text-gray-700">{{ cursoSelecionado?.carga_horaria }} horas</p>
                </div>
                
                <div>
                  <span class="text-sm font-medium text-gray-500">Local:</span>
                  <p class="text-sm text-gray-700">{{ cursoSelecionado?.localizacao || 'Não informado' }}</p>
                </div>
                
                <div>
                  <span class="text-sm font-medium text-gray-500">Modalidade:</span>
                  <p class="text-sm text-gray-700">
                    <span 
                      v-if="cursoSelecionado?.modalidade"
                      :class="[formatarModalidade(cursoSelecionado.modalidade).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                    >
                      {{ formatarModalidade(cursoSelecionado.modalidade).texto }}
                    </span>
                    <span v-else>Não informada</span>
                  </p>
                </div>
                
                <div>
                  <span class="text-sm font-medium text-gray-500">Status do Curso:</span>
                  <p class="text-sm text-gray-700">
                    <span 
                      v-if="cursoSelecionado?.status"
                      :class="[formatarStatusCurso(cursoSelecionado.status).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                    >
                      {{ formatarStatusCurso(cursoSelecionado.status).texto }}
                    </span>
                    <span v-else>Não informado</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Status da matrícula -->
          <div class="mb-6">
            <h4 class="text-md font-medium text-gray-700 mb-2">Status da Matrícula</h4>
            
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="flex items-center mb-2">
                <span class="text-sm font-medium text-gray-500 mr-2">Situação:</span>
                <span 
                  :class="[formatarStatusMatricula(cursoSelecionado?.status_matricula).classe, 'px-2 py-1 text-xs rounded-full font-medium']"
                >
                  {{ formatarStatusMatricula(cursoSelecionado?.status_matricula).texto }}
                </span>
              </div>
              
              <!-- Data da inscrição -->
              <div v-if="cursoSelecionado?.data_inscricao" class="mb-2">
                <span class="text-sm font-medium text-gray-500">Data da Inscrição:</span>
                <p class="text-sm text-gray-700">{{ formatarData(cursoSelecionado?.data_inscricao) }}</p>
              </div>
              
              <!-- Motivo da rejeição, se houver -->
              <div v-if="temMotivoRejeicao" class="mt-3 p-3 bg-red-50 border border-red-100 rounded-md">
                <span class="text-sm font-medium text-red-800">Motivo da não aprovação:</span>
                <p class="text-sm text-red-700 mt-1">{{ cursoSelecionado?.motivo_rejeicao }}</p>
              </div>
            </div>
          </div>
          
          <!-- Botões de ação -->
          <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
            <button 
              v-if="cursoSelecionado?.status === 'concluído' && cursoSelecionado?.status_matricula === 'aprovada'"
              @click="emitirCertificado(cursoSelecionado?.id)"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
            >
              <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Emitir Certificado
              </span>
            </button>
            
            <button 
              @click="navegarParaDetalhes(cursoSelecionado?.id)"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
            >
              <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Ver Página do Curso
              </span>
            </button>
            
            <button 
              @click="fecharModal"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors"
            >
              Fechar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.historico-cursos {
  @apply py-6;
}

/* Animações para o modal */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { transform: translateY(-20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.fixed {
  animation: fadeIn 0.3s ease-out;
}

.fixed > div {
  animation: slideIn 0.3s ease-out;
}
</style>