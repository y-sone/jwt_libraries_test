<?php

require_once 'vendor/autoload.php';

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\RSA\Sha256;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;

class LcobucciJWT
{
    private Configuration $config;

    public function __construct()
    {
        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file('keys/jwt_test.key'),
            InMemory::file('keys/jwt_test.key.pub')
        );
    }

    // tokenの生成
    public function createToken(): string
    {
        $token = $this->config->builder()
            ->issuedBy('hogehoge')              // iss
            ->relatedTo('Hello!')               // sub
            ->withClaim('name', 'fugafuga')     // name(パブリッククレーム)
            ->withClaim('admin', true)          // admin(プライベートクレーム)
            ->issuedAt(new DateTimeImmutable()) // iat
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    // tokenの検証
    public function checkToken(string $jwt): bool
    {
        $token = $this->config->parser()->parse($jwt);
        $this->config->setValidationConstraints(...[
            // 署名検証
            new SignedWith($this->config->signer(), $this->config->verificationKey()),
            // iss検証
            new IssuedBy('hogehoge'),
            // sub検証
            new RelatedTo('Hello!')
        ]);

        // バリデーションエラーを例外で返したい場合はassert()を使用する
        return $this->config->validator()->validate($token, ...$this->config->validationConstraints());
    }

    // tokenからデータを取得
    public function getData(string $jwt): array
    {
        $token = $this->config->parser()->parse($jwt);

        return $token->claims()->all();
    }
}

$lcobucci_jwt = new LcobucciJWT();
$jwt = $lcobucci_jwt->createToken();
$lcobucci_jwt->checkToken($jwt);
$lcobucci_jwt->getData($jwt);
