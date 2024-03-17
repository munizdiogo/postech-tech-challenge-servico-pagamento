<?php

//  RESPONSÁVEL POR REALIZAR A COBRANÇA DE UM PEDIDO GERADO ANTERIORMENTE.

header('Content-Type: application/json; charset=utf-8');
require "./config.php";
require "./utils/EnviarEmail.php";
require "./utils/RespostasJson.php";
require "./src/External/MySqlConnection.php";
require "./src/External/DynamoDBConnection.php";
require "./src/Controllers/PagamentoController.php";
require "./src/Controllers/AutenticacaoController.php";
require "./src/Controllers/PedidoController.php";
require "./src/Controllers/RabbitMqController.php";

use Autenticacao\Controllers\AutenticacaoController;
use Pagamento\External\MySqlConnection;
use Pagamento\External\DynamoDBConnection;
use Pagamento\Controllers\PagamentoController;
use Pagamento\Controllers\PedidoController;
use Pagamento\Controllers\RabbitMqController;

$dbConnection = new MySqlConnection();
$dbConnectionNoSQL = new DynamoDBConnection();
$pedidoController = new PedidoController();
$pagamentoController = new PagamentoController();

if (!empty($_GET["acao"])) {
    switch ($_GET["acao"]) {

        case "atualizarStatus":
            $id = !empty($_POST["id"]) ? (int)$_POST["id"] : 0;
            $status = $_POST["status"] ?? "";
            $cpf = !empty($_POST["cpf"]) ? str_replace([".", "-"], "",  $_POST["cpf"]) : "";

            $dadosPagamento = [
                "IdPedido" => $id,
                "Status" => $status,
                "Cpf" => $cpf
            ];

            foreach ($dadosPagamento as $chave => $valor) {
                if (empty($valor)) {
                    retornarRespostaJSON("O parametro $chave é obrigatório.", 400);
                    exit;
                }
            }

            $atualizarStatusPagamento = $pagamentoController->atualizarStatusPagamentoPorIdPedido($dbConnectionNoSQL, $id, $status);

            if (!$atualizarStatusPagamento) {
                retornarRespostaJSON("Ocorreu um erro ao salvar os dados do pagamento.", 500);
                exit;
            }

            $atualizarStatusPagamentoPedido = $pedidoController->atualizarStatusPagamentoPedido($dbConnection, $id, $status);

            if (!$atualizarStatusPagamentoPedido) {
                retornarRespostaJSON("Ocorreu um erro ao atualizar o status do pagamento do pedido.", 500);
                exit;
            }

            $mensagem = json_encode($dadosPagamento);

            if ($status == "aprovado") {
                $pedidoController->atualizarStatusPedido($dbConnection, $id, "em_preparacao");
                $rabbitMqController = new RabbitMqController();
                $rabbitMqController->enviarMsgParaQueue("pagamentos_confirmados", $mensagem);
            } else {
                $autenticacaoController = new AutenticacaoController();
                $cpf = str_replace([".", "-"], "", $cpf);
                $dadosCliente = $autenticacaoController->obterPorCpf($dbConnection, $cpf);
                $destinatario = $dadosCliente[0]["email"];
                $nome = $dadosCliente[0]["nome"];
                $assunto = "Pedido: " . $dadosPagamento["IdPedido"] . " - Pagamento Rejeitado";
                $mensagem = "O pagamento foi rejeitado, verifique os dados de pagamento e tente novamente.";
                enviarEmail($destinatario, $nome, $assunto, $mensagem);
            }

            retornarRespostaJSON("Status do pagamento do pedido atualizado com sucesso.", 200);
            break;

        default:
            echo '{"mensagem": "A ação informada é inválida."}';
            http_response_code(400);
    }
}

function random_string($length)
{
    $str = random_bytes($length);
    $str = base64_encode($str);
    $str = str_replace(["+", "/", "="], "", $str);
    $str = substr($str, 0, $length);
    return $str;
}
