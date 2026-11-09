<?php

namespace App\Support;

class OrderMetadata
{
    private const MARKER = 'samudera-order-metadata-v1';

    public static function decode(?string $note): array
    {
        if ($note !== null) {
            $metadata = json_decode($note, true);

            if (
                is_array($metadata)
                && ($metadata['_format'] ?? null) === self::MARKER
                && is_array($metadata['returns'] ?? null)
            ) {
                return [
                    'customer_note' => $metadata['customer_note'] ?? null,
                    'returns' => $metadata['returns'],
                ];
            }
        }

        return ['customer_note' => $note, 'returns' => []];
    }

    public static function encode(?string $customerNote, array $returns): string
    {
        return json_encode([
            '_format' => self::MARKER,
            'customer_note' => $customerNote,
            'returns' => array_values($returns),
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function customerNote(?string $note): ?string
    {
        return self::decode($note)['customer_note'];
    }

    public static function findReturn(?string $note, int $returnId): ?array
    {
        foreach (self::decode($note)['returns'] as $return) {
            if ((int) ($return['id_return'] ?? 0) === $returnId) {
                return $return;
            }
        }

        return null;
    }

    public static function updateReturn(?string $note, int $returnId, callable $callback): ?string
    {
        $metadata = self::decode($note);
        $found = false;

        foreach ($metadata['returns'] as $index => $return) {
            if ((int) ($return['id_return'] ?? 0) === $returnId) {
                $metadata['returns'][$index] = $callback($return);
                $found = true;
                break;
            }
        }

        return $found ? self::encode($metadata['customer_note'], $metadata['returns']) : null;
    }
}
