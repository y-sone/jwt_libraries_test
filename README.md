# jwt_libraries_test
- PHPのJWTライブラリを色々試す
  - インストール
```shell
% docker-compose exec app bash

% composer require firebase/php-jwt
% composer require lcobucci/jwt
```
  - firebase/php-jwt
    https://github.com/firebase/php-jwt
  - lcobucci/jwt
    https://github.com/lcobucci/jwt
- キーペアの生成
  https://www.millionwaves.com/development/auth/3180
```shell
% docker-compose exec app bash

% ssh-keygen -t rsa -b 4096 -f jwt_test.key
% ssh-keygen -p -f jwt_test.key -m pem
% openssl rsa -in jwt_test.key -pubout -outform PEM -out jwt_test.key.pub
```
- 元の記事
  https://qiita.com/y_sone/items/a188d50bbcb9f7e8f495
