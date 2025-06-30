<template>
  <div class="container">
    <h2 class="mb-4 text-center">Controla el Rover en Marte 🚀</h2>
    
    <div class="row">
      <div class="col-md-6">
        <form @submit.prevent="executeCommands">
          <div class="mb-3">
            <label for="x" class="form-label">Posición X (0-199)</label>
            <input 
              type="number" 
              class="form-control" 
              :class="{ 'is-invalid': errors.x }"
              id="x" 
              v-model.number="form.x" 
              min="0" 
              max="199" 
              @input="validateX"
              required
            >
            <div class="invalid-feedback" v-if="errors.x">
              {{ errors.x }}
            </div>
          </div>
          <div class="mb-3">
            <label for="y" class="form-label">Posición Y (0-199)</label>
            <input 
              type="number" 
              class="form-control" 
              :class="{ 'is-invalid': errors.y }"
              id="y" 
              v-model.number="form.y" 
              min="0" 
              max="199" 
              @input="validateY"
              required
            >
            <div class="invalid-feedback" v-if="errors.y">
              {{ errors.y }}
            </div>
          </div>
          <div class="mb-3">
            <label for="direction" class="form-label">Dirección inicial</label>
            <select 
              class="form-select" 
              :class="{ 'is-invalid': errors.direction }"
              id="direction" 
              v-model="form.direction" 
              @change="validateDirection"
              required
            >
              <option value="N">Norte (N)</option>
              <option value="E">Este (E)</option>
              <option value="S">Sur (S)</option>
              <option value="W">Oeste (W)</option>
            </select>
            <div class="invalid-feedback" v-if="errors.direction">
              {{ errors.direction }}
            </div>
          </div>
          <div class="mb-3">
            <label for="commands" class="form-label">Comandos (F, L, R)</label>
            <input 
              type="text" 
              class="form-control" 
              :class="{ 'is-invalid': errors.commands }"
              id="commands" 
              v-model="form.commands" 
              @input="validateCommands"
              maxlength="100"
              required
            >
            <div class="form-text">Ejemplo: FFRFFFRL</div>
            <div class="invalid-feedback" v-if="errors.commands">
              {{ errors.commands }}
            </div>
          </div>
          <div class="mb-3">
            <label for="obstacles" class="form-label">Obstáculos (opcional, formato: x1,y1;x2,y2)</label>
            <input 
              type="text" 
              class="form-control" 
              :class="{ 'is-invalid': errors.obstacles }"
              id="obstacles" 
              v-model="form.obstacles" 
              @input="validateObstacles"
              maxlength="200"
              placeholder="0,2;2,3"
            >
            <div class="form-text">Ejemplo: 0,2;2,3</div>
            <div class="invalid-feedback" v-if="errors.obstacles">
              {{ errors.obstacles }}
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100" :disabled="loading || hasErrors">
            {{ loading ? 'Enviando...' : 'Enviar comandos' }}
          </button>
        </form>
        <div class="result" v-if="result">
          <div :class="resultClass">
            <strong>{{ resultTitle }}</strong>
            <pre>{{ JSON.stringify(result, null, 2) }}</pre>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="history-section">
          <h4>📊 {{ movements.length > 0 ? `Últimos ${movements.length} registros` : 'Historial de Movimientos' }}</h4>
          <div class="mb-3">
            <button 
              type="button" 
              class="btn btn-outline-secondary" 
              @click="loadHistory"
              :disabled="loadingHistory"
            >
              {{ loadingHistory ? '🔄 Cargando...' : '🔄 Cargar Historial' }}
            </button>
          </div>
          <div v-if="historyResult" class="history-container">
            <div v-if="movements.length > 0" class="table-responsive history-scroll">
              <table class="table table-sm table-striped history-table mb-0">
                <thead class="sticky-header">
                  <tr>
                    <th>Fecha</th>
                    <th>Inicio</th>
                    <th>Comandos</th>
                    <th>Resultado</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="movement in movements" :key="movement.id">
                    <td>{{ formatDate(movement.created_at) }}</td>
                    <td>({{ movement.x }}, {{ movement.y }}) {{ movement.direction }}</td>
                    <td><code>{{ movement.commands }}</code></td>
                    <td>({{ movement.result_x }}, {{ movement.result_y }})</td>
                    <td :class="movement.result_status === 'ok' ? 'status-ok' : 'status-obstacle'">
                      {{ movement.result_status.toUpperCase() }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="alert alert-warning">
              No hay movimientos registrados aún.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'

export default {
  name: 'RoverControl',
  setup() {
    const loading = ref(false)
    const loadingHistory = ref(false)
    const result = ref(null)
    const resultClass = ref('')
    const resultTitle = ref('')
    const historyResult = ref(false)
    const movements = ref([])

    const form = reactive({
      x: 0,
      y: 0,
      direction: 'N',
      commands: 'FFRFFFRL',
      obstacles: ''
    })

    const errors = reactive({
      x: '',
      y: '',
      direction: '',
      commands: '',
      obstacles: ''
    })

    // Validaciones
    const validateX = () => {
      const value = parseInt(form.x)
      if (isNaN(value) || value < 0 || value > 199) {
        errors.x = 'La posición X debe ser un número entre 0 y 199'
      } else {
        errors.x = ''
      }
    }

    const validateY = () => {
      const value = parseInt(form.y)
      if (isNaN(value) || value < 0 || value > 199) {
        errors.y = 'La posición Y debe ser un número entre 0 y 199'
      } else {
        errors.y = ''
      }
    }

    const validateDirection = () => {
      const validDirections = ['N', 'E', 'S', 'W']
      if (!validDirections.includes(form.direction)) {
        errors.direction = 'La dirección debe ser N, E, S o W'
      } else {
        errors.direction = ''
      }
    }

    const validateCommands = () => {
      // Limpiar comandos de caracteres no permitidos
      form.commands = form.commands.replace(/[^FLR]/gi, '').toUpperCase()
      
      if (form.commands.length === 0) {
        errors.commands = 'Debe ingresar al menos un comando'
      } else if (form.commands.length > 100) {
        errors.commands = 'Los comandos no pueden exceder 100 caracteres'
      } else {
        errors.commands = ''
      }
    }

    const validateObstacles = () => {
      if (!form.obstacles.trim()) {
        errors.obstacles = ''
        return
      }

      // Validar formato: x1,y1;x2,y2
      const obstaclePattern = /^(\d+,\d+)(;\d+,\d+)*$/
      if (!obstaclePattern.test(form.obstacles)) {
        errors.obstacles = 'Formato inválido. Use: x1,y1;x2,y2'
        return
      }

      // Validar que las coordenadas estén en rango
      const obstacles = form.obstacles.split(';')
      for (let obstacle of obstacles) {
        const [x, y] = obstacle.split(',').map(Number)
        if (x < 0 || x > 199 || y < 0 || y > 199) {
          errors.obstacles = 'Las coordenadas deben estar entre 0 y 199'
          return
        }
      }

      errors.obstacles = ''
    }

    const hasErrors = computed(() => {
      return Object.values(errors).some(error => error !== '')
    })

    const executeCommands = async () => {
      // Validar todo antes de enviar
      validateX()
      validateY()
      validateDirection()
      validateCommands()
      validateObstacles()

      if (hasErrors.value) {
        return
      }

      loading.value = true
      result.value = null
      
      try {
        const payload = { 
          x: parseInt(form.x), 
          y: parseInt(form.y), 
          direction: form.direction, 
          commands: form.commands 
        }
        
        if (form.obstacles.trim()) {
          const obstacles = form.obstacles.split(';').map(pair => {
            const [ox, oy] = pair.split(',').map(Number)
            return [ox, oy]
          })
          payload.obstacles = obstacles
        }

        const response = await fetch('/api/rover/execute', {
          method: 'POST',
          headers: { 
            'Content-Type': 'application/json', 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify(payload)
        })

        const data = await response.json()
        
        if (response.ok) {
          result.value = data
          resultClass.value = 'alert alert-success'
          resultTitle.value = 'Resultado:'
          // Auto-cargar historial después de un comando exitoso
          setTimeout(loadHistory, 1000)
        } else {
          result.value = data
          resultClass.value = 'alert alert-danger'
          resultTitle.value = 'Error:'
        }
      } catch (error) {
        result.value = { error: 'Error de red o servidor.' }
        resultClass.value = 'alert alert-danger'
        resultTitle.value = 'Error:'
      } finally {
        loading.value = false
      }
    }

    const loadHistory = async () => {
      loadingHistory.value = true
      historyResult.value = true
      
      try {
        const response = await fetch('/api/rover/history', {
          headers: { 'Accept': 'application/json' }
        })
        
        const data = await response.json()
        
        if (response.ok) {
          movements.value = data
        } else {
          movements.value = []
        }
      } catch (error) {
        movements.value = []
      } finally {
        loadingHistory.value = false
      }
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleString('es-ES')
    }

    onMounted(() => {
      loadHistory()
    })

    return {
      form,
      errors,
      loading,
      loadingHistory,
      result,
      resultClass,
      resultTitle,
      historyResult,
      movements,
      hasErrors,
      executeCommands,
      loadHistory,
      formatDate,
      validateX,
      validateY,
      validateDirection,
      validateCommands,
      validateObstacles
    }
  }
}
</script>

<style scoped>
body {
  background: url('/images/mars_field.jpg') no-repeat center center fixed;
  background-size: cover;
}

.container { 
  max-width: 800px; 
  margin-top: 40px; 
  background: rgba(255,255,255,0.85); 
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.15);
  padding: 24px;
}

.result { 
  margin-top: 20px; 
}

.history-section { 
  margin-top: 30px; 
}

.history-container {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  overflow: hidden;
}

.history-scroll {
  max-height: 400px;
  overflow-y: auto;
}

.history-table { 
  font-size: 0.9em; 
  margin-bottom: 0;
}

.sticky-header {
  position: sticky;
  top: 0;
  background: #f8f9fa;
  z-index: 10;
  border-bottom: 2px solid #dee2e6;
}

.sticky-header th {
  background: #f8f9fa;
  font-weight: 600;
  color: #495057;
}

.status-ok { 
  color: #198754; 
  font-weight: bold; 
}

.status-obstacle { 
  color: #dc3545; 
  font-weight: bold; 
}

/* Estilos para el scrollbar */
.history-scroll::-webkit-scrollbar {
  width: 8px;
}

.history-scroll::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.history-scroll::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.history-scroll::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style> 