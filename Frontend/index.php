<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard de Monitoreo</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .alert-box {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .alert-content {
      background: white;
      padding: 20px;
      border-radius: 12px;
      max-width: 300px;
      width: 100%;
      text-align: center;
      color: #000;
    }

    .alert-content h3,
    .alert-content p,
    .alert-content label {
      color: #000;
    }

    input {
      color: #000;
    }

    .alarm {
      background-color: #ffb4b4 !important;
      box-shadow: 0 0 20px rgba(255, 0, 0, 0.6);
    }
  </style>
</head>

<body class="dashboard-page">

<header>
   <img src="img/BioAirSolutionsLogo.png" alt="BioAirSolution" class="logo">
</header>

<nav>
  <div class="nav-links">
    <p class="activo">Inicio</p>
    <p>Historial</p>
  </div>
  <a href="logout.php"><button class="logout-btn">Cerrar sesión</button></a>
</nav>

<div class="container">
  <section class="dashboard">
    <h2>Bienvenido al Sistema de Monitoreo</h2>
    <p>Monitoreo en tiempo real de las condiciones ambientales</p>

    <div class="gauges-container">

      <div class="gauge-card" id="card-temp">
        <canvas id="tempGauge"></canvas>
        <p>Temperatura (°C)</p>
        <button class="setpoint-btn" onclick="setSetpoint('temp')">Setpoint</button>
      </div>

      <div class="gauge-card" id="card-hum">
        <canvas id="humGauge"></canvas>
        <p>Humedad (%)</p>
        <button class="setpoint-btn" onclick="setSetpoint('hum')">Setpoint</button>
      </div>

      <div class="gauge-card" id="card-pres">
        <canvas id="presGauge"></canvas>
        <p>Presión (hPa)</p>
        <button class="setpoint-btn" onclick="setSetpoint('pres')">Setpoint</button>
      </div>

      <div class="gauge-card" id="card-air">
        <canvas id="airGauge"></canvas>
        <p>Calidad del Aire (ppm)</p>
        <button class="setpoint-btn" onclick="setSetpoint('air')">Setpoint</button>
      </div>

    </div>
  </section>
</div>

<!-- MODAL SETPOINT -->
<div id="setpointBox" class="alert-box">
  <div class="alert-content">
    <h3>⚙ Configurar setpoint</h3>
    <p id="setpointLabel"></p>

    <label>Mínimo:</label>
    <input type="number" id="setpointMin">

    <label>Máximo:</label>
    <input type="number" id="setpointMax">

    <br><br>
    <button onclick="guardarSetpoint()">Guardar</button>
    <button onclick="cerrarSetpoint()" style="background:#888;">Cancelar</button>
  </div>
</div>

<!-- MODAL ALERTA -->
<div id="alertBox" class="alert-box">
  <div class="alert-content">
    <h3>⚠ ALARMA ACTIVADA</h3>
    <p id="alertMessage"></p>

    <label>Código para desactivar:</label>
    <input type="password" id="alertCode">

    <br><br>
    <button onclick="validarCodigo()">Desactivar</button>
  </div>
</div>

<script>
let gauges = {};
let setpointActual = null;

let setpoints = {
  temp: {min:null, max:null},
  hum:  {min:null, max:null},
  pres: {min:null, max:null},
  air:  {min:null, max:null}
};

let valores = {
  temperatura: 20,
  humedad: 40,
  presion: 900,
  calidad: 30
};

const codigoAlarma = "1234";
let alarmaActiva = false;

// ========= GAUGES ===========
function createOrUpdateGauge(id, value, max) {
  const canvas = document.getElementById(id);
  const ctx = canvas.getContext('2d');

  const percent = (value / max) * 100;
  const color = percent < 60 ? '#219EBC' : percent < 80 ? '#FFB703' : '#FB8500';

  if (gauges[id]) {
    gauges[id].data.datasets[0].data = [value, max - value];
    gauges[id].data.datasets[0].backgroundColor[0] = color;
    gauges[id].currentValue = value;
    gauges[id].update();
    return;
  }

  gauges[id] = new Chart(ctx, {
    type: 'doughnut',
    data: {
      datasets: [{
        data: [value, max - value],
        backgroundColor: [color, '#e0e0e0'],
        borderWidth: 0
      }]
    },
    options: {
      circumference: 180,
      rotation: 270,
      cutout: '80%',
      plugins: {
        tooltip: { enabled: false },
        legend: { display: false }
      }
    },
    plugins: [{
      id: 'centerText',
      afterDraw(chart) {
        const ctx = chart.ctx;
        ctx.save();
        ctx.font = '18px Arial';
        ctx.fillStyle = '#023047';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(
          chart.currentValue?.toFixed(1) || '0.0',
          chart.width / 2,
          chart.height / 1.4
        );
        ctx.restore();
      }
    }]
  });

  gauges[id].currentValue = value;
}

// ========= CONFIGURAR SETPOINT ===========
function setSetpoint(type) {
  setpointActual = type;

  const names = {
    temp: "Temperatura (°C)",
    hum: "Humedad (%)",
    pres: "Presión (hPa)",
    air: "Calidad del aire (ppm)"
  };

  document.getElementById("setpointLabel").innerText = names[type];

  document.getElementById("setpointMin").value = setpoints[type].min ?? "";
  document.getElementById("setpointMax").value = setpoints[type].max ?? "";

  document.getElementById("setpointBox").style.display = "flex";
}

function guardarSetpoint() {
  const min = document.getElementById("setpointMin").value;
  const max = document.getElementById("setpointMax").value;

  if (min === "" || max === "") {
    alert("Debes ingresar mínimo y máximo.");
    return;
  }

  setpoints[setpointActual].min = parseFloat(min);
  setpoints[setpointActual].max = parseFloat(max);

  cerrarSetpoint();
}

function cerrarSetpoint() {
  document.getElementById("setpointBox").style.display = "none";
}

// ========= ALERTA ===========
function activarAlerta(tipo, valor) {
  if (alarmaActiva) return;

  alarmaActiva = true;

  document.getElementById("alertMessage").innerText =
    `El sensor de ${tipo.toUpperCase()} salió del rango (${valor.toFixed(1)}).`;

  document.getElementById("alertBox").style.display = "flex";
}

function validarCodigo() {
  let code = document.getElementById("alertCode").value;

  if (code === codigoAlarma) {
    alarmaActiva = false;
    document.getElementById("alertBox").style.display = "none";
  } else {
    alert("Código incorrecto.");
  }
}

// ========= VERIFICAR ALARMA ===========
function verificarAlarma(tipo, valor) {
  const sp = setpoints[tipo];

  if (sp.min !== null && valor < sp.min) {
    activarAlerta(tipo, valor);
    return;
  }

  if (sp.max !== null && valor > sp.max) {
    activarAlerta(tipo, valor);
    return;
  }
}

// ========= SIMULACIÓN ===========
function simularDatos() {
  valores.temperatura += Math.random() * 2;
  valores.humedad += Math.random() * 2;
  valores.presion += Math.random() * 3;
  valores.calidad += Math.random() * 4;

  createOrUpdateGauge('tempGauge', valores.temperatura, 50);
  createOrUpdateGauge('humGauge', valores.humedad, 100);
  createOrUpdateGauge('presGauge', valores.presion, 1100);
  createOrUpdateGauge('airGauge', valores.calidad, 500);

  verificarAlarma('temp', valores.temperatura);
  verificarAlarma('hum', valores.humedad);
  verificarAlarma('pres', valores.presion);
  verificarAlarma('air', valores.calidad);
}

setInterval(simularDatos, 2000);
</script>

</body>
</html>
