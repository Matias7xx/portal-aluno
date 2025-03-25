<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const cursosRealizados = ref([]);
const cursosEmAndamento = ref([]);
const carregando = ref(false);

onMounted(() => {
  // Verificar se os dados foram passados via props
  if (page.props.cursosRealizados) {
    cursosRealizados.value = page.props.cursosRealizados;
  }
  
  if (page.props.cursosEmAndamento) {
    cursosEmAndamento.value = page.props.cursosEmAndamento;
  }
});

// Formatar data para exibição
const formatarData = (dataString) => {
  const data = new Date(dataString);
  return data.toLocaleDateString('pt-BR');
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
      <!-- Cursos em andamento -->
      <div class="mb-10">
        <h2 class="text-xl font-semibold text-blue-800 mb-4 flex items-center">
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
            class="bg-white rounded-lg shadow border border-blue-100 overflow-hidden"
          >
            <div class="bg-blue-50 px-4 py-3 border-b border-blue-100">
              <h3 class="font-medium text-blue-900">{{ curso.nome }}</h3>
            </div>
            <div class="p-4">
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ curso.descricao }}</p>
              
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
                
                <div class="flex items-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Status: 
                    <span class="font-medium text-yellow-600">{{ curso.status_matricula.charAt(0).toUpperCase() + curso.status_matricula.slice(1) }}</span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Cursos realizados -->
      <div>
        <h2 class="text-xl font-semibold text-green-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Cursos Realizados
        </h2>
        
        <div v-if="cursosRealizados.length === 0" class="bg-gray-50 rounded-lg p-6 text-center">
          <p class="text-gray-500">Você ainda não realizou nenhum curso.</p>
        </div>
        
        <div v-else class="overflow-hidden shadow-sm border border-gray-200 rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carga Horária</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="curso in cursosRealizados" :key="curso.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ curso.nome }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatarData(curso.data_inicio) }} a {{ formatarData(curso.data_fim) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ curso.carga_horaria }} horas</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Concluído
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <a href="#" class="text-blue-600 hover:text-blue-900">Emitir Certificado</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.historico-cursos {
  @apply py-6;
}
</style>