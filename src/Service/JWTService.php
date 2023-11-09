<?php
namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    // Génération du token JSON WebToken   (JWT)

    /**
     * 
     * 
     */

    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if($validity > 0){

            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }


        // encodage en base64

        $base64Header = base64_encode(json_encode(($header)));
        $base64Payload = base64_encode(json_encode(($payload)));

        // Nettoyage des valeurs encodées ( retrait des +, / et = qui ne sont pas utiles pour le token)

        $base64Header = str_replace(['+', '/', '='], ['-', '_', '']
        , $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', '']
        , $base64Payload);

        // Génération de la signature

        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true); // true pour sortie binaire

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', '']
        , $base64Signature);

        // Création du token 

        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;


        return $jwt;
        
    }

    // Vérification de la validité du token (correctement formé)

    public function isValid(string $token): bool 
    {
        return preg_match('/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/', $token) === 1;
    }

    // Récupération du payload

    public function getPayload(string $token): array
    {
        // Démontage du tocken

        $array = explode('.', $token);

        // Décodage du payload

        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;

    }

        // Récupération du header

        public function getHeader(string $token): array
        {
            // Démontage du token
  
            $array = explode('.', $token);
    
            // Décodage du header
    
            $header = json_decode(base64_decode($array[0]), true);
    
            return $header;
    
        }
    // Vérification si la token a expiré

    public function isExpired (string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // Vérification de la signature du token

    public function check(string $token, string $secret): bool 
    {
        // Récupération du header et du payload

        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // Régénération du token
        $verifToken = $this->generate($header, $payload, $secret, 0); // on met 0 pour éviter d'écraser les valeurs d'expiration
        
        return $token === $verifToken;

    }

}