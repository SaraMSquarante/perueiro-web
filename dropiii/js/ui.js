
    // --- MODAIS: ABRIR / FECHAR ---
    function abrirModal(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    }
    function fecharModal(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.add('hidden'); document.body.style.overflow = ''; }
    }

    // --- ADICIONAR CARTÃO ---
    function adicionarCartao() {
        fecharModal('modal-cartao');
        showToast('💳 Cartão adicionado com sucesso!', 'success');
    }
    function formatarCartao(input) {
        let v = input.value.replace(/\D/g,'').substring(0,16);
        input.value = v.replace(/(\d{4})(?=\d)/g,'$1 ');
    }
    function formatarValidade(input) {
        let v = input.value.replace(/\D/g,'').substring(0,4);
        if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
        input.value = v;
    }

    // --- WIRE UP: botão "Adicionar novo cartão" ---
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[onclick*="abrirModal"]').forEach(() => {});
    });
    // Encontra o botão de adicionar cartão dinamicamente e conecta o modal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-modal]');
        if (btn) abrirModal(btn.dataset.modal);
    });

    // --- AVALIAÇÕES: estrelas interativas ---
    let estrelaSelecionada = 0;
    function setStar(n) {
        estrelaSelecionada = n;
        const stars = document.querySelectorAll('#estrelas-avaliacao i');
        stars.forEach((s, i) => {
            s.className = i < n
                ? 'fas fa-star text-yellow-400 transition text-4xl cursor-pointer'
                : 'far fa-star text-slate-300 hover:text-yellow-400 transition text-4xl cursor-pointer';
        });
    }
    function enviarAvaliacao() {
        if (estrelaSelecionada === 0) {
            showToast('⭐ Selecione pelo menos 1 estrela.', 'warning');
            return;
        }
        fecharModal('modal-avaliar');
        estrelaSelecionada = 0;
        setStar(0);
        showToast('⭐ Avaliação enviada! Obrigado pelo feedback.', 'success');
    }

    // --- TOAST SYSTEM ---
    function showToast(msg, tipo = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const colors = {
            success: 'bg-slate-900 text-white border-green-500',
            warning: 'bg-slate-900 text-white border-yellow-400',
            error:   'bg-slate-900 text-white border-red-500',
            info:    'bg-slate-900 text-white border-blue-500',
        };
        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-center gap-3 px-6 py-4 rounded-2xl shadow-2xl border-l-4 font-semibold text-sm max-w-sm ${colors[tipo] || colors.info} translate-y-10 opacity-0 transition-all duration-300`;
        toast.textContent = msg;
        container.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.replace('translate-y-10', 'translate-y-0');
            toast.classList.replace('opacity-0', 'opacity-100');
        });
        setTimeout(() => {
            toast.classList.replace('translate-y-0', 'translate-y-10');
            toast.classList.replace('opacity-100', 'opacity-0');
            setTimeout(() => toast.remove(), 350);
        }, 3500);
    }

    // --- CARREGAR MAIS AVALIAÇÕES (cliente) ---
    const avaliacoesMock = [
        { inicial: 'R', nome: 'Roberto (Perueiro)', rota: 'SP → Campinas', data: '20/01/2026', estrelas: 5, cor: 'bg-green-100 text-green-600', texto: '"Tudo perfeito! Cliente muito organizado. A embalagem estava impecável."' },
        { inicial: 'F', nome: 'Fernando (Perueiro)', rota: 'BH → Contagem',  data: '10/12/2025', estrelas: 4, cor: 'bg-purple-100 text-purple-600', texto: '"Boa comunicação e pagamento pontual. Recomendo para futuros envios."' },
        { inicial: 'D', nome: 'Domingos (Perueiro)', rota: 'RJ → Niterói',   data: '05/11/2025', estrelas: 5, cor: 'bg-blue-100 text-blue-600', texto: '"Excelente! Foi um prazer trabalhar com este cliente. Pontualidade total."' },
    ];
    let avalIndex = 0;
    function carregarMaisAvaliacoes() {
        if (avalIndex >= avaliacoesMock.length) {
            const btn = document.getElementById('btn-mais-aval');
            if (btn) { btn.innerHTML = '✅ Todas as avaliações carregadas'; btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); }
            return;
        }
        const a = avaliacoesMock[avalIndex++];
        const estrelaHtml = Array.from({length:5}, (_,i) =>
            `<i class="${i < a.estrelas ? 'fas' : 'far'} fa-star ${i < a.estrelas ? '' : 'text-slate-300'}"></i>`
        ).join('');
        const div = document.createElement('div');
        div.className = 'bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 hover:shadow-md transition-shadow';
        div.innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-4">
                    <div class="${a.cor} w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg">${a.inicial}</div>
                    <div>
                        <h4 class="text-lg font-bold text-blue-950">${a.nome}</h4>
                        <span class="text-xs text-slate-400 font-medium">${a.rota} • ${a.data}</span>
                    </div>
                </div>
                <div class="flex text-yellow-400 text-sm gap-0.5">${estrelaHtml}</div>
            </div>
            <p class="text-slate-600 leading-relaxed">${a.texto}</p>`;
        const btn = document.getElementById('btn-mais-aval');
        btn?.parentNode?.insertBefore(div, btn);
        if (avalIndex >= avaliacoesMock.length) {
            if (btn) { btn.innerHTML = '✅ Todas as avaliações carregadas'; btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); }
        }
    }

    // --- WIRE "Adicionar novo cartão" button ---
    document.addEventListener('click', function(e) {
        const alvo = e.target.closest('.add-card-btn');
        if (alvo) abrirModal('modal-cartao');
    });

    // --- BOTÃO AVALIAR nas entregas concluídas ---
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-acao="avaliar"]')) {
            abrirModal('modal-avaliar');
        }
    });

    // --- ADMIN: SALVAR LOGÍSTICA ---
    function salvarLogistica() {
        showToast('✅ Configurações de logística salvas com sucesso!', 'success');
    }
