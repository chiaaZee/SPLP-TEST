<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Format Standard Response Sukses
     *
     * @param mixed $data Data utama (Array/Object)
     * @param string|null $message Pesan tambahan (Opsional)
     * @param int $code HTTP Status Code (Default: 200)
     * @param array $metadata Metadata tambahan (Pagination, generated_at, dll)
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data, $message = null, $code = 200, $metadata = [])
    {
        $response = [
            'status' => 'success',
            'code' => $code,
            'data' => $data,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if (!empty($metadata)) {
            $response['metadata'] = $metadata;
        }

        return response()->json($response, $code);
    }

    /**
     * Format Standard Response Error (RFC 7807 Simplified)
     *
     * @param string $title Judul Error (Singkat)
     * @param string|null $detail Penjelasan Detail Error
     * @param int $code HTTP Status Code (Default: 500)
     * @param string $type Tipe Error (Constant, UPPERCASE)
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($title, $detail = null, $code = 500, $type = 'SERVER_ERROR')
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'error' => [
                'type' => $type,
                'title' => $title,
                'detail' => $detail
            ]
        ], $code);
    }
}
