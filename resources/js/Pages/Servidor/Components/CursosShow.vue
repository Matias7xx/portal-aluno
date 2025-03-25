<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Receber props da página
const page = usePage();
const props = defineProps({
  curso: {
    type: Object,
    required: true
  }
});

// Estado local
const loading = ref(false);

// Formatador de data
const formatarData = (dataString) => {
  const data = new Date(dataString);
  return data.toLocaleDateString('pt-BR');
};

// Verificar se o usuário já está inscrito
const usuarioInscrito = computed(() => {
  return props.curso.usuario_inscrito;
});

// Verificar se há vagas disponíveis
const temVagas = computed(() => {
  return props.curso.vagas_disponiveis > 0;
});

// Verificar se o curso está aberto para inscrições
const inscricoesAbertas = computed(() => {
  return props.curso.status === 'aberto';
});

// Próximos passos: inscrição ou informa que já está inscrito
const proximoPasso = computed(() => {
  if (usuarioInscrito.value) {
    return 'inscrito';
  } else if (!temVagas.value) {
    return 'sem_vagas';
  } else if (!inscricoesAbertas.value) {
    return 'fechado';
  } else {
    return 'disponivel';
  }
});

// Redirecionamento para inscrição
const irParaInscricao = () => {
  loading.value = true;
  window.location.href = route('servidor.curso.formulario', props.curso.id);
};

// Para compartilhamento nas redes sociais
const shareUrl = window.location.href;
const shareTitle = `Curso de ${props.curso.nome} - ACADEPOL`;

// Funções de compartilhamento
const compartilharTwitter = () => {
  window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareTitle)}`, '_blank');
};

const compartilharFacebook = () => {
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank');
};

const compartilharWhatsApp = () => {
  window.open(`https://wa.me/?text=${encodeURIComponent(shareTitle + ' ' + shareUrl)}`, '_blank');
};

const copiarLink = () => {
  navigator.clipboard.writeText(shareUrl).then(() => {
    alert('Link copiado para a área de transferência');
  });
};
</script>

<template>
  <div class="curso-show">
    <!-- Hero image com informações do curso -->
    <div class="relative h-72 bg-gray-900 overflow-hidden">
      <!-- Imagem de fundo com overlay escuro -->
      <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>
      <img 
        v-if="curso.imagem" 
        :src="curso.imagem" 
        alt="Imagem do curso" 
        class="absolute inset-0 h-full w-full object-cover"
      />
      
      <!-- Conteúdo sobreposto à imagem -->
      <div class="relative z-20 container mx-auto px-4 h-full flex flex-col justify-end pb-8">
        <div class="flex flex-wrap items-center mb-2 gap-2">
          <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
            {{ curso.modalidade }}
          </span>
          <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
            {{ curso.status }}
          </span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ curso.nome }}</h1>
      </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="container mx-auto px-4 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Coluna principal -->
        <div class="lg:col-span-2">
          <!-- Sobre o curso -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Sobre o curso</h2>
            <div class="text-gray-600 prose max-w-none">
              <p>{{ curso.descricao }}</p>
            </div>
            
            <!-- Informações adicionais do curso em grid -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Carga Horária -->
              <div class="flex items-start">
                <div class="bg-yellow-100 p-3 rounded-lg mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Carga Horária</h3>
                  <p class="text-gray-600">{{ curso.carga_horaria }} horas</p>
                </div>
              </div>
              
              <!-- Período -->
              <div class="flex items-start">
                <div class="bg-blue-100 p-3 rounded-lg mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Período</h3>
                  <p class="text-gray-600">{{ formatarData(curso.data_inicio) }} a {{ formatarData(curso.data_fim) }}</p>
                </div>
              </div>
              
              <!-- Localização -->
              <div class="flex items-start">
                <div class="bg-green-100 p-3 rounded-lg mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Localização</h3>
                  <p class="text-gray-600">{{ curso.localizacao }}</p>
                </div>
              </div>
              
              <!-- Capacidade -->
              <div class="flex items-start">
                <div class="bg-purple-100 p-3 rounded-lg mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Capacidade</h3>
                  <p class="text-gray-600">{{ curso.capacidade_maxima }} participantes</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Pré-requisitos do curso -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Pré-requisitos</h2>
            <ul v-if="curso.pre_requisitos && curso.pre_requisitos.length > 0" class="list-disc pl-6 space-y-2 text-gray-600">
              <li v-for="(requisito, index) in curso.pre_requisitos" :key="index">
                {{ requisito }}
              </li>
            </ul>
            <p v-else class="text-gray-600">Não há pré-requisitos específicos para este curso.</p>
          </div>
          
          <!-- Material necessário -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Material necessário</h2>
            <ul v-if="curso.enxoval && curso.enxoval.length > 0" class="list-disc pl-6 space-y-2 text-gray-600">
              <li v-for="(item, index) in curso.enxoval" :key="index">
                {{ item }}
              </li>
            </ul>
            <p v-else class="text-gray-600">Não há materiais específicos necessários para este curso.</p>
          </div>
        </div>
        
        <!-- Barra lateral -->
        <div class="lg:col-span-1">
          <!-- Card de inscrição -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Inscrições</h2>
            
            <div class="space-y-4">
              <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <span class="font-medium text-gray-600">Status:</span>
                <span class="px-3 py-1 rounded-full text-sm font-medium"
                  :class="curso.status === 'aberto' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                >
                  {{ curso.status === 'aberto' ? 'aberto' : 'fechado' }}
                </span>
              </div>
              
              <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <span class="font-medium text-gray-600">Vagas:</span>
                <span class="font-medium text-gray-800">{{ curso.vagas_disponiveis }}</span>
              </div>
              
              <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <span class="font-medium text-gray-600">Período de matrícula:</span>
                <span class="font-medium text-gray-800">Até {{ formatarData(curso.data_inicio) }}</span>
              </div>
              
              <!-- Botão de inscrição baseado no estado -->
              <div v-if="proximoPasso === 'disponivel'" class="pt-3">
                <button 
                  @click="irParaInscricao" 
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                  :disabled="loading"
                >
                  <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ loading ? 'Redirecionando...' : 'Inscrever-se' }}</span>
                </button>
              </div>
              
              <div v-else-if="proximoPasso === 'inscrito'" class="pt-3">
                <div class="w-full bg-green-100 text-green-800 font-medium py-3 px-4 rounded-lg text-center">
                  Você já está inscrito neste curso
                </div>
              </div>
              
              <div v-else-if="proximoPasso === 'sem_vagas'" class="pt-3">
                <div class="w-full bg-red-100 text-red-800 font-medium py-3 px-4 rounded-lg text-center">
                  Não há vagas disponíveis
                </div>
              </div>
              
              <div v-else-if="proximoPasso === 'fechado'" class="pt-3">
                <div class="w-full bg-gray-100 text-gray-800 font-medium py-3 px-4 rounded-lg text-center">
                  Inscrições encerradas
                </div>
              </div>
            </div>
          </div>
          
          <!-- Card de compartilhamento -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Compartilhe</h2>
            
            <div class="flex justify-between">
              <!-- Twitter -->
              <button 
                @click="compartilharTwitter"
                class="bg-gray-100 hover:bg-gray-200 p-3 rounded-full transition duration-200"
                title="Compartilhar no Twitter"
              >
                <svg class="h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                </svg>
              </button>
              
              <!-- Facebook -->
              <button 
                @click="compartilharFacebook"
                class="bg-gray-100 hover:bg-gray-200 p-3 rounded-full transition duration-200"
                title="Compartilhar no Facebook"
              >
                <svg class="h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02z" />
                </svg>
              </button>
              
              <!-- WhatsApp -->
              <button 
                @click="compartilharWhatsApp"
                class="bg-gray-100 hover:bg-gray-200 p-3 rounded-full transition duration-200"
                title="Compartilhar via WhatsApp"
              >
                <svg class="h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
              </button>
              
              <!-- Copiar link -->
              <button 
                @click="copiarLink"
                class="bg-gray-100 hover:bg-gray-200 p-3 rounded-full transition duration-200"
                title="Copiar link"
              >
                <svg class="h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Botão de voltar -->
      <div class="mt-8">
        <a 
          :href="route('servidor.calendario')" 
          class="inline-flex items-center text-gray-600 hover:text-blue-600"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          Voltar para o calendário de cursos
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
.curso-show h2 {
  font-family: 'Inter', sans-serif;
}

/* Efeito de transição suave nos botões */
button {
  transition: all 0.2s ease-in-out;
}
</style>