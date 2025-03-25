<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const cursos = ref([]);
const mesesDisponiveis = ref([]);
const mesSelecionado = ref('');
const carregando = ref(false);

// Receber os dados dos cursos das props
onMounted(() => {
  // Verificar se os cursos foram passados via props
  if (page.props.cursos) {
    cursos.value = page.props.cursos;
    
    // Verificar se os meses disponíveis foram passados via props
    if (page.props.meses) {
      mesesDisponiveis.value = page.props.meses;
    } else {
      // Extrair os meses disponíveis dos cursos
      const meses = new Set();
      cursos.value.forEach(curso => {
        const data = new Date(curso.data_inicio);
        const mesAno = `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}`;
        meses.add(mesAno);
      });
      
      mesesDisponiveis.value = Array.from(meses).sort();
    }
    
    // Seleciona o primeiro mês por padrão
    if (mesesDisponiveis.value.length > 0 && !mesSelecionado.value) {
      mesSelecionado.value = mesesDisponiveis.value[0];
    }
  }
});

// Filtra cursos pelo mês selecionado
const cursosFiltrados = computed(() => {
  if (!mesSelecionado.value) return cursos.value;
  
  return cursos.value.filter(curso => {
    const data = new Date(curso.data_inicio);
    const mesAno = `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}`;
    return mesAno === mesSelecionado.value;
  });
});

// Formatar data para exibição
const formatarData = (dataString) => {
  const data = new Date(dataString);
  return data.toLocaleDateString('pt-BR');
};

// Redireciona para a página de detalhes do curso
const irParaDetalhesCurso = (curso) => {
  window.location.href = route('servidor.curso.detalhes', curso.id);
};

// Formatar nome do mês para exibição
const formatarNomeMes = (mesAno) => {
  const [ano, mes] = mesAno.split('-');
  const data = new Date(ano, parseInt(mes) - 1, 1);
  return data.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
};
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
    <div v-else-if="cursosFiltrados.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="curso in cursosFiltrados" 
        :key="curso.id" 
        class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300"
      >
        <!-- Imagem do curso com fallback para uma cor sólida -->
        <div class="h-40 bg-gray-300 relative">
          <div v-if="curso.imagem" class="absolute inset-0">
            <img :src="curso.imagem" alt="Imagem do curso" class="w-full h-full object-cover">
          </div>
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
            @click="irParaDetalhesCurso(curso)"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-300 flex items-center justify-center"
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
  </div>
</template>

<style scoped>
.calendario-cursos {
  @apply py-6;
}
</style>