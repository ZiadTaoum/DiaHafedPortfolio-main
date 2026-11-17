<?php
namespace App\Support;

/**
 * Click-through floor plan rooms, stored as plain text so the admin never has to edit JSON.
 *
 * One room per line:
 *   x, y, width, height | Name | Size | Note | /media/uploads/room.jpg
 *
 * The four numbers are percentages (0–100) of the plan image, measured from its top-left corner.
 * Size, note and image are optional. The "|" character separates fields, so it cannot appear inside them.
 */
class PlanRooms {
    public const MAX_ROOMS = 40;
    private const PATH = '~^/(?!/)[a-zA-Z0-9/_\-.]+$~';

    /** @return array<int, array{x:float,y:float,w:float,h:float,name:string,size:string,note:string,image:string}> */
    public static function parse(?string $text): array {
        $rooms = [];
        foreach (preg_split('/\R/', (string) $text) as $line) {
            $room = self::line($line);
            if ($room !== null) $rooms[] = $room;
            if (count($rooms) >= self::MAX_ROOMS) break;
        }
        return $rooms;
    }

    /** @return string[] one message per problem, for the admin form */
    public static function errors(?string $text): array {
        $errors = [];
        $count = 0;
        foreach (preg_split('/\R/', (string) $text) as $index => $line) {
            if (trim($line) === '') continue;
            $count++;
            if (self::line($line) === null) {
                $errors[] = 'Plan rooms, line '.($index + 1).': use  x, y, width, height | Name | Size | Note | /media/image.jpg  — the four numbers are percentages from 0 to 100 and must stay inside the plan; size, note and image are optional.';
            }
        }
        if ($count > self::MAX_ROOMS) $errors[] = 'Plan rooms: add at most '.self::MAX_ROOMS.' rooms.';
        return $errors;
    }

    private static function line(string $line): ?array {
        $line = trim($line);
        if ($line === '') return null;
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) < 2 || $parts[1] === '' || mb_strlen($parts[1]) > 80) return null;
        $box = array_map('trim', explode(',', $parts[0]));
        if (count($box) !== 4) return null;
        foreach ($box as $value) {
            if (!is_numeric($value)) return null;
        }
        [$x, $y, $w, $h] = array_map('floatval', $box);
        if ($x < 0 || $y < 0 || $w <= 0 || $h <= 0 || $x > 100 || $y > 100 || $x + $w > 100.01 || $y + $h > 100.01) return null;
        $image = $parts[4] ?? '';
        if ($image !== '' && !preg_match(self::PATH, $image)) return null;
        return [
            'x' => $x, 'y' => $y, 'w' => min($w, 100 - $x), 'h' => min($h, 100 - $y),
            'name' => $parts[1],
            'size' => mb_substr($parts[2] ?? '', 0, 80),
            'note' => mb_substr($parts[3] ?? '', 0, 600),
            'image' => $image,
        ];
    }
}
