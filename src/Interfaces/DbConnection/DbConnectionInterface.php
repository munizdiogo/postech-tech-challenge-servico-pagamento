<?php

namespace Pagamento\Interfaces\DbConnection;

interface DbConnectionInterface
{
    public function conectar();
    public function inserir(string $nomeTabela, array $parametros);
    public function buscarPorParametros(string $nomeTabela, array $campos, array $parametros): array;
    public function atualizar(string $nomeTabela, int $id, array $parametros): bool;
    public function excluir(string $nomeTabela, int $id): bool;
    public function buscarTodosPedidosPorCpf(string $nomeTabela, string $cpf);
}
