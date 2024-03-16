<?php

namespace Pagamento\Controllers;

if (file_exists("./src/UseCases/PagamentoUseCases.php")) {
    require "./src/UseCases/PagamentoUseCases.php";
    require "./src/Gateways/PagamentoGateway.php";
    require "./src/Interfaces/Controllers/PagamentoControllerInterface.php";
} else {
    require "../UseCases/PagamentoUseCases.php";
    require "../Gateways/PagamentoGateway.php";
    require "../Interfaces/Controllers/PagamentoControllerInterface.php";
}

use Pagamento\Gateways\PagamentoGateway;
use Pagamento\Interfaces\Controllers\PagamentoControllerInterface;
use Pagamento\UseCases\PagamentoUseCases;

class PagamentoController implements PagamentoControllerInterface
{
    public function cadastrar($dbConnectionNoSQL, array $dados): bool
    {
        $pagamentoGateway = new PagamentoGateway($dbConnectionNoSQL);
        $pagamentoUseCases = new PagamentoUseCases();

        $dadosSalvosComSucesso = $pagamentoUseCases->cadastrar($pagamentoGateway, $dados);
        return $dadosSalvosComSucesso;
    }

    public function atualizarStatusPagamentoPorIdPedido($dbConnectionNoSQL, $idPedido, $status): bool
    {
        $pagamentoGateway = new PagamentoGateway($dbConnectionNoSQL);
        $pagamentoUseCases = new PagamentoUseCases();

        $dadosSalvosComSucesso = $pagamentoUseCases->atualizarStatusPagamentoPorIdPedido($pagamentoGateway, $idPedido, $status);
        return $dadosSalvosComSucesso;
    }
}
