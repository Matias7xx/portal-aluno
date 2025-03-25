<template> 
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">
              Bem-vindo ao Portal do Aluno de Capacitação
            </h1>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              Utilize o menu lateral para acessar os recursos disponíveis.
            </p>
            
            <!-- adicionar cards de resumo, avisos, notificações, etc. -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
              <!-- Card de Cursos -->
              <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-6 shadow-sm border border-blue-100 dark:border-blue-800">
                <div class="flex items-center mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-300">Cursos Disponíveis</h2>
                </div>
                <p class="text-blue-700 dark:text-blue-200 mb-4">Veja os cursos disponíveis para inscrição.</p>
                <a href="/servidor/calendario" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline">
                  Ver calendário
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </div>
              
              <!-- Card de Histórico -->
              <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-6 shadow-sm border border-green-100 dark:border-green-800">
                <div class="flex items-center mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                  </svg>
                  <h2 class="text-lg font-semibold text-green-800 dark:text-green-300">Histórico de Cursos</h2>
                </div>
                <p class="text-green-700 dark:text-green-200 mb-4">Acesse seu histórico de cursos realizados.</p>
                <a href="#" class="inline-flex items-center text-green-600 dark:text-green-400 hover:underline">
                  Ver histórico
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </div>
              
              <!-- Card de Certificados -->
              <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-6 shadow-sm border border-purple-100 dark:border-purple-800">
                <div class="flex items-center mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 dark:text-purple-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                  </svg>
                  <h2 class="text-lg font-semibold text-purple-800 dark:text-purple-300">Certificados</h2>
                </div>
                <p class="text-purple-700 dark:text-purple-200 mb-4">Emita seus certificados de capacitação.</p>
                <a href="#" class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:underline">
                  Ver certificados
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </div>
            </div>
</template>