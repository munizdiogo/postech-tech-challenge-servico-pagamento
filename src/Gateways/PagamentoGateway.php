<?php

namespace Pagamento\Gateways;

require "./src/Interfaces/Gateways/PagamentoGatewayInterface.php";

use Pagamento\Interfaces\DbConnection\DbConnectionNoSQLInterface;
use Pagamento\Interfaces\Gateways\PagamentoGatewayInterface;

class PagamentoGateway implements PagamentoGatewayInterface
{
    private $repositorioDados;
    private $nomeTabelaPagamentos = "pagamentos";
    public function __construct(DbConnectionNoSQLInterface $database)
    {
        $this->repositorioDados = $database;
    }

    public function cadastrar($dados)
    {
        $cadastrarPagamento = $this->repositorioDados->inserir($this->nomeTabelaPagamentos, $dados);
        return $cadastrarPagamento;
    }
    public function excluir($id)
    {
        $cadastrarPagamento = $this->repositorioDados->excluir($this->nomeTabelaPagamentos, $id);
        return $cadastrarPagamento;
    }
}
