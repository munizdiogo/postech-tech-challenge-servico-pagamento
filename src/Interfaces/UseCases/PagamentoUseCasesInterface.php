<?php

namespace Pagamento\Interfaces\UseCases;

use Pagamento\Gateways\PagamentoGateway;

interface PagamentoUseCasesInterface
{
    public function cadastrar(PagamentoGateway $pagamentoGateway, Array $dados);
}
