## Sensor Biométrico Overalt 1.0

Descrição:
----------

A função básica do programa é acionar uma SDK fornecida pela Nitgen, fazer a leitura de impressão digital e checar a identidade do usuário. 

O código foi feito em C# e PHP5.

O código C# foi testado para o dispositivo Hamster DX ( Model: HFDU06 ), leitor de impressão digital, no windows 7 e Visual Studio 2013.

O código PHP foi testado na versão 3.2.1 do Xampp para Windows.

Funcionamento:
--------------

A SDK da Nitgen trabalha gravando e lendo arquivo binários do computador para salvar a impressão digital do usuário em um arquivo binário criptografado. Porém, o arquivo binário contém caracteres especias que não podem ser arquivados na forma de string, o que dá muito problema com o servidor PHP, por isto, a classe toma o cuidado de converter o binário para a base 64 antes de fazer o tráfego de informações entre a aplicação e o servidor web.

Para o fluxo de dados entre a aplicação e o servidor web, são usados dois json. O primeiro contém:

**id:** id do usuário;
**fingerFile:** arquivo com a impressão digital do mesmo;
**name:** nome completo do usuário;
**photoPath:** caminho completo da foto do usuário no servidor web;
**token:** token de segurança do servidor;
**ip:** ip da máquina onde a aplicação está localizada;
**data:** data e hora do evento de comunicação segundo a máquina do usuário;
**cpf:** CPF do usuário;
**action:** **add** para cadastro de nova impressão digital; **get** para testar uma impressão previamente cadastrada;
**error:** erro no processo de comunicação. 

O segundo json contém apenas o campo **data** e este campo recebe o primeiro json criptografado usando o processo RJ256, compatível com o servidor PHP.

**Cuidado com o Token** 

O token enviado ao servidor deve ser devolvido sem alterações à aplicação, pois, ele serve para evitar que uma comunicação seja clonada e reaproveitada. Para entender melhor isto, entenda o token como sendo um número aleatório escrito na base 83 com 100 dígitos, o que dá uma chance de acerto em 83^100, ou, 8.087406*10 seguidos de 191 zeros.

**Exemplo de comunicação: [RAW JSon data]**
```
{
	"data": "wIklD3ukdzjDalVt7cH6O4\/ExDkf5xMpG48BWARe7ciU5IFAU2ZxOoFiSeKFvRktdDWeoYUHNIVmzT\/zM+L+JdE\/vPQ5TK+uVvgT7ow9kvxN7Mzuul9mN6egyzkRMyc2bhm04gkbP\/YWR9Eg\/MHrwo1Ff17WBFAbOjpP0\/6APtdJU7Ti7RQARN0RHf5nOLEDZbAhzd\/gWTG+5u0esw9qCA=="
}
```

Funcionamento do código C#
---------------------------

**Atenção:**
Para usar o código C#, instale o sensor Nitgen Hamster DX no windows;
Abra o MS Visual Studio e adicione uma referencia ao projeto;
Escolha a última opção, "browse" e adicione a dll:
"C:\Windows\assembly\GAC_MSIL\NITGEN.SDK.NBioBSP\1.2.3.0__96eee45103d523d1\NITGEN.SDK.NBioBSP.dll"

**mainForm.cs:** Classe principal responsável pelo ambiente gráfico;
**Base64.cs**
