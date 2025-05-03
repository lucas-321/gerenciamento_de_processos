function mascaraInscricao(input) {
    let valor = input.value.replace(/\D/g, '');

    valor = valor.replace(/(\d{2})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{2})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{4})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{4})(\d{1,3})$/, '$1.$2');

    input.value = valor;
}

function mascaraCpfCnpj(campo) {
    let valor = campo.value.replace(/\D/g, ''); // remove tudo que não for número

    if (valor.length <= 11) {
      // CPF: 000.000.000-00
      valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
      valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
      valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
      // CNPJ: 00.000.000/0000-00
      valor = valor.replace(/^(\d{2})(\d)/, '$1.$2');
      valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
      valor = valor.replace(/\.(\d{3})(\d)/, '.$1/$2');
      valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
    }

    campo.value = valor;
  }

  function mascaraTelefone(campo) {
    
    let valor = campo.value.replace(/\D/g, ''); // remove tudo que não for número

    if (valor.length <= 10) {
      // telefone: (75) 0000-0000
      valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
      valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
      valor = valor.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    } else {
      // celular: (75) 00000-0000
      valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
      valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
      valor = valor.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }

    campo.value = valor;

  }