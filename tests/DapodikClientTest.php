<?php

namespace Smansage\Dapodik\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Smansage\Dapodik\Dapodik;
use Smansage\Dapodik\DapodikClient;
use Smansage\Dapodik\Exceptions\DapodikAuthException;
use Smansage\Dapodik\Exceptions\DapodikException;
use Smansage\Dapodik\Exceptions\DapodikHttpException;

class DapodikClientTest extends TestCase
{
    protected function createMockClient(array $responses): DapodikClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handlerStack]);

        $client = new DapodikClient([
            'npsn' => '20300001',
            'token' => 'secret-token-12345',
            'host' => '192.168.1.50',
            'port' => 5774,
        ]);

        // Inject Guzzle Client with Reflection
        $ref = new \ReflectionClass($client);
        $prop = $ref->getProperty('httpClient');
        $prop->setAccessible(true);
        $prop->setValue($client, $guzzle);

        return $client;
    }

    public function test_validates_required_config()
    {
        $this->expectException(DapodikException::class);
        new DapodikClient(['npsn' => '', 'token' => '123']);
    }

    public function test_get_sekolah_returns_collection()
    {
        $json = json_encode([
            'status' => 'success',
            'rows' => [
                ['sekolah_id' => 'abc', 'nama' => 'SMA Negeri 1 Gedeg', 'npsn' => '20300001']
            ]
        ]);

        $client = $this->createMockClient([
            new Response(200, ['Content-Type' => 'application/json'], $json)
        ]);

        $result = $client->getSekolah();
        $this->assertEquals('SMA Negeri 1 Gedeg', $result->first()['nama']);
        $this->assertEquals(1, $result->count());
    }

    public function test_get_sekolah_handles_single_object_rows()
    {
        $json = json_encode([
            'status' => 'success',
            'rows' => ['sekolah_id' => 'abc', 'nama' => 'SMA Negeri 1 Gedeg', 'npsn' => '20300001']
        ]);

        $client = $this->createMockClient([
            new Response(200, ['Content-Type' => 'application/json'], $json)
        ]);

        $result = $client->sekolah();
        $this->assertEquals('SMA Negeri 1 Gedeg', $result->first()['nama']);
        $this->assertEquals(1, $result->count());
    }

    public function test_get_peserta_didik_alias_pd()
    {
        $json = json_encode([
            'status' => 'success',
            'rows' => [
                ['peserta_didik_id' => 'p1', 'nama' => 'Budi'],
                ['peserta_didik_id' => 'p2', 'nama' => 'Siti'],
            ]
        ]);

        $client = $this->createMockClient([
            new Response(200, ['Content-Type' => 'application/json'], $json)
        ]);

        $result = $client->pd();
        $this->assertEquals(2, $result->count());
        $this->assertEquals('Budi', $result->first()['nama']);
    }

    public function test_auth_exception_on_401()
    {
        $this->expectException(DapodikAuthException::class);

        $client = $this->createMockClient([
            new Response(401, [], 'Unauthorized')
        ]);

        $client->getSekolah();
    }

    public function test_http_exception_on_500()
    {
        $this->expectException(DapodikHttpException::class);

        $client = $this->createMockClient([
            new Response(500, [], 'Internal Server Error')
        ]);

        $client->getGtk();
    }

    public function test_dapodik_factory_instance()
    {
        $dapodik = new Dapodik('127.0.0.1', 5774);
        $client = $dapodik->api('my-token', '20300001');

        $this->assertInstanceOf(DapodikClient::class, $client);
        $this->assertEquals('20300001', $client->getNpsn());
    }

    public function test_get_mata_pelajaran_and_matev_nilai()
    {
        $mapelJson = json_encode(['status' => 'success', 'rows' => [['mata_pelajaran_id' => 1, 'nama' => 'Matematika']]]);
        $matevJson = json_encode(['status' => 'success', 'rows' => [['id_evaluasi' => 'e1', 'nama_mata_pelajaran' => 'Matematika']]]);

        $client = $this->createMockClient([
            new Response(200, ['Content-Type' => 'application/json'], $mapelJson),
            new Response(200, ['Content-Type' => 'application/json'], $matevJson),
        ]);

        $mapel = $client->mataPelajaran('20241');
        $this->assertEquals('Matematika', $mapel->first()['nama']);

        $matev = $client->matevNilai(['semester_id' => '20241']);
        $this->assertEquals('e1', $matev->first()['id_evaluasi']);
    }

    public function test_post_nilai_rapor()
    {
        $respJson = json_encode(['status' => 'success', 'message' => 'Tersimpan']);
        $client = $this->createMockClient([
            new Response(200, ['Content-Type' => 'application/json'], $respJson),
        ]);

        $resp = $client->postNilai([['nilai' => 90]], ['semester_id' => '20241']);
        $this->assertEquals('Tersimpan', $resp->get('message'));
    }

    public function test_fetch_all_peserta_didik_pagination()
    {
        $page1 = json_encode(['status' => 'success', 'rows' => [['nama' => 'A'], ['nama' => 'B']]]);
        $page2 = json_encode(['status' => 'success', 'rows' => [['nama' => 'C']]]);

        $client = $this->createMockClient([
            new Response(200, ['Content-Type' => 'application/json'], $page1),
            new Response(200, ['Content-Type' => 'application/json'], $page2),
        ]);

        $all = $client->fetchAllPesertaDidik(2);
        $this->assertEquals(3, $all->count());
        $this->assertEquals(['A', 'B', 'C'], $all->pluck('nama')->all());
    }
}
