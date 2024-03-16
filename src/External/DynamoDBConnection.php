<?php

namespace Pagamento\External;

if (file_exists("./dynamoDbCredentials.php")) {
    require "./dynamoDbCredentials.php";
    require "./src/Interfaces/DbConnection/DbConnectionNoSQLInterface.php";
    require "./vendor/autoload.php";
} else {
    require "../../dynamoDbCredentials.php";
    require "../Interfaces/DbConnection/DbConnectionNoSQLInterface.php";
    require "../../vendor/autoload.php";
}

use Aws\Credentials\Credentials;
use Pagamento\Interfaces\DbConnection\DbConnectionNoSQLInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

class DynamoDBConnection implements DbConnectionNoSQLInterface
{
    private $credentials;
    private $region;

    public function __construct()
    {
        $this->credentials = new Credentials(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);
        $this->region = 'us-east-1';
    }
    public function conectar()
    {
        $dynamodb = new DynamoDbClient([
            'version'     => 'latest',
            'region'      => $this->region,
            'credentials' => $this->credentials,
        ]);

        return $dynamodb;
    }

    public function inserir(string $nomeTabela, array $parametros)
    {
        $dynamodb = $this->conectar();
        $marshaler = new Marshaler();

        $tableName = $nomeTabela;

        $item = [
            'IdTransacao' => "{$parametros["IdTransacao"]}",
            'DataCriacao' => "{$parametros["DataCriacao"]}",
            'IdPedido' => "{$parametros["IdPedido"]}",
            'Cpf' => "{$parametros["Cpf"]}",
            'Valor' => "{$parametros["Valor"]}",
            'FormaPagamento' => "{$parametros["FormaPagamento"]}",
            'Status' => "{$parametros["Status"]}"
        ];

        $params = [
            'TableName' => $tableName,
            'Item'      => $marshaler->marshalItem($item),
        ];

        $result = $dynamodb->putItem($params);

        return $result['@metadata']['statusCode'] == 200;
    }

    public function atualizarStatusPagamentoPorIdPedido(string $nomeTabela, int $idPedido, string $status)
    {
        $dynamodb = $this->conectar();
        $tableName = $nomeTabela;

        $updateExpression = 'SET #attributeName = :attributeValue';
        $expressionAttributeNames = ['#attributeName' => 'Status'];
        $expressionAttributeValues = [':attributeValue' => ['S' => $status]];

        $result = $dynamodb->updateItem([
            'TableName'                 => $tableName,
            'Key'                       => [
                'IdPedido' => ['S' => "$idPedido"],
            ],
            'UpdateExpression'          => $updateExpression,
            'ExpressionAttributeNames'  => $expressionAttributeNames,
            'ExpressionAttributeValues' => $expressionAttributeValues,
            'ReturnValues'              => 'ALL_NEW'
        ]);

        return $result['@metadata']['statusCode'] == 200;
    }

    public function excluir(string $nomeTabela, string $idPedido)
    {
        $dynamodb = $this->conectar();
        $tableName = $nomeTabela;

        $result = $dynamodb->deleteItem(
            [
                'TableName' => $tableName,
                'Key' => [
                    'IdPedido' => ['S' => $idPedido],
                ],
            ]
        );

        return $result['@metadata']['statusCode'] == 200;
    }
}
