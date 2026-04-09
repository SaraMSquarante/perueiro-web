<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropi Express | Logística Inteligente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        :root { --dropi-blue: #002B5B; --dropi-accent: #3b82f6; --dropi-success: #22c55e; }
        .bg-dropi { background-color: var(--dropi-blue); }
        .text-dropi { color: var(--dropi-blue); }
        .page { display: none; opacity: 0; transform: translateY(10px); transition: all 0.4s ease; }
        .page.active { display: block; opacity: 1; transform: translateY(0); }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .sidebar-link { transition: all 0.2s; border-left: 4px solid transparent; }
        .sidebar-link.active { background: rgba(255,255,255,0.1); border-left-color: #fff; }
        input:focus, select:focus, textarea:focus { border-color: var(--dropi-accent) !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #002B5B; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <div id="loader" class="fixed inset-0 bg-dropi z-[999] flex items-center justify-center transition-opacity duration-500 pointer-events-none opacity-0">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-white"></div>
    </div>

    <nav id="nav-public" class="fixed top-0 w-full z-50 glass border-b border-slate-200 px-6 py-4 flex justify-between items-center transition-all">
       <div class="hidden md:flex items-center gap-8 font-semibold text-slate-600">
    <a href="#" onclick="goTo('funciona')" class="hover:text-dropi cursor-pointer">Como Funciona</a>
    <a href="#" onclick="goTo('tracking')" class="hover:text-dropi">Rastrear</a>
    </div>
       
        <div class="flex items-center gap-4">
            <button onclick="goTo('login')" class="font-bold text-dropi px-4">Entrar</button>
            <button onclick="goTo('register')" class="bg-dropi text-white px-6 py-2.5 rounded-full font-bold shadow-lg shadow-blue-900/20 hover:scale-105 transition">Criar Conta</button>
        </div>
    </nav>
    <section id="dash-admin" class="page min-h-screen bg-slate-50">
        <div class="flex flex-col md:flex-row">
            <aside class="w-full md:w-72 bg-slate-900 text-white p-8 md:min-h-screen">
                <div class="flex items-center gap-3 mb-12">
                    <div class="bg-red-500 p-2 rounded-xl"><i class="fas fa-shield-alt text-white"></i></div>
                    <span class="text-2xl font-black italic tracking-tighter">DROPI <span class="text-red-500">ADMIN</span></span>
                </div>
                <nav class="space-y-4">
                    <a href="#" class="sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold bg-red-600"><i class="fas fa-map-marked-alt"></i> Logística (Estados)</a>
                    <a href="#" class="sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100"><i class="fas fa-users"></i> Usuários</a>
                    <button onclick="goTo('login')" class="p-4 text-red-400 flex items-center gap-4 mt-10"><i class="fas fa-sign-out-alt"></i> Sair</button>
                </nav>
            </aside>
            
            <main class="flex-1 p-6 md:p-12">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900">Configuração de Logística</h2>
                        <p class="text-slate-500 font-medium">Gerencie as tarifas e a disponibilidade por Estado/Província.</p>
                    </div>
                    <button onclick="salvarLogistica()" class="bg-dropi text-white px-8 py-4 rounded-2xl font-bold shadow-xl hover:scale-105 transition"><i class="fas fa-save mr-2"></i> Salvar Alterações</button>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-4 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-slate-400 text-xs uppercase font-bold border-b border-slate-50">
                                    <th class="p-4">Estado / Província</th>
                                    <th class="p-4">Status de Operação</th>
                                    <th class="p-4">Tarifa Base (Reais)</th>
                                    <th class="p-4">Tempo Est. Médio</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-semibold text-slate-700">
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                    <td class="p-4 flex items-center gap-3"><i class="fas fa-map-pin text-red-500"></i> Luanda</td>
                                    <td class="p-4">
                                        <select class="p-2 rounded-lg bg-slate-100 border-none outline-none font-bold text-green-600">
                                            <option value="ativo" selected>🟢 Ativo</option>
                                            <option value="inativo">🔴 Inativo</option>
                                        </select>
                                    </td>
                                    <td class="p-4"><input type="number" value="2000" class="w-24 p-2 bg-slate-100 border-2 border-transparent focus:border-blue-500 rounded-lg outline-none"></td>
                                    <td class="p-4"><input type="text" value="30 min" class="w-24 p-2 bg-slate-100 border-2 border-transparent focus:border-blue-500 rounded-lg outline-none"></td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                    <td class="p-4 flex items-center gap-3"><i class="fas fa-map-pin text-slate-400"></i> Benguela</td>
                                    <td class="p-4">
                                        <select class="p-2 rounded-lg bg-slate-100 border-none outline-none font-bold text-green-600">
                                            <option value="ativo" selected>🟢 Ativo</option>
                                            <option value="inativo">🔴 Inativo</option>
                                        </select>
                                    </td>
                                    <td class="p-4"><input type="number" value="3500" class="w-24 p-2 bg-slate-100 border-2 border-transparent focus:border-blue-500 rounded-lg outline-none"></td>
                                    <td class="p-4"><input type="text" value="2 horas" class="w-24 p-2 bg-slate-100 border-2 border-transparent focus:border-blue-500 rounded-lg outline-none"></td>
                                </tr>
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                    <td class="p-4 flex items-center gap-3"><i class="fas fa-map-pin text-slate-400"></i> Huambo</td>
                                    <td class="p-4">
                                        <select class="p-2 rounded-lg bg-slate-100 border-none outline-none font-bold text-red-500">
                                            <option value="ativo">🟢 Ativo</option>
                                            <option value="inativo" selected>🔴 Inativo (Em breve)</option>
                                        </select>
                                    </td>
                                    <td class="p-4"><input type="number" value="0" class="w-24 p-2 bg-slate-100 border-2 border-transparent focus:border-blue-500 rounded-lg outline-none" disabled></td>
                                    <td class="p-4"><input type="text" value="-" class="w-24 p-2 bg-slate-100 border-2 border-transparent focus:border-blue-500 rounded-lg outline-none" disabled></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </section>

    <section id="home" class="page active pt-32 pb-20">
        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="bg-blue-100 text-dropi px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-6 inline-block">Logística para Todos🇦🇴</span>
                <h1 class="text-6xl font-extrabold text-slate-900 leading-[1.1] mb-6">Suas encomendas na mão de quem <span class="text-blue-600">confia.</span></h1>
                <p class="text-lg text-slate-600 mb-10 leading-relaxed">Conectamos você aos perueiros mais rápidos do Brasil. Segurança total do clique até a entrega.</p>
                <div class="flex flex-wrap gap-4">
                    <button onclick="goTo('register')" class="bg-dropi text-white px-8 py-4 rounded-2xl font-bold text-lg flex items-center gap-3 shadow-xl">Começar agora <i class="fas fa-arrow-right"></i></button>
                    <div class="flex -space-x-3 items-center ml-4">
                        <img src="https://i.pravatar.cc/100?u=1" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                        <img src="https://i.pravatar.cc/100?u=2" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                        <img src="https://i.pravatar.cc/100?u=3" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                        <span class="ml-6 text-sm font-bold text-slate-500">+500 Perueiros ativos</span>
                    </div>
                </div>
            </div>
          <div class="relative">
    <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-200 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob"></div>
    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-purple-200 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob animation-delay-2000"></div>
    <img src="mascote-dropi.jpeg" alt="Entregador Dropi Express" class="relative rounded-3xl shadow-2xl w-2/3 h-auto object-cover border-4 border-white mx-auto">
</div>
    </section>

    <section id="funciona" class="page min-h-screen pt-32 pb-20 bg-white">
        <div class="container mx-auto px-6 max-w-5xl">
            <div class="text-center mb-16">
                <span class="bg-blue-100 text-dropi px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 inline-block">Passo a Passo</span>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4">Como o <span class="text-blue-600">Dropi Express</span> Funciona?</h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">Conectamos quem precisa enviar algo com perueiros de confiança que já estão no caminho. Rápido, seguro e sem burocracia.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 shadow-sm text-center card-hover">
                    <div class="bg-blue-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6"><i class="fas fa-box-open text-2xl text-blue-600"></i></div>
                    <h3 class="text-xl font-bold mb-3">1. Solicite o Envio</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Informe o local de coleta, destino e o que precisa ser levado. O app calcula uma estimativa de preço na hora.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 shadow-sm text-center card-hover">
                    <div class="bg-orange-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6"><i class="fas fa-truck-fast text-2xl text-orange-600"></i></div>
                    <h3 class="text-xl font-bold mb-3">2. Perueiro a Caminho</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Um motorista parceiro próximo aceita o seu pedido e vai até você para coletar a encomenda com segurança.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 shadow-sm text-center card-hover">
                    <div class="bg-green-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6"><i class="fas fa-map-location-dot text-2xl text-green-600"></i></div>
                    <h3 class="text-xl font-bold mb-3">3. Acompanhe Tudo</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Use o código de rastreio para acompanhar o trajeto em tempo real pelo mapa até a entrega ser confirmada.</p>
                </div>
            </div>

            <div class="bg-dropi text-white rounded-[3rem] p-10 text-center shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
                <h3 class="text-3xl font-black mb-6 relative z-10">Pronto para simplificar sua logística?</h3>
                <div class="flex flex-col sm:flex-row justify-center gap-4 relative z-10">
                    <button onclick="goTo('register')" class="bg-white text-dropi px-8 py-4 rounded-full font-bold shadow-lg hover:scale-105 transition">Criar Conta Grátis</button>
                    <button onclick="goTo('home')" class="border-2 border-white/20 text-white px-8 py-4 rounded-full font-bold hover:bg-white/10 transition">Voltar ao Início</button>
                </div>
            </div>
        </div>
    </section>

    <section id="login" class="page pt-40 min-h-screen bg-slate-50">
        <div class="max-w-md mx-auto bg-white p-10 rounded-[2.5rem] shadow-2xl shadow-slate-200">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Bem-vindo!</h2>
            <p class="text-slate-500 mb-8 font-medium">Faça login para gerenciar suas encomendas.</p>
            <form action="login.php" method="POST" class="space-y-5">
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">E-mail</label>
        <input type="email" name="email" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" placeholder="seu@email.com" required>
    </div>
    
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Senha</label>
        <input type="password" name="senha" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" placeholder="••••••••" required>
    </div>

    <button type="submit" class="w-full bg-dropi text-white font-bold text-lg p-4 rounded-2xl shadow-xl hover:scale-105 transition">Entrar na Conta</button>
</form>
               <p class="text-center font-semibold text-slate-500">Ainda não tem conta? <a href="#" onclick="goTo('register')" class="text-blue-600 cursor-pointer hover:underline">Criar agora</a></p>
            </div>
        </div>
    </section>

<section id="dash-client" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-dropi text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-blue-950/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-white p-2.5 rounded-2xl shadow-lg"><i class="fas fa-box text-dropi text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI</span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-client')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-chart-line w-6 text-xl"></i> Dashboard
                </button>
                <button onclick="goTo('new-order')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-plus-circle w-6 text-xl"></i> Novo Envio
                </button>
                <button onclick="goTo('client-history')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-history w-6 text-xl"></i> Histórico
                </button>
                <button onclick="goTo('client-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
                <button onclick="goTo('client-reviews')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('client-payments')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-credit-card w-6 text-xl"></i> Pagamentos
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome_usuario']); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', $_SESSION['nome_usuario'])[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-300 font-semibold hover:text-red-400">Sair</button>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-12 pb-6 border-b border-slate-200">
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400">Bem-vindo de volta,👋</span>
                    <h2 class="text-4xl font-black text-blue-950 mt-1"><?php echo isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Visitante'; ?></h2>
                </div>
                <button onclick="goTo('new-order')" class="bg-dropi text-white px-10 py-5 rounded-full font-bold shadow-2xl shadow-blue-900/40 flex items-center gap-3 hover:scale-105 hover:-translate-y-1 transition-all duration-300 text-lg">
                    <i class="fas fa-paper-plane"></i> Solicitar Novo Envio
                </button>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white p-9 rounded-[2.5rem] border border-slate-100 shadow-sm card-hover border-l-8 border-l-blue-500 relative overflow-hidden group">
                    <i class="fas fa-route absolute -right-6 -bottom-6 text-9xl text-slate-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                    <div class="relative z-10 flex flex-col gap-4">
                        <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">Total Envios</p>
                        <h4 id="dash-total-envios" class="text-5xl font-black text-blue-950">0</h4>
                        <span class="text-sm text-slate-500">Pacotes já enviados na plataforma</span>
                    </div>
                </div>
                <div class="bg-white p-9 rounded-[2.5rem] border border-slate-100 shadow-sm card-hover border-l-8 border-l-orange-500 relative overflow-hidden group">
                    <i class="fas fa-clock absolute -right-6 -bottom-6 text-9xl text-slate-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                    <div class="relative z-10 flex flex-col gap-4">
                        <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">Pendentes / Em Trânsito</p>
                        <h4 id="dash-pendentes" class="text-5xl font-black text-orange-600">0</h4>
                        <span class="text-sm text-orange-700/80 font-medium bg-orange-100 px-3 py-1 rounded-full w-fit">Aguardando Coleta / Caminho</span>
                    </div>
                </div>
                <div class="bg-white p-9 rounded-[2.5rem] border border-slate-100 shadow-sm card-hover border-l-8 border-l-green-600 relative overflow-hidden group">
                    <i class="fas fa-money-bill-wave absolute -right-6 -bottom-6 text-9xl text-slate-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                    <div class="relative z-10 flex flex-col gap-4">
                        <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">Gasto Total</p>
                        <h4 id="dash-gasto" class="text-5xl font-black text-green-700">0,00 <span class="text-xl">Reais</span></h4>
                        <span class="text-sm text-slate-500">Investimento realizado em logística</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="p-9 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-2xl font-black text-blue-950 flex items-center gap-3"><i class="fas fa-satellite text-blue-600"></i> Rastreio em Tempo Real</h3>
                    <button class="bg-blue-100 text-blue-700 text-sm font-bold px-6 py-3 rounded-full hover:bg-blue-200 transition-colors">
                        <i class="fas fa-map-marked-alt mr-2"></i> Ver Mapa Completo
                    </button>
                </div>
                
                <div id="dash-rastreio-container" class="p-12 flex flex-col items-center text-center gap-8 py-20 bg-white">
                    <div class="bg-slate-100 p-8 rounded-full shadow-inner border border-slate-200">
                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076470.png" alt="Sem dados" class="w-32 h-32 opacity-80">
                    </div>
                    <div>
                        <h4 class="text-2xl font-black text-blue-950 mb-3">Tudo limpo por aqui! 👋</h4>
                        <p class="text-slate-600 max-w-md mx-auto leading-relaxed">Você não tem nenhuma encomenda ativa no momento. Que tal começar um novo envio agora mesmo?</p>
                    </div>
                    <button onclick="goTo('new-order')" class="bg-dropi text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2 shadow-lg hover:scale-105 transition-all">
                        <i class="fas fa-plus-circle"></i> Começar Primeiro Envio
                    </button>
                </div>
            </div>
        </main>
    </div>
</section>

<section id="client-history" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-dropi text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-blue-950/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-white p-2.5 rounded-2xl shadow-lg"><i class="fas fa-box text-dropi text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI</span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-client')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-chart-line w-6 text-xl"></i> Dashboard
                </button>
                <button onclick="goTo('new-order')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-plus-circle w-6 text-xl"></i> Novo Envio
                </button>
                <button onclick="goTo('client-history')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-history w-6 text-xl"></i> Histórico
                </button>
                <button onclick="goTo('client-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
                <button onclick="goTo('client-payments')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-credit-card w-6 text-xl"></i> Pagamentos
                </button>
            </nav>
        
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome_usuario']); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', $_SESSION['nome_usuario'])[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-300 font-semibold hover:text-red-400">Sair</button>
                </div>
            </div>
        </aside>
        
        <main class="w-full p-8 md:p-12">
            <header class="mb-12">
                <h2 class="text-4xl font-black text-blue-950 mb-2 flex items-center gap-3"><i class="fas fa-history text-blue-600"></i> Histórico Completo</h2>
                <p class="text-slate-500 mb-8 text-lg">Revise todos os seus envios realizados com sucesso ou cancelados.</p>
            </header>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-8 flex flex-col md:flex-row gap-4 items-center">
                <input type="text" placeholder="🔍 Buscar código (Ex: DRP-1029)..." class="w-full flex-1 p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition">
                <select class="w-full md:w-auto p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition text-slate-600">
                    <option value="">Filtrar Status</option>
                    <option value="delivered">Entregue</option>
                    <option value="canceled">Cancelado</option>
                </select>
                <button class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:scale-105 transition w-full md:w-auto">
                    Aplicar Filtros
                </button>
            </div>

            <div class="bg-white rounded-[2.5rem] p-6 shadow-xl border border-slate-100 overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-50">
                        <tr>
                            <th class="px-6 py-5 rounded-tl-2xl">CÓDIGO</th>
                            <th class="px-6 py-5">DATA SOLICITAÇÃO</th>
                            <th class="px-6 py-5"><i class="fas fa-map-marker-alt"></i> DESTINO</th>
                            <th class="px-6 py-5">STATUS</th>
                            <th class="px-6 py-5">VALOR PAGO</th>
                            <th class="px-6 py-5 rounded-tr-2xl">AÇÃO</th>
                        </tr>
                    </thead>
                    
                    <tbody id="tabela-historico-cliente">
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <div class="p-6 bg-slate-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 border border-slate-100">
                                    <i class="fas fa-box-open text-5xl opacity-40"></i>
                                </div>
                                <h4 class="text-lg font-bold text-slate-600 mb-1">Nenhum envio finalizado ainda.</h4>
                                <p class="text-slate-500 max-w-sm mx-auto">Assim que você completar seu primeiro envio, ele aparecerá nesta lista com todos os detalhes.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</section>

<section id="client-settings" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-dropi text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-blue-950/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-white p-2.5 rounded-2xl shadow-lg"><i class="fas fa-box text-dropi text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI</span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-client')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-chart-line w-6 text-xl"></i> Dashboard
                </button>
                <button onclick="goTo('new-order')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-plus-circle w-6 text-xl"></i> Novo Envio
                </button>
                <button onclick="goTo('client-history')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-history w-6 text-xl"></i> Histórico
                </button>
                <button onclick="goTo('client-settings')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
                <button onclick="goTo('client-payments')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-credit-card w-6 text-xl"></i> Pagamentos
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome_usuario']); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', $_SESSION['nome_usuario'])[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-300 font-semibold hover:text-red-400">Sair</button>
                </div>
            </div>
        </aside>
        
        <main class="w-full p-8 md:p-12">
            <header class="mb-12">
                <h2 class="text-4xl font-black text-blue-950 mb-2 flex items-center gap-3"><i class="fas fa-user-cog text-blue-600"></i> Gestão da Conta</h2>
                <p class="text-slate-500 mb-8 text-lg">Mantenha seus dados pessoais e credenciais de acesso atualizados para maior segurança.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-lg border border-slate-100 text-center flex flex-col items-center gap-6 h-fit">
                    <div class="relative group">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome_usuario']); ?>&size=128&background=002B5B&color=fff&rounded=true" alt="Foto Perfil" class="w-32 h-32 rounded-full border-4 border-slate-100 shadow-lg transition group-hover:opacity-70 group-hover:scale-105">
                        <button class="absolute bottom-0 right-0 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div>
                        <h4 class="text-2xl font-black text-blue-950"><?php echo $_SESSION['nome_usuario']; ?></h4>
                        <p class="text-slate-500 font-medium">Cliente DROPIExpress🇦🇴</p>
                    </div>
                    <span class="text-xs text-slate-400">Membro desde Março, 2026</span>
                </div>

                <div class="md:col-span-2 bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-white">
                   <form action="atualizar_perfil.php" method="POST" class="space-y-8">
                        <div>
                            <h3 class="text-2xl font-black text-blue-950 mb-1 flex items-center gap-2"><i class="fas fa-id-card text-slate-400"></i> Informações Pessoais</h3>
                            <p class="text-slate-500 mb-6">Como devemos te chamar na plataforma.</p>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Nome Completo</label>
                                    <div class="relative">
                                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        <input type="text" name="nome" value="<?php echo isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : ''; ?>" class="w-full pl-12 pr-4 p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Endereço de E-mail</label>
                                    <div class="relative">
                                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        <input type="email" name="email" placeholder="seu@email.com" class="w-full pl-12 pr-4 p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100">
                            <h3 class="text-2xl font-black text-blue-950 mb-1 flex items-center gap-2"><i class="fas fa-lock text-slate-400"></i> Segurança</h3>
                            <p class="text-slate-500 mb-6">Atualize sua credencial de acesso.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Nova Senha</label>
                                    <input type="password" name="nova_senha" placeholder="••••••••" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Confirmar Nova Senha</label>
                                    <input type="password" name="confirma_senha" placeholder="••••••••" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition">
                                </div>
                                <span class="md:col-span-2 text-xs text-slate-400 bg-slate-100 p-3 rounded-lg flex items-center gap-2"><i class="fas fa-info-circle text-blue-600"></i> Deixe em branco se não desejar alterar a senha atual.</span>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100 text-right">
                            <button type="submit" class="bg-blue-900 text-white px-10 py-5 rounded-full font-bold shadow-lg hover:scale-105 hover:-translate-y-1 transition-all duration-300 w-full md:w-auto text-lg flex items-center gap-2 justify-center">
                                <i class="fas fa-save"></i> Salvar Todas as Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</section>
<section id="client-reviews" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-dropi text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-blue-950/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-white p-2.5 rounded-2xl shadow-lg"><i class="fas fa-box text-dropi text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI</span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-client')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-chart-line w-6 text-xl"></i> Dashboard
                </button>
                <button onclick="goTo('new-order')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-plus-circle w-6 text-xl"></i> Novo Envio
                </button>
                <button onclick="goTo('client-history')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-history w-6 text-xl"></i> Histórico
                </button>
                <button onclick="goTo('client-reviews')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('client-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
                <button onclick="goTo('client-payments')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-credit-card w-6 text-xl"></i> Pagamentos
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome_usuario']); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', $_SESSION['nome_usuario'])[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-300 font-semibold hover:text-red-400">Sair</button>
                </div>
            </div>
        </aside>
        
        <main class="w-full p-8 md:p-12">
            <header class="mb-12">
                <h2 class="text-4xl font-black text-blue-950 mb-2 flex items-center gap-3"><i class="fas fa-star text-yellow-400"></i> Minha Reputação</h2>
                <p class="text-slate-500 mb-8 text-lg">Veja o que os motoristas estão dizendo sobre as suas encomendas e envios.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-lg border border-slate-100 text-center flex flex-col items-center justify-center h-fit">
                    <h3 class="text-lg font-bold text-slate-400 uppercase tracking-widest mb-4">Média Geral</h3>
                    <h1 class="text-7xl font-black text-blue-950 mb-4">4.8</h1>
                    <div class="flex items-center gap-2 text-2xl text-yellow-400 mb-6">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="text-slate-500 font-medium bg-slate-50 px-6 py-3 rounded-full border border-slate-100">
                        Baseado em <b>12</b> avaliações
                    </p>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-4">
                                <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg">
                                    C
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-blue-950">Carlos (Perueiro)</h4>
                                    <span class="text-xs text-slate-400 font-medium">Viagem para Luanda • 12/03/2026</span>
                                </div>
                            </div>
                            <div class="flex text-yellow-400 text-sm">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-slate-600 leading-relaxed">"Excelente cliente! A encomenda estava super bem embalada e pronta no horário combinado. Recomendo muito!"</p>
                    </div>

                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-4">
                                <div class="bg-orange-100 w-12 h-12 rounded-full flex items-center justify-center text-orange-600 font-bold text-lg">
                                    M
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-blue-950">Mário (Perueiro)</h4>
                                    <span class="text-xs text-slate-400 font-medium">Viagem para Benguela • 05/02/2026</span>
                                </div>
                            </div>
                            <div class="flex text-yellow-400 text-sm">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star text-slate-300"></i>
                            </div>
                        </div>
                        <p class="text-slate-600 leading-relaxed">"Tudo certo com a entrega. O pagamento foi feito na hora, só demorou um pouquinho para responder as mensagens no momento da coleta."</p>
                    </div>

                    <button onclick="carregarMaisAvaliacoes()" id="btn-mais-aval" class="w-full py-4 text-blue-600 font-bold hover:bg-blue-50 rounded-xl transition">
                        Carregar mais avaliações <i class="fas fa-chevron-down ml-2"></i>
                    </button>

                </div>
            </div>
        </main>
    </div>
</section>
<section id="client-payments" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-dropi text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-blue-950/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-white p-2.5 rounded-2xl shadow-lg"><i class="fas fa-box text-dropi text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI</span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-client')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-chart-line w-6 text-xl"></i> Dashboard
                </button>
                <button onclick="goTo('new-order')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-plus-circle w-6 text-xl"></i> Novo Envio
                </button>
                <button onclick="goTo('client-history')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-history w-6 text-xl"></i> Histórico
                </button>
                <button onclick="goTo('client-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
                <button onclick="goTo('client-reviews')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('client-payments')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-credit-card w-6 text-xl"></i> Pagamentos
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Cliente'); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Cliente')[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-300 font-semibold hover:text-red-400">Sair</button>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12">
            <header class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-4xl font-black text-blue-950 flex items-center gap-3"><i class="fas fa-credit-card text-blue-600"></i> Meus Pagamentos</h2>
                    <p class="text-slate-500 font-medium mt-2">Acompanhe todos os valores pagos e a conciliação com os perueiros.</p>
                </div>
                <button onclick="goTo('new-order')" class="bg-dropi text-white px-8 py-4 rounded-2xl font-bold shadow-xl hover:scale-105 transition flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Novo Envio
                </button>
            </header>

            <!-- Cards de resumo financeiro -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm border-l-8 border-l-blue-500 relative overflow-hidden group">
                    <i class="fas fa-receipt absolute -right-4 -bottom-4 text-8xl text-slate-50 group-hover:scale-110 transition-transform duration-500"></i>
                    <div class="relative z-10">
                        <p class="text-xs font-bold uppercase text-slate-400 tracking-widest mb-3">Total Gasto</p>
                        <h4 id="pay-total-gasto" class="text-4xl font-black text-blue-950">0,00 <span class="text-lg">R$</span></h4>
                        <p class="text-sm text-slate-500 mt-2">Em todos os envios</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm border-l-8 border-l-orange-400 relative overflow-hidden group">
                    <i class="fas fa-clock absolute -right-4 -bottom-4 text-8xl text-slate-50 group-hover:scale-110 transition-transform duration-500"></i>
                    <div class="relative z-10">
                        <p class="text-xs font-bold uppercase text-slate-400 tracking-widest mb-3">Aguardando Entrega</p>
                        <h4 id="pay-a-pagar" class="text-4xl font-black text-orange-500">0,00 <span class="text-lg">R$</span></h4>
                        <p class="text-sm text-slate-500 mt-2">Pedidos em andamento</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm border-l-8 border-l-green-500 relative overflow-hidden group">
                    <i class="fas fa-check-double absolute -right-4 -bottom-4 text-8xl text-slate-50 group-hover:scale-110 transition-transform duration-500"></i>
                    <div class="relative z-10">
                        <p class="text-xs font-bold uppercase text-slate-400 tracking-widest mb-3">Pagos & Confirmados</p>
                        <h4 id="pay-confirmados" class="text-4xl font-black text-green-600">0,00 <span class="text-lg">R$</span></h4>
                        <p class="text-sm text-slate-500 mt-2">Entregas concluídas</p>
                    </div>
                </div>
            </div>

            <!-- Conciliação com o Perueiro -->
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-8">
                <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <div>
                        <h3 class="text-2xl font-black text-blue-950 flex items-center gap-3">
                            <i class="fas fa-handshake text-blue-600"></i> Conciliação de Pagamentos
                        </h3>
                        <p class="text-slate-400 text-sm font-medium mt-1">O que você pagou × o que o perueiro recebeu</p>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-4 py-2 rounded-full flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i> 100% Transparente
                    </span>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="text-xs text-slate-400 uppercase font-bold bg-slate-50">
                                <th class="px-5 py-4 rounded-tl-xl">Pedido</th>
                                <th class="px-5 py-4">Destino</th>
                                <th class="px-5 py-4">Perueiro</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Valor Pago</th>
                                <th class="px-5 py-4 text-right rounded-tr-xl">Repasse ao Perueiro</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-pagamentos-cliente" class="font-semibold text-slate-700">
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                                    <div class="bg-slate-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <i class="fas fa-file-invoice-dollar text-4xl opacity-40"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-600 mb-1">Nenhum pagamento registado ainda.</h4>
                                    <p class="text-slate-500 max-w-sm mx-auto text-sm">Assim que você realizar um envio, os detalhes financeiros aparecerão aqui.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Métodos de pagamento -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-2xl font-black text-blue-950 mb-6 flex items-center gap-3"><i class="fas fa-wallet text-blue-600"></i> Formas de Pagamento</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-900 to-blue-700 text-white p-8 rounded-3xl shadow-xl shadow-blue-900/30 relative overflow-hidden cursor-pointer hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-wifi absolute top-4 right-4 text-2xl opacity-50 rotate-90"></i>
                        <i class="fas fa-circle absolute -bottom-6 -left-6 text-9xl opacity-5"></i>
                        <p class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-8">Cartão Principal</p>
                        <p class="font-mono text-xl font-bold mb-6 tracking-widest">•••• •••• •••• 4521</p>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-blue-300 text-xs uppercase mb-1">Titular</p>
                                <p class="font-bold"><?php echo isset($_SESSION['nome_usuario']) ? strtoupper(explode(' ', $_SESSION['nome_usuario'])[0]) : 'CLIENTE'; ?></p>
                            </div>
                            <i class="fab fa-cc-visa text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <div onclick="abrirModal('modal-cartao')" class="bg-slate-50 p-8 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center gap-4 text-slate-400 cursor-pointer hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50/50 transition-all duration-300 group">
                        <div class="w-14 h-14 rounded-full bg-white border-2 border-slate-200 flex items-center justify-center group-hover:border-blue-400 transition-colors">
                            <i class="fas fa-plus text-xl"></i>
                        </div>
                        <p class="font-bold text-sm text-center">Adicionar novo cartão</p>
                    </div>

                    <div class="bg-green-50 p-8 rounded-3xl border border-green-100 flex flex-col gap-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30">
                                <i class="fas fa-barcode text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-800">Pix / Boleto</p>
                                <p class="text-xs text-green-600 font-bold">Disponível</p>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">Pague via Pix com desconto de 5% ou por boleto bancário (compensação em 1 dia útil).</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</section>
<section id="dash-driver" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        
        <aside class="w-full md:w-72 bg-slate-900 text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-slate-900/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-600/50"><i class="fas fa-truck-fast text-white text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI<span class="text-blue-500">PRO</span></span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-driver')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-map-location-dot w-6 text-xl"></i> Buscar Coletas
                </button>
                <button onclick="goTo('driver-active')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-box-open w-6 text-xl"></i> Entregas Ativas
                </button>
                <button onclick="goTo('driver-earnings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-wallet w-6 text-xl"></i> Meus Ganhos
                </button>
                <button onclick="goTo('driver-reviews')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('driver-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista'); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista')[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-400 font-semibold hover:text-red-300">Desconectar</button>
                </div>
            </div>
        </aside>
        
        <main class="flex-1 p-6 md:p-12">
            <header class="mb-10">
                <span class="text-xs font-bold uppercase text-slate-400">Radar DropiPRO 📡</span>
                <h2 class="text-4xl font-black text-slate-800 mt-1">Central de Coleta</h2>
                <p class="text-slate-500 font-medium mt-2">Há encomendas disponíveis perto de você.</p>
            </header>

            <div id="radar-pedidos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div id="driver-view" class="hidden bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Foto" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Novo Pedido</h3>
                                <div class="text-yellow-500 text-xs mt-0.5"><i class="fas fa-star"></i> <span class="text-slate-600 font-bold">Avaliação pendente</span></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-0.5">Ganhos</p>
                            <h4 id="driver-preco" class="text-xl font-black text-green-500">R$ 0,00</h4>
                        </div>
                    </div>
                    <div class="space-y-4 mb-4 text-sm flex-1">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Coletar em:</p>
                            <p id="driver-origem" class="font-bold text-slate-700 truncate">Aguardando...</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Entregar em:</p>
                            <p id="driver-destino" class="font-bold text-slate-700 truncate">Aguardando...</p>
                        </div>
                    </div>
                    <div class="bg-blue-50/50 rounded-xl p-3 mb-5 text-xs border border-blue-50">
                        <p id="driver-tipo" class="font-semibold text-slate-700 mb-1"><i class="fas fa-box text-blue-400 mr-2"></i>...</p>
                        <p id="driver-peso" class="font-semibold text-slate-700"><i class="fas fa-weight-hanging text-blue-400 mr-2"></i>...</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-auto">
                        <button onclick="document.getElementById('driver-view').classList.add('hidden')" class="py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition text-sm">
                            Ignorar
                        </button>
                        <button onclick="aceitarCorrida()" class="py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 transition text-sm shadow-lg shadow-blue-600/30">
                            Aceitar
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>

<section id="driver-active" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-slate-900 text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-slate-900/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-600/50"><i class="fas fa-truck-fast text-white text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI<span class="text-blue-500">PRO</span></span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-driver')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-map-location-dot w-6 text-xl"></i> Buscar Coletas
                </button>
                <button onclick="goTo('driver-active')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-box-open w-6 text-xl"></i> Entregas Ativas
                </button>
                <button onclick="goTo('driver-earnings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-wallet w-6 text-xl"></i> Meus Ganhos
                </button>
                <button onclick="goTo('driver-reviews')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('driver-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista'); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista')[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-400 font-semibold hover:text-red-300">Desconectar</button>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12">
            <header class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-4xl font-black text-slate-800">Minhas Entregas Ativas</h2>
                    <p class="text-slate-500 font-medium mt-1">Atualize o status de cada entrega em tempo real.</p>
                </div>
                <div id="badge-ativas" class="hidden bg-blue-600 text-white text-sm font-bold px-5 py-2 rounded-full shadow-lg shadow-blue-600/30 flex items-center gap-2">
                    <i class="fas fa-circle animate-pulse text-xs"></i> <span id="count-ativas">0</span> em andamento
                </div>
            </header>

            <!-- Grid dinâmico de entregas -->
            <div id="lista-fotos-entregas" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-dashed border-slate-300">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box-open text-4xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500 font-bold text-lg mb-1">Nenhuma entrega ativa.</p>
                    <p class="text-slate-400 text-sm">Aceite uma coleta no Radar para ela aparecer aqui.</p>
                    <button onclick="goTo('dash-driver')" class="mt-6 bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                        <i class="fas fa-search-location mr-2"></i> Buscar Coletas
                    </button>
                </div>
            </div>
        </main>
    </div>
</section>

<section id="driver-earnings" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-slate-900 text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-slate-900/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-600/50"><i class="fas fa-truck-fast text-white text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI<span class="text-blue-500">PRO</span></span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-driver')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-map-location-dot w-6 text-xl"></i> Buscar Coletas
                </button>
                <button onclick="goTo('driver-active')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-box-open w-6 text-xl"></i> Entregas Ativas
                </button>
                <button onclick="goTo('driver-earnings')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-wallet w-6 text-xl"></i> Meus Ganhos
                </button>
                <button onclick="goTo('driver-reviews')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('driver-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista'); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista')[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-400 font-semibold hover:text-red-300">Desconectar</button>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12">
            <header class="mb-10">
                <h2 class="text-4xl font-black text-slate-800 flex items-center gap-3"><i class="fas fa-wallet text-green-500"></i> Financeiro</h2>
            </header>

            <!-- Saldo dinâmico -->
            <div class="bg-slate-900 text-white p-10 md:p-12 rounded-[2.5rem] mb-8 shadow-2xl shadow-slate-900/40 relative overflow-hidden group">
                <i class="fas fa-money-bill-wave absolute -right-10 -bottom-10 text-9xl text-white opacity-5 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <p class="text-blue-400 font-bold uppercase text-xs tracking-widest mb-2">Saldo Disponível para Saque</p>
                        <h3 id="earn-saldo" class="text-6xl font-black">0,00 <span class="text-2xl text-slate-400">R$</span></h3>
                    </div>
                    <button onclick="abrirModal('modal-saque')" class="bg-green-500 text-slate-900 px-10 py-5 rounded-full font-black hover:bg-green-400 hover:scale-105 transition-all shadow-lg shadow-green-500/30 flex items-center gap-2">
                        <i class="fas fa-university"></i> Levantar Dinheiro
                    </button>
                </div>
            </div>

            <!-- Mini-cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-xs font-bold uppercase text-slate-400 tracking-widest mb-2">Total Ganho</p>
                    <h4 id="earn-total" class="text-3xl font-black text-slate-800">0,00 <span class="text-base text-slate-400">R$</span></h4>
                </div>
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-xs font-bold uppercase text-slate-400 tracking-widest mb-2">Entregas Concluídas</p>
                    <h4 id="earn-count" class="text-3xl font-black text-slate-800">0</h4>
                </div>
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-xs font-bold uppercase text-slate-400 tracking-widest mb-2">Em Andamento</p>
                    <h4 id="earn-andamento" class="text-3xl font-black text-orange-500">0</h4>
                </div>
            </div>

            <!-- Histórico dinâmico -->
            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                <h4 class="font-black text-2xl text-slate-800 mb-6 flex items-center gap-3"><i class="fas fa-list-ul text-slate-400"></i> Histórico de Ganhos</h4>
                <div id="earn-historico" class="space-y-3">
                    <div class="text-center py-10 text-slate-400">
                        <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                        <p class="font-medium">Nenhuma entrega concluída ainda.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>

<section id="driver-reviews" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-slate-900 text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-slate-900/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-600/50"><i class="fas fa-truck-fast text-white text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI<span class="text-blue-500">PRO</span></span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-driver')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-map-location-dot w-6 text-xl"></i> Buscar Coletas
                </button>
                <button onclick="goTo('driver-active')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-box-open w-6 text-xl"></i> Entregas Ativas
                </button>
                <button onclick="goTo('driver-earnings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-wallet w-6 text-xl"></i> Meus Ganhos
                </button>
                <button onclick="goTo('driver-reviews')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('driver-settings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista'); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista')[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-400 font-semibold hover:text-red-300">Desconectar</button>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12">
            <header class="mb-10">
                <h2 class="text-4xl font-black text-slate-800 flex items-center gap-3"><i class="fas fa-award text-yellow-500"></i> Sua Reputação</h2>
            </header>

            <div class="grid md:grid-cols-3 gap-8 mb-10">
                <div class="bg-white p-10 rounded-[2.5rem] text-center shadow-xl shadow-slate-200/50 border border-slate-100 border-b-8 border-yellow-400 flex flex-col justify-center items-center h-full">
                    <h1 class="text-7xl font-black text-slate-800">4.9</h1>
                    <div class="text-yellow-400 text-2xl my-4 flex gap-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Média Geral</p>
                </div>
                
                <div class="md:col-span-2 bg-white p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                    <h4 class="font-black text-2xl text-slate-800 mb-6">Últimos Comentários</h4>
                    <div class="space-y-4">
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 flex gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold shrink-0">AP</div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-slate-800">Ana Paula</span>
                                    <span class="text-yellow-400 text-xs"><i class="fas fa-star"></i> 5.0</span>
                                </div>
                                <p class="text-slate-600 italic">"Muito rápido e cuidadoso com a mercadoria! Recomendo."</p>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 flex gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold shrink-0">MB</div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-slate-800">Marcos B.</span>
                                    <span class="text-yellow-400 text-xs"><i class="fas fa-star"></i> 5.0</span>
                                </div>
                                <p class="text-slate-600 italic">"Excelente serviço na zona de Viana. Chegou antes do tempo previsto."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>

<section id="driver-settings" class="page min-h-screen bg-slate-50">
    <div class="flex flex-col md:flex-row">
        <aside class="w-full md:w-72 bg-slate-900 text-white p-8 md:min-h-screen flex flex-col shadow-2xl shadow-slate-900/30">
            <div class="flex items-center gap-3 mb-16">
                <div class="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-600/50"><i class="fas fa-truck-fast text-white text-xl"></i></div>
                <span class="text-3xl font-black italic tracking-tighter">DROPI<span class="text-blue-500">PRO</span></span>
            </div>
            <nav class="space-y-3 flex-1">
                <button onclick="goTo('dash-driver')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-map-location-dot w-6 text-xl"></i> Buscar Coletas
                </button>
                <button onclick="goTo('driver-active')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-box-open w-6 text-xl"></i> Entregas Ativas
                </button>
                <button onclick="goTo('driver-earnings')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-wallet w-6 text-xl"></i> Meus Ganhos
                </button>
                <button onclick="goTo('driver-reviews')" class="w-full text-left sidebar-link flex items-center gap-4 p-4 rounded-2xl font-semibold opacity-70 hover:opacity-100 hover:bg-white/5 transition-all duration-300">
                    <i class="fas fa-star w-6 text-xl"></i> Avaliações
                </button>
                <button onclick="goTo('driver-settings')" class="w-full text-left sidebar-link active flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all duration-300">
                    <i class="fas fa-user-cog w-6 text-xl"></i> Definições
                </button>
            </nav>
            <div class="pt-8 mt-10 border-t border-white/10 flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista'); ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-12 h-12 border-2 border-white/20 rounded-full">
                <div>
                    <p class="font-bold text-white"><?php echo explode(' ', isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista')[0]; ?></p>
                    <button onclick="goTo('home')" class="text-sm text-red-400 font-semibold hover:text-red-300">Desconectar</button>
                </div>
            </div>
        </aside>
        
        <main class="w-full p-6 md:p-12">
            <header class="mb-10">
                <h2 class="text-4xl font-black text-slate-800 mb-2 flex items-center gap-3"><i class="fas fa-tools text-slate-600"></i> Conta do Motorista</h2>
                <p class="text-slate-500 font-medium mt-2">Atualize seus dados pessoais e credenciais de segurança.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100 text-center flex flex-col items-center gap-6 h-fit relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-24 bg-slate-900"></div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Motorista'); ?>&size=128&background=2563EB&color=fff&rounded=true" alt="Foto Motorista" class="w-32 h-32 rounded-full border-4 border-white shadow-lg relative z-10 mt-6">
                    <div class="relative z-10 w-full">
                        <h4 class="text-2xl font-black text-slate-800"><?php echo isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Nome não definido'; ?></h4>
                        <div class="flex items-center justify-center gap-2 mt-3">
                            <span class="bg-blue-50 text-blue-600 font-bold px-4 py-1.5 rounded-full text-xs uppercase tracking-widest flex items-center gap-1">
                                <i class="fas fa-shield-check"></i> Motorista Verificado
                            </span>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100">
                    <form action="atualizar_perfil.php" method="POST" class="space-y-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2"><i class="fas fa-id-card text-blue-500"></i> Informações Pessoais</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Nome Completo</label>
                                    <input type="text" name="nome" value="<?php echo isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : ''; ?>" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition font-medium text-slate-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Endereço de E-mail</label>
                                    <input type="email" name="email" placeholder="motorista@email.com" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition font-medium text-slate-700">
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100">
                            <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2"><i class="fas fa-lock text-blue-500"></i> Segurança da Conta</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Nova Senha</label>
                                    <input type="password" name="nova_senha" placeholder="Deixe em branco para manter" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2.5 ml-1">Confirmar Nova Senha</label>
                                    <input type="password" name="confirma_senha" placeholder="Repita a nova senha" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 transition">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="redirect_to" value="dash-driver">

                        <div class="pt-8 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-full font-bold shadow-lg shadow-slate-900/30 hover:bg-blue-600 hover:-translate-y-1 transition-all duration-300 w-full md:w-auto text-lg flex items-center gap-3 justify-center">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</section>
  </section>
    <section id="new-order" class="page min-h-screen pt-20 bg-slate-100 px-6">
    <div class="max-w-4xl mx-auto">
        
        <div class="relative flex justify-center items-center mb-10">
            <button onclick="voltarPasso()" class="absolute left-0 text-slate-500 font-bold flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
            <div class="flex gap-4">
                <div id="step-i-1" class="w-10 h-10 rounded-full bg-dropi text-white flex items-center justify-center font-bold">1</div>
                <div id="step-i-2" class="w-10 h-10 rounded-full bg-slate-300 text-white flex items-center justify-center font-bold">2</div>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-white">
            
            <div id="step-1" class="space-y-8">
                <h2 class="text-4xl font-black">Onde coletamos?</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">País</label>
                            <select name="pais" class="w-full p-4 bg-slate-100 border-2 border-slate-200 rounded-2xl outline-none text-slate-500 cursor-not-allowed" readonly>
                                <option value="BR">Brasil</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Estado</label>
                            <select name="estado" id="input-estado-origem" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="AC">Acre (AC)</option>
                                <option value="AL">Alagoas (AL)</option>
                                <option value="AP">Amapá (AP)</option>
                                <option value="AM">Amazonas (AM)</option>
                                <option value="BA">Bahia (BA)</option>
                                <option value="CE">Ceará (CE)</option>
                                <option value="DF">Distrito Federal (DF)</option>
                                <option value="ES">Espírito Santo (ES)</option>
                                <option value="GO">Goiás (GO)</option>
                                <option value="MA">Maranhão (MA)</option>
                                <option value="MT">Mato Grosso (MT)</option>
                                <option value="MS">Mato Grosso do Sul (MS)</option>
                                <option value="MG">Minas Gerais (MG)</option>
                                <option value="PA">Pará (PA)</option>
                                <option value="PB">Paraíba (PB)</option>
                                <option value="PR">Paraná (PR)</option>
                                <option value="PE">Pernambuco (PE)</option>
                                <option value="PI">Piauí (PI)</option>
                                <option value="RJ">Rio de Janeiro (RJ)</option>
                                <option value="RN">Rio Grande do Norte (RN)</option>
                                <option value="RS">Rio Grande do Sul (RS)</option>
                                <option value="RO">Rondônia (RO)</option>
                                <option value="RR">Roraima (RR)</option>
                                <option value="SC">Santa Catarina (SC)</option>
                                <option value="SP">São Paulo (SP)</option>
                                <option value="SE">Sergipe (SE)</option>
                                <option value="TO">Tocantins (TO)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Cidade de Saída</label>
                            <input type="text" id="input-cidade-origem" name="cidade_saida" placeholder="Ex: São Paulo" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Cidade de Destino</label>
                            <input type="text" id="input-cidade-destino" name="cidade_destino" placeholder="Ex: Campinas" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Rua / Avenida (Destino)</label>
                            <input type="text" id="input-rua-destino" name="rua_destino" placeholder="Nome da rua..." class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Número</label>
                            <input type="text" id="input-numero-destino" name="numero_destino" placeholder="Ex: 123" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" required>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="button" onclick="calcularEAvancar()" class="bg-dropi text-white px-10 py-5 rounded-2xl font-bold text-lg w-full md:w-auto shadow-xl">
                        Prosseguir para Itens
                    </button>
                </div>
            </div>

            <div id="step-2" class="hidden space-y-8">
                <h2 class="text-4xl font-black">O que vamos levar?</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <select class="w-full p-5 bg-slate-50 border rounded-2xl outline-none font-bold">
                        <option>Tipo de Pacote...</option>
                        <option>Documentos</option>
                        <option>Eletrodoméstico</option>
                        <option>Vestuário</option>
                    </select>
                    <input type="text" placeholder="Peso estimado (kg)" class="w-full p-5 bg-slate-50 border rounded-2xl outline-none font-bold">
                    <textarea class="w-full md:col-span-2 p-5 bg-slate-50 border rounded-2xl outline-none h-32" placeholder="Observações importantes..."></textarea>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-blue-400 uppercase">Preço Estimado</p>
                        <h4 class="text-3xl font-black text-dropi italic">Dinâmico</h4>
                    </div>
                    <button onclick="confirmOrder()" class="bg-dropi text-white px-10 py-5 rounded-2xl font-bold text-lg shadow-xl shadow-blue-900/30">Confirmar Encomenda</button>
                </div>
            </div>

        </div> </div> </section>
    <section id="register" class="page pt-32 pb-20 min-h-screen bg-slate-50">
        <div class="max-w-2xl mx-auto bg-white p-10 rounded-[3rem] shadow-2xl border border-white">
            <h2 class="text-4xl font-black text-slate-900 mb-2">Criar conta grátis</h2>
            <p class="text-slate-500 mb-8 font-medium">Junte-se à maior rede de logística.</p>
            
            <form action="cadastrar.php" method="POST">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="font-bold text-slate-400 text-xs ml-2 uppercase">Nome Completo</label>
                        <input type="text" name="nome" placeholder="Digite seu nome completo" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-dropi transition" required>
                    </div>

                    <div class="space-y-2">
                        <label class="font-bold text-slate-400 text-xs ml-2 uppercase">CPF</label>
                        <input type="text" name="cpf" placeholder="000.000.000-00" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-dropi transition" required>
                    </div>

                    <div class="space-y-2">
                        <label class="font-bold text-slate-400 text-xs ml-2 uppercase">Telemóvel</label>
                        <input type="tel" name="telefone" placeholder="+55 11 9XXXX-XXXX" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-dropi transition" required>
                    </div>

                    <div class="space-y-2">
                        <label class="font-bold text-slate-400 text-xs ml-2 uppercase">E-mail</label>
                        <input type="email" name="email" placeholder="nome@exemplo.com" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-dropi transition" required>
                    </div>
                    
                    <div class="space-y-2 md:col-span-2">
                        <label class="font-bold text-slate-400 text-xs ml-2 uppercase">Crie uma Senha</label>
                        <input type="password" name="senha" placeholder="••••••••" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-dropi transition" required>
                    </div>
                </div>

                <div class="mt-8 p-6 bg-blue-50 rounded-[2rem] border border-blue-100">
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" name="is_driver" value="sim" id="is-driver-check" onchange="toggleDriverFields()" class="w-6 h-6">
                        <div>
                            <p class="font-bold text-dropi">Quero ser um Perueiro Pro</p>
                            <p class="text-xs text-blue-400">Marque para cadastrar seu veículo e ganhar dinheiro.</p>
                        </div>
                    </label>

                    <div id="driver-extra-fields" class="hidden grid md:grid-cols-2 gap-4 mt-6 pt-6 border-t border-blue-100/50">
                        <input type="text" name="modelo_veiculo" placeholder="Modelo do Veículo" class="w-full p-4 bg-white border-none rounded-xl">
                        <input type="text" name="placa" placeholder="Placa (Matrícula)" class="w-full p-4 bg-white border-none rounded-xl">
                    </div>
                </div>

                <button type="submit" class="w-full bg-dropi text-white py-5 rounded-2xl font-bold text-lg mt-8 shadow-xl hover:scale-105 transition">Finalizar Cadastro</button>
            </form>
        </div>
    </section>

    <section id="tracking" class="page pt-40 min-h-screen">
        <div class="max-w-2xl mx-auto px-6 text-center">
            <div class="bg-blue-100 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-8 text-dropi text-3xl">
                <i class="fas fa-location-crosshairs animate-pulse"></i>
            </div>
            <h2 class="text-4xl font-black mb-4">Onde está sua encomenda?</h2>
            <p class="text-slate-500 mb-10">Insira o código de rastreio enviado para o seu e-mail.</p>
            
            <div class="relative max-w-md mx-auto">
                <input id="track-input" type="text" placeholder="Ex: DRP-1234" class="w-full p-6 pr-32 bg-white border-2 border-slate-200 rounded-3xl text-xl font-bold uppercase tracking-widest outline-none focus:border-dropi transition-all shadow-xl">
                <button onclick="rastrearPedido()" class="absolute right-3 top-3 bottom-3 bg-dropi text-white px-6 rounded-2xl font-bold shadow-lg">Buscar</button>
            </div>

            <div id="track-result" class="hidden mt-12 max-w-md mx-auto"></div>
        </div>
    </section>

  <script>
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
</script>
<!-- ===== MODAL: SAQUE DO PERUEIRO ===== -->
<div id="modal-saque" class="fixed inset-0 z-[998] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="fecharModal('modal-saque')"></div>
    <div class="relative bg-white rounded-[2.5rem] shadow-2xl p-10 max-w-md w-full mx-4 z-10">
        <button onclick="fecharModal('modal-saque')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition"><i class="fas fa-times text-xl"></i></button>
        <div class="bg-green-100 w-16 h-16 rounded-2xl flex items-center justify-center mb-6"><i class="fas fa-university text-green-600 text-2xl"></i></div>
        <h3 class="text-2xl font-black text-slate-800 mb-1">Levantar Dinheiro</h3>
        <p class="text-slate-500 text-sm mb-6">Informe os dados bancários para transferência.</p>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Banco</label>
                <select class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold text-slate-700">
                    <option>Selecione o banco...</option>
                    <option>Nubank</option><option>Itaú</option><option>Bradesco</option><option>Caixa Econômica</option>
                    <option>Banco do Brasil</option><option>Santander</option><option>Inter</option><option>C6 Bank</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Agência</label>
                    <input type="text" placeholder="0000" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Conta</label>
                    <input type="text" placeholder="00000-0" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Valor a Sacar (R$)</label>
                <input id="valor-saque" type="number" placeholder="0,00" min="10" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold">
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 text-xs text-blue-700 font-medium flex items-start gap-2">
                <i class="fas fa-info-circle mt-0.5 shrink-0"></i>
                <span>Saques são processados em até 1 dia útil via PIX ou TED. Valor mínimo: R$ 10,00.</span>
            </div>
        </div>
        <button onclick="confirmarSaque()" class="w-full mt-6 bg-green-500 text-slate-900 font-black py-5 rounded-2xl shadow-lg shadow-green-500/30 hover:bg-green-400 hover:scale-105 transition-all">
            <i class="fas fa-paper-plane mr-2"></i> Solicitar Saque
        </button>
    </div>
</div>

<!-- ===== MODAL: ADICIONAR CARTÃO ===== -->
<div id="modal-cartao" class="fixed inset-0 z-[998] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="fecharModal('modal-cartao')"></div>
    <div class="relative bg-white rounded-[2.5rem] shadow-2xl p-10 max-w-md w-full mx-4 z-10">
        <button onclick="fecharModal('modal-cartao')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition"><i class="fas fa-times text-xl"></i></button>
        <div class="bg-blue-100 w-16 h-16 rounded-2xl flex items-center justify-center mb-6"><i class="fas fa-credit-card text-blue-600 text-2xl"></i></div>
        <h3 class="text-2xl font-black text-slate-800 mb-1">Adicionar Cartão</h3>
        <p class="text-slate-500 text-sm mb-6">Seus dados são criptografados e protegidos.</p>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Número do Cartão</label>
                <input type="text" placeholder="0000 0000 0000 0000" maxlength="19" oninput="formatarCartao(this)" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-mono font-bold text-lg tracking-widest">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Nome no Cartão</label>
                <input type="text" placeholder="NOME COMO NO CARTÃO" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold uppercase">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Validade</label>
                    <input type="text" placeholder="MM/AA" maxlength="5" oninput="formatarValidade(this)" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">CVV</label>
                    <input type="password" placeholder="•••" maxlength="4" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold">
                </div>
            </div>
        </div>
        <button onclick="adicionarCartao()" class="w-full mt-6 bg-dropi text-white font-black py-5 rounded-2xl shadow-lg shadow-blue-900/30 hover:scale-105 transition-all">
            <i class="fas fa-lock mr-2"></i> Salvar Cartão com Segurança
        </button>
    </div>
</div>

<!-- ===== TOAST NOTIFICATIONS ===== -->
<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-3 pointer-events-none"></div>

<!-- ===== MODAL: AVALIAR PERUEIRO (Cliente) ===== -->
<div id="modal-avaliar" class="fixed inset-0 z-[998] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="fecharModal('modal-avaliar')"></div>
    <div class="relative bg-white rounded-[2.5rem] shadow-2xl p-10 max-w-md w-full mx-4 z-10 text-center">
        <button onclick="fecharModal('modal-avaliar')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition"><i class="fas fa-times text-xl"></i></button>
        <div class="bg-yellow-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6"><i class="fas fa-star text-yellow-500 text-2xl"></i></div>
        <h3 class="text-2xl font-black text-slate-800 mb-1">Avaliar Perueiro</h3>
        <p class="text-slate-500 text-sm mb-6">Como foi a sua experiência?</p>
        <div id="estrelas-avaliacao" class="flex justify-center gap-3 mb-6 text-4xl cursor-pointer">
            <i class="far fa-star text-slate-300 hover:text-yellow-400 transition" onclick="setStar(1)"></i>
            <i class="far fa-star text-slate-300 hover:text-yellow-400 transition" onclick="setStar(2)"></i>
            <i class="far fa-star text-slate-300 hover:text-yellow-400 transition" onclick="setStar(3)"></i>
            <i class="far fa-star text-slate-300 hover:text-yellow-400 transition" onclick="setStar(4)"></i>
            <i class="far fa-star text-slate-300 hover:text-yellow-400 transition" onclick="setStar(5)"></i>
        </div>
        <textarea id="comentario-avaliacao" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none resize-none h-28 font-medium text-slate-700" placeholder="Deixe um comentário (opcional)..."></textarea>
        <button onclick="enviarAvaliacao()" class="w-full mt-5 bg-yellow-400 text-slate-900 font-black py-4 rounded-2xl hover:bg-yellow-300 hover:scale-105 transition-all">
            <i class="fas fa-paper-plane mr-2"></i> Enviar Avaliação
        </button>
    </div>
</div>

</body>
</html>