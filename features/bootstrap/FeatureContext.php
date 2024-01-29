<?php

require "./src/Controllers/PagamentoController.php";
require "./src/Gateways/PagamentoGateway.php";
require "./src/External/DynamoDBConnection.php";
require "./src/UseCases/PagamentoUseCases.php";

use Pagamento\Controllers\PagamentoController;
use PHPUnit\Framework\TestCase;
use Behat\Behat\Context\Context;
use Pagamento\External\DynamoDBConnection;
use Pagamento\Gateways\PagamentoGateway;
use Pagamento\UseCases\PagamentoUseCases;


class FeatureContext extends TestCase implements Context
{
    private $resultado;
    private $exceptionMessage;
    private $exceptionCode;
    private $dadosPagamento;
    private $pagamentoController;
    private $pagamentoDBConnection;
    private $pagamentoUseCases;
    private $pagamentoGateway;

    public function __construct()
    {
        $this->pagamentoDBConnection = new DynamoDBConnection();
        $this->pagamentoController = new PagamentoController();
        $this->pagamentoUseCases = new PagamentoUseCases();
        $this->pagamentoGateway = new PagamentoGateway($this->pagamentoDBConnection);
    }

    /**
     * @Given que existem dados válidos para cadastrar um pagamento
     */
    public function queExistemDadosValidosParaCadastrarUmPagamento()
    {
        $idTransacao = 9999999999999999;
        $dataCriacao = new \DateTime("now");
        $dadosPagamento = [
            "IdTransacao" => $idTransacao,
            "DataCriacao" => $dataCriacao->format('Y-m-d H:i:s'),
            "IdPedido" => 9999999999999999,
            "Cpf" => "42157363823",
            "Valor" => "100.00",
            "FormaPagamento" => "debito",
            "Status" => "aprovado"
        ];

        $this->dadosPagamento = $dadosPagamento;
    }

    /**
     * @When eu chamar a função cadastrar pagamento
     */
    public function euChamarAFuncaoCadastrarPagamento()
    {
        $this->resultado = $this->pagamentoController->cadastrar($this->pagamentoDBConnection, $this->dadosPagamento);
    }

    /**
     * @Then eu devo receber uma confirmação de que os dados foram salvos com sucesso
     */
    public function euDevoReceberUmaConfirmacaoDeQueOsDadosForamSalvosComSucesso()
    {
        $this->assertTrue($this->resultado);
        $idTransacao = $this->resultado;
        $excluidoComSucesso = $this->pagamentoUseCases->excluir($this->pagamentoGateway, $idTransacao);
        $this->assertTrue($excluidoComSucesso);
    }

    /**
     * @Given que existem dados inválidos para cadastrar um pagamento
     */
    public function queExistemDadosInvalidosParaCadastrarUmPagamento()
    {
        $dataCriacao = new \DateTime("now");
        $dadosPagamento = [
            "DataCriacao" => $dataCriacao->format('Y-m-d H:i:s'),
            "IdPedido" => 9999999999999999,
            "Cpf" => "42157363823",
            "Valor" => "100.00",
            "FormaPagamento" => "debito",
            "Status" => "aprovado"
        ];

        $this->dadosPagamento = $dadosPagamento;
    }

    /**
     * @When eu chamar a função cadastrar pagamento com dados inválidos
     */
    public function euChamarAFuncaoCadastrarPagamentoComDadosInvalidos()
    {
        try {
            $this->resultado = $this->pagamentoController->cadastrar($this->pagamentoDBConnection, $this->dadosPagamento);
        } catch (Exception $e) {
            $this->exceptionMessage = $e->getMessage();
            $this->exceptionCode = $e->getCode();
        }
    }

    /**
     * @Then eu devo receber uma resposta de que o campo obrigatório para criar o pagamento não foi informado
     */
    public function euDevoReceberUmaRespostaDeQueOCampoObrigatorioParaCriarOPagamentoNaoFoiInformado()
    {
        $this->assertEquals("O campo IdTransacao é obrigatório.", $this->exceptionMessage);
        $this->assertEquals(400, $this->exceptionCode);
    }
}
