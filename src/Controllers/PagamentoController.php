<?php

namespace Pagamento\Controllers;

require "./src/Interfaces/Controllers/PagamentoControllerInterface.php";

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
}
