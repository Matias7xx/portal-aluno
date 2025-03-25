<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const isOpen = ref(false);
const page = usePage();

// Determina a role do usuário atual
const userRole = computed(() => {
  const user = page.props.auth.user;
  // Verifica se o usuário tem a propriedade roles e se é um array
  return user && user.roles && Array.isArray(user.roles) ? user.roles[0]?.name : null;
});

// Determina se o usuário é servidor ou aluno
const isServidor = computed(() => userRole.value === 'servidor');
const isAluno = computed(() => userRole.value === 'aluno');

// Itens de menu com estado expansível
const menuItems = ref([]);

// Configura os itens de menu com base na role
const setupMenuItems = () => {
  if (isServidor.value) {
    // Menu para alunos de capacitação (servidores)
    menuItems.value = [
      { 
        name: 'Início', 
        icon: 'home', 
        route: 'servidor.home',
        isOpen: false
      },
      { 
        name: 'Legislação', 
        icon: 'book', 
        isOpen: false,
        submenu: [
          { name: 'Lei Orgânica 12455/2022', route: '' },
          { name: 'Lei Complementar 85/08', route: '' },
          { name: 'Portaria 677/22 DEGEPOL', route: '' }
        ]
      },
      { 
        name: 'Capacitação', 
        icon: 'graduation-cap',
        isOpen: false,
        submenu: [
          { name: 'Plano de Capacitação Anual', route: '' },
          { name: 'Calendário de Cursos', route: 'servidor.calendario' },
          { name: 'Meus Cursos Realizados', route: 'servidor.historico' },
          { name: 'Cursos Sugeridos', route: '' },
          { name: 'Trilha de Conhecimento', route: '' }
        ]
      },
      { 
        name: 'Biblioteca Virtual', 
        icon: 'book-open', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Materiais Didáticos', 
        icon: 'file-text', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Reserva de Alojamento', 
        icon: 'home', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Certificados', 
        icon: 'award', 
        route: '',
        isOpen: false
      }
    ];
  } else if (isAluno.value) {
    // Menu para alunos de formação
    menuItems.value = [
      { 
        name: 'Início', 
        icon: 'home', 
        route: 'aluno.home',
        isOpen: false
      },
      { 
        name: 'Documentos Oficiais', 
        icon: 'file-text',
        isOpen: false, 
        submenu: [
          { name: 'Regime Jurídico', route: '' },
          { name: 'Edital do Concurso', route: '' },
          { name: 'Regimento Interno', route: '' },
          { name: 'Manual do Aluno', route: '' }
        ]
      },
      { 
        name: 'Calendário de Aulas', 
        icon: 'calendar', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Requerimentos', 
        icon: 'file', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Comunicações Oficiais', 
        icon: 'message-square', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Avaliação de Docentes', 
        icon: 'star', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Biblioteca Virtual', 
        icon: 'book-open', 
        route: '',
        isOpen: false
      },
      { 
        name: 'Materiais Didáticos', 
        icon: 'file-text', 
        route: '',
        isOpen: false
      }
    ];
  } else {
    // Menu padrão em caso de nenhuma role reconhecida
    menuItems.value = [
      { name: 'Início', icon: 'home', route: 'dashboard', isOpen: false }
    ];
  }
};

// Atualizar menu quando a role mudar
watch([isServidor, isAluno], () => {
  setupMenuItems();
});

// Inicializar menu na montagem do componente
onMounted(() => {
  setupMenuItems();
});

const toggleMenu = () => {
  isOpen.value = !isOpen.value;
};

const toggleSubmenu = (index) => {
  menuItems.value[index].isOpen = !menuItems.value[index].isOpen;
};
</script>

<template>
  <div class="sidebar-container">
    <!-- Botão para mobile -->
    <button @click="toggleMenu" class="md:hidden fixed top-24 left-4 z-40 bg-blue-600 text-white p-2 rounded-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
      </svg>
    </button>

    <!-- Sidebar para desktop e mobile (quando aberta) -->
    <aside class="fixed top-20 left-0 z-30 h-screen w-64 transition-transform transform bg-gray-700 text-white"
      :class="{ '-translate-x-full': !isOpen, 'translate-x-0': isOpen, 'md:translate-x-0': true }">
      
      <!-- Banner do tipo de usuário -->
      <div class="p-4 bg-blue-700 font-bold text-lg">
        <template v-if="isServidor">
          Portal do Aluno de Capacitação
        </template>
        <template v-else-if="isAluno">
          Portal do Aluno de Formação
        </template>
        <template v-else>
          Portal do Aluno
        </template>
      </div>

      <nav class="mt-4">
        <ul class="space-y-1">
          <li v-for="(item, index) in menuItems" :key="index" class="px-2">
            <!-- Item com submenu -->
            <div v-if="item.submenu" class="mb-2">
              <!-- Cabeçalho do submenu -->
              <div @click="toggleSubmenu(index)" 
                  class="flex items-center justify-between p-2 cursor-pointer hover:bg-gray-700 rounded-md">
                <div class="flex items-center">
                  <!-- Ícone do item -->
                  <svg v-if="item.icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" 
                      viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                      stroke-linecap="round" stroke-linejoin="round">
                    <template v-if="item.icon === 'home'">
                      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                      <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </template>
                    <template v-else-if="item.icon === 'book'">
                      <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                      <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </template>
                    <template v-else-if="item.icon === 'graduation-cap'">
                      <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                      <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path>
                    </template>
                    <template v-else-if="item.icon === 'book-open'">
                      <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                      <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </template>
                    <template v-else-if="item.icon === 'file-text'">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </template>
                    <template v-else-if="item.icon === 'calendar'">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </template>
                    <template v-else-if="item.icon === 'file'">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                    </template>
                    <template v-else-if="item.icon === 'message-square'">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </template>
                    <template v-else-if="item.icon === 'star'">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </template>
                    <template v-else-if="item.icon === 'award'">
                      <circle cx="12" cy="8" r="7"></circle>
                      <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </template>
                    <template v-else>
                      <circle cx="12" cy="12" r="10"></circle>
                    </template>
                  </svg>
                  {{ item.name }}
                </div>
                <!-- Ícone de expansão -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" 
                    :class="{ 'rotate-180': item.isOpen }" fill="none" viewBox="0 0 24 24" 
                    stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
              
              <!-- Itens do submenu -->
              <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
              >
                <ul v-show="item.isOpen" class="pl-4 mt-1 space-y-1">
                  <li v-for="subItem in item.submenu" :key="subItem.name">
                    <a :href="subItem.route ? (route(subItem.route) || '#') : '#'" 
                       class="block p-2 text-sm hover:bg-gray-700 rounded-md">
                      {{ subItem.name }}
                    </a>
                  </li>
                </ul>
              </transition>
            </div>
            
            <!-- Item sem submenu -->
            <a v-else :href="item.route ? (route(item.route) || '#') : '#'" 
               class="flex items-center p-2 hover:bg-gray-700 rounded-md">
              <!-- Ícone do item -->
              <svg v-if="item.icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" 
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                  stroke-linecap="round" stroke-linejoin="round">
                <template v-if="item.icon === 'home'">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </template>
                <template v-else-if="item.icon === 'book'">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </template>
                <template v-else-if="item.icon === 'graduation-cap'">
                  <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                  <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path>
                </template>
                <template v-else-if="item.icon === 'book-open'">
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </template>
                <template v-else-if="item.icon === 'file-text'">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </template>
                <template v-else-if="item.icon === 'calendar'">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </template>
                <template v-else-if="item.icon === 'file'">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                </template>
                <template v-else-if="item.icon === 'message-square'">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </template>
                <template v-else-if="item.icon === 'star'">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </template>
                <template v-else-if="item.icon === 'award'">
                  <circle cx="12" cy="8" r="7"></circle>
                  <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </template>
                <template v-else>
                  <circle cx="12" cy="12" r="10"></circle>
                </template>
              </svg>
              {{ item.name }}
            </a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Overlay para fechar o menu no mobile -->
    <div v-if="isOpen" @click="toggleMenu" class="fixed inset-0 bg-black opacity-50 z-20 md:hidden"></div>
  </div>
</template>

<style scoped>
.sidebar-container {
  min-height: calc(100vh - 5rem);
}
</style>