
    // --- SISTEMA DE NAVEGAÇÃO ---
    function goTo(pageId) {
        const loader = document.getElementById('loader');
        loader.classList.replace('opacity-0', 'opacity-100');
        
        setTimeout(() => {
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            const target = document.getElementById(pageId);
            target.classList.add('active');
            
            const isPublic = ['home', 'login', 'register', 'tracking'].includes(pageId);
            document.getElementById('nav-public').style.display = isPublic ? 'flex' : 'none';
            
            window.scrollTo(0,0);
            loader.classList.replace('opacity-100', 'opacity-0');

            // Força a atualização do painel se for para as telas do cliente
            if(pageId === 'dash-client' || pageId === 'client-history') {
                atualizarPainelCliente();
            }
            if(pageId === 'client-payments') {
                atualizarPagamentosCliente();
            }
            if(pageId === 'driver-active') {
                renderizarFotos();
            }
            if(pageId === 'driver-earnings') {
                atualizarGanhosPerueiro();
            }
        }, 600);
    }

    // --- TIPO DE USUÁRIO NO LOGIN ---
    let userType = 'client';
    function setType(type) {
        userType = type;
        const clientBtn = document.getElementById('type-client');
        const driverBtn = document.getElementById('type-driver');
        
        if(type === 'client') {
            clientBtn.classList.add('bg-white', 'text-dropi', 'shadow-sm');
            clientBtn.classList.remove('text-slate-500');
            driverBtn.classList.remove('bg-white', 'text-dropi', 'shadow-sm');
            driverBtn.classList.add('text-slate-500');
        } else {
            driverBtn.classList.add('bg-white', 'text-dropi', 'shadow-sm');
            driverBtn.classList.remove('text-slate-500');
            clientBtn.classList.remove('bg-white', 'text-dropi', 'shadow-sm');
            clientBtn.classList.add('text-slate-500');
        }
    }

    function handleLogin() {
        const email = document.querySelector('input[type="email"]').value;
        const pass = document.querySelector('input[type="password"]').value;

        if (email === "" || pass === "") {
            alert("Por favor, preencha os campos.");
            return;
        }

        let nomeExtraido = email.split('@')[0]; 
        nomeExtraido = nomeExtraido.charAt(0).toUpperCase() + nomeExtraido.slice(1);
        
        localStorage.setItem('usuarioLogado', nomeExtraido);

        if (email === "cliente@dropi.com" && pass === "123") {
            userType = 'client';
            goTo('dash-client');
        } 
        else if (email === "motorista@dropi.com" && pass === "123") {
            userType = 'driver';
            goTo('dash-driver');
        } 
        else {
            if(userType === 'client') goTo('dash-client');
            else goTo('dash-driver');
        }
    }

    // --- STEPPER (Novo Pedido Cliente) ---
    function nextStep(s) {
        document.getElementById('step-1').classList.add('hidden');
        document.getElementById('step-2').classList.add('hidden');
        document.getElementById('step-' + s).classList.remove('hidden');
        
        if(s === 2) {
            document.getElementById('step-i-2').classList.replace('bg-slate-300', 'bg-dropi');
        }
    }

    // NOVA FUNÇÃO: Voltar passo de forma inteligente
    function voltarPasso() {
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const bolinha1 = document.getElementById('step-i-1');
        const bolinha2 = document.getElementById('step-i-2');

        // Se o Passo 2 estiver visível, volta para o Passo 1
        if (!step2.classList.contains('hidden')) {
            step2.classList.add('hidden');
            step1.classList.remove('hidden');
            
            // Arruma as cores das bolinhas
            bolinha1.classList.remove('bg-slate-300');
            bolinha1.classList.add('bg-dropi');
            
            bolinha2.classList.remove('bg-dropi');
            bolinha2.classList.add('bg-slate-300');
        } else {
            // Se já estiver no Passo 1, volta pro dashboard
            goTo('dash-client');
        }
    }

    // --- 🌍 APIs DE ROTAS E GEOLOCALIZAÇÃO ---
    let rotaSalva = null;
    let precoSalvo = 0;
    let coordsSalvas = {};

    // 1. Converte o endereço em Latitude/Longitude
    async function buscarCoordenadas(endereco) {
        try {
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}&limit=1`;
            const resposta = await fetch(url);
            const dados = await resposta.json();
            if(dados && dados.length > 0) return { lat: dados[0].lat, lon: dados[0].lon, nomeExato: dados[0].display_name.split(',')[0] };
            return null;
        } catch(e) { console.error("Erro na geolocalização", e); return null; }
    }

    // 2. Calcula Distância em KM e Tempo em Minutos
    async function calcularDistancia(coord1, coord2) {
        try {
            const url = `https://router.project-osrm.org/route/v1/driving/${coord1.lon},${coord1.lat};${coord2.lon},${coord2.lat}?overview=false`;
            const resposta = await fetch(url);
            const dados = await resposta.json();
            
            if(dados.routes && dados.routes.length > 0) {
                return {
                    distanciaKm: (dados.routes[0].distance / 1000).toFixed(1),
                    tempoMin: Math.round(dados.routes[0].duration / 60)
                };
            }
            return null;
        } catch(e) { console.error("Erro no roteamento", e); return null; }
    }

    // 3. CALCULA A ROTA ANTES DE IR PARA O PASSO 2
    async function calcularEAvancar() {
        const btn = event.currentTarget;
        const textoOriginal = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculando Rota...';
        btn.disabled = true;

        const estOrigem = document.getElementById('input-estado-origem') ? document.getElementById('input-estado-origem').value : "Minas Gerais";
        const cidOrigem = document.getElementById('input-cidade-origem') ? document.getElementById('input-cidade-origem').value : "Barão de Cocais";
        const cidDestino = document.getElementById('input-cidade-destino') ? document.getElementById('input-cidade-destino').value : "Catas Altas";
        const ruaDestino = document.getElementById('input-rua-destino') ? document.getElementById('input-rua-destino').value : "";

        if(!ruaDestino) {
            alert("Por favor, preencha a rua de destino.");
            btn.innerHTML = textoOriginal;
            btn.disabled = false;
            return;
        }

        // Limpa o estado para o GPS não confundir
        const estadoLimpo = estOrigem.split('(')[0].trim();

        // Agora busca no Brasil!
        const enderecoOrigemCompleto = `${cidOrigem}, ${estadoLimpo}, Brasil`;
        const enderecoDestinoCompleto = `${ruaDestino}, ${cidDestino}, ${estadoLimpo}, Brasil`;

        let coordOrigem = await buscarCoordenadas(enderecoOrigemCompleto);
        let coordDestino = await buscarCoordenadas(enderecoDestinoCompleto);

        // --- SISTEMA SALVA-VIDAS ---
        if (!coordOrigem) {
            console.log("Tentando achar só a cidade de origem...");
            coordOrigem = await buscarCoordenadas(`${cidOrigem}, ${estadoLimpo}, Brasil`);
        }
        if (!coordDestino) {
            console.log("Tentando achar só a cidade de destino...");
            coordDestino = await buscarCoordenadas(`${cidDestino}, ${estadoLimpo}, Brasil`);
        }

        if(!coordOrigem || !coordDestino) {
            alert("Não conseguimos encontrar as cidades no GPS. Verifique se os nomes estão corretos.");
            btn.innerHTML = textoOriginal;
            btn.disabled = false;
            return;
        }

        const rota = await calcularDistancia(coordOrigem, coordDestino);
        
        if(!rota) {
            alert("Erro ao traçar a rota.");
            btn.innerHTML = textoOriginal;
            btn.disabled = false;
            return;
        }

        const taxaBase = 15; 
        const custoPorKm = 0.50;
        precoSalvo = (taxaBase + (rota.distanciaKm * custoPorKm)).toFixed(2);
        rotaSalva = rota;
        coordsSalvas = { origem: coordOrigem, destino: coordDestino, cidOrigem, estOrigem, ruaDestino, cidDestino };

        // Exibe o preço no Passo 2
        const precoDisplay = document.querySelector('#step-2 h4');
        if(precoDisplay) precoDisplay.innerHTML = `R$ ${precoSalvo.replace('.', ',')}`;

        btn.innerHTML = textoOriginal;
        btn.disabled = false;
        nextStep(2);
    }

    // --- NOVA FUNÇÃO DE CONFIRMAR PEDIDO ---
    function confirmOrder() {
        if(!rotaSalva || !precoSalvo) {
            alert("Erro: Rota não calculada. Volte e tente novamente.");
            return;
        }

        const nomeDoCliente = localStorage.getItem('usuarioLogado') || "Cliente";

        const novoPedido = {
            id: Math.floor(Math.random() * 10000),
            cliente: nomeDoCliente, 
            status: "Pendente",
            origemNome: `${coordsSalvas.cidOrigem} (${coordsSalvas.estOrigem})`,
            destinoNome: `${coordsSalvas.ruaDestino}, ${coordsSalvas.cidDestino}`,
            latCliente: coordsSalvas.origem.lat, 
            lngCliente: coordsSalvas.origem.lon,
            latDestino: coordsSalvas.destino.lat,
            lngDestino: coordsSalvas.destino.lon,
            distancia: rotaSalva.distanciaKm,
            tempo: rotaSalva.tempoMin,
            valorFinal: precoSalvo
        };

        let pedidos = JSON.parse(localStorage.getItem('pedidosDropi')) || [];
        pedidos.push(novoPedido);
        localStorage.setItem('pedidosDropi', JSON.stringify(pedidos));

        alert(`Sucesso! Distância calculada: ${rotaSalva.distanciaKm}km. Valor: ${precoSalvo} Reais.\nSua solicitação foi enviada aos perueiros próximos.`);
        
        rotaSalva = null; precoSalvo = 0; // Limpa cache
        atualizarPainelCliente(); 
        goTo('dash-client');
    }

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

    // --- LOOP EM TEMPO REAL ---
    setInterval(() => {
        carregarPedidosDisponiveis();
        atualizarPainelCliente();
        atualizarPagamentosCliente();
        renderizarFotos();
        atualizarGanhosPerueiro();
    }, 2000);

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

    // --- FUNÇÕES AUXILIARES DE PREVENÇÃO DE ERROS ---
    function toggleDriverFields() {
        const fields = document.getElementById('driver-extra-fields');
        if (fields) fields.classList.toggle('hidden');
    }

    function simulateTrack() {
        const result = document.getElementById('track-result');
        if (result) result.classList.remove('hidden');
    }

    // --- INIT LANDING ANIMATION ---
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('nav-public');
        if(window.scrollY > 50) {
            nav.classList.add('py-2', 'shadow-lg');
        } else {
            nav.classList.remove('py-2', 'shadow-lg');
        }
    });

    // --- CORREÇÃO DE REDIRECIONAMENTO AO ATUALIZAR (F5) ---
    if (window.location.search.includes('logado=cliente')) {
        window.history.replaceState(null, null, window.location.pathname);
        goTo('dash-client'); 
    } else if (window.location.search.includes('logado=perueiro')) {
        window.history.replaceState(null, null, window.location.pathname);
        goTo('dash-driver'); 
    }

    // ================================================================
    // === FUNÇÕES ADICIONAIS — FUNCIONALIDADES COMPLETAS ==============
    // ================================================================

    // --- ADMIN: SALVAR LOGÍSTICA ---
    function salvarLogistica() {
        showToast('✅ Configurações de logística salvas com sucesso!', 'success');
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

    // --- MODAIS: ABRIR / FECHAR ---
    function abrirModal(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    }
    function fecharModal(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.add('hidden'); document.body.style.overflow = ''; }
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

    // --- FUNCIONA PAGE: mostrar a partir da nav ---
    // A secção "funciona" já usa class="page" sem hidden; goTo() trata corretamente.

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

    // --- ESTENDER O LOOP EM TEMPO REAL ---
    setInterval(() => {
        verificarNovosPedidos();
        atualizarBadgeRadar();
    }, 3000);

    // --- SALVAR LOGÍSTICA DO ADMIN (com feedback) ---
    // (já definido acima como salvarLogistica)

    // --- TRACKING: permitir busca ao pressionar Enter ---
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const ti = document.getElementById('track-input');
            if (ti && document.activeElement === ti) rastrearPedido();
        }
    });

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

    // --- CORREÇÃO DE REDIRECIONAMENTO AO ATUALIZAR (F5) ---
    if (window.location.search.includes('logado=cliente')) {
        window.history.replaceState(null, null, window.location.pathname);
        goTo('dash-client'); 
    } else if (window.location.search.includes('logado=perueiro')) {
        window.history.replaceState(null, null, window.location.pathname);
        goTo('dash-driver'); 
    }
