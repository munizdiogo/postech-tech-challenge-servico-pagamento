<?php

namespace Pagamento\Tests\Gateways;

use Exception;
use PHPUnit\Framework\TestCase;
use Pagamento\Gateways\PagamentoGateway;
use Pagamento\External\DynamoDBConnection;

class PagamentoGatewayTest extends TestCase
{
    private $dbConnection;
    private $pagamentoGateway;

    protected function setUp(): void
    {
        $this->dbConnection = new DynamoDBConnection();
        $this->pagamentoGateway = new PagamentoGateway($this->dbConnection);
    }

    public function testCadastrarComSucesso()
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
        $dadosSalvosComSucesso = $this->pagamentoGateway->cadastrar($dadosPagamento);
        $this->assertTrue($dadosSalvosComSucesso);
        $excluidoComSucesso = $this->pagamentoGateway->excluir($idTransacao);
        $this->assertTrue($excluidoComSucesso);
    }

    public function testCadastrarComErro()
    {
        try {
            $dataCriacao = new \DateTime("now");
            $dadosPagamento = [
                "IdTransacao" => "",
                "DataCriacao" => $dataCriacao->format('Y-m-d H:i:s'),
                "IdPedido" => 9999999999999999,
                "Cpf" => "42157363823",
                "Valor" => "100.00",
                "FormaPagamento" => "debito",
                "Status" => "aprovado"
            ];
            $this->pagamentoGateway->cadastrar($dadosPagamento);
        } catch (Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), "error") !== false);
        }
    }

    public function testExcluirComSucesso()
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
        $dadosSalvosComSucesso = $this->pagamentoGateway->cadastrar($dadosPagamento);
        $this->assertTrue($dadosSalvosComSucesso);
        $excluidoComSucesso = $this->pagamentoGateway->excluir($idTransacao);
        $this->assertTrue($excluidoComSucesso);
    }
}
