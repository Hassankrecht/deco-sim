<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        $now = Carbon::now('Africa/Luanda');
        $startsAt = $this->starts_at ? Carbon::parse($this->starts_at, 'Africa/Luanda') : null;
        $expiresAt = $this->expiration_date ? Carbon::parse($this->expiration_date, 'Africa/Luanda') : null;
        $isInactive = !(bool) $this->status;
        $isNotStarted = $startsAt && $now->lt($startsAt);
        $isExpired = $expiresAt && $now->gte($expiresAt);
        $isLimitReached = $this->usage_limit > 0 && $this->used_count >= $this->usage_limit;
        $userUsedCount = (int) ($this->user_used_count ?? 0);
        $isUserUsed = $this->user_usage_limit > 0 && $userUsedCount >= $this->user_usage_limit;
        $isManualPublicCoupon = !$this->user_id && in_array(strtolower((string) $this->generated_for), ['manual', 'manuel'], true);
        $isAssignedToCurrentUser = (bool) ($this->is_assigned_to_current_user ?? $isManualPublicCoupon);
        $isUsable = $isAssignedToCurrentUser && !$isInactive && !$isNotStarted && !$isExpired && !$isLimitReached && !$isUserUsed;

        $typeLabel = $this->type === 'percent' ? 'Percentage discount' : 'Fixed discount';
        $valueLabel = $this->type === 'percent'
            ? "{$this->value}% off"
            : "{$this->value} off";

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $typeLabel,
            'name' => $typeLabel,
            'description' => $valueLabel,
            'details' => $valueLabel,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_order_total' => $this->min_total,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expiration_date,
            'active' => (bool) $this->status,
            'usage_limit' => $this->usage_limit,
            'user_usage_limit' => $this->user_usage_limit,
            'used_count' => $this->used_count,
            'user_used_count' => $userUsedCount,
            'is_used' => $isLimitReached || $isUserUsed,
            'is_assigned_to_current_user' => $isAssignedToCurrentUser,
            'is_expired' => (bool) $isExpired,
            'is_usable' => $isUsable,
            'status_label' => $this->statusLabel($isInactive, $isNotStarted, $isExpired, $isLimitReached, $isUserUsed, $isAssignedToCurrentUser),
            'generated_for' => $this->generated_for,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function statusLabel(bool $isInactive, bool $isNotStarted, bool $isExpired, bool $isLimitReached, bool $isUserUsed, bool $isAssignedToCurrentUser): string
    {
        if (!$isAssignedToCurrentUser) {
            return 'Not assigned to you';
        }
        if ($isInactive) {
            return 'Inactive';
        }
        if ($isNotStarted) {
            return 'Not started';
        }
        if ($isExpired) {
            return 'Expired';
        }
        if ($isLimitReached) {
            return 'Used';
        }
        if ($isUserUsed) {
            return 'Used by you';
        }

        return 'Available';
    }
}
