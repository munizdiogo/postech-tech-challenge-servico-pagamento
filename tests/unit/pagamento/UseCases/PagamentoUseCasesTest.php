<?php

require "./src/External/MySqlConnection.php";
require "./src/UseCases/PedidoUseCases.php";
require "./src/Gateways/PedidoGateway.php";
require "./src/Entities/Pedido.php";

use Pagamento\External\DynamoDBConnection;
use Pagamento\External\MySqlConnection;
use PHPUnit\Framework\TestCase;
use Pagamento\UseCases\PedidoUseCases;
use Pagamento\UseCases\PagamentoUseCases;
use Pagamento\Gateways\PedidoGateway;
use Pagamento\Entities\Pedido;
use Pagamento\Gateways\PagamentoGateway;

class PagamentoUseCasesTest extends TestCase
{
    protected $dbConnection;
    protected $dbConnectionNoSQL;
    protected $pedidoGateway;
    protected $pagamentoGateway;
    protected $pedidoUseCases;
    protected $pagamentoUseCases;
    public function setUp(): void
    {
        parent::setUp();
        $this->dbConnection = new MySqlConnection;
        $this->dbConnectionNoSQL = new DynamoDBConnection;
        $this->pagamentoUseCases = new PagamentoUseCases;
        $this->pagamentoGateway =  new PagamentoGateway($this->dbConnectionNoSQL);
        $this->pedidoUseCases = new PedidoUseCases;
        $this->pedidoGateway = new PedidoGateway($this->dbConnection);
    }

    public function testAtualizarStatusPagamentoPedidoComSucesso()
    {
        $dadosPedido = '{
            "cpf": "42157363823",
            "produtos": [
                {
                    "id": 2,
                    "nome": "Produto 1",
                    "descricao": "Descrição do Produto 1",
                    "preco": 20.99,
                    "categoria": "lanche"
                },
                {
                    "id": 3,
                    "nome": "Produto 2",
                    "descricao": "Descrição do Produto 2",
                    "preco": 15.99,
                    "categoria": "bebida"
                }
            ]
        }';

        $pedidoArray = json_decode($dadosPedido, true);
        $pedido = new Pedido("recebido", "42157363823", $pedidoArray["produtos"]);

        $idPedido = $this->pedidoUseCases->cadastrar($this->pedidoGateway, $pedido);
        $this->assertIsInt($idPedido);

        $pedidos = $this->pedidoUseCases->buscarPedidosPorCpf($this->pedidoGateway, "42157363823");

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

        $pagamentoCadastrado = $this->pagamentoUseCases->cadastrar($this->pagamentoGateway, $dadosPagamento);
        $this->assertTrue($pagamentoCadastrado);

        $resultado = $this->pedidoUseCases->atualizarStatusPagamentoPedido($this->pedidoGateway, $pedidos[0]["idPedido"], "aprovado");
        $this->assertTrue($resultado);

        $pedidoExcluido = $this->pedidoUseCases->excluir($this->pedidoGateway,  $pedidos[0]["idPedido"]);
        $this->assertTrue($pedidoExcluido);
    }

    public function testCadastrarPagamentoComCampoObrigatorioNaoInformado()
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

        try {
            $this->pagamentoUseCases->cadastrar($this->pagamentoGateway, $dadosPagamento);
        } catch (Exception $e) {
            $this->assertEquals("O campo IdTransacao é obrigatório.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }
}
