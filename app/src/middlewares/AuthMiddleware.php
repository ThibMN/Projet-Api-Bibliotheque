<?php 

namespace App\Middlewares;

use App\Utils\JWT;

class AuthMiddleware {
    public function __invoke($request, $response, $next) {
        $token = $request->getHeader('Authorization')[0] ?? '';

        if ($token) {
            try {
                $decoded = JWT::decode($token);
                if ($decoded) {
                    // Assurez-vous que $decoded est un tableau avant d'assigner des valeurs
                    if (is_array($decoded)) {
                        // Ajouter l'ID utilisateur à la requête en utilisant withAttribute
                        $request = $request->withAttribute('user_id', $decoded['user_id']);
                    } else {
                        throw new \Exception('Invalid token payload');
                    }
                } else {
                    throw new \Exception('Invalid token');
                }
            } catch (\Exception $e) {
                return $this->unauthorizedResponse($response, $e->getMessage());
            }
        } else {
            return $this->unauthorizedResponse($response, 'Token not provided');
        }

        // Proceed with the request if JWT is valid
        return $next($request, $response);
    }

    // Helper method to return an unauthorized response
    private function unauthorizedResponse($response, $message) {
        // Here, you could return a response with a 401 status code and an error message
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
}