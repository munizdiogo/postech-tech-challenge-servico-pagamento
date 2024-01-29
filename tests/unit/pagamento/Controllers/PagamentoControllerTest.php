<?php

namespace Pagamento\Controllers;

require "./src/External/DynamoDBConnection.php";
require "./src/Gateways/PagamentoGateway.php";
require "./src/Controllers/PagamentoController.php";
require "./src/UseCases/PagamentoUseCases.php";

use Pagamento\Gateways\PagamentoGateway;
use Pagamento\Controllers\PagamentoController;
use Pagamento\External\DynamoDBConnection;
use Pagamento\UseCases\PagamentoUseCases;
use PHPUnit\Framework\TestCase;

class PagamentoControllerTest extends TestCase
{
    private $dbConnection;
    private $pagamentoGateway;
    private $pagamentoController;
    private $pagamentoUseCases;

    public function setUp(): void
    {
        parent::setUp();
        $this->dbConnection = new DynamoDBConnection;
        $this->pagamentoGateway = new PagamentoGateway($this->dbConnection);
        $this->pagamentoController = new PagamentoController();
        $this->pagamentoUseCases = new PagamentoUseCases();
    }

    public function testCadastrarPagamentoComSucesso()
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
        $this->assertIsArray($dadosPagamento);
        $dadosSalvosComSucesso = $this->pagamentoController->cadastrar($this->dbConnection, $dadosPagamento);
        $this->assertTrue($dadosSalvosComSucesso);
        $excluidoComSucesso = $this->pagamentoUseCases->excluir($this->pagamentoGateway, $idTransacao);
        $this->assertTrue($excluidoComSucesso);
    }
}
