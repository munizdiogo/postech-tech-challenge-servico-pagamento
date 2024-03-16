<?php

namespace Pagamento\UseCases;

if (file_exists("./src/Interfaces/UseCases/PagamentoUseCasesInterface.php")) {
    require "./src/Interfaces/UseCases/PagamentoUseCasesInterface.php";
} else {
    require "../Interfaces/UseCases/PagamentoUseCasesInterface.php";
}

use Pagamento\Gateways\PagamentoGateway;
use Pagamento\Interfaces\UseCases\PagamentoUseCasesInterface;

class PagamentoUseCases implements PagamentoUseCasesInterface
{
    public function cadastrar(PagamentoGateway $pagamentoGateway, $dados)
    {
        $camposObrigatorios = ["IdTransacao", "DataCriacao", "IdPedido", "Cpf", "Valor", "FormaPagamento", "Status"];
        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                throw new \Exception("O campo $campo é obrigatório.", 400);
            }
        }
        return $pagamentoGateway->cadastrar($dados);
    }

    public function atualizarStatusPagamentoPorIdPedido(PagamentoGateway $pagamentoGateway, $idPedido, $status)
    {
        return $pagamentoGateway->atualizarStatusPagamentoPorIdPedido($idPedido, $status);
    }

    public function excluir(PagamentoGateway $pagamentoGateway, $id)
    {
        return $pagamentoGateway->excluir("$id");
    }
}
