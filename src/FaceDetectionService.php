<?php

namespace Joinapi\DocGcpVision;

use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

use Google\Cloud\Vision\V1\FaceAnnotation;

class FaceDetectionService
{
    protected  $client;

    /**
     * @throws ValidationException
     */
    public function __construct(array $config = [])
    {
        // Configuração do cliente Google Vision (necessário o arquivo de credenciais JSON)
        $this->client = new ImageAnnotatorClient
        ([
            'credentials' => $config['keyFile'] ?? null,
        ]);
    }

    /**
     * @param string $imagePath
     * @return array
     * @throws ApiException
     */
    public function detectFaces(string $imagePath): array
    {
        // Verifica se o arquivo existe ou é uma URL
        if (!filter_var($imagePath, FILTER_VALIDATE_URL) && !file_exists($imagePath)) {
            throw new \InvalidArgumentException("O caminho para a imagem é inválido.");
        }

        // Carrega o conteúdo da imagem
        $imageContent = file_get_contents($imagePath);

        // Envia a imagem para o Google Vision
        $response = $this->client->faceDetection($imageContent);

        // Processa a resposta
        $faces = $response->getFaceAnnotations();
        if (!$faces) {
            return []; // Nenhuma face detectada
        }

        $result = [];
        foreach ($faces as $face) {
            $result[] = [
                'boundingPoly' => $this->formatBoundingPoly($face->getBoundingPoly()),
                'landmarks' => $this->formatLandmarks($face->getLandmarks()),
                'detectionConfidence' => $face->getDetectionConfidence(),
            ];
        }

        return $result;
    }
    /**
     * Formata o Bounding Poly (contorno da face)
     *
     * @param \Google\Cloud\Vision\V1\BoundingPoly $boundingPoly
     * @return array
     */
    protected function formatBoundingPoly($boundingPoly): array
    {
        $vertices = [];
        foreach ($boundingPoly->getVertices() as $vertex) {
            $vertices[] = [
                'x' => $vertex->getX(),
                'y' => $vertex->getY(),
            ];
        }
        return $vertices;
    }

    /**
     * Formata os marcos faciais (ex.: olhos, nariz, boca)
     *
     * @param array $landmarks
     * @return array
     */
    protected function formatLandmarks($landmarks): array
    {
        $mappedLandmarks = [];
        foreach ($landmarks as $landmark) {
            $mappedLandmarks[] = [
                'type' => $landmark->getType(),
                'position' => [
                    'x' => $landmark->getPosition()->getX(),
                    'y' => $landmark->getPosition()->getY(),
                    'z' => $landmark->getPosition()->getZ(),
                ]
            ];
        }

        return $mappedLandmarks;
    }


}