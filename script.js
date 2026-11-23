'use strict';

// Referências aos elementos usados
const form = document.getElementById('form-inscricao');
const mensagem = document.getElementById('mensagem');
const contador = document.getElementById('contador');
const grupoOficinas = document.getElementById('grupo-oficinas');
const erroOficinas = document.getElementById('erro-oficinas');
const oficinas = document.querySelectorAll('input[name="oficinas[]"]');

//1. Contador de caracteres em tempo real
function atualizarContador() {
  const restantes = mensagem.maxLength - mensagem.value.length;
  contador.textContent =
    restantes === 1 ? 'Resta 1 caractere' : 'Restam ' + restantes + ' caracteres';
}

mensagem.addEventListener('input', atualizarContador);
atualizarContador();

//2. Validação preventiva das oficinas
function haOficinaSelecionada() {
  return Array.from(oficinas).some((caixa) => caixa.checked);
}

function mostrarErroOficinas() {
  erroOficinas.hidden = false;
  grupoOficinas.classList.add('is-invalid');
}

function limparErroOficinas() {
  erroOficinas.hidden = true;
  grupoOficinas.classList.remove('is-invalid');
}

form.addEventListener('submit', (event) => {
  if (!haOficinaSelecionada()) {
    event.preventDefault();
    mostrarErroOficinas();
    oficinas[0].focus();
  }
});

oficinas.forEach((caixa) => {
  caixa.addEventListener('change', () => {
    if (haOficinaSelecionada()) {
      limparErroOficinas();
    }
  });
});
