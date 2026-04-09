
    // --- SISTEMA DE RADAR DO PERUEIRO ---
    function carregarPedidosDisponiveis() {
        const container = document.getElementById('radar-pedidos');
        if (!container) return; 

        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        let pedidosPendentes = pedidos.filter(p => p.status === 'Pendente');

        if (pedidosPendentes.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-10 bg-white/50 rounded-3xl border border-dashed border-slate-300">
                    <p class="text-slate-500 font-medium animate-pulse">Buscando novas solicitações na região...</p>
                </div>`;
            return;
        }

        container.innerHTML = pedidosPendentes.map(pedido => `
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 border-t-8 border-t-blue-500 flex flex-col justify-between hover:-translate-y-2 transition-transform duration-300">
                <div>
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase flex items-center gap-2">
                            <i class="fas fa-box"></i> Pacote
                        </span>
                        <span class="text-2xl font-black text-slate-900 italic text-green-600">${parseFloat(pedido.valorFinal).toLocaleString('pt-AO')} <span class="text-sm">Reais</span></span>
                    </div>
                    <h4 class="font-black text-lg text-slate-800 mb-1">Encomenda de ${pedido.cliente}</h4>
                    <p class="text-sm text-slate-400 mb-6">Distância: ~${pedido.distancia}km • ${pedido.tempo} min</p>
                    
                    <div class="space-y-3 mb-8 relative before:absolute before:inset-y-0 before:left-2.5 before:w-0.5 before:bg-slate-200">
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-5 h-5 rounded-full bg-blue-500 border-4 border-white shadow-sm"></div>
                            <p class="font-bold text-slate-700 text-sm truncate max-w-[200px]">${pedido.origemNome} <span class="block text-xs font-normal text-slate-400">Origem</span></p>
                        </div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-5 h-5 rounded-full bg-slate-800 border-4 border-white shadow-sm"></div>
                            <p class="font-bold text-slate-700 text-sm truncate max-w-[200px]">${pedido.destinoNome} <span class="block text-xs font-normal text-slate-400">Destino</span></p>
                        </div>
                    </div>
                </div>
                <button onclick="motoristaAceitaPedido(${pedido.id})" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold shadow-lg shadow-slate-900/30 hover:bg-blue-600 transition-colors duration-300 flex justify-center items-center gap-2">
                    <i class="fas fa-check-circle"></i> Aceitar Entrega
                </button>
            </div>
        `).join('');
    }

    function motoristaAceitaPedido(idDoPedido) {
        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        let index = pedidos.findIndex(p => p.id === idDoPedido);
        
        if (index !== -1) {
            pedidos[index].status = 'Aguardando Coleta';
            localStorage.setItem('pedidosDropi', JSON.stringify(pedidos));

            const p = pedidos[index];
            aceitarEntrega(
                p.id,
                "Encomenda de " + p.cliente,
                "https://images.unsplash.com/photo-1615460549969-36fa19521a4f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60",
                p.origemNome,
                p.destinoNome,
                p.valorFinal
            );
        }
    }

    // --- SISTEMA DE ENTREGAS ATIVAS DO PERUEIRO ---
    // entregasAceitas é persistida no localStorage para sobreviver a navegações
    function getEntregas() {
        return JSON.parse(localStorage.getItem('entregasAceitas')) || [];
    }
    function saveEntregas(arr) {
        localStorage.setItem('entregasAceitas', JSON.stringify(arr));
    }

    function aceitarEntrega(idPedido, nomeItem, urlFoto, origemNome, destinoNome, valor) {
        let arr = getEntregas();
        // Evita duplicatas
        if (arr.find(e => e.id === idPedido)) {
            goTo('driver-active');
            return;
        }
        arr.push({ id: idPedido, nome: nomeItem, foto: urlFoto, status: 'Aguardando Coleta', origemNome, destinoNome, valor });
        saveEntregas(arr);
        renderizarFotos();
        goTo('driver-active');
    }

    function renderizarFotos() {
        const container = document.getElementById('lista-fotos-entregas');
        if (!container) return;

        let arr = getEntregas();

        // Atualiza badge do contador
        const badge = document.getElementById('badge-ativas');
        const countEl = document.getElementById('count-ativas');
        if (badge && countEl) {
            if (arr.length > 0) {
                badge.classList.remove('hidden');
                countEl.textContent = arr.length;
            } else {
                badge.classList.add('hidden');
            }
        }

        if (arr.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-dashed border-slate-300">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box-open text-4xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500 font-bold text-lg mb-1">Nenhuma entrega ativa.</p>
                    <p class="text-slate-400 text-sm">Aceite uma coleta no Radar para ela aparecer aqui.</p>
                    <button onclick="goTo('dash-driver')" class="mt-6 bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                        <i class="fas fa-search-location mr-2"></i> Buscar Coletas
                    </button>
                </div>`;
            return;
        }

        const statusIcon = {
            'Aguardando Coleta': { icon: 'fa-map-pin', color: 'text-blue-500', bg: 'bg-blue-50', border: 'border-blue-200' },
            'Em Trânsito':       { icon: 'fa-truck-fast', color: 'text-purple-500', bg: 'bg-purple-50', border: 'border-purple-200' },
            'Chegando ao Destino':{ icon: 'fa-map-marker-alt', color: 'text-orange-500', bg: 'bg-orange-50', border: 'border-orange-200' },
            'Entregue':          { icon: 'fa-check-circle', color: 'text-green-500', bg: 'bg-green-50', border: 'border-green-200' },
        };

        container.innerHTML = arr.map((entrega, index) => {
            const s = statusIcon[entrega.status] || statusIcon['Aguardando Coleta'];
            const repasseValor = (parseFloat(entrega.valor || 0) * 0.85).toLocaleString('pt-BR', {minimumFractionDigits:2});
            return `
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden flex flex-col hover:-translate-y-1 transition-transform duration-300">
                <!-- Cabeçalho colorido com status -->
                <div class="${s.bg} ${s.border} border-b px-6 py-4 flex items-center justify-between">
                    <span class="flex items-center gap-2 font-black text-slate-700 text-sm">
                        <i class="fas ${s.icon} ${s.color} text-lg"></i> ${entrega.status}
                    </span>
                    <span class="bg-white font-black text-xs text-slate-600 px-3 py-1 rounded-full shadow-sm border border-slate-100">#DRP-${entrega.id}</span>
                </div>

                <div class="p-6 flex flex-col gap-5 flex-1">
                    <!-- Rota -->
                    <div class="space-y-3 relative before:absolute before:top-2 before:bottom-2 before:left-[7px] before:w-0.5 before:bg-slate-200">
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-4 h-4 rounded-full bg-blue-500 border-2 border-white shadow shrink-0"></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Coletar em</p>
                                <p class="font-bold text-slate-700 text-sm leading-tight">${entrega.origemNome || '—'}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-4 h-4 rounded-full bg-slate-800 border-2 border-white shadow shrink-0"></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Entregar em</p>
                                <p class="font-bold text-slate-700 text-sm leading-tight">${entrega.destinoNome || '—'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Valor do repasse -->
                    <div class="bg-green-50 rounded-2xl px-5 py-4 border border-green-100 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Seu Ganho (85%)</p>
                            <p class="text-2xl font-black text-green-600">R$ ${repasseValor}</p>
                        </div>
                        <i class="fas fa-wallet text-3xl text-green-200"></i>
                    </div>

                    <!-- Select de status -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Atualizar Status</label>
                        <select onchange="mudarStatus(${index}, this.value, ${entrega.id})"
                            class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-bold text-slate-700 cursor-pointer focus:border-blue-500 transition-all">
                            <option value="Aguardando Coleta" ${entrega.status === 'Aguardando Coleta' ? 'selected' : ''}>📍 Aguardando Coleta</option>
                            <option value="Em Trânsito"       ${entrega.status === 'Em Trânsito'       ? 'selected' : ''}>🚚 Em Trânsito</option>
                            <option value="Chegando ao Destino" ${entrega.status === 'Chegando ao Destino' ? 'selected' : ''}>🗺️ Chegando ao Destino</option>
                            <option value="Entregue"          ${entrega.status === 'Entregue'          ? 'selected' : ''}>✅ Entregue</option>
                        </select>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function mudarStatus(index, novoStatus, idPedidoLocal) {
        let arr = getEntregas();
        if (!arr[index]) return;
        arr[index].status = novoStatus;
        saveEntregas(arr);

        // Sincroniza com o pedido do cliente no localStorage
        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        let indexGeral = pedidos.findIndex(p => p.id === idPedidoLocal);
        let valorDoPedido = arr[index].valor || 0;
        if (indexGeral !== -1) {
            pedidos[indexGeral].status = novoStatus;
            valorDoPedido = pedidos[indexGeral].valorFinal;
            localStorage.setItem('pedidosDropi', JSON.stringify(pedidos));
        }

        // Atualiza ganhos em tempo real
        atualizarGanhosPerueiro();
        // Atualiza painel cliente também
        atualizarPainelCliente();
        atualizarPagamentosCliente();

        if (novoStatus === 'Entregue') {
            const ganho = (parseFloat(valorDoPedido) * 0.85).toLocaleString('pt-BR', {minimumFractionDigits:2});
            setTimeout(() => {
                alert(`🎉 Entrega #DRP-${idPedidoLocal} finalizada!\n💰 R$ ${ganho} adicionados aos seus ganhos.`);
                // Remove da lista de ativas
                let arr2 = getEntregas();
                arr2.splice(index, 1);
                saveEntregas(arr2);
                renderizarFotos();
                atualizarGanhosPerueiro();
                goTo('driver-earnings');
            }, 400);
        } else {
            renderizarFotos();
        }
    }

    // --- GANHOS DO PERUEIRO (dinâmico, baseado em pedidos Entregues) ---
    function atualizarGanhosPerueiro() {
        const TAXA = 0.85; // 85% vai ao perueiro
        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];

        // Pedidos que o perueiro entregou (status = Entregue) e estão no histórico de entregas aceitas
        // Usamos todos os pedidos Entregues como proxy (sistema demo)
        let entregues = pedidos.filter(p => p.status === 'Entregue');
        let emAndamento = (JSON.parse(localStorage.getItem('entregasAceitas')) || []).length;

        let totalGanho = entregues.reduce((s, p) => s + (parseFloat(p.valorFinal || 0) * TAXA), 0);
        const fmt = v => v.toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});

        const elSaldo    = document.getElementById('earn-saldo');
        const elTotal    = document.getElementById('earn-total');
        const elCount    = document.getElementById('earn-count');
        const elAnd      = document.getElementById('earn-andamento');
        const elHist     = document.getElementById('earn-historico');

        if (elSaldo) elSaldo.innerHTML = `${fmt(totalGanho)} <span class="text-2xl text-slate-400">R$</span>`;
        if (elTotal) elTotal.innerHTML = `${fmt(totalGanho)} <span class="text-base text-slate-400">R$</span>`;
        if (elCount) elCount.textContent = entregues.length;
        if (elAnd)   elAnd.textContent   = emAndamento;

        if (elHist) {
            if (entregues.length === 0) {
                elHist.innerHTML = `
                    <div class="text-center py-10 text-slate-400">
                        <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                        <p class="font-medium">Nenhuma entrega concluída ainda.</p>
                    </div>`;
            } else {
                elHist.innerHTML = entregues.slice().reverse().map(p => {
                    const ganho = (parseFloat(p.valorFinal || 0) * TAXA);
                    return `
                    <div class="flex justify-between items-center p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-green-50 hover:border-green-100 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center text-xl shrink-0">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Entrega #DRP-${p.id}</p>
                                <p class="text-xs text-slate-400">${p.destinoNome || 'Destino'}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-green-600 text-lg block">+R$ ${fmt(ganho)}</span>
                            <span class="text-xs text-slate-400">85% do frete</span>
                        </div>
                    </div>`;
                }).join('');
            }
        }
    }

    // --- ACEITAR CORRIDA (card antigo do radar) ---
    function aceitarCorrida() {
        const origem  = document.getElementById('driver-origem')?.textContent || 'Origem';
        const destino = document.getElementById('driver-destino')?.textContent || 'Destino';
        const preco   = document.getElementById('driver-preco')?.textContent.replace('R$ ','') || '0';

        const idFicticio = Math.floor(Math.random() * 9000) + 1000;
        aceitarEntrega(idFicticio, 'Pacote', 
            'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
            origem, destino, preco);

        showToast('🚀 Entrega aceita! Dirija-se ao local de coleta.', 'success');
    }

    // --- SAQUE DO PERUEIRO ---
    function confirmarSaque() {
        const val = parseFloat(document.getElementById('valor-saque')?.value || 0);
        if (!val || val < 10) {
            showToast('⚠️ Valor mínimo para saque é R$ 10,00.', 'warning');
            return;
        }
        fecharModal('modal-saque');
        showToast(`💰 Saque de R$ ${val.toLocaleString('pt-BR',{minimumFractionDigits:2})} solicitado! Processamento em até 1 dia útil.`, 'success');
    }
