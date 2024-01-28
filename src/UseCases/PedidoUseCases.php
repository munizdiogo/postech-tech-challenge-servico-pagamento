<?php

namespace Pagamento\UseCases;

require "./src/Interfaces/UseCases/PedidoUseCasesInterface.php";

use Pagamento\Entities\Pedido;
use Pagamento\Gateways\PedidoGateway;
use Pagamento\Interfaces\UseCases\PedidoUseCasesInterface;

class PedidoUseCases implements PedidoUseCasesInterface
{
    public function cadastrar(PedidoGateway $pedidoGateway, Pedido $pedido)
    {
        if (empty($pedido->getCPF())) {
            throw new \Exception("O campo cpf é obrigatório.", 400);
        }

        if (empty($pedido->getProdutos())) {
            throw new \Exception("O campo produtos é obrigatório.", 400);
        }

        $idPedido = $pedidoGateway->cadastrar($pedido);
        return $idPedido;
    }
    public function buscarPedidosPorCpf(PedidoGateway $pedidoGateway, $cpf)
    {
        $resultado = $pedidoGateway->buscarPedidosPorCpf($cpf);
        return $resultado;
    }
    public function excluir(PedidoGateway $pedidoGateway, $id)
    {
        $resultado = $pedidoGateway->excluir($id);
        return $resultado;
    }

    public function atualizarStatusPagamentoPedido(PedidoGateway $pedidoGateway, int $id, string $status)
    {
        $statusPermitidos = ["aprovado", "recusado"];
        $statusValido = in_array($status, $statusPermitidos);
        $pedidoValido = (bool)$pedidoGateway->obterPorId($id);

        if (empty($id)) {
            throw new \Exception("O campo id é obrigatório.", 400);
        }

        if (empty($status)) {
            throw new \Exception("O campo status é obrigatório.", 400);
        }

        if (!$statusValido) {
            throw new \Exception("O status informado é inválido.", 400);
        }

        if (!$pedidoValido) {
            throw new \Exception("Não foi encontrado um pedido com o ID informado.", 400);
        }

        $pedidos = $pedidoGateway->atualizarStatusPagamentoPedido($id, $status);
        return $pedidos;
    }
}
