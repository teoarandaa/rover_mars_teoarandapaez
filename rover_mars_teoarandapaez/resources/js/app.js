import './bootstrap';
import { createApp } from 'vue';
import RoverControl from './components/RoverControl.vue';

// Inicializar Vue cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  const app = createApp(RoverControl);
  app.mount('#app');
}); 