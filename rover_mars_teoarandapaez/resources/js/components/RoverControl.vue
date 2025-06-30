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
              id="x" 
              v-model="form.x" 
              min="0" 
              max="199" 
              required
            >
          </div>
          <div class="mb-3">
            <label for="y" class="form-label">Posición Y (0-199)</label>
            <input 
              type="number" 
              class="form-control" 
              id="y" 
              v-model="form.y" 
              min="0" 
              max="199" 
              required
            >
          </div>
          <div class="mb-3">
            <label for="direction" class="form-label">Dirección inicial</label>
            <select class="form-select" id="direction" v-model="form.direction" required>
              <option value="N">Norte (N)</option>
              <option value="E">Este (E)</option>
              <option value="S">Sur (S)</option>
              <option value="W">Oeste (W)</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="commands" class="form-label">Comandos (F, L, R)</label>
            <input 
              type="text" 
              class="form-control" 
              id="commands" 
              v-model="form.commands" 
              required
            >
            <div class="form-text">Ejemplo: FFRFFFRL</div>
          </div>
          <div class="mb-3">
            <label for="obstacles" class="form-label">Obstáculos (opcional, formato: x1,y1;x2,y2)</label>
            <input 
              type="text" 
              class="form-control" 
              id="obstacles" 
              v-model="form.obstacles" 
              placeholder="0,2;2,3"
            >
            <div class="form-text">Ejemplo: 0,2;2,3</div>
          </div>
          <button type="submit" class="btn btn-primary w-100" :disabled="loading">
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
          <h4>📊 Historial de Movimientos</h4>
          <button 
            type="button" 
            class="btn btn-outline-secondary mb-3" 
            @click="loadHistory"
            :disabled="loadingHistory"
          >
            {{ loadingHistory ? '🔄 Cargando...' : '🔄 Cargar Historial' }}
          </button>
          <div v-if="historyResult">
            <div v-if="movements.length > 0" class="table-responsive">
              <table class="table table-sm table-striped history-table">
                <thead>
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
import { ref, reactive, onMounted } from 'vue'

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

    const executeCommands = async () => {
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
            'Accept': 'application/json' 
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
      loading,
      loadingHistory,
      result,
      resultClass,
      resultTitle,
      historyResult,
      movements,
      executeCommands,
      loadHistory,
      formatDate
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

.history-table { 
  font-size: 0.9em; 
}

.status-ok { 
  color: #198754; 
  font-weight: bold; 
}

.status-obstacle { 
  color: #dc3545; 
  font-weight: bold; 
}
</style> 