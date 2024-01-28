<?php

namespace Pagamento\Interfaces\UseCases;

use Pagamento\Entities\Pedido;
use Pagamento\Gateways\PedidoGateway;

interface PedidoUseCasesInterface
{
    public function cadastrar(PedidoGateway $pedidoGateway, Pedido $pedido);
    public function buscarPedidosPorCpf(PedidoGateway $pedidoGateway, $cpf);
    public function excluir(PedidoGateway $pedidoGateway, $id);
    public function atualizarStatusPagamentoPedido(PedidoGateway $pedidoGateway, int $id, string $status);
}
