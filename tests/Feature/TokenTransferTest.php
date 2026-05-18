<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\TokenTransfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TokenTransferTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test: User can view token transfer form
     */
    public function test_authenticated_user_can_view_transfer_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('token-transfer.form'));

        $response->assertStatus(200);
        $response->assertViewIs('token-transfer.form');
    }

    /**
     * Test: Unauthenticated user is redirected to login
     */
    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get(route('token-transfer.form'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Valid transfer submission
     */
    public function test_valid_transfer_submission()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('token-transfer.submit'), [
                'recipient_wallet' => 'EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd',
                'amount' => 100.50,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify transfer was recorded
        $this->assertDatabaseHas('token_transfers', [
            'user_id' => $this->user->id,
            'recipient_wallet' => 'EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd',
            'amount' => 100.50,
        ]);
    }

    /**
     * Test: Invalid wallet address is rejected
     */
    public function test_invalid_wallet_address_rejected()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('token-transfer.submit'), [
                'recipient_wallet' => 'invalid_wallet_address',
                'amount' => 100,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test: Amount less than minimum is rejected
     */
    public function test_amount_less_than_minimum_rejected()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('token-transfer.submit'), [
                'recipient_wallet' => 'EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd',
                'amount' => 0.001,
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test: User can get wallet info
     */
    public function test_user_can_get_wallet_info()
    {
        $this->user->update(['wallet_address' => '3xQ...']);

        $response = $this->actingAs($this->user)
            ->getJson(route('token-transfer.wallet-info'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'wallet_address' => '3xQ...',
            ]
        ]);
    }

    /**
     * Test: User can get balance
     */
    public function test_user_can_get_balance()
    {
        $this->user->update(['wallet_address' => 'EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd']);

        $response = $this->actingAs($this->user)
            ->getJson(route('token-transfer.balance'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test: Transfer history can be retrieved
     */
    public function test_transfer_history_query()
    {
        // Create multiple transfers
        TokenTransfer::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        // Query transfers for user
        $transfers = TokenTransfer::byUser($this->user->id)->get();

        $this->assertCount(5, $transfers);
    }

    /**
     * Test: Completed transfers can be filtered
     */
    public function test_completed_transfers_filter()
    {
        TokenTransfer::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        TokenTransfer::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $completed = TokenTransfer::byUser($this->user->id)
            ->completed()
            ->count();

        $this->assertEquals(3, $completed);
    }

    /**
     * Test: TokenTransfer model relationships
     */
    public function test_token_transfer_user_relationship()
    {
        $transfer = TokenTransfer::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue($transfer->user->is($this->user));
    }

    /**
     * Test: Mark transfer as completed
     */
    public function test_mark_transfer_as_completed()
    {
        $transfer = TokenTransfer::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $transfer->markAsCompleted();

        $this->assertEquals('completed', $transfer->status);
        $this->assertNotNull($transfer->completed_at);
        $this->assertTrue($transfer->isCompleted());
    }

    /**
     * Test: Mark transfer as failed
     */
    public function test_mark_transfer_as_failed()
    {
        $transfer = TokenTransfer::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $transfer->markAsFailed('Insufficient balance');

        $this->assertEquals('failed', $transfer->status);
        $this->assertEquals('Insufficient balance', $transfer->error_message);
        $this->assertTrue($transfer->isFailed());
    }

    /**
     * Test: Transfers are logged with correct data
     */
    public function test_transfer_data_integrity()
    {
        $transferData = [
            'recipient_wallet' => 'EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd',
            'amount' => 250.75,
        ];

        $this->actingAs($this->user)
            ->postJson(route('token-transfer.submit'), $transferData);

        $transfer = TokenTransfer::where('user_id', $this->user->id)->first();

        $this->assertNotNull($transfer);
        $this->assertEquals($transferData['recipient_wallet'], $transfer->recipient_wallet);
        $this->assertEquals($transferData['amount'], $transfer->amount);
    }

    /**
     * Test: Rate limiting (if implemented)
     */
    public function test_rate_limiting_on_transfers()
    {
        // This test assumes rate limiting is implemented
        // Adjust based on your actual rate limiting configuration

        $this->markTestIncomplete(
            'Rate limiting tests should be implemented based on your throttling configuration'
        );
    }
}
