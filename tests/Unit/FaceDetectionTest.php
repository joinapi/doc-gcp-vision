<?php


namespace Unit;


use Google\ApiCore\ValidationException;
use Joinapi\DocGcpVision\FaceDetection;
use PHPUnit\Framework\TestCase;


class FaceDetectionTest extends TestCase

{
    /**
     * @throws ValidationException
     */
    public function testDetectFaces()
    {

        $detection = new FaceDetection([
            'keyFile' => 'credentials.json',
        ]);

        $results = $detection->detectFaces('/media/rux/projects/political-flow/docs/docs/face.png');

        $this->assertIsArray($results);
        $this->assertNotEmpty($results, 'Nenhuma face encontrada.');
    }

}