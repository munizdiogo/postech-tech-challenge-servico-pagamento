<?php

namespace Pagamento\Tests\Gateways;

use Pagamento\Entities\Pedido;
use PHPUnit\Framework\TestCase;
use Pagamento\Gateways\PedidoGateway;
use Pagamento\External\MySqlConnection;

class PedidoGatewayTest extends TestCase
{
    private $dbConnection;
    private $pedidoGateway;

    protected function setUp(): void
    {
        $this->dbConnection = new MySqlConnection();
        $this->pedidoGateway = new PedidoGateway($this->dbConnection);
    }

    public function testCadastrarComErro()
    {
        $pedido = new Pedido("", "", []);
        $resultado = $this->pedidoGateway->cadastrar($pedido);
        $this->assertFalse($resultado);
    }
}
