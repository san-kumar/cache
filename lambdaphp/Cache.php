<?php

namespace LambdaPHP {

    use Aws\DynamoDb\DynamoDbClient;

    class Cache {
        protected $table_name;
        /**
         * @var DynamoDbClient
         */
        private $client;

        /**
         * Cache constructor.
         * @param DynamoDbClient $client
         * @param string $table_name
         */
        public function __construct(DynamoDbClient $client, string $table_name = 'lambda_cache') {
            $this->client = $client;
            $this->table_name = $table_name;
        }

        public function get(string $key, $default = null, $expires = 86400) {
            if ($result = $this->client->getItem(['TableName' => $this->table_name, 'Key' => ['id' => ['S' => $key]], 'ConsistentRead' => false,])) {
                $result = isset($result['Item']) ? $result['Item'] : [];
                foreach ($result as $key => $value) {
                    $item[$key] = current($value);
                }

                $data = $item['expires'] < time() ? json_decode($item['data'], true) : null;
            }

            if (empty($data) && !empty($default)) {
                if ($data = $default instanceof \Closure ? $default() : $default) {
                    $this->set($key, $data, $expires);
                }
            }

            return $data;
        }

        public function set($key, $value, $expires = 86400) {
            return $this->client->updateItem([
                'TableName' => $this->table_name,
                'Key' => ['id' => ['S' => $key]],
                'AttributeUpdates' => [
                    'expires' => ['Value' => ['N' => (string) (time() + $expires)]],
                    'data' => ['Value' => ['S' => json_encode($value)]],
                ],
            ]);
        }
    }
}