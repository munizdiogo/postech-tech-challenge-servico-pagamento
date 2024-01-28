<?php

namespace Pagamento\External;

require "config.php";
require "./src/Interfaces/DbConnection/DbConnectionNoSQLInterface.php";
require "vendor/autoload.php";

use Aws\Credentials\Credentials;
use Pagamento\Interfaces\DbConnection\DbConnectionNoSQLInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

class DynamoDBConnection implements DbConnectionNoSQLInterface
{
    private $credentials;
    private $region;

    public function __construct()
    {
        $this->credentials = new Credentials(AWS_ACCESS_KEY, AWS_ACCESS_SECRET);
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

    public function excluir(string $nomeTabela, string $id)
    {
        $dynamodb = $this->conectar();
        $tableName = $nomeTabela;

        $result = $dynamodb->deleteItem(
            [
                'TableName' => $tableName,
                'Key' => [
                    'IdTransacao' => ['S' => $id],
                ],
            ]
        );

        return $result['@metadata']['statusCode'] == 200;
    }
}
