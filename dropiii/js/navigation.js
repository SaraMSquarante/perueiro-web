
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
