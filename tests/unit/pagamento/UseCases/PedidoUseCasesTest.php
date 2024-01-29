<?php

use Pagamento\External\MySqlConnection;
use Pagamento\UseCases\PedidoUseCases;
use PHPUnit\Framework\TestCase;
use Pagamento\Entities\Pedido;
use Pagamento\Gateways\PedidoGateway;

class PedidoUseCasesTest extends TestCase
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
        $this->pedidoUseCases = new PedidoUseCases;
        $this->pedidoGateway = new PedidoGateway($this->dbConnection);
    }

    public function testCadastrarComCPFNaoInformado()
    {
        $dadosPedido = '{
            "cpf": "",
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
        $pedido = new Pedido("recebido", "", $pedidoArray["produtos"]);

        try {
            $this->pedidoUseCases->cadastrar($this->pedidoGateway, $pedido);
        } catch (Exception $e) {
            $this->assertEquals("O campo cpf é obrigatório.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function testCadastrarComProdutosNaoInformados()
    {
        $pedido = new Pedido("recebido", "42157363823", []);

        try {
            $this->pedidoUseCases->cadastrar($this->pedidoGateway, $pedido);
        } catch (Exception $e) {
            $this->assertEquals("O campo produtos é obrigatório.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function testExcluirComProdutosNaoInformados()
    {
        $pedido = new Pedido("recebido", "42157363823", []);

        try {
            $this->pedidoUseCases->cadastrar($this->pedidoGateway, $pedido);
        } catch (Exception $e) {
            $this->assertEquals("O campo produtos é obrigatório.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function testAtualizarStatusPagamentoPedidoComIdNaoInformado()
    {
        try {
            $this->pedidoUseCases->atualizarStatusPagamentoPedido($this->pedidoGateway, 0, "aprovado");
        } catch (Exception $e) {
            $this->assertEquals("O campo id é obrigatório.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function testAtualizarStatusPagamentoPedidoComStatusNaoInformado()
    {
        try {
            $this->pedidoUseCases->atualizarStatusPagamentoPedido($this->pedidoGateway, 9999999999999999, "");
        } catch (Exception $e) {
            $this->assertEquals("O campo status é obrigatório.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function testAtualizarStatusPagamentoPedidoComIdNaoEncontrado()
    {
        try {
            $this->pedidoUseCases->atualizarStatusPagamentoPedido($this->pedidoGateway, 999999999999999, "aprovado");
        } catch (Exception $e) {
            $this->assertEquals("Não foi encontrado um pedido com o ID informado.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }
    public function testAtualizarStatusPagamentoPedidoComStatusInvalido()
    {
        try {
            $this->pedidoUseCases->atualizarStatusPagamentoPedido($this->pedidoGateway, 999999999999999, "approved");
        } catch (Exception $e) {
            $this->assertEquals("O status informado é inválido.", $e->getMessage());
            $this->assertEquals(400, $e->getCode());
        }
    }
}
