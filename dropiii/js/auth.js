
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

    function toggleDriverFields() {
        const fields = document.getElementById('driver-extra-fields');
        if (fields) fields.classList.toggle('hidden');
    }
