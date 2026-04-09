
    // --- RASTREAMENTO REAL POR ID ---
    function rastrearPedido() {
        const input  = document.getElementById('track-input');
        const result = document.getElementById('track-result');
        if (!input || !result) return;

        const raw = input.value.trim().toUpperCase().replace('DRP-', '');
        const id  = parseInt(raw, 10);

        if (!raw) {
            showToast('⚠️ Digite um código de rastreio.', 'warning');
            return;
        }

        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        let pedido  = pedidos.find(p => p.id === id);

        result.classList.remove('hidden');

        const statusMap = {
            'Pendente':             { icon: 'fa-hourglass-start', cor: 'text-yellow-600',  bg: 'bg-yellow-50',  border: 'border-yellow-200', label: 'Aguardando perueiro aceitar' },
            'Aguardando Coleta':    { icon: 'fa-motorcycle',      cor: 'text-blue-600',    bg: 'bg-blue-50',    border: 'border-blue-200',   label: 'Perueiro a caminho para coleta' },
            'Em Trânsito':          { icon: 'fa-truck-fast',      cor: 'text-purple-600',  bg: 'bg-purple-50',  border: 'border-purple-200', label: 'Encomenda em movimento' },
            'Chegando ao Destino':  { icon: 'fa-map-marker-alt',  cor: 'text-orange-600',  bg: 'bg-orange-50',  border: 'border-orange-200', label: 'Quase lá! Chegando ao destino' },
            'Entregue':             { icon: 'fa-check-circle',    cor: 'text-green-600',   bg: 'bg-green-50',   border: 'border-green-200',  label: 'Entregue com sucesso 🎉' },
        };

        if (!pedido) {
            result.innerHTML = `
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-red-100 text-left">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-search text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-xl text-slate-800">Código não encontrado</h3>
                            <p class="text-slate-500 text-sm">Verifique se o código está correto.</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 bg-slate-50 p-3 rounded-xl mt-2">💡 O código de rastreio aparece na confirmação do pedido. Formato: DRP-1234</p>
                </div>`;
            return;
        }

        const st = statusMap[pedido.status] || statusMap['Pendente'];
        const progresso = { 'Pendente': 10, 'Aguardando Coleta': 30, 'Em Trânsito': 60, 'Chegando ao Destino': 85, 'Entregue': 100 };
        const pct = progresso[pedido.status] || 10;

        result.innerHTML = `
            <div class="bg-white p-8 rounded-3xl shadow-lg border ${st.border} text-left">
                <div class="${st.bg} rounded-2xl p-5 mb-6 flex items-center gap-4 border ${st.border}">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm">
                        <i class="fas ${st.icon} ${st.cor} text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Status Atual</p>
                        <h3 class="font-black text-xl ${st.cor}">${pedido.status}</h3>
                        <p class="text-sm text-slate-500">${st.label}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between text-xs font-bold text-slate-400 mb-2">
                        <span>Progresso da entrega</span><span>${pct}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full bg-blue-500 transition-all duration-700" style="width:${pct}%"></div>
                    </div>
                </div>

                <div class="space-y-3 mb-5 text-sm border-t border-slate-100 pt-5">
                    <div class="flex justify-between"><span class="text-slate-400 font-medium">Código</span><span class="font-black text-blue-900">#DRP-${pedido.id}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400 font-medium">Origem</span><span class="font-semibold text-slate-700 text-right max-w-[60%]">${pedido.origemNome}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400 font-medium">Destino</span><span class="font-semibold text-slate-700 text-right max-w-[60%]">${pedido.destinoNome}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400 font-medium">Distância</span><span class="font-semibold text-slate-700">~${pedido.distancia} km</span></div>
                    <div class="flex justify-between"><span class="text-slate-400 font-medium">Tempo est.</span><span class="font-semibold text-slate-700">${pedido.tempo} min</span></div>
                    <div class="flex justify-between"><span class="text-slate-400 font-medium">Valor</span><span class="font-black text-green-600">R$ ${parseFloat(pedido.valorFinal).toLocaleString('pt-BR',{minimumFractionDigits:2})}</span></div>
                </div>

                ${pedido.status === 'Entregue' ? `<div class="bg-green-50 border border-green-100 rounded-2xl p-4 text-center text-green-700 font-bold text-sm"><i class="fas fa-check-circle mr-2"></i>Entrega confirmada com sucesso!</div>` : `<div class="bg-blue-50 border border-blue-100 rounded-2xl p-3 text-center text-blue-600 text-xs font-semibold flex items-center justify-center gap-2"><i class="fas fa-satellite-dish animate-pulse"></i> Acompanhando em tempo real...</div>`}
            </div>`;
    }

    function simulateTrack() {
        const result = document.getElementById('track-result');
        if (result) result.classList.remove('hidden');
    }

    // --- TRACKING: permitir busca ao pressionar Enter ---
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const ti = document.getElementById('track-input');
            if (ti && document.activeElement === ti) rastrearPedido();
        }
    });
