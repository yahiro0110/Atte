# Atte（アット）

## Outline

PHP のフレームワーク Laravel で作成された Web アプリケーション（勤怠管理システム）です。<br />
アプリケーションの詳細は Notion でまとめておりますので、[そちら](https://h-yamasita.notion.site/Atte-ed456123c4f645dfbac62ff0c5e69372?pvs=4) をご覧ください。

## Architecture

![](./img/architecture.drawio.svg)

### Requirement

-   nginx: 1.21.1
-   php: 7.4.9
-   Laravel 8.x
-   MySQL: 8.0.26

## Setup Instructions

1.  リポジトリをクローンします。

    ```bash
    git clone https://github.com/yahiro0110/Atte.git
    ```

2.  プロジェクトディレクトリに移動します。

    ```bash
    cd Atte
    ```

3.  環境変数用のファイルを用意します。

    ```bash
    cp ./src/.env.example ./src/.env
    ```

-   .env ファイル内のデータベース接続設定を次のように書きかえてください。

    ```markdown
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_db
    DB_USERNAME=laravel_user
    DB_PASSWORD=laravel_pass
    ```

4.  Docker コンテナを起動します。

    ```bash
    docker-compose up -d --build
    ```

5.  PHP コンテナ(Application server)へログインし、Laravel アプリケーションの準備をします。

    ```bash
    # PHPコンテナ(Application server)へのログイン
    docker-compose exec php bash

    # Laravelアプリケーションの依存関係をインストール
    composer update

    # アプリケーションキーの生成
    php artisan key:generate

    # データベーステーブルの作成
    php artisan migrate

    # 初期データの投入
    php artisan db:seed
    ```

6.  以下の URL にアクセスし、ログイン画面を表示します。

-   ログイン画面 (マネージャー用) http://localhost/login/manager

    -   メールアドレス：manager@example.com
    -   パスワード：password-manager

-   ログイン画面 (スタッフ用) http://localhost/login

    -   メールアドレス：staff@example.com
    -   パスワード：password-staff

7. もしアカウントを新規で登録したい場合は以下の URL にアクセスしてください。

-   新規登録画面 (マネージャー用) http://localhost/register/manager
-   新規登録画面 (スタッフ用) http://localhost/register
