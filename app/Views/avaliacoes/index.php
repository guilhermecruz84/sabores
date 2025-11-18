<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
    .dia-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 3px solid #e0e0e0;
        background: white;
        min-height: 180px;
    }
    .dia-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .dia-card.avaliado {
        border-color: #28a745;
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
    }
    .dia-card.pendente {
        border-color: #007bff;
        background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    }
    .dia-card.desabilitado {
        border-color: #ddd;
        background: #f5f5f5;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .dia-card.desabilitado:hover {
        transform: none;
        box-shadow: none;
    }
    .dia-icone {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }
    .dia-nome {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 0.3rem;
    }
    .dia-data {
        font-size: 0.9rem;
        color: #666;
    }
    .avaliacao-estrelas {
        font-size: 1.5rem;
        margin-top: 0.5rem;
    }
</style>

<div class="mb-4">
    <h2 class="mb-0">
        <i class="fas fa-star me-2"></i>
        Avaliar Cardápios
    </h2>
    <p class="text-muted">Selecione um dia da semana para avaliar</p>
</div>

<!-- Navegação de Semana -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-outline-primary" onclick="mudarSemana(-1)">
                <i class="fas fa-chevron-left me-2"></i>
                Semana Anterior
            </button>
            <h5 class="mb-0" id="tituloSemana"></h5>
            <button class="btn btn-outline-primary" onclick="mudarSemana(1)">
                Próxima Semana
                <i class="fas fa-chevron-right ms-2"></i>
            </button>
        </div>

        <div class="row g-3" id="diasSemana">
            <!-- Dias da semana serão inseridos aqui via JavaScript -->
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let semanaAtual = 0;
const avaliacoes = <?= json_encode($todasAvaliacoes ?? []) ?>;

function mudarSemana(direcao) {
    semanaAtual += direcao;
    renderizarSemana();
}

function getDataDaSemana(offset) {
    const hoje = new Date();
    const diaAtual = hoje.getDay(); // 0 = Domingo, 1 = Segunda, etc.

    // Calcular domingo da semana atual
    const domingo = new Date(hoje);
    domingo.setDate(hoje.getDate() - diaAtual);

    // Adicionar offset de semanas
    domingo.setDate(domingo.getDate() + (offset * 7));

    return domingo;
}

function formatarData(data) {
    const dia = String(data.getDate()).padStart(2, '0');
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    return `${dia}/${mes}`;
}

function formatarDataISO(data) {
    const ano = data.getFullYear();
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const dia = String(data.getDate()).padStart(2, '0');
    return `${ano}-${mes}-${dia}`;
}

function verificarAvaliacao(dataISO) {
    return avaliacoes.find(a => a.data === dataISO);
}

function renderizarSemana() {
    const domingo = getDataDaSemana(semanaAtual);

    // Calcular segunda e sexta
    const segunda = new Date(domingo);
    segunda.setDate(domingo.getDate() + 1); // Segunda = Domingo + 1

    const sexta = new Date(domingo);
    sexta.setDate(domingo.getDate() + 5); // Sexta = Domingo + 5

    // Atualizar título
    document.getElementById('tituloSemana').textContent =
        `Semana de ${formatarData(segunda)} a ${formatarData(sexta)}`;

    // Dias da semana (apenas Segunda a Sexta)
    const diasNomes = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira'];
    const diasAbrev = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex'];

    const container = document.getElementById('diasSemana');
    container.innerHTML = '';

    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    // Loop apenas de Segunda (i=1) a Sexta (i=5)
    for (let i = 1; i <= 5; i++) {
        const data = new Date(domingo);
        data.setDate(domingo.getDate() + i);
        const dataISO = formatarDataISO(data);

        const avaliacao = verificarAvaliacao(dataISO);
        const dataFutura = data > hoje;

        let classCard = 'dia-card ';
        let icone = '<i class="fas fa-utensils"></i>';
        let conteudoExtra = '';
        let desabilitado = false;

        if (dataFutura) {
            classCard += 'desabilitado';
            icone = '<i class="fas fa-lock"></i>';
            desabilitado = true;
        } else if (avaliacao) {
            classCard += 'avaliado';
            const estrelas = {
                'otimo': '⭐⭐⭐⭐',
                'bom': '⭐⭐⭐',
                'regular': '⭐⭐',
                'ruim': '⭐'
            };
            conteudoExtra = `
                <div class="avaliacao-estrelas">${estrelas[avaliacao.avaliacao]}</div>
                <small class="text-success"><i class="fas fa-check"></i> Avaliado</small>
            `;
        } else {
            classCard += 'pendente';
            conteudoExtra = '<small class="text-primary"><i class="far fa-star"></i> Pendente</small>';
        }

        const onclick = desabilitado ? '' : `onclick="avaliarDia('${dataISO}')"`;

        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 col-lg';
        col.innerHTML = `
            <div class="${classCard}" ${onclick}>
                <div class="card-body text-center">
                    <div class="dia-icone">${icone}</div>
                    <div class="dia-nome">${diasAbrev[i-1]}</div>
                    <div class="dia-data">${formatarData(data)}</div>
                    ${conteudoExtra}
                </div>
            </div>
        `;

        container.appendChild(col);
    }
}

function avaliarDia(data) {
    window.location.href = '<?= base_url('avaliacoes/avaliar-dia/') ?>' + data;
}

// Renderizar ao carregar
$(document).ready(function() {
    renderizarSemana();
});
</script>
<?= $this->endSection() ?>
