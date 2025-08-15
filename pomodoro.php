<?php
// Incluindo o header
require_once 'includes/header.php';
require_once 'includes/auth.php';
?>

<div class="column-main">
    <h1>🍅 Pomodoro de Estudos</h1>
    
    <div style="max-width: 600px; margin: 30px auto; text-align: center; background-color: #f8f9fa; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- Display do tempo -->
        <div id="timer" style="font-size: 3.5em; font-weight: bold; color: #2c3e50; margin: 20px 0; font-family: 'Crimson Text', Georgia, serif;">
            25:00
        </div>
        
        <!-- Botões de controle -->
        <div style="margin: 30px 0;">
            <button id="startBtn" style="background-color: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 6px; margin: 0 10px; cursor: pointer; font-size: 1.1em;">
                ▶️ Iniciar
            </button>
            <button id="pauseBtn" style="background-color: #ffc107; color: #212529; padding: 12px 24px; border: none; border-radius: 6px; margin: 0 10px; cursor: pointer; font-size: 1.1em;" disabled>
                ⏸️ Pausar
            </button>
            <button id="resetBtn" style="background-color: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 6px; margin: 0 10px; cursor: pointer; font-size: 1.1em;">
                🔄 Reiniciar
            </button>
        </div>
        
        <!-- Configurações -->
        <div style="margin: 20px 0; padding: 15px; background-color: #e9ecef; border-radius: 8px;">
            <h3 style="margin-bottom: 15px; color: #495057;">Configurações</h3>
            
            <div style="margin-bottom: 15px;">
                <label for="workTime" style="display: block; margin-bottom: 5px; color: #495057;">Tempo de estudo (min):</label>
                <input type="number" id="workTime" value="25" min="1" max="60" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 80px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="breakTime" style="display: block; margin-bottom: 5px; color: #495057;">Tempo de pausa (min):</label>
                <input type="number" id="breakTime" value="5" min="1" max="30" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 80px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="autoStartBreaks" style="display: block; margin-bottom: 5px; color: #495057;">
                    <input type="checkbox" id="autoStartBreaks" checked>
                    Iniciar pausas automaticamente
                </label>
            </div>
        </div>
        
        <!-- Status -->
        <div id="status" style="margin: 20px 0; font-size: 1.2em; font-weight: bold; color: #28a745;">
            Tempo de estudo
        </div>
        
        <!-- Estatísticas -->
        <div id="stats" style="margin-top: 20px; padding: 15px; background-color: #d4edda; border-radius: 8px; color: #155724;">
            <div>Pomodoros concluídos hoje: <span id="pomodorosToday">0</span></div>
            <div>Total de pomodoros: <span id="pomodorosTotal">0</span></div>
            <div>Tempo total de estudo: <span id="totalStudyTime">0h 0m</span></div>
        </div>
    </div>
    
    <!-- Instruções -->
    <div style="max-width: 800px; margin: 30px auto; text-align: center; color: #666; font-style: italic;">
        <p>O método Pomodoro é uma técnica de gerenciamento de tempo que divide o trabalho em intervalos de 25 minutos, separados por pausas curtas.</p>
        <p>Após 4 ciclos, faça uma pausa mais longa (15-30 minutos).</p>
    </div>
</div>

<script>
// Configuração do Pomodoro
let timer;
let isRunning = false;
let isPaused = false;
let workTime = 25 * 60; // 25 minutos em segundos
let breakTime = 5 * 60; // 5 minutos em segundos
let currentTime = workTime;
let isWorkTime = true;
let pomodorosToday = 0;
let pomodorosTotal = 0;
let totalStudyTime = 0; // em segundos

// Elementos do DOM
const timerDisplay = document.getElementById('timer');
const startBtn = document.getElementById('startBtn');
const pauseBtn = document.getElementById('pauseBtn');
const resetBtn = document.getElementById('resetBtn');
const statusDisplay = document.getElementById('status');
const workTimeInput = document.getElementById('workTime');
const breakTimeInput = document.getElementById('breakTime');
const autoStartBreaks = document.getElementById('autoStartBreaks');
const pomodorosTodayDisplay = document.getElementById('pomodorosToday');
const pomodorosTotalDisplay = document.getElementById('pomodorosTotal');
const totalStudyTimeDisplay = document.getElementById('totalStudyTime');

// Carregar estatísticas do localStorage
function loadStats() {
    const savedStats = localStorage.getItem('pomodoroStats');
    if (savedStats) {
        const stats = JSON.parse(savedStats);
        pomodorosToday = stats.pomodorosToday || 0;
        pomodorosTotal = stats.pomodorosTotal || 0;
        totalStudyTime = stats.totalStudyTime || 0;
        
        // Verificar se é um novo dia
        const lastDate = new Date(stats.lastDate);
        const today = new Date();
        if (lastDate.toDateString() !== today.toDateString()) {
            pomodorosToday = 0;
        }
        
        updateStatsDisplay();
    }
}

// Salvar estatísticas no localStorage
function saveStats() {
    const stats = {
        pomodorosToday,
        pomodorosTotal,
        totalStudyTime,
        lastDate: new Date().toISOString()
    };
    localStorage.setItem('pomodoroStats', JSON.stringify(stats));
}

// Atualizar exibição das estatísticas
function updateStatsDisplay() {
    pomodorosTodayDisplay.textContent = pomodorosToday;
    pomodorosTotalDisplay.textContent = pomodorosTotal;
    
    const hours = Math.floor(totalStudyTime / 3600);
    const minutes = Math.floor((totalStudyTime % 3600) / 60);
    totalStudyTimeDisplay.textContent = `${hours}h ${minutes}m`;
}

// Formatar tempo (minutos:segundos)
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// Atualizar display do tempo
function updateTimerDisplay() {
    timerDisplay.textContent = formatTime(currentTime);
    
    // Efeito visual quando faltar pouco tempo
    if (currentTime <= 10) {
        timerDisplay.style.color = '#dc3545';
        timerDisplay.style.animation = 'pulse 1s infinite';
    } else {
        timerDisplay.style.color = '#2c3e50';
        timerDisplay.style.animation = 'none';
    }
}

// Iniciar timer
function startTimer() {
    if (isRunning) return;
    
    isRunning = true;
    startBtn.disabled = true;
    pauseBtn.disabled = false;
    
    timer = setInterval(() => {
        currentTime--;
        
        if (isWorkTime) {
            totalStudyTime++;
        }
        
        updateTimerDisplay();
        
        if (currentTime <= 0) {
            clearInterval(timer);
            isRunning = false;
            startBtn.disabled = false;
            pauseBtn.disabled = true;
            
            if (isWorkTime) {
                // Terminou tempo de estudo
                isWorkTime = false;
                currentTime = breakTime;
                statusDisplay.textContent = 'Pausa';
                statusDisplay.style.color = '#ffc107';
                
                // Notificação
                alert('Tempo de estudo concluído! Hora da pausa!');
                
                // Iniciar pausa automaticamente se configurado
                if (autoStartBreaks.checked) {
                    startTimer();
                }
            } else {
                // Terminou pausa
                isWorkTime = true;
                currentTime = workTime;
                statusDisplay.textContent = 'Tempo de estudo';
                statusDisplay.style.color = '#28a745';
                
                // Incrementar pomodoros
                pomodorosToday++;
                pomodorosTotal++;
                
                // Notificação
                alert('Pausa concluída! Hora de voltar aos estudos!');
                
                // Iniciar novo ciclo automaticamente se configurado
                if (autoStartBreaks.checked) {
                    startTimer();
                }
            }
            
            updateTimerDisplay();
            updateStatsDisplay();
            saveStats();
        }
    }, 1000);
}

// Pausar timer
function pauseTimer() {
    if (!isRunning) return;
    
    clearInterval(timer);
    isRunning = false;
    isPaused = true;
    startBtn.disabled = false;
    pauseBtn.disabled = true;
}

// Reiniciar timer
function resetTimer() {
    clearInterval(timer);
    isRunning = false;
    isPaused = false;
    isWorkTime = true;
    currentTime = workTime;
    
    startBtn.disabled = false;
    pauseBtn.disabled = true;
    
    statusDisplay.textContent = 'Tempo de estudo';
    statusDisplay.style.color = '#28a745';
    
    updateTimerDisplay();
}

// Atualizar tempos de trabalho e pausa
function updateTimes() {
    workTime = parseInt(workTimeInput.value) * 60;
    breakTime = parseInt(breakTimeInput.value) * 60;
    
    if (!isRunning && isWorkTime) {
        currentTime = workTime;
        updateTimerDisplay();
    }
}

// Event listeners
startBtn.addEventListener('click', startTimer);
pauseBtn.addEventListener('click', pauseTimer);
resetBtn.addEventListener('click', resetTimer);
workTimeInput.addEventListener('change', updateTimes);
breakTimeInput.addEventListener('change', updateTimes);

// Inicialização
updateTimerDisplay();
loadStats();
updateStatsDisplay();

// Adicionar estilo de pulsação
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>

<style>
/* Estilos adicionais para o Pomodoro */
#timer {
    font-family: 'Crimson Text', Georgia, serif;
    transition: color 0.3s ease;
}

button {
    font-family: 'Crimson Text', Georgia, serif;
    transition: all 0.2s ease;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

input[type="number"] {
    font-family: 'Crimson Text', Georgia, serif;
    font-size: 1em;
}
</style>