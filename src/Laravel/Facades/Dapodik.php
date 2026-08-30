<?php

namespace Smansage\Dapodik\Laravel\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection getSekolah(array|string $params = [])
 * @method static Collection getPengguna(array $params = [])
 * @method static Collection getGtk(array $params = [])
 * @method static Collection getRombonganBelajar(array|string $params = [])
 * @method static Collection getPesertaDidik(array $params = [])
 * @method static Collection getMataPelajaran(array|string $params = [])
 * @method static Collection getMatevNilai(array $params = [])
 * @method static Collection post(string $endpoint, mixed $body, array $params = [])
 * @method static Collection postMatevRapor(mixed $body, array $params = [])
 * @method static Collection postNilai(mixed $body, array $params = [])
 * @method static Collection fetchAllPesertaDidik(int $limit = 100, int $delayMs = 0, ?callable $onProgress = null)
 * @method static Collection fetchAllGtk(int $limit = 100, int $delayMs = 0, ?callable $onProgress = null)
 * @method static Collection sekolah(array|string $params = [])
 * @method static Collection pengguna(array $params = [])
 * @method static Collection gtk(array $params = [])
 * @method static Collection rombel(array|string $params = [])
 * @method static Collection pd(array $params = [])
 * @method static Collection mataPelajaran(array|string $params = [])
 * @method static Collection matevNilai(array $params = [])
 *
 * @see \Smansage\Dapodik\DapodikClient
 */
class Dapodik extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'dapodik';
    }
}
