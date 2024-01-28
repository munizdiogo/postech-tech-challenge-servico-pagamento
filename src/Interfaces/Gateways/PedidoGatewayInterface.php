<?php

namespace Pagamento\Interfaces\Gateways;

use Pagamento\Entities\Pedido;

interface PedidoGatewayInterface
{
    public function cadastrar(Pedido $pedido);
    public function buscarPedidosPorCpf($cpf);
    public function excluir(int $id): bool;
    public function atualizarStatusPagamentoPedido($id, $status): bool;
    public function obterPorId($id): array;
}
