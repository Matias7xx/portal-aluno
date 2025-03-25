<script setup>
import { ref, onMounted } from 'vue';


const cursos = ref([]);
const mesesDisponiveis = ref([]);
const mesSelecionado = ref('');
const showModal = ref(false);
const cursoSelecionado = ref(null);
const carregando = ref(false);
const inscricaoStatus = ref(null);


const mockCursos = [
  {
    id: 1,
    nome: "Curso de Tiro Defensivo",
    descricao: "Técnicas avançadas de tiro defensivo para situações de risco",
    data_inicio: "2025-04-15",
    data_fim: "2025-04-20",
    carga_horaria: 40,
    localizacao: "Estande de Tiro da ACADEPOL",
    capacidade_maxima: 30,
    vagas_disponiveis: 12,
    modalidade: "presencial",
    pre_requisitos: ["Curso Básico de Armamento e Tiro"],
    enxoval: ["Coldre", "Cinto tático", "Protetor auricular", "Óculos de proteção"],
    status: "aberto",
    imagem: "/images/curso-tiro.jpg"
  },
  {
    id: 2,
    nome: "Investigação de Homicídios",
    descricao: "Métodos e técnicas para investigação de crimes contra a vida",
    data_inicio: "2025-04-22",
    data_fim: "2025-04-30",
    carga_horaria: 60,
    localizacao: "Sala de Treinamento 3 - ACADEPOL",
    capacidade_maxima: 25,
    vagas_disponiveis: 25,
    modalidade: "presencial",
    pre_requisitos: [],
    enxoval: ["Notebook (opcional)"],
    status: "aberto",
    imagem: "/images/curso-homicidios.jpg"
  },
  {
    id: 3,
    nome: "Análise de Dados para Inteligência Policial",
    descricao: "Ferramentas e métodos de análise de dados para inteligência",
    data_inicio: "2025-05-05",
    data_fim: "2025-05-15",
    carga_horaria: 80,
    localizacao: "Laboratório de Informática - ACADEPOL",
    capacidade_maxima: 20,
    vagas_disponiveis: 15,
    modalidade: "presencial",
    pre_requisitos: ["Noções Básicas de Informática"],
    enxoval: ["Notebook pessoal"],
    status: "aberto",
    imagem: "/images/curso-dados.jpg"
  },
  {
    id: 4,
    nome: "Táticas de Abordagem e Contenção",
    descricao: "Técnicas de abordagem segura e contenção de suspeitos",
    data_inicio: "2025-05-10",
    data_fim: "2025-05-12",
    carga_horaria: 24,
    localizacao: "Ginásio da ACADEPOL",
    capacidade_maxima: 40,
    vagas_disponiveis: 22,
    modalidade: "presencial",
    pre_requisitos: [],
    enxoval: ["Uniforme tático", "Coturno"],
    status: "aberto",
    imagem: "/images/curso-taticas.jpg"
  },
  {
    id: 5,
    nome: "Legislação Penal Atualizada",
    descricao: "Atualizações na legislação penal e processual penal",
    data_inicio: "2025-05-20",
    data_fim: "2025-05-25",
    carga_horaria: 30,
    localizacao: "Auditório Principal - ACADEPOL",
    capacidade_maxima: 50,
    vagas_disponiveis: 50,
    modalidade: "presencial",
    pre_requisitos: [],
    enxoval: [],
    status: "aberto",
    imagem: "/images/curso-legislacao.jpg"
  },
  {
    id: 6,
    nome: "Perícia Criminal",
    descricao: "Fundamentos de perícia criminal para investigadores",
    data_inicio: "2025-06-05",
    data_fim: "2025-06-15",
    carga_horaria: 80,
    localizacao: "Laboratório Forense - ACADEPOL",
    capacidade_maxima: 15,
    vagas_disponiveis: 10,
    modalidade: "presencial",
    pre_requisitos: [],
    enxoval: ["Jaleco", "Luvas descartáveis"],
    status: "aberto",
    imagem: "/images/curso-pericia.jpg"
  }
];

// Função para carregar os cursos com inscrições abertas
const carregarCursos = async () => {
  try {
    carregando.value = true;
    
    
    
    // Usando dados mockados por enquanto
    setTimeout(() => {
      cursos.value = mockCursos;
      
      // Extrair os meses disponíveis
      const meses = new Set();
      cursos.value.forEach(curso => {
        const data = new Date(curso.data_inicio);
        const mesAno = `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}`;
        meses.add(mesAno);
      });
      
      mesesDisponiveis.value = Array.from(meses).sort();
      
      // Seleciona o primeiro mês por padrão
      if (mesesDisponiveis.value.length > 0 && !mesSelecionado.value) {
        mesSelecionado.value = mesesDisponiveis.value[0];
      }
      
      carregando.value = false;
    }, 600); // Simulando delay da rede
    
  } catch (error) {
    console.error('Erro ao carregar cursos:', error);
    carregando.value = false;
  }
};

// Filtra cursos pelo mês selecionado
const cursosFiltrados = () => {
  if (!mesSelecionado.value) return cursos.value;
  
  return cursos.value.filter(curso => {
    const data = new Date(curso.data_inicio);
    const mesAno = `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}`;
    return mesAno === mesSelecionado.value;
  });
};

// Formatar data para exibição
const formatarData = (dataString) => {
  const data = new Date(dataString);
  return data.toLocaleDateString('pt-BR');
};

// Abre o modal com detalhes do curso
const abrirDetalhesCurso = (curso) => {
  cursoSelecionado.value = curso;
  showModal.value = true;
};

// Fecha o modal
const fecharModal = () => {
  showModal.value = false;
  setTimeout(() => {
    cursoSelecionado.value = null;
    inscricaoStatus.value = null;
  }, 300);
};

// Solicitar inscrição no curso
const solicitarInscricao = () => {
  
  
  // Simulando resposta da API
  inscricaoStatus.value = {
    sucesso: true,
    mensagem: "Solicitação de inscrição enviada com sucesso! Aguarde a aprovação."
  };
  
  // Em caso de erro seria algo como:
  // inscricaoStatus.value = {
  //   sucesso: false,
  //   mensagem: "Erro ao solicitar inscrição. Tente novamente mais tarde."
  // };
};

// Formatar nome do mês para exibição
const formatarNomeMes = (mesAno) => {
  const [ano, mes] = mesAno.split('-');
  const data = new Date(ano, parseInt(mes) - 1, 1);
  return data.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
};

// Carregar cursos quando o componente for montado
onMounted(() => {
  carregarCursos();
});
</script>

<template>
  <div class="calendario-cursos">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">Calendário de Cursos</h1>
      <p class="text-gray-600">Confira os cursos disponíveis para inscrição</p>
    </div>
    
    <!-- Filtro por mês -->
    <div class="flex flex-wrap gap-2 mb-6">
      <button 
        v-for="mes in mesesDisponiveis" 
        :key="mes"
        :class="[
          'px-4 py-2 rounded-md text-sm font-medium transition-colors',
          mesSelecionado === mes 
            ? 'bg-blue-600 text-white' 
            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
        ]"
        @click="mesSelecionado = mes"
      >
        {{ formatarNomeMes(mes) }}
      </button>
    </div>
    
    <!-- Estado de carregamento -->
    <div v-if="carregando" class="flex justify-center my-12">
      <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <!-- Lista de cursos -->
    <div v-else-if="cursosFiltrados().length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="curso in cursosFiltrados()" 
        :key="curso.id" 
        class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300"
      >
        <!-- Imagem do curso com fallback para uma cor sólida -->
        <div class="h-40 bg-gray-300 relative">
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end">
            <div class="p-4 text-white">
              <span class="inline-block px-2 py-1 text-xs font-semibold rounded-md" 
                :class="curso.vagas_disponiveis > 0 ? 'bg-green-500' : 'bg-red-500'">
                {{ curso.vagas_disponiveis > 0 ? `${curso.vagas_disponiveis} vagas` : 'Sem vagas' }}
              </span>
            </div>
          </div>
        </div>
        
        <div class="p-4">
          <h2 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ curso.nome }}</h2>
          
          <div class="mb-3 flex items-center text-sm text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ formatarData(curso.data_inicio) }} a {{ formatarData(curso.data_fim) }}
          </div>
          
          <div class="mb-3 flex items-center text-sm text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ curso.carga_horaria }}h
          </div>
          
          <div class="mb-4 flex items-center text-sm text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ curso.localizacao }}
          </div>
          
          <button 
            @click="abrirDetalhesCurso(curso)"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-300 flex items-center justify-center"
            :disabled="curso.vagas_disponiveis <= 0"
            :class="{ 'opacity-50 cursor-not-allowed': curso.vagas_disponiveis <= 0 }"
          >
            <span>Ver Detalhes</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Estado vazio -->
    <div v-else class="bg-gray-50 rounded-lg p-8 text-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhum curso disponível</h3>
      <p class="text-gray-500">Não há cursos disponíveis para o período selecionado.</p>
    </div>
    
    <!-- Modal de detalhes do curso -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="fecharModal"></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <div v-if="cursoSelecionado" class="bg-white">
            <!-- Cabeçalho -->
            <div class="px-6 pt-6 pb-4">
              <div class="flex justify-between">
                <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                  {{ cursoSelecionado.nome }}
                </h3>
                <button @click="fecharModal" class="text-gray-400 hover:text-gray-500">
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- Corpo -->
            <div class="px-6 py-4">
              <div class="text-gray-700 mb-4">
                {{ cursoSelecionado.descricao }}
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <h4 class="font-medium text-gray-700 mb-2">Informações Gerais</h4>
                  <ul class="space-y-2">
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span>Período: {{ formatarData(cursoSelecionado.data_inicio) }} a {{ formatarData(cursoSelecionado.data_fim) }}</span>
                    </li>
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>Carga horária: {{ cursoSelecionado.carga_horaria }} horas</span>
                    </li>
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span>Local: {{ cursoSelecionado.localizacao }}</span>
                    </li>
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      <span>Vagas: {{ cursoSelecionado.vagas_disponiveis }} / {{ cursoSelecionado.capacidade_maxima }}</span>
                    </li>
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                      </svg>
                      <span>Modalidade: {{ cursoSelecionado.modalidade.charAt(0).toUpperCase() + cursoSelecionado.modalidade.slice(1) }}</span>
                    </li>
                  </ul>
                </div>
                
                <div>
                  <h4 class="font-medium text-gray-700 mb-2">Requisitos e Materiais</h4>
                  <div v-if="cursoSelecionado.pre_requisitos && cursoSelecionado.pre_requisitos.length" class="mb-3">
                    <h5 class="text-sm font-medium text-gray-600">Pré-requisitos:</h5>
                    <ul class="list-disc list-inside text-sm pl-2 text-gray-700">
                      <li v-for="(requisito, index) in cursoSelecionado.pre_requisitos" :key="index">
                        {{ requisito }}
                      </li>
                    </ul>
                  </div>
                  <div v-else class="mb-3">
                    <h5 class="text-sm font-medium text-gray-600">Pré-requisitos:</h5>
                    <p class="text-sm text-gray-700">Não há pré-requisitos específicos</p>
                  </div>
                  
                  <div v-if="cursoSelecionado.enxoval && cursoSelecionado.enxoval.length">
                    <h5 class="text-sm font-medium text-gray-600">Materiais necessários:</h5>
                    <ul class="list-disc list-inside text-sm pl-2 text-gray-700">
                      <li v-for="(item, index) in cursoSelecionado.enxoval" :key="index">
                        {{ item }}
                      </li>
                    </ul>
                  </div>
                  <div v-else>
                    <h5 class="text-sm font-medium text-gray-600">Materiais necessários:</h5>
                    <p class="text-sm text-gray-700">Não há materiais específicos necessários</p>
                  </div>
                </div>
              </div>
              
              <!-- Feedback de inscrição -->
              <div v-if="inscricaoStatus" class="mb-6">
                <div 
                  class="p-4 rounded-md" 
                  :class="inscricaoStatus.sucesso ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'"
                >
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <!-- Ícone de sucesso -->
                      <svg v-if="inscricaoStatus.sucesso" class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                      <!-- Ícone de erro -->
                      <svg v-else class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium">
                        {{ inscricaoStatus.mensagem }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Ações -->
              <div class="flex justify-between">
                <button 
                  @click="fecharModal" 
                  class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors"
                >
                  Voltar
                </button>
                
                <button 
                  v-if="!inscricaoStatus"
                  @click="solicitarInscricao" 
                  class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors"
                  :disabled="cursoSelecionado.vagas_disponiveis <= 0"
                  :class="{ 'opacity-50 cursor-not-allowed': cursoSelecionado.vagas_disponiveis <= 0 }"
                >
                  Solicitar Inscrição
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.calendario-cursos {
  @apply py-6;
}
</style>