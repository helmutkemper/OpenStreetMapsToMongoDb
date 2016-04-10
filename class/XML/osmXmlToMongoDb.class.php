<?php

  /**
   * @autor Helmut Kemper
   * @email helmut.kemper@gmail.com
   * @site http://www.kemper.com.br
   *
   * Class osmXmlToMongoDb
   *
   * Descrição:
   *
   * Classe de conexão ao banco de dados MongoDB.
   *
   * Licença:
   *
   * Este código é aberto e feito com o intuito de ajudar, porém, sem garantias de funcionar ou obrigação de suporte de
   * qualquer forma da minha parte.
   *
   * Você é livre para copiar e usar o código desse repositório conforme as suas necessidades e livre para lucrar com
   * ele, sem ter que me pagar royalties, desde que siga as regras abaixo:
   *
   * - Para usar este código, você se compromete a divulgar meu nome e trabalho como sendo o criador original da
   *   ferramenta de integração com o seu sistema de mapas no site da sua aplicação;
   * - Você se compromete a me enviar de forma documentada qualquer correção e/ou melhorias feitas no código para que eu
   *   decida quais das mesmas sejam adicionas ao projeto original de forma aberta a toda a comunidade, sem custos;
   * - Você se compromete a contribuir tecnicamente com a comunidade de desenvolvedores de forma gratuita;
   * - Você se compromete a me manter informado onde o código é usado e me enviar material de divulgação, para seja
   *   feita propaganda desse código e casos de sucesso;
   * - Você se compromete a não usar o código em aplicações que possam colocar a vida de pessoas em risco desnecessários
   *   e/ou aplicações militares sem autorização prévia da minha parte.
   */
  class osmXmlToMongoDb
  {
    /**
     * @var $connectionCObj Object de conexão da classe MongoClient.
     */
    protected $connectionCObj;

    /**
     * @var $dataBaseCObj Object de conexão ao banco de dados.
     */
    protected $dataBaseCObj;

    /**
     * osmXmlToMongoDb constructor.
     */
    public function __construct()
    {

    }

    /**
     * Construtor da classe.
     *
     * @param null $connectionAStr String de conexão ao banco de dados ou null para conexão local insegura.
     */
    public function connect( $connectionAStr = null )
    {
      $this->connectionCObj = new MongoClient( $connectionAStr );
    }

    /**
     * Determina o nome do banco de dados a ser usado.
     *
     * @param $dataBaseAStr String com o nome do banco de dados
     */
    public function setDataBase( $dataBaseAStr )
    {
      $this->dataBaseCObj        = $this->connectionCObj->$dataBaseAStr;
    }
  }