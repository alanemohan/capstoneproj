<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case Rejected = 'rejected';
    case OnHold = 'on_hold';
    case Flagged = 'flagged';
    case Archived = 'archived';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Draft => in_array($target, [self::Pending, self::Published, self::Archived], true),
            self::Pending => in_array($target, [self::Published, self::Rejected, self::OnHold, self::Flagged, self::Archived], true),
            self::Published => in_array($target, [self::OnHold, self::Flagged, self::Archived], true),
            self::Rejected => in_array($target, [self::Pending, self::Archived], true),
            self::OnHold => in_array($target, [self::Pending, self::Rejected, self::Flagged, self::Archived], true),
            self::Flagged => in_array($target, [self::Pending, self::Rejected, self::OnHold, self::Archived], true),
            self::Archived => in_array($target, [self::Draft], true),
        };
    }

    public static function values(): array
    {
        return array_map(static fn (self $status) => $status->value, self::cases());
    }
}