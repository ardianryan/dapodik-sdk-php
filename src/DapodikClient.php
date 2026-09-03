<?php

namespace Smansage\Dapodik;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Smansage\Dapodik\Exceptions\DapodikAuthException;
use Smansage\Dapodik\Exceptions\DapodikConnectionException;
use Smansage\Dapodik\Exceptions\DapodikException;
use Smansage\Dapodik\Exceptions\DapodikHttpException;

class DapodikClient
{
    protected string $baseUrl;
    protected string $npsn;
    protected string $token;
    protected float $timeout;
    protected GuzzleClient $httpClient;

    /**
     * @param array{
     *     host?: string,
     *     port?: int|string,
     *     baseUrl?: string,
     *     npsn: string,
     *     token: string,
     *     timeout?: float
     * } $config
     * @throws DapodikException
     */
    public function __construct(array $config)
    {
        if (empty($config['npsn'])) {
            throw new DapodikException("NPSN wajib diisi");
        }

        if (preg_match('/[\r\n]/', (string) $config['npsn'])) {
            throw new DapodikException("NPSN tidak boleh mengandung karakter newline");
        }

        if (empty($config['token'])) {
            throw new DapodikException("Token WebService Dapodik wajib diisi");
        }

        if (preg_match('/[\r\n]/', (string) $config['token'])) {
            throw new DapodikException("Token tidak boleh mengandung karakter newline (CRLF injection prevention)");
        }

        $this->npsn = trim((string) $config['npsn']);
        $this->token = trim((string) $config['token']);
        $this->timeout = (float) ($config['timeout'] ?? 30.0);

        if (!empty($config['baseUrl'])) {
            $this->baseUrl = rtrim($config['baseUrl'], '/');
        } else {
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 5774;
            $normalizedHost = str_starts_with($host, 'http://') || str_starts_with($host, 'https://')
                ? $host
                : 'http://' . $host;
            $this->baseUrl = "{$normalizedHost}:{$port}/WebService";
        }

        $this->httpClient = new GuzzleClient([
            'base_uri' => $this->baseUrl . '/',
            'timeout' => $this->timeout,
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json, text/plain, */*',
                'User-Agent' => '@smansage/dapodik-sdk-php',
            ],
        ]);
    }

    /**
     * Mengirim request ke WebService Dapodik
     *
     * @param string $method
     * @param string $endpoint
     * @param array $queryParams
     * @param mixed $body
     * @return Collection
     * @throws DapodikException
     */
    public function request(string $method, string $endpoint, array $queryParams = [], mixed $body = null): Collection
    {
        $cleanEndpoint = ltrim($endpoint, '/');
        if (str_contains($cleanEndpoint, '..') || str_contains($cleanEndpoint, '\\')) {
            throw new DapodikException("Endpoint tidak valid (path traversal detected)");
        }
        $query = array_merge(['npsn' => $this->npsn], $queryParams);

        $options = [
            'query' => $query,
            'http_errors' => false,
        ];

        if ($body !== null) {
            $options['json'] = $body;
        }

        try {
            $response = $this->httpClient->request($method, $cleanEndpoint, $options);
        } catch (ConnectException $e) {
            throw new DapodikConnectionException("Gagal terhubung ke server Dapodik ({$this->baseUrl}): " . $e->getMessage(), 0, $e);
        } catch (RequestException $e) {
            throw new DapodikException("Error saat request ke Dapodik: " . $e->getMessage(), 0, $e);
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode === 401 || $statusCode === 403) {
            throw new DapodikAuthException("Akses ditolak ({$statusCode}). Periksa token atau whitelist IP client di WebService Dapodik.");
        }

        $rawBody = (string) $response->getBody();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new DapodikHttpException($statusCode, $response->getReasonPhrase(), $cleanEndpoint, $rawBody);
        }

        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DapodikException("Respons bukan JSON yang valid dari Dapodik: " . substr($rawBody, 0, 200));
        }

        if (is_array($data) && array_is_list($data)) {
            return collect($data);
        }

        if (isset($data['rows'])) {
            if (is_array($data['rows']) && !array_is_list($data['rows']) && !empty($data['rows'])) {
                // Objek tunggal (seperti getSekolah)
                return collect([$data['rows']]);
            }
            return collect($data['rows']);
        }

        if (isset($data['data']) && is_array($data['data'])) {
            return collect($data['data']);
        }

        return collect($data);
    }

    /**
     * Menarik profil dan data izin operasional sekolah
     */
    public function getSekolah(array|string $params = []): Collection
    {
        $query = is_string($params) ? ['semester_id' => $params] : $params;
        return $this->request('GET', 'getSekolah', $query);
    }

    /**
     * Menarik akun operator / pengguna Dapodik
     */
    public function getPengguna(array $params = []): Collection
    {
        return $this->request('GET', 'getPengguna', $params);
    }

    /**
     * Menarik data Guru dan Tenaga Kependidikan (GTK)
     */
    public function getGtk(array $params = []): Collection
    {
        return $this->request('GET', 'getGtk', $params);
    }

    /**
     * Menarik data Rombongan Belajar (Kelas)
     */
    public function getRombonganBelajar(array|string $params = []): Collection
    {
        $query = is_string($params) ? ['semester_id' => $params] : $params;
        return $this->request('GET', 'getRombonganBelajar', $query);
    }

    /**
     * Menarik data Peserta Didik (Siswa)
     */
    public function getPesertaDidik(array $params = []): Collection
    {
        return $this->request('GET', 'getPesertaDidik', $params);
    }

    /**
     * Menarik referensi mata pelajaran nasional
     */
    public function getMataPelajaran(array|string $params = []): Collection
    {
        $query = is_string($params) ? ['semester_id' => $params] : $params;
        return $this->request('GET', 'getMataPelajaran', $query);
    }

    /**
     * Menarik data mata evaluasi nilai
     */
    public function getMatevNilai(array $params = []): Collection
    {
        return $this->request('GET', 'getMatevNilai', $params);
    }

    /**
     * Mengirim data via HTTP POST ke WebService Dapodik
     */
    public function post(string $endpoint, mixed $body, array $params = []): Collection
    {
        return $this->request('POST', $endpoint, $params, $body);
    }

    /**
     * Mengirim data Mata Evaluasi Rapor
     */
    public function postMatevRapor(mixed $body, array $params = []): Collection
    {
        return $this->post('postMatevRapor', $body, $params);
    }

    /**
     * Mengirim data Nilai Rapor ke tabel rapor
     */
    public function postNilai(mixed $body, array $params = []): Collection
    {
        return $this->post('postNilai', $body, array_merge(['table' => 'rapor'], $params));
    }

    /**
     * Menarik seluruh data Peserta Didik secara otomatis dengan paging
     */
    public function fetchAllPesertaDidik(int $limit = 100, int $delayMs = 0, ?callable $onProgress = null): Collection
    {
        $allItems = [];
        $page = 1;

        while (true) {
            $rows = $this->getPesertaDidik(['page' => $page, 'limit' => $limit]);
            $count = $rows->count();

            if ($count === 0) {
                break;
            }

            $allItems = array_merge($allItems, $rows->all());

            if ($onProgress) {
                $onProgress($page, $count, count($allItems));
            }

            if ($count < $limit) {
                break;
            }

            $page++;

            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }

        return collect($allItems);
    }

    /**
     * Menarik seluruh data GTK secara otomatis dengan paging
     */
    public function fetchAllGtk(int $limit = 100, int $delayMs = 0, ?callable $onProgress = null): Collection
    {
        $allItems = [];
        $page = 1;

        while (true) {
            $rows = $this->getGtk(['page' => $page, 'limit' => $limit]);
            $count = $rows->count();

            if ($count === 0) {
                break;
            }

            $allItems = array_merge($allItems, $rows->all());

            if ($onProgress) {
                $onProgress($page, $count, count($allItems));
            }

            if ($count < $limit) {
                break;
            }

            $page++;

            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }

        return collect($allItems);
    }

    // =========================================================================
    // Aliases (Kompatibel dengan pustaka PHP adereksisusanto)
    // =========================================================================

    public function sekolah(array|string $params = []): Collection
    {
        return $this->getSekolah($params);
    }

    public function pengguna(array $params = []): Collection
    {
        return $this->getPengguna($params);
    }

    public function gtk(array $params = []): Collection
    {
        return $this->getGtk($params);
    }

    public function rombel(array|string $params = []): Collection
    {
        return $this->getRombonganBelajar($params);
    }

    public function pd(array $params = []): Collection
    {
        return $this->getPesertaDidik($params);
    }

    public function mataPelajaran(array|string $params = []): Collection
    {
        return $this->getMataPelajaran($params);
    }

    public function matevNilai(array $params = []): Collection
    {
        return $this->getMatevNilai($params);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getNpsn(): string
    {
        return $this->npsn;
    }
}
