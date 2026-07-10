from typing import List, Optional
from pydantic import BaseModel, Field


class VendorPayload(BaseModel):
    """Vendor data from Laravel database"""
    id: int
    name: str
    category: Optional[str] = None
    description: Optional[str] = None
    is_favorite: bool = False


class EventBudgetRequest(BaseModel):
    """What Laravel sends to FastAPI"""
    event_id: int
    name: str
    description: Optional[str] = None
    event_type: Optional[str] = None
    event_type_description: Optional[str] = None
    start_date: Optional[str] = None
    start_time: Optional[str] = None
    end_date: Optional[str] = None
    end_time: Optional[str] = None
    location: Optional[str] = None
    guest_estimate: Optional[int] = None
    budget_overall: float = 0
    favorite_vendors: List[VendorPayload] = Field(default_factory=list)


class AssistantSuggestion(BaseModel):
    """Suggested assistant with responsibilities"""
    assistant_number: int
    fee: float
    responsibilities: List[str] = Field(default_factory=list)


class BudgetCategory(BaseModel):
    """One budget category"""
    category: str
    estimated_cost: float
    guest_based_note: Optional[str] = None
    suggested_orders: List[str] = Field(default_factory=list)
    suggested_assistant_work: List[str] = Field(default_factory=list)


class BudgetPlanResponse(BaseModel):
    """What FastAPI returns to Laravel"""
    total_client_budget: float
    planner_fee_percentage: float = 15.0
    planner_fee_amount: float
    remaining_for_event: float
    suggested_assistants: int
    assistants: List[AssistantSuggestion] = Field(default_factory=list)
    total_assistant_fees: float
    final_budget_for_categories: float
    categories: List[BudgetCategory]
    warnings: List[str] = Field(default_factory=list)
    planner_questions: List[str] = Field(default_factory=list)