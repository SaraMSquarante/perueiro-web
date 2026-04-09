
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
