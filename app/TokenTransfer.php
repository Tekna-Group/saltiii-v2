<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokenTransfer extends Model
{
    protected $table = 'token_transfers';

    protected $fillable = [
        'user_id',
        'recipient_wallet',
        'amount',
        'transaction_signature',
        'status',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that initiated the transfer
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if transfer is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transfer failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Mark transfer as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Mark transfer as failed
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->save();
    }

    /**
     * Scope to get transfers by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get completed transfers
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed transfers
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get pending transfers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get transfers after a specific date
     */
    public function scopeAfter($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }
}
