<?php
// ======================
//  CONEXÃO E PREPARO
// ======================
include('../api/conexao.php');

// Se você já tem $condicoes para filtrar (ex: WHERE …), use aqui
$condicoes = "WHERE processos.ativo = 1"; // exemplo, troque pelo seu

$anoSelecionado = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

$anosDisponiveis = [];
$sqlAnos = "SELECT DISTINCT YEAR(data_processo) AS ano FROM processos ORDER BY ano DESC";
$resAnos = $conexao->query($sqlAnos);
while ($row = $resAnos->fetch_assoc()) {
    $anosDisponiveis[] = $row['ano'];
}

if($anoSelecionado){
    $condAno = " AND YEAR(processos.data_processo) = $anoSelecionado";
}else{
    $condAno = "";
}

// ----------------------
// 1) Processos por mês
// ----------------------
$sqlMes = "
    SELECT DATE_FORMAT(processos.data_processo, '%Y-%m') AS mes,
           COUNT(*) AS total
    FROM processos
    $condicoes
    $condAno
    GROUP BY DATE_FORMAT(processos.data_processo, '%Y-%m')
    ORDER BY mes
";
$resMes = $conexao->query($sqlMes);
$labelsMes = $valoresMes = [];
while ($row = $resMes->fetch_assoc()) {
    $labelsMes[]  = $row['mes'];
    $valoresMes[] = (int)$row['total'];
}

// ----------------------
// 2) Processos por assunto
// ----------------------
$sqlAssunto = "
    SELECT assuntos.nome AS assunto, COUNT(*) AS total
    FROM processos
    INNER JOIN assuntos ON processos.assunto = assuntos.id
    $condicoes
    $condAno
    GROUP BY assuntos.nome
    ORDER BY total DESC
";
$resAssunto = $conexao->query($sqlAssunto);
$labelsAssunto = $valoresAssunto = [];
while ($row = $resAssunto->fetch_assoc()) {
    $labelsAssunto[]  = $row['assunto'];
    $valoresAssunto[] = (int)$row['total'];
}

// ----------------------
// 3) Processos por usuário (última localização)
// ----------------------
$sqlUsuario = "
    SELECT agentes.nome AS usuario, COUNT(*) AS total
    FROM processos
    LEFT JOIN (
        SELECT l1.id_processo, l1.destino_id, l1.destino_tipo
        FROM localizacoes l1
        INNER JOIN (
            SELECT id_processo, MAX(localizado_em) AS max_localizado
            FROM localizacoes
            WHERE ativo = 1
            GROUP BY id_processo
        ) l2
          ON l1.id_processo = l2.id_processo
         AND l1.localizado_em = l2.max_localizado
    ) AS ultima_localizacao
      ON ultima_localizacao.id_processo = processos.id
    LEFT JOIN agentes
      ON ultima_localizacao.destino_tipo = 'usuario'
     AND ultima_localizacao.destino_id = agentes.id
    $condicoes
    $condAno
    GROUP BY agentes.nome
    ORDER BY total DESC
";
$resUsuario = $conexao->query($sqlUsuario);
$labelsUsuario = $valoresUsuario = [];
while ($row = $resUsuario->fetch_assoc()) {
    $labelsUsuario[]  = $row['usuario'] ?: 'Sem usuário';
    $valoresUsuario[] = (int)$row['total'];
}

// ----------------------
// 4) Processos por status
// ----------------------
$sqlStatus = "
    SELECT status, COUNT(*) AS total
    FROM processos
    $condicoes
    $condAno
    GROUP BY status
";
$resStatus = $conexao->query($sqlStatus);
$labelsStatus = $valoresStatus = [];
while ($row = $resStatus->fetch_assoc()) {
    $labelsStatus[]  = $row['status'] ?: 'Indefinido';
    $valoresStatus[] = (int)$row['total'];
}
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body { font-family: Arial, sans-serif; }

  .content{
    width: 95%;
    margin: auto;
    background: whitesmoke;
    border-radius: 10px;
    display: flex;
    justify-content: center;
  }

  .flex-row {
    width: 100%;
    margin: 2rem 0;
  }

  .grafico-container {
      width: 45%;
      display: inline-block;
      margin: 20px;
      vertical-align: top;
  }

  .grafico-titulo {
    font-size: small;
    text-align: center;
  }
</style>
</head>
<body>

<div class="content">

    <h2>Relatório de Processos</h2>

    <form method="get" style="margin-bottom:20px;">
        <div class="form-group">
            <label for="ano">Selecione o Ano:</label>
            <select name="ano" id="ano" onchange="this.form.submit()">
                <?php foreach ($anosDisponiveis as $ano): ?>
                    <option value="<?= $ano ?>" <?= $ano == $anoSelecionado ? 'selected' : '' ?>>
                        <?= $ano ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <div class="flex-row">
        <div class="grafico-container">
            <canvas id="graficoMes"></canvas>
        </div>
        <div class="grafico-container">
            <canvas id="graficoAssunto"></canvas>
        </div>
    </div>

    <div class="flex-row">
        <div class="grafico-container">
            <div class="grafico-titulo">
                Processos por Usuário
            </div>
            <canvas id="graficoUsuario"></canvas>
        </div>
        <div class="grafico-container">
            <div class="grafico-titulo">
                Processos por Status
            </div>
            <canvas id="graficoStatus"></canvas>
        </div>
    </div>

</div>

<script>
const graficoMes = new Chart(document.getElementById('graficoMes'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labelsMes); ?>,
        datasets: [{
            label: 'Processos por Mês',
            data: <?php echo json_encode($valoresMes); ?>,
            borderColor: '#36A2EB',
            fill: false
        }]
    }
});

const graficoAssunto = new Chart(document.getElementById('graficoAssunto'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labelsAssunto); ?>,
        datasets: [{
            label: 'Processos por Assunto',
            data: <?php echo json_encode($valoresAssunto); ?>,
            backgroundColor: '#FF6384'
        }]
    },
    options: { indexAxis: 'y' }
});

const graficoUsuario = new Chart(document.getElementById('graficoUsuario'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labelsUsuario); ?>,
        datasets: [{
            label: 'Processos por Usuário',
            data: <?php echo json_encode($valoresUsuario); ?>,
            backgroundColor: ['#FF9F40','#4BC0C0','#9966FF','#FFCD56','#36A2EB','#FF6384']
        }]
    }
});

const graficoStatus = new Chart(document.getElementById('graficoStatus'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($labelsStatus); ?>,
        datasets: [{
            label: 'Processos por Status',
            data: <?php echo json_encode($valoresStatus); ?>,
            backgroundColor: ['#4BC0C0','#FF6384','#FFCE56','#36A2EB','#9966FF']
        }]
    }
});
</script>


