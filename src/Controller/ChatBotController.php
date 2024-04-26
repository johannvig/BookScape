<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatBotController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/chatbot', name: 'chatbot', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $messages = $content['messages'] ?? [];

        // Assurez-vous que l'URL est correcte et à jour
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        // Vérifiez que la clé API est correctement configurée dans vos paramètres
        $apiKey = $this->getParameter('openai.api_key');
        if (empty($apiKey)) {
            return new Response("La clé API OpenAI n'est pas configurée.", 500);
        }

        try {
            $response = $this->httpClient->request('POST', $apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo', // Assurez-vous d'utiliser le modèle correct
                    'messages' => $messages,
                    'max_tokens' => 75,
                ],
            ]);

            $data = $response->toArray();

            if (isset($data['choices'][0]['message']['content'])) {
                return new Response($data['choices'][0]['message']['content']);
            } else {
                return new Response("Erreur lors de la réception de la réponse de l'API.", 500);
            }
        } catch (\Exception $e) {
            // Gestion des erreurs de requête HTTP
            return new Response("Erreur lors de la requête : " . $e->getMessage(), 500);
        }
    }
}
