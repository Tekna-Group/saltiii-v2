<?php

namespace Database\Factories;

use App\TokenTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokenTransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TokenTransfer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\User::factory(),
            'recipient_wallet' => 'EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd',
            'amount' => $this->faker->randomFloat(8, 0.01, 1000),
            'transaction_signature' => $this->faker->sha256(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'error_message' => null,
            'completed_at' => now(),
        ];
    }

    /**
     * Indicate that the transfer is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'completed_at' => null,
                'error_message' => null,
            ];
        });
    }

    /**
     * Indicate that the transfer is completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'completed_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the transfer is failed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'error_message' => $this->faker->sentence(),
                'completed_at' => null,
            ];
        });
    }

    /**
     * Set a specific recipient wallet.
     *
     * @param string $wallet
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forRecipient($wallet)
    {
        return $this->state(function (array $attributes) use ($wallet) {
            return [
                'recipient_wallet' => $wallet,
            ];
        });
    }

    /**
     * Set a specific amount.
     *
     * @param float $amount
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withAmount($amount)
    {
        return $this->state(function (array $attributes) use ($amount) {
            return [
                'amount' => $amount,
            ];
        });
    }

    /**
     * Set a specific user.
     *
     * @param \App\User $user
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUser($user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}
