
    // --- LOOP EM TEMPO REAL (2s) ---
    setInterval(() => {
        carregarPedidosDisponiveis();
        atualizarPainelCliente();
        atualizarPagamentosCliente();
        renderizarFotos();
        atualizarGanhosPerueiro();
    }, 2000);

    // --- NOTIFICAÇÃO VISUAL DE NOVO PEDIDO PARA PERUEIRO ---
    let ultimaContPedidos = 0;
    function verificarNovosPedidos() {
        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        let pendentes = pedidos.filter(p => p.status === 'Pendente').length;
        if (pendentes > ultimaContPedidos && ultimaContPedidos !== 0) {
            showToast('📦 Novo pedido disponível na sua área!', 'info');
        }
        ultimaContPedidos = pendentes;
    }

    // --- ATUALIZAR BADGE DE PEDIDOS NO RADAR (sidebar do perueiro) ---
    function atualizarBadgeRadar() {
        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        let pendentes = pedidos.filter(p => p.status === 'Pendente').length;
        // Atualiza todos os badges de "Buscar Coletas" se existirem
        document.querySelectorAll('[data-badge-radar]').forEach(el => {
            el.textContent = pendentes > 0 ? pendentes : '';
            el.style.display = pendentes > 0 ? 'inline-flex' : 'none';
        });
    }

    // --- LOOP EM TEMPO REAL (3s) ---
    setInterval(() => {
        verificarNovosPedidos();
        atualizarBadgeRadar();
    }, 3000);
