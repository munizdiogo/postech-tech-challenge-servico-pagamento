<?php

namespace Pagamento\Controllers;

require "./src/Interfaces/Controllers/PedidoControllerInterface.php";
require "./src/UseCases/PedidoUseCases.php";
require "./src/Gateways/PedidoGateway.php";
require "./src/Entities/Pedido.php";

use Pagamento\Gateways\PedidoGateway;
use Pagamento\Entities\Pedido;
use Pagamento\Interfaces\Controllers\PedidoControllerInterface;
use Pagamento\UseCases\PedidoUseCases;

class PedidoController implements PedidoControllerInterface
{
    public function cadastrar($dbConnection, array $dados)
    {
        $dados = $dados ?? [];
        $cpf = $dados["cpf"] ?? "";
        $produtos = $dados["produtos"] ?? [];
        $pedidoGateway = new PedidoGateway($dbConnection);
        $pedidoUseCases = new PedidoUseCases();
        $pedido = new Pedido("recebido", $cpf, $produtos);
        $idPedido = $pedidoUseCases->cadastrar($pedidoGateway, $pedido);
        return $idPedido;
    }
    public function buscarPedidosPorCpf($dbConnection, $cpf)
    {
        $cpf = $cpf ?? "";
        $pedidoGateway = new PedidoGateway($dbConnection);
        $pedidoUseCases = new PedidoUseCases();
        $resultado = $pedidoUseCases->buscarPedidosPorCpf($pedidoGateway, $cpf);
        return $resultado;
    }
    public function atualizarStatusPagamentoPedido($dbConnection, $id, $status)
    {
        $pedidoGateway = new PedidoGateway($dbConnection);
        $pedidoUseCases = new PedidoUseCases();
        $resultado = $pedidoUseCases->atualizarStatusPagamentoPedido($pedidoGateway, $id, $status);
        return $resultado;
    }
    public function excluir($dbConnection, $id)
    {
        $id = $id ?? "";
        $pedidoGateway = new PedidoGateway($dbConnection);
        $pedidoUseCases = new PedidoUseCases();
        $resultado = $pedidoUseCases->excluir($pedidoGateway, $id);
        return $resultado;
    }
}
