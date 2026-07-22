<?php

namespace Tests\Feature\Planner;

use App\Models\User;
use App\Models\Budget;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    protected $planner;
    protected $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->planner = User::factory()->create(['role' => 'planner']);
        $this->event = Event::factory()->create();
    }

    /**
     * Test budget can be created
     */
    public function test_budget_can_be_created()
    {
        $budget = Budget::factory()->create([
            'event_id' => $this->event->id,
            'planner_id' => $this->planner->id,
            'total_client_budget' => 5000,
        ]);

        $this->assertDatabaseHas('budgets', ['total_client_budget' => 5000]);
    }

    /**
     * Test budget can be retrieved
     */
    public function test_budget_can_be_retrieved()
    {
        $budget = Budget::factory()->create(['event_id' => $this->event->id]);

        $retrievedBudget = Budget::find($budget->id);
        $this->assertNotNull($retrievedBudget);
        $this->assertEquals($budget->event_id, $retrievedBudget->event_id);
    }

    /**
     * Test budget can be updated
     */
    public function test_budget_can_be_updated()
    {
        $budget = Budget::factory()->create([
            'event_id' => $this->event->id,
            'total_client_budget' => 5000,
        ]);

        $budget->update(['total_client_budget' => 6000]);

        $this->assertDatabaseHas('budgets', ['id' => $budget->id, 'total_client_budget' => 6000]);
    }

    /**
     * Test budget can be deleted
     */
    public function test_budget_can_be_deleted()
    {
        $budget = Budget::factory()->create(['event_id' => $this->event->id]);
        $budgetId = $budget->id;

        $budget->delete();

        $this->assertDatabaseMissing('budgets', ['id' => $budgetId]);
    }

    /**
     * Test budget total_client_budget is numeric
     */
    public function test_budget_total_client_budget_is_numeric()
    {
        $budget = Budget::factory()->create([
            'event_id' => $this->event->id,
            'total_client_budget' => 5000.50,
        ]);

        $this->assertIsNumeric($budget->total_client_budget);
    }

    /**
     * Test budget status defaults to draft
     */
    public function test_budget_status_defaults_to_draft()
    {
        $budget = Budget::factory()->create(['event_id' => $this->event->id]);

        $this->assertEquals('draft', $budget->status);
    }
}

