<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Components/Sidebar.vue';
import BemVindo from './Components/BemVindo.vue';

// Importar todos os componentes possíveis
import CalendarioCursos from './Components/CalendarioCursos.vue';

const page = usePage();

// Obtém o componente atual a partir da propriedade da rota
const componenteAtual = computed(() => {
  const route = page.props.route || 'home';
  
  // Mapeamento de rotas para componentes
  const componentesMap = {
    'home': null, // Página inicial vazia ou dashboard geral
    'calendario': CalendarioCursos,
    // Adicione outros componentes conforme necessário
    // 'historico': HistoricoCursos,
    // 'sugeridos': CursosSugeridos,
    // etc...
  };
  
  return componentesMap[route];
});

// Título da página baseado na rota atual
const pageTitle = computed(() => {
  const titulos = {
    'home': 'Portal do Aluno SERVIDOR',
    'calendario': 'Calendário de Cursos',
    // Adicione outros títulos
    // 'historico': 'Histórico de Cursos',
    // 'sugeridos': 'Cursos Sugeridos',
    // etc...
  };
  
  return titulos[page.props.route || 'home'];
});
</script>

<template>
  <Head :title="pageTitle" />

  <AuthenticatedLayout>
    <div class="flex flex-col md:flex-row">
      <!-- Sidebar à esquerda -->
      <Sidebar />
      
      <!-- Conteúdo principal à direita -->
      <main class="flex-1 md:ml-64 bg-gray-100">
        <div class="p-6">
          <!-- Renderiza o componente dinâmico baseado na rota atual -->
          <component v-if="componenteAtual" :is="componenteAtual" />
          
          <!-- Conteúdo padrão para a página inicial -->
          <div v-else class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <BemVindo />
            
          </div>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>