<?php

namespace Pagamento\Interfaces\Controllers;

interface PedidoControllerInterface
{
    public function cadastrar($dbConnection, array $dados);
    public function buscarPedidosPorCpf($dbConnection, $cpf);
    public function excluir($dbConnection, $id);
    public function atualizarStatusPedido($dbConnection, $id, $status);
}
