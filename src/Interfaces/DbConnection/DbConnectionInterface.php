<?php

namespace Pagamento\Interfaces\DbConnection;

interface DbConnectionInterface
{
    public function conectar();
    public function obterPorCpf($obterPorCpf, $cpf);
    public function inserir(string $nomeTabela, array $parametros);
    public function excluir(string $nomeTabela, int $id): bool;
    public function atualizar(string $nomeTabela, int $id, array $parametros): bool;
    public function buscarTodosPedidosPorCpf(string $nomeTabela, string $cpf): array;
    public function buscarPorParametros(string $nomeTabela, array $campos, array $parametros);
}
