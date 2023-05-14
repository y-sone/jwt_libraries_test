<?php

require_once 'vendor/autoload.php';

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\RSA\Sha256;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\Clock\FrozenClock;

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
        $datetime = new DateTimeImmutable();

        $token = $this->config->builder()
            ->issuedBy('hogehoge') // iss
            ->relatedTo('Hello!') // sub
            ->withClaim('name', 'fugafuga') // name(パブリッククレーム)
            ->withClaim('admin', true) // admin(プライベートクレーム)
            ->issuedAt($datetime) // iat
            ->expiresAt($datetime->add(new DateInterval('PT1H'))) // exp
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    // tokenの検証
    public function checkToken(string $jwt): bool
    {
        $datetime = new DateTimeImmutable();

        $token = $this->config->parser()->parse($jwt);
        $this->config->setValidationConstraints(...[
            // 署名検証
            new SignedWith($this->config->signer(), $this->config->verificationKey()),
            // iss検証
            new IssuedBy('hogehoge'),
            // sub検証
            new RelatedTo('Hello!'),
            // iat, exp検証
            new LooseValidAt(new FrozenClock($datetime)),
//            new StrictValidAt(new FrozenClock($datetime)),
        ]);

        // バリデーションエラーを例外で返したい場合はassert()を使用する
        return $this->config->validator()->validate($token, ...$this->config->validationConstraints());
//        return $this->config->validator()->assert($token, ...$this->config->validationConstraints());
    }

    // tokenからデータを取得
    public function getData(string $jwt): array
    {
        $token = $this->config->parser()->parse($jwt);

        return $token->claims()->all();
    }
}

try {
    $lcobucci_jwt = new LcobucciJWT();
    $jwt = $lcobucci_jwt->createToken();
    $result = $lcobucci_jwt->checkToken($jwt);
    $data = $lcobucci_jwt->getData($jwt);
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}
