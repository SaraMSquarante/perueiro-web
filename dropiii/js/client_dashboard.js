
    // --- PAINEL DO CLIENTE (DASHBOARD E HISTÓRICO) ---
    function atualizarPainelCliente() {
        const nomeDoCliente = localStorage.getItem('usuarioLogado') || "Cliente";
        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        
        let meusPedidos = pedidos.filter(p => p.cliente === nomeDoCliente);
        
        const elTotal = document.getElementById('dash-total-envios');
        const elPendentes = document.getElementById('dash-pendentes');
        const elGasto = document.getElementById('dash-gasto');
        const elRastreio = document.getElementById('dash-rastreio-container');

        if(elTotal && elPendentes && elGasto) {
            elTotal.innerText = meusPedidos.length;
            
            let pendentes = meusPedidos.filter(p => p.status !== 'Entregue' && p.status !== 'Cancelado');
            elPendentes.innerText = pendentes.length;
            
            // Soma o gasto dinâmico real dos pedidos Entregues
            let entregues = meusPedidos.filter(p => p.status === 'Entregue');
            let gastoTotal = entregues.reduce((total, p) => total + parseFloat(p.valorFinal || 0), 0);
            elGasto.innerHTML = gastoTotal.toLocaleString('pt-AO', {minimumFractionDigits: 2}) + ' <span class="text-xl">Reais</span>';

            // Atualiza o Rastreio se houver pedidos ativos
            if (pendentes.length > 0 && elRastreio) {
                let ultimo = pendentes[pendentes.length - 1];
                let icone = "fa-box"; let corText = "text-orange-600";
                if(ultimo.status === 'Aguardando Coleta') { icone = "fa-motorcycle"; corText = "text-blue-600"; }
                if(ultimo.status === 'Em Trânsito') { icone = "fa-truck-fast"; corText = "text-purple-600"; }
                if(ultimo.status === 'Chegando ao Destino') { icone = "fa-map-marker-alt"; corText = "text-green-600"; }

                elRastreio.innerHTML = `
                    <div class="w-full flex flex-col md:flex-row items-center justify-between bg-slate-50 p-6 rounded-3xl border border-slate-200 gap-6">
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 bg-white rounded-full shadow-md flex items-center justify-center border-4 border-slate-100">
                                <i class="fas ${icone} ${corText} text-3xl"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-bold uppercase text-slate-400 tracking-widest mb-1">Status Atual</p>
                                <h4 class="text-2xl font-black ${corText}">${ultimo.status}</h4>
                                <p class="text-sm font-semibold text-slate-500 mt-1">Pedido <span class="text-slate-800">#${ultimo.id}</span></p>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-center md:items-end w-full md:w-auto">
                            <p class="text-sm text-slate-500 mb-2 font-medium"><i class="fas fa-satellite-dish mr-1 text-blue-500 animate-pulse"></i> Sincronizado ao vivo</p>
                            <div class="w-full md:w-[200px] bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-blue-600 h-2.5 rounded-full w-2/3 animate-[pulse_2s_ease-in-out_infinite]"></div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (pendentes.length === 0 && elRastreio && meusPedidos.length > 0) {
                 elRastreio.innerHTML = `
                    <div class="bg-green-50 p-8 rounded-full shadow-inner border border-green-100">
                        <i class="fas fa-check-circle text-6xl text-green-500"></i>
                    </div>
                    <div>
                        <h4 class="text-2xl font-black text-green-700 mb-3">Tudo entregue!</h4>
                        <p class="text-slate-600 max-w-md mx-auto leading-relaxed">Você não possui entregas em andamento. Todas as suas solicitações foram finalizadas.</p>
                    </div>
                    <button onclick="goTo('new-order')" class="bg-dropi text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2 shadow-lg hover:scale-105 transition-all">
                        <i class="fas fa-plus-circle"></i> Novo Envio
                    </button>
                 `;
            }
        }

        // Tabela do Histórico Dinâmica
        const elTabela = document.getElementById('tabela-historico-cliente');
        if(elTabela && meusPedidos.length > 0) {
            let trs = meusPedidos.slice().reverse().map(p => {
                let badgeClass = "bg-orange-100 text-orange-700";
                if(p.status === 'Entregue') badgeClass = "bg-green-100 text-green-700";
                else if(p.status === 'Cancelado') badgeClass = "bg-red-100 text-red-700";
                else if(p.status !== 'Pendente') badgeClass = "bg-blue-100 text-blue-700"; 

                return `
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-blue-900">DRP-${p.id}</td>
                        <td class="px-6 py-4 font-medium">Hoje</td>
                        <td class="px-6 py-4 truncate max-w-[150px]">${p.destinoNome}</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-bold ${badgeClass}">${p.status}</span></td>
                        <td class="px-6 py-4 font-bold text-slate-700">${parseFloat(p.valorFinal).toLocaleString('pt-AO')} Reais</td>
                        <td class="px-6 py-4">${p.status === 'Entregue' ? `<button data-acao="avaliar" class="text-xs font-bold text-yellow-600 bg-yellow-50 px-3 py-1.5 rounded-full border border-yellow-200 hover:bg-yellow-100 transition"><i class="fas fa-star mr-1"></i>Avaliar</button>` : `<span class="text-xs text-slate-400">—</span>`}</td>
                    </tr>
                `;
            }).join('');
            
            elTabela.innerHTML = trs;
        }
    }

    // --- PAGAMENTOS DO CLIENTE (CONCILIAÇÃO COM PERUEIRO) ---
    function atualizarPagamentosCliente() {
        const nomeDoCliente = localStorage.getItem('usuarioLogado') || "Cliente";
        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        let meusPedidos = pedidos.filter(p => p.cliente === nomeDoCliente);

        // Calcula os cards de resumo
        const totalGasto    = meusPedidos.reduce((s, p) => s + parseFloat(p.valorFinal || 0), 0);
        const emAndamento   = meusPedidos.filter(p => p.status !== 'Entregue' && p.status !== 'Cancelado')
                                         .reduce((s, p) => s + parseFloat(p.valorFinal || 0), 0);
        const confirmados   = meusPedidos.filter(p => p.status === 'Entregue')
                                         .reduce((s, p) => s + parseFloat(p.valorFinal || 0), 0);

        const fmt = v => v.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        const elTotal = document.getElementById('pay-total-gasto');
        const elAnd   = document.getElementById('pay-a-pagar');
        const elConf  = document.getElementById('pay-confirmados');
        if (elTotal) elTotal.innerHTML = `${fmt(totalGasto)} <span class="text-lg">R$</span>`;
        if (elAnd)   elAnd.innerHTML   = `${fmt(emAndamento)} <span class="text-lg">R$</span>`;
        if (elConf)  elConf.innerHTML  = `${fmt(confirmados)} <span class="text-lg">R$</span>`;

        // Preenche a tabela de conciliação
        const elTabela = document.getElementById('tabela-pagamentos-cliente');
        if (!elTabela) return;

        if (meusPedidos.length === 0) return; // mantém o estado vazio padrão

        // Taxa de serviço da plataforma: 15%, resto vai ao perueiro
        const TAXA_PLATAFORMA = 0.15;

        const statusBadge = {
            'Pendente':           'bg-yellow-100 text-yellow-700',
            'Aguardando Coleta':  'bg-blue-100 text-blue-700',
            'Em Trânsito':        'bg-purple-100 text-purple-700',
            'Chegando ao Destino':'bg-indigo-100 text-indigo-700',
            'Entregue':           'bg-green-100 text-green-700',
            'Cancelado':          'bg-red-100 text-red-700',
        };

        // Nomes fictícios de perueiros para enriquecer a UI
        const nomePerueiro = (id) => {
            const nomes = ['Carlos Silva', 'António Neto', 'João Mendes', 'Mário Costa', 'Pedro Lopes'];
            return id === 0 ? '—' : nomes[id % nomes.length];
        };

        const rows = meusPedidos.slice().reverse().map(p => {
            const valor     = parseFloat(p.valorFinal || 0);
            const repasse   = (valor * (1 - TAXA_PLATAFORMA)).toFixed(2);
            const badge     = statusBadge[p.status] || 'bg-slate-100 text-slate-700';
            const entregue  = p.status === 'Entregue';
            const perueiro  = entregue ? nomePerueiro(p.id) : '<span class="text-slate-400 italic text-xs">Aguardando aceite</span>';
            const repasseHtml = entregue
                ? `<span class="text-green-600 font-black">R$ ${parseFloat(repasse).toLocaleString('pt-BR', {minimumFractionDigits:2})}</span>`
                : `<span class="text-slate-400 text-xs italic">Pendente entrega</span>`;

            return `
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-4 font-bold text-blue-900">DRP-${p.id}</td>
                    <td class="px-5 py-4 text-slate-600 truncate max-w-[130px]">${p.destinoNome}</td>
                    <td class="px-5 py-4 font-semibold text-slate-700">${perueiro}</td>
                    <td class="px-5 py-4"><span class="px-3 py-1 rounded-full text-xs font-bold ${badge}">${p.status}</span></td>
                    <td class="px-5 py-4 font-black text-slate-800 text-right">R$ ${valor.toLocaleString('pt-BR', {minimumFractionDigits:2})}</td>
                    <td class="px-5 py-4 text-right">${repasseHtml}</td>
                </tr>`;
        }).join('');

        elTabela.innerHTML = rows;
    }
