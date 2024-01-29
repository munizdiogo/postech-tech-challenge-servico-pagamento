<?php

namespace Pagamento\Interfaces\Entities;

interface PedidoInterface
{
    public function getStatus(): string;
    public function getCPF(): string;
    public function getProdutos(): array;
}