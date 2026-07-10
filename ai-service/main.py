from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.budget_generator import generate_budget_plan
from app.schemas import BudgetPlanResponse, EventBudgetRequest

app = FastAPI(title="Event Budget AI Service")

# Allow Laravel to call this
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/health")
def health():
    return {"status": "ok", "service": "Budget AI"}


@app.post("/generate-budget", response_model=BudgetPlanResponse)
async def generate_budget(request: EventBudgetRequest):
    """
    Generate budget suggestions for one event.
    
    Returns:
    - Planner fee (15%)
    - Number of assistants + individual fees + responsibilities
    - 5 categories: Catering, Venue, Decoration, Photography, Entertainment
    - Per-person catering calculation
    - Suggested orders and assistant work per category
    - Warnings and client questions
    
    NO task assignments - suggestions only.
    """
    return await generate_budget_plan(request)
