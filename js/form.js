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

  function validarFormulario() {
      var senha = document.getElementById("senha").value;
      var confirmarSenha = document.getElementById("confirma_senha").value;

      if (senha !== confirmarSenha) {
          alert("As senhas não coincidem. Tente novamente.");
          return false;
      }

      alert("Cadastro realizado com sucesso!");
      return true;
  }