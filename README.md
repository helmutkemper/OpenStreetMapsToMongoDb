OpenStreetMaps to MongoDB
-------------------------

![Imagem de exemplo](https://github.com/helmutkemper/OpenStreetMapsToMongoDb/blob/master/map_test_cut1.png)

Este é um trabalho preliminar e sujeito a alterações sem aviso prévio, por favor, use por sua conta e risco.

##Licença:

Este código é aberto e feito com o intuito de ajudar, porém, sem garantias de funcionar ou obrigação de suporte de qualquer forma da minha parte.

Você é livre para copiar e usar o código desse repositório conforme as suas necessidades e livre para lucrar com ele, sem ter que me pagar royalties, desde que siga as regras abaixo:

* Para usar este código, você se compromete a divulgar meu nome como sendo o criador original;
* Você se compromete a me enviar de forma documentada qualquer correção e/ou melhorias feitas no código para que as mesmas sejam adicionas ao projeto original de forma aberta a toda a comunidade, sem custos;
* Você se compromete a contribuir tecnicamente com a comunidade de desenvolvedores de forma gratuita;
* Você se compromete a me manter informado onde o código é usado e me enviar material de divulgação, para seja feita propaganda desse código e casos de sucesso;
* Você se compromete a não usar o código em aplicações que possam colocar a vida de pessoas em risco desnecessários ou aplicações militares sem autorização prévia da minha parte.

###Finalidade:

Este repositório contém uma série de códigos feito para importar um arquivo XML no padrão OSM do OpenStreetMaps e permitir o processamento geográfico de informações contidas no mapa para as mais diversas aplicações, incluindo IoT, onde o georreferenciamento é fundamental.

####Código Modular.

O código é todo dividido em módulos, de forma a permitir o balanceamento dos servidores e testado em uma simples instalação xampp sem alterações sempre que possível, o que não se aplica ao pré-processamento do ambiente gráfico atual ( v0.1 de 04/2016 )

####MongoDB

Todo o projeto fei feito pensado em se aproveitar todo o poder de processamento das novas tecnologias noSQL, e por isto, foi escolhido o MongoDB para a finalidade.

Quando instalar o servidor, tome cuidado de adicionar os servidores "replica sets" antes da primeira carga, pois, mapas como o do Brasil contém mais 30 milhões de entradas fácil, e deixar para replicar os servidores em produção pode gerar um trafego de rede inesperado.

####IoT e MQTT

Como o código é modular, fica fácil arquivar os dados geográficos de todos os dispositivos em uma nova coleção de dados e isto será devidamente explicado assim que o sistema fique pronto.

Cordialmente,<br>
Helmut Kemper<br>
helmut.kemper no mail do google.
