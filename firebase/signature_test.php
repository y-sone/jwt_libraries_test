<?php

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FirebaseJWT
{
    private string $signer;
    private string $private_key;
    private string $public_key;

    public function __construct()
    {
        $this->signer = 'RS256';
        $this->private_key = file_get_contents('keys/jwt_test.key');
        $this->public_key = file_get_contents('keys/jwt_test.key.pub');
    }

    // tokenの生成
    public function createToken(): string
    {
        $payload = [
            'iss' => 'hogehoge',
            'sub' => 'Hello!',
            'name' => 'fugafuga',
            'admin' => true,
            'iat' => time()
        ];

        return JWT::encode($payload, $this->private_key, $this->signer);
    }

    // tokenの検証
    public function checkToken(string $jwt): bool
    {
        try {
            // 署名検証
            $decoded = JWT::decode($jwt, new Key($this->public_key, $this->signer));
            // iss検証
            if ($decoded->iss !== 'hogehoge') {
                throw new Exception('iss error');
            }
            // sub検証
            if ($decoded->sub !== 'Hello!') {
                throw new Exception('sub error');
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    // tokenからデータを取得
    public function getData(string $jwt): array
    {
        return (array)JWT::decode($jwt, new Key($this->public_key, $this->signer));
    }
}

$firebase_jwt = new FirebaseJWT();
$jwt = $firebase_jwt->createToken();
$firebase_jwt->checkToken($jwt);
$firebase_jwt->getData($jwt);
