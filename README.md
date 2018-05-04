# cakephp-jwt-claims

[![Build Status](https://img.shields.io/travis/ramosisw/cakephp-jwt-claims/master.svg?style=flat-square)](https://travis-ci.org/ramosisw/cakephp-jwt-claims)
[![Coverage](https://img.shields.io/codecov/c/github/ramosisw/cakephp-jwt-claims.svg?style=flat-square)](https://codecov.io/github/ramosisw/cakephp-jwt-claims)
[![Total Downloads](https://img.shields.io/packagist/dt/ramosisw/cakephp-jwt-claims.svg?style=flat-square)](https://packagist.org/packages/ramosisw/cakephp-jwt-claims)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.txt)

CakePHP 3.6+ Component to read claims of JWT on Authorization 


## Installation

```sh
composer require ramosisw/cakephp-jwt-claims
```

## Configuration:

Setup `ClaimsComponent`:

```php

    // In your controller, for e.g. src/Controller/AppController.php
    public function initialize()
    {
        parent::initialize();
        //Config JWT with ADmad Plugin for more info see https://github.com/ADmad/cakephp-jwt-auth
        $this->loadComponent('Auth', [/*..*/]);
        //Load Claims component
        $this->loadComponent('RamosISW/Jwt.Claims',[
            'claims_key' => 'data', //name where is claims on JWT Payload
            //Wath claims be read
            'data' => [
                'user_id', 'user_email', 'user_name'
            ]
        ]);
    }
```

## Working

To read claims on route that user can access


```php
    public function index(){
        //read user email sends on token
        $this->log($this->Claims->user_email);
        
        //set claims to use on view
        $this->set('Claims', $this->Claims);
    }
```

## Token Generation

You can use `\Firebase\JWT\JWT::encode()` of the [firebase/php-jwt](https://github.com/firebase/php-jwt)
lib, which this plugin depends on, to generate tokens.

**The payload should have the "sub" (subject) claim whos value is used to query the
Users model and find record matching the "id" field.**

**Generate Claims**
```php
    public function generateToken($user){
        $token = \Firebase\JWT\JWT::encode([
                'sub' => $user['id'],
                'exp' => time() + 604800,
                'data' => [
                    'user_id' => $user['id'],
                    'user_email' => $user['email'],
                    'user_name' => $user['username']
                ]
        ], \Cake\Utility\Security::getSalt());
        
        return $token;
    }
```
