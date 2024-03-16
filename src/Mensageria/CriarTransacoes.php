<?php

require '../../config.php';
require '../../vendor/autoload.php';
require "../External/DynamoDBConnection.php";
require "../Controllers/PagamentoController.php";

use Pagamento\Controllers\PagamentoController;
use Pagamento\External\DynamoDBConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$dbConnectionNoSQL = new DynamoDBConnection();
$pagamentoController = new PagamentoController();

$connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USERNAME, RABBITMQ_PASSWORD);
$channel = $connection->channel();

$channel->queue_declare('pagamentos_pendentes', false, true, false, false);

$callback = function ($msg) use ($pagamentoController, $dbConnectionNoSQL) {

    $dadosArray = json_decode($msg->body, true);

    if (empty($dadosArray["idPedido"]) || empty($dadosArray["cpf"]) || empty($dadosArray["valor"])) {
        echo "Campo obrigatório não informado";
    } else {
        $dadosPagamento = [
            "IdTransacao" => random_string(10),
            "DataCriacao" => date('Y-m-d H:i:s'),
            "IdPedido" => $dadosArray["idPedido"],
            "Cpf" => $dadosArray["cpf"],
            "Valor" => $dadosArray["valor"],
            "FormaPagamento" => "cartao",
            "Status" => "pendente"
        ];
        $cadastrarPagamentoPendente = $pagamentoController->cadastrar($dbConnectionNoSQL, $dadosPagamento);
        if (!$cadastrarPagamentoPendente) {
            echo "Ocorreu um erro ao salvar os dados do pagamento. Dados: " . json_encode($dadosPagamento);
        }
    }
};

$channel->basic_consume('pagamentos_pendentes', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}


function random_string($length)
{
    $str = random_bytes($length);
    $str = base64_encode($str);
    $str = str_replace(["+", "/", "="], "", $str);
    $str = substr($str, 0, $length);
    return $str;
}