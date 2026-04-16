<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class AiController extends Controller
{
    private const HISTORY_LIMIT = 20;
    private const MAX_ATTACHMENT_SIZE = 20971520; // 20 MB
    private const MAX_SQL_RESULT_ROWS = 50;
    private const MAX_SQL_RESULT_TEXT = 15000;

    public function chat(Request $request)
    {
        $apiKey = (string) config('services.gemini.key');
        $model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $message = trim((string) $request->input('message', ''));
        $attachment = $request->file('attachment');

        if (!$apiKey) {
            return response()->json(['message' => "Gemini API anahtar\u{0131} tan\u{0131}ml\u{0131} de\u{011f}il."], 500);
        }

        if ($message === '' && !$attachment) {
            return response()->json(['message' => "Bir mesaj yaz\u{0131}n veya dosya ekleyin."], 422);
        }

        if ($attachment && !$attachment->isValid()) {
            return response()->json(['message' => "Dosya y\u{00fc}klenirken bir hata olu\u{015f}tu."], 422);
        }

        if ($attachment && (int) $attachment->getSize() > self::MAX_ATTACHMENT_SIZE) {
            return response()->json(['message' => "Dosya boyutu en fazla 20 MB olabilir."], 422);
        }

        $history = $this->normalizeHistory(Session::get('ai_history', []));
        $uploadedGeminiFile = null;

        try {
            $userParts = [];

            if ($attachment) {
                $uploadedGeminiFile = $this->uploadGeminiFile($attachment, $apiKey);

                $userParts[] = [
                    'type' => 'file',
                    'mime_type' => $uploadedGeminiFile['mime_type'],
                    'file_uri' => $uploadedGeminiFile['file_uri'],
                    'file_name' => $uploadedGeminiFile['file_name'],
                    'display_name' => $uploadedGeminiFile['display_name'],
                ];
            }

            $promptText = $message !== ''
                ? $message
                : ($attachment ? $this->defaultPromptForAttachment($attachment) : '');

            if ($promptText !== '') {
                $userParts[] = [
                    'type' => 'text',
                    'text' => $promptText,
                ];
            }

            $history[] = [
                'role' => 'user',
                'parts' => $userParts,
            ];

            $schemaSummary = $this->buildSchemaSummary();
            $plan = $this->planAssistantAction($history, $schemaSummary, $apiKey, $model);

            if (($plan['action'] ?? '') === 'sql') {
                $sqlResult = $this->executeSql($plan['sql']);
                $reply = $this->summarizeSqlResult($history, $schemaSummary, $plan, $sqlResult, $apiKey, $model);
            } else {
                $reply = trim((string) ($plan['reply'] ?? ''));
            }

            if ($reply === '') {
                if ($uploadedGeminiFile) {
                    $this->deleteGeminiFiles([$uploadedGeminiFile['file_name']], $apiKey);
                }

                return response()->json(['message' => "Yan\u{0131}t al\u{0131}namad\u{0131}. L\u{00fc}tfen tekrar deneyin."], 500);
            }

            $history[] = [
                'role' => 'model',
                'parts' => [
                    [
                        'type' => 'text',
                        'text' => $reply,
                    ],
                ],
            ];

            [$history, $expiredFileNames] = $this->trimHistory($history);

            if ($expiredFileNames !== []) {
                $this->deleteGeminiFiles($expiredFileNames, $apiKey);
            }

            Session::put('ai_history', $history);

            return response()->json(['reply' => $reply]);
        } catch (RuntimeException $e) {
            if ($uploadedGeminiFile) {
                $this->deleteGeminiFiles([$uploadedGeminiFile['file_name']], $apiKey);
            }

            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            if ($uploadedGeminiFile) {
                $this->deleteGeminiFiles([$uploadedGeminiFile['file_name']], $apiKey);
            }

            return response()->json(['message' => "Yapay zeka servisine ula\u{015f}\u{0131}lamad\u{0131}."], 500);
        }
    }

    public function reset()
    {
        $apiKey = (string) config('services.gemini.key');
        $history = Session::get('ai_history', []);

        if ($apiKey && $history !== []) {
            $this->deleteGeminiFiles($this->collectFileNames($history), $apiKey);
        }

        Session::forget('ai_history');

        return response()->json(['ok' => true]);
    }

    private function planAssistantAction(array $history, string $schemaSummary, string $apiKey, string $model): array
    {
        $response = $this->geminiRequest($apiKey)
            ->post($this->generateContentUrl($model), [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $this->buildPlannerPrompt($schemaSummary)],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                    'responseJsonSchema' => $this->plannerResponseSchema(),
                ],
                'contents' => $this->formatHistoryForGemini($history),
            ]);

        if ($response->failed()) {
            throw new RuntimeException($this->translateGeminiError($response));
        }

        return $this->parsePlannerPayload($this->extractReplyText($response));
    }

    private function summarizeSqlResult(
        array $history,
        string $schemaSummary,
        array $plan,
        array $sqlResult,
        string $apiKey,
        string $model
    ): string {
        $resultJson = json_encode($sqlResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($resultJson === false) {
            $resultJson = '{}';
        }

        if (mb_strlen($resultJson) > self::MAX_SQL_RESULT_TEXT) {
            $resultJson = mb_substr($resultJson, 0, self::MAX_SQL_RESULT_TEXT) . "\n\n[truncated]";
        }

        $contents = $this->formatHistoryForGemini($history);
        $contents[] = [
            'role' => 'user',
            'parts' => [
                [
                    'text' =>
                        "SQL execution context:\n" .
                        "reason: " . trim((string) ($plan['reason'] ?? '')) . "\n" .
                        "sql: " . $sqlResult['sql'] . "\n" .
                        "result:\n" . $resultJson . "\n\n" .
                        "Produce the final answer for the user in Turkish. Explain what happened clearly and briefly. " .
                        "Do not output JSON. Do not invent data outside the SQL result.",
                ],
            ],
        ];

        $response = $this->geminiRequest($apiKey)
            ->post($this->generateContentUrl($model), [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $this->buildSqlSummaryPrompt($schemaSummary)],
                    ],
                ],
                'contents' => $contents,
            ]);

        if ($response->failed()) {
            throw new RuntimeException($this->translateGeminiError($response));
        }

        return trim($this->extractReplyText($response));
    }

    private function executeSql(string $sql): array
    {
        $sql = $this->normalizeSql($sql);
        $command = $this->sqlCommand($sql);

        try {
            if ($command === 'select') {
                $rows = array_map(fn ($row) => (array) $row, DB::select($sql));
                $totalRows = count($rows);
                $visibleRows = array_slice($rows, 0, self::MAX_SQL_RESULT_ROWS);

                return [
                    'kind' => 'read',
                    'sql' => $sql,
                    'row_count' => $totalRows,
                    'truncated' => $totalRows > count($visibleRows),
                    'rows' => $visibleRows,
                ];
            }

            if ($command === 'insert') {
                DB::insert($sql);

                return [
                    'kind' => 'write',
                    'sql' => $sql,
                    'affected_rows' => 1,
                    'last_insert_id' => DB::getPdo()->lastInsertId(),
                ];
            }

            $affectedRows = DB::affectingStatement($sql);

            return [
                'kind' => 'write',
                'sql' => $sql,
                'affected_rows' => $affectedRows,
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException("SQL \u{00e7}al\u{0131}\u{015f}t\u{0131}r\u{0131}lamad\u{0131}: " . $e->getMessage());
        }
    }

    private function normalizeSql(string $sql): string
    {
        $sql = trim($sql);

        if ($sql === '') {
            throw new RuntimeException("SQL bo\u{015f} olamaz.");
        }

        if (str_starts_with($sql, '```')) {
            $sql = preg_replace('/^```[a-zA-Z]*\s*|\s*```$/', '', $sql) ?? $sql;
            $sql = trim($sql);
        }

        if (preg_match('/(--|\/\*|#)/', $sql)) {
            throw new RuntimeException("SQL yorum sat\u{0131}r\u{0131} i\u{00e7}eremez.");
        }

        $sql = rtrim($sql, " \r\n\t;");

        if ($sql === '') {
            throw new RuntimeException("SQL bo\u{015f} olamaz.");
        }

        if (preg_match('/;/', $sql)) {
            throw new RuntimeException("Bir istekte yaln\u{0131}zca tek SQL komutu \u{00e7}al\u{0131}\u{015f}t\u{0131}r\u{0131}labilir.");
        }

        $blocked = [
            'alter',
            'create',
            'drop',
            'truncate',
            'rename',
            'grant',
            'revoke',
            'use',
            'set',
            'call',
            'exec',
            'execute',
            'merge',
            'replace',
            'lock',
            'unlock',
            'commit',
            'rollback',
            'start transaction',
        ];

        foreach ($blocked as $keyword) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $sql)) {
                throw new RuntimeException("Bu SQL komutu izin verilmeyen bir anahtar kelime i\u{00e7}eriyor: {$keyword}");
            }
        }

        $command = $this->sqlCommand($sql);

        if (!in_array($command, ['select', 'insert', 'update', 'delete'], true)) {
            throw new RuntimeException("Yaln\u{0131}zca SELECT, INSERT, UPDATE ve DELETE komutlar\u{0131}na izin veriliyor.");
        }

        return $sql;
    }

    private function sqlCommand(string $sql): string
    {
        $firstToken = strtolower((string) strtok(ltrim($sql), " \r\n\t("));

        return $firstToken;
    }

    private function parsePlannerPayload(string $text): array
    {
        $payload = trim($text);

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/is', $payload, $matches)) {
            $payload = trim($matches[1]);
        } elseif (preg_match('/\{.*\}/s', $payload, $matches)) {
            $payload = trim($matches[0]);
        }

        $data = json_decode($payload, true);

        if (!is_array($data) || !isset($data['action'])) {
            throw new RuntimeException("Yapay zeka SQL plan\u{0131} \u{00fc}retemedi.");
        }

        $action = strtolower(trim((string) $data['action']));

        if ($action === 'reply') {
            $reply = trim((string) ($data['reply'] ?? ''));

            if ($reply === '') {
                throw new RuntimeException("Yapay zeka bo\u{015f} yan\u{0131}t d\u{00f6}nd\u{00fc}rd\u{00fc}.");
            }

            return [
                'action' => 'reply',
                'reply' => $reply,
            ];
        }

        if ($action === 'sql') {
            $sql = trim((string) ($data['sql'] ?? ''));

            if ($sql === '') {
                throw new RuntimeException("Yapay zeka SQL \u{00fc}retmedi.");
            }

            return [
                'action' => 'sql',
                'sql' => $sql,
                'reason' => trim((string) ($data['reason'] ?? '')),
            ];
        }

        throw new RuntimeException("Yapay zeka ge\u{00e7}ersiz bir plan d\u{00f6}nd\u{00fc}rd\u{00fc}.");
    }

    private function buildPlannerPrompt(string $schemaSummary): string
    {
        return
            "You are an internal admin assistant for a jewelry management app.\n" .
            "Respond in Turkish.\n" .
            "Do not use any personal name or persona.\n" .
            "You know the database schema below and may ask the backend to run exactly one raw MySQL SQL statement when needed.\n\n" .
            "Database schema:\n{$schemaSummary}\n\n" .
            "Rules:\n" .
            "- Use only tables and columns from the schema.\n" .
            "- Tables with deleted_at use soft delete. Unless the user explicitly asks for deleted rows, add deleted_at IS NULL filters.\n" .
            "- If the request can be answered without SQL, do not request SQL.\n" .
            "- If SQL is needed, output exactly one MySQL-compatible raw SQL statement.\n" .
            "- Allowed SQL verbs: SELECT, INSERT, UPDATE, DELETE.\n" .
            "- Forbidden SQL: CREATE, ALTER, DROP, TRUNCATE, RENAME, GRANT, REVOKE, USE, SET, CALL, EXECUTE, MERGE, REPLACE, LOCK, UNLOCK, comments, multiple statements.\n" .
            "- Prefer precise WHERE clauses and safe updates.\n" .
            "- Files or images attached by the user are part of the context.\n\n" .
            "Return valid JSON only.\n" .
            "If SQL is not needed:\n" .
            "{\"action\":\"reply\",\"reply\":\"...\"}\n" .
            "If SQL is needed:\n" .
            "{\"action\":\"sql\",\"sql\":\"SELECT ...\",\"reason\":\"...\"}";
    }

    private function plannerResponseSchema(): array
    {
        return [
            'type' => 'object',
            'propertyOrdering' => ['action', 'reply', 'sql', 'reason'],
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['reply', 'sql'],
                ],
                'reply' => [
                    'type' => ['string', 'null'],
                ],
                'sql' => [
                    'type' => ['string', 'null'],
                ],
                'reason' => [
                    'type' => ['string', 'null'],
                ],
            ],
            'required' => ['action'],
        ];
    }

    private function buildSqlSummaryPrompt(string $schemaSummary): string
    {
        return
            "You are an internal admin assistant for a jewelry management app.\n" .
            "Respond in Turkish.\n" .
            "Do not use any personal name or persona.\n" .
            "You already know the schema below:\n{$schemaSummary}\n\n" .
            "You will receive the user conversation plus the executed SQL and its result.\n" .
            "Write a short, clear user-facing answer in Turkish.\n" .
            "If rows were returned, summarize the relevant findings.\n" .
            "If a write query ran, clearly state whether it succeeded and how many rows were affected.\n" .
            "Do not output JSON or code fences.\n" .
            "Do not invent values not present in the SQL result.";
    }

    private function buildSchemaSummary(): string
    {
        try {
            $rows = DB::select(
                "SELECT table_name, column_name, column_type, is_nullable, column_key, column_default
                 FROM information_schema.columns
                 WHERE table_schema = DATABASE()
                 AND table_name <> 'migrations'
                 ORDER BY table_name, ordinal_position"
            );

            if (!$rows) {
                return $this->fallbackSchemaSummary();
            }

            $tables = [];

            foreach ($rows as $row) {
                $tableName = $row->table_name;
                $tables[$tableName][] =
                    $row->column_name .
                    ' ' . $row->column_type .
                    ($row->is_nullable === 'NO' ? ' not null' : ' nullable') .
                    ($row->column_key !== '' ? ' key=' . strtolower($row->column_key) : '') .
                    ($row->column_default !== null ? ' default=' . $row->column_default : '');
            }

            $lines = [];

            foreach ($tables as $table => $columns) {
                $lines[] = $table . ': ' . implode(', ', $columns);
            }

            $lines[] = 'soft delete tables: users, categories, products, windows, carts, bracelets, orders, order_carts, order_products, order_bracelets';
            $lines[] = 'important relations: categories.root_id -> categories.id; products.ctg_id -> categories.id; windows.user_id -> users.id; windows.product_id -> products.id; carts.user_id -> users.id; carts.product_id -> products.id; bracelets.cart_id -> carts.id; orders.user_id -> users.id; orders.auth_id -> users.id; order_carts.order_id -> orders.id; order_carts.user_id -> users.id; order_carts.product_id -> order_products.id; order_products.order_id -> orders.id; order_products.ctg_id -> categories.id; order_bracelets.cart_id -> order_carts.id; carts_order_carts.cart_id -> carts.id; carts_order_carts.order_cart_id -> order_carts.id';
            $lines[] = 'domain notes: orders.status values are -1=iptal, 0=beklemede, 1=hazirlaniyor, 2=tamamlandi';
            $lines[] = 'domain notes: products.type currently uses bracelets; bracelet heights are height_56, height_58, height_60, height_62, height_64, height_66, height_68, height_70, height_72, height_74';
            $lines[] = 'domain notes: customer records are stored in users table; customer = role 1 and usually admin 0; staff = role 0; mail must be unique; pass stores md5 hash text';

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            return $this->fallbackSchemaSummary();
        }
    }

    private function fallbackSchemaSummary(): string
    {
        return implode("\n", [
            'users: id, role, name, mail, pass, token, phone, address, admin, created_at, updated_at, deleted_at',
            'categories: id, name, root_id, created_at, updated_at, deleted_at',
            'products: id, name, type, photo, ctg_id, width, weight, between, empty, created_at, updated_at, deleted_at',
            'windows: id, user_id, product_id, created_at, updated_at, deleted_at',
            'carts: id, user_id, product_id, width, weight, photo, note, quantity, weight_total, created_at, updated_at, deleted_at',
            'bracelets: id, cart_id, height_56, height_58, height_60, height_62, height_64, height_66, height_68, height_70, height_72, height_74, created_at, updated_at, deleted_at',
            'orders: id, user_id, auth_id, note, quantity, weight, status, finished_at, created_at, updated_at, deleted_at',
            'order_carts: id, order_id, user_id, product_id, width, weight, photo, note, quantity, weight_total, created_at, updated_at, deleted_at',
            'order_products: row_id, id, order_id, name, type, photo, ctg_id, width, weight, between, empty, created_at, updated_at, deleted_at',
            'order_bracelets: id, cart_id, height_56, height_58, height_60, height_62, height_64, height_66, height_68, height_70, height_72, height_74, created_at, updated_at, deleted_at',
            'carts_order_carts: cart_id, order_cart_id',
            'relations: categories.root_id -> categories.id; products.ctg_id -> categories.id; carts.user_id -> users.id; carts.product_id -> products.id; bracelets.cart_id -> carts.id; orders.user_id -> users.id; orders.auth_id -> users.id; order_carts.order_id -> orders.id; order_products.order_id -> orders.id; order_bracelets.cart_id -> order_carts.id',
            'orders.status: -1=iptal, 0=beklemede, 1=hazirlaniyor, 2=tamamlandi',
            'customer semantics: customer records live in users; use role=1 and admin=0; mail unique; pass field stores md5 hash text',
        ]);
    }

    private function geminiRequest(string $apiKey)
    {
        return Http::timeout(120)->withHeaders([
            'x-goog-api-key' => $apiKey,
        ]);
    }

    private function generateContentUrl(string $model): string
    {
        return "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    }

    private function uploadGeminiFile(UploadedFile $attachment, string $apiKey): array
    {
        $mimeType = $attachment->getMimeType() ?: $attachment->getClientMimeType() ?: 'application/octet-stream';
        $displayName = $attachment->getClientOriginalName() ?: $attachment->hashName();
        $size = (int) $attachment->getSize();

        $startResponse = Http::timeout(120)->withHeaders([
            'x-goog-api-key' => $apiKey,
            'X-Goog-Upload-Protocol' => 'resumable',
            'X-Goog-Upload-Command' => 'start',
            'X-Goog-Upload-Header-Content-Length' => (string) $size,
            'X-Goog-Upload-Header-Content-Type' => $mimeType,
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/upload/v1beta/files', [
            'file' => [
                'display_name' => $displayName,
            ],
        ]);

        if ($startResponse->failed()) {
            throw new RuntimeException($this->translateGeminiError($startResponse));
        }

        $uploadUrl = $startResponse->header('X-Goog-Upload-URL') ?: $startResponse->header('x-goog-upload-url');

        if (!$uploadUrl) {
            throw new RuntimeException("Dosya y\u{00fc}kleme ba\u{011f}lant\u{0131}s\u{0131} al\u{0131}namad\u{0131}.");
        }

        $uploadResponse = Http::timeout(120)->withHeaders([
            'x-goog-api-key' => $apiKey,
            'X-Goog-Upload-Offset' => '0',
            'X-Goog-Upload-Command' => 'upload, finalize',
            'Content-Length' => (string) $size,
        ])->withBody(file_get_contents($attachment->getRealPath()), $mimeType)
            ->post($uploadUrl);

        if ($uploadResponse->failed()) {
            throw new RuntimeException($this->translateGeminiError($uploadResponse));
        }

        $fileInfo = $uploadResponse->json('file');

        if (!$fileInfo) {
            throw new RuntimeException("Dosya y\u{00fc}kleme yan\u{0131}t\u{0131} al\u{0131}namad\u{0131}.");
        }

        $fileInfo = $this->waitForGeminiFile($fileInfo, $apiKey);

        if (empty($fileInfo['name']) || empty($fileInfo['uri'])) {
            throw new RuntimeException("Dosya Gemini taraf\u{0131}ndan kullan\u{0131}ma haz\u{0131}rlanamad\u{0131}.");
        }

        return [
            'file_name' => (string) ($fileInfo['name'] ?? ''),
            'file_uri' => (string) ($fileInfo['uri'] ?? ''),
            'mime_type' => (string) ($fileInfo['mimeType'] ?? $mimeType),
            'display_name' => $displayName,
        ];
    }

    private function waitForGeminiFile(array $fileInfo, string $apiKey): array
    {
        $state = strtoupper((string) ($fileInfo['state'] ?? ''));
        $name = (string) ($fileInfo['name'] ?? '');

        if ($state === '' || $state === 'ACTIVE' || $name === '') {
            return $fileInfo;
        }

        for ($attempt = 0; $attempt < 10; $attempt++) {
            if ($state !== 'PROCESSING') {
                break;
            }

            sleep(1);

            $statusResponse = $this->geminiRequest($apiKey)
                ->get("https://generativelanguage.googleapis.com/v1beta/{$name}");

            if ($statusResponse->failed()) {
                throw new RuntimeException("Y\u{00fc}klenen dosya i\u{015f}lenemedi.");
            }

            $fileInfo = $statusResponse->json('file') ?: $statusResponse->json();
            $state = strtoupper((string) ($fileInfo['state'] ?? ''));

            if ($state === '' || $state === 'ACTIVE') {
                return $fileInfo;
            }
        }

        if ($state !== '' && $state !== 'ACTIVE') {
            throw new RuntimeException("Dosya hen\u{00fc}z i\u{015f}lenemedi. L\u{00fc}tfen tekrar deneyin.");
        }

        return $fileInfo;
    }

    private function formatHistoryForGemini(array $history): array
    {
        return array_values(array_filter(array_map(function (array $entry) {
            $parts = [];

            foreach ($entry['parts'] ?? [] as $part) {
                if (($part['type'] ?? '') === 'file') {
                    $parts[] = [
                        'file_data' => [
                            'mime_type' => $part['mime_type'],
                            'file_uri' => $part['file_uri'],
                        ],
                    ];
                    continue;
                }

                $text = trim((string) ($part['text'] ?? ''));

                if ($text !== '') {
                    $parts[] = ['text' => $text];
                }
            }

            return [
                'role' => $entry['role'],
                'parts' => $parts,
            ];
        }, $history), fn (array $entry) => $entry['parts'] !== []));
    }

    private function normalizeHistory(array $history): array
    {
        $normalized = [];

        foreach ($history as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            if (isset($entry['parts']) && is_array($entry['parts'])) {
                $normalized[] = $entry;
                continue;
            }

            $text = trim((string) ($entry['content'] ?? ''));

            if ($text === '') {
                continue;
            }

            $role = ($entry['role'] ?? '') === 'assistant' ? 'model' : 'user';

            $normalized[] = [
                'role' => $role,
                'parts' => [
                    [
                        'type' => 'text',
                        'text' => $text,
                    ],
                ],
            ];
        }

        return $normalized;
    }

    private function extractReplyText(Response $response): string
    {
        $parts = $response->json('candidates.0.content.parts', []);
        $texts = [];

        foreach ($parts as $part) {
            $text = trim((string) ($part['text'] ?? ''));

            if ($text !== '') {
                $texts[] = $text;
            }
        }

        return trim(implode("\n", $texts));
    }

    private function translateGeminiError(Response $response): string
    {
        $message = trim((string) $response->json('error.message', ''));
        $normalized = strtolower($message);

        if ($normalized === '') {
            return "Gemini iste\u{011f}i i\u{015f}lenemedi.";
        }

        if (str_contains($normalized, 'api key') || str_contains($normalized, 'permission')) {
            return "Gemini API anahtar\u{0131} ge\u{00e7}ersiz veya yetkisiz.";
        }

        if (str_contains($normalized, 'mime') || str_contains($normalized, 'unsupported') || str_contains($normalized, 'file type')) {
            return "Bu dosya t\u{00fc}r\u{00fc} desteklenmiyor.";
        }

        if (str_contains($normalized, 'too large') || str_contains($normalized, 'size')) {
            return "Dosya boyutu desteklenen s\u{0131}n\u{0131}r\u{0131} a\u{015f}\u{0131}yor.";
        }

        return "Gemini iste\u{011f}i i\u{015f}lenemedi.";
    }

    private function defaultPromptForAttachment(UploadedFile $attachment): string
    {
        $mimeType = $attachment->getMimeType() ?: $attachment->getClientMimeType() ?: '';

        if (str_starts_with($mimeType, 'image/')) {
            return "Bu g\u{00f6}rseli inceleyip k\u{0131}sa bir \u{00f6}zet \u{00e7}\u{0131}kar.";
        }

        return "Bu dosyay\u{0131} inceleyip k\u{0131}sa bir \u{00f6}zet \u{00e7}\u{0131}kar.";
    }

    private function trimHistory(array $history): array
    {
        if (count($history) <= self::HISTORY_LIMIT) {
            return [$history, []];
        }

        $removedEntries = array_slice($history, 0, count($history) - self::HISTORY_LIMIT);
        $trimmedHistory = array_slice($history, -self::HISTORY_LIMIT);

        return [$trimmedHistory, $this->collectFileNames($removedEntries)];
    }

    private function collectFileNames(array $history): array
    {
        $names = [];

        foreach ($history as $entry) {
            foreach ($entry['parts'] ?? [] as $part) {
                if (($part['type'] ?? '') === 'file' && !empty($part['file_name'])) {
                    $names[] = $part['file_name'];
                }
            }
        }

        return array_values(array_unique($names));
    }

    private function deleteGeminiFiles(array $fileNames, string $apiKey): void
    {
        foreach ($fileNames as $fileName) {
            if (!$fileName) {
                continue;
            }

            try {
                $this->geminiRequest($apiKey)
                    ->delete("https://generativelanguage.googleapis.com/v1beta/{$fileName}");
            } catch (\Throwable $e) {
                // Best effort cleanup.
            }
        }
    }
}
