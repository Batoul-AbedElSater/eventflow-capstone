import json
import os
from dotenv import load_dotenv
from app.prompts import SYSTEM_PROMPT, build_user_prompt
from app.schemas import (
    BudgetPlanResponse,
    BudgetCategory,
    AssistantSuggestion,
    EventBudgetRequest,
)

load_dotenv()


def build_questions(request: EventBudgetRequest) -> list:
    """Generate relevant questions based on event type and description."""
    event_type = (request.event_type or "").lower()
    description = (request.description or "").lower()
    questions = []
    
    # Catering questions
    if "wedding" in event_type:
        questions.append("Does the client prefer buffet, plated dinner, or family style?")
    elif "birthday" in event_type:
        questions.append("Does the client want a cake or dessert table?")
    elif "corporate" in event_type:
        questions.append("Should catering be formal sit-down or casual networking style?")
    else:
        questions.append("Does the client prefer buffet or plated dinner?")
    
    # Entertainment questions
    if "wedding" in event_type:
        questions.append("Does the client prefer a DJ, live band, or both?")
    elif "corporate" in event_type:
        questions.append("Will there be speeches, presentations, or background music only?")
    elif "birthday" in event_type:
        questions.append("Any preference for DJ, live music, or playlist?")
    else:
        questions.append("Any preference for DJ vs live band?")
    
    # Venue questions based on description
    if description and "outdoor" in description:
        questions.append("Should we plan for a weather backup indoor option?")
    if description and ("garden" in description or "beach" in description):
        questions.append("Are there any venue restrictions for decorations or music?")
    
    # Guest-specific
    if request.guest_estimate and request.guest_estimate > 100:
        questions.append("Are there any dietary restrictions we should plan for?")
    
    # Budget questions
    if request.budget_overall and request.guest_estimate:
        budget_per_guest = request.budget_overall / request.guest_estimate
        if budget_per_guest < 10:
            questions.append("Can the client increase the budget or reduce the guest count?")
    
    return questions


def calculate_budget_breakdown(request: EventBudgetRequest) -> BudgetPlanResponse:
    """Formula-based budget calculation."""
    total_budget = max(float(request.budget_overall or 0), 0)
    guests = request.guest_estimate or 0
    event_type = (request.event_type or "").lower()

    if total_budget <= 0:
        return BudgetPlanResponse(
            total_client_budget=0, planner_fee_percentage=15.0,
            planner_fee_amount=0, remaining_for_event=0,
            suggested_assistants=0, assistants=[], total_assistant_fees=0,
            final_budget_for_categories=0, categories=[],
            warnings=["No overall budget was provided."],
            planner_questions=["What is the maximum budget for this event?"],
        )

    # Minimum budget check: at least $5 per guest
    if guests > 0 and total_budget < guests * 5:
        planner_fee = round(total_budget * 0.15, 2)
        remaining = round(total_budget - planner_fee, 2)
        
        return BudgetPlanResponse(
            total_client_budget=total_budget,
            planner_fee_percentage=15.0,
            planner_fee_amount=planner_fee,
            remaining_for_event=remaining,
            suggested_assistants=0,
            assistants=[],
            total_assistant_fees=0,
            final_budget_for_categories=remaining,
            categories=[
                BudgetCategory(
                    category="Catering (Only)",
                    estimated_cost=remaining,
                    guest_based_note=f"Based on {guests} guests at ${round(remaining/guests, 2):.2f} per person" if guests > 0 else None,
                    suggested_orders=[f"Basic meals for {guests} guests" if guests > 0 else "Basic meals"],
                    suggested_assistant_work=["Planner: Focus entire budget on catering only"]
                ),
                BudgetCategory(category="Venue", estimated_cost=0, suggested_orders=["Use free/outdoor venue or client's home"], suggested_assistant_work=[]),
                BudgetCategory(category="Decoration", estimated_cost=0, suggested_orders=["Do it yourself decorations or skip entirely"], suggested_assistant_work=[]),
                BudgetCategory(category="Photography", estimated_cost=0, suggested_orders=["Ask friend/family to take photos"], suggested_assistant_work=[]),
                BudgetCategory(category="Entertainment", estimated_cost=0, suggested_orders=["Use phone playlist and speakers"], suggested_assistant_work=[]),
            ],
            warnings=[
                f"Budget of ${total_budget:.2f} is extremely low for {guests} guests.",
                f"Recommended minimum: ${guests * 5:.2f} ($5/person).",
                "Suggestion: Spend entire budget on basic catering only.",
                "Skip decoration, photography, and entertainment."
            ],
            planner_questions=build_questions(request),
        )

    # Step 1: Planner fee (15% of total)
    planner_fee = round(total_budget * 0.15, 2)

    # Step 2: Budget after planner fee
    budget_after_planner = total_budget - planner_fee

    # Step 3: Number of assistants (1 per 50 guests, min 1, max 3)
    num_assistants = max(1, min(3, guests // 50 + 1))
    
    # Reduce assistants if budget is tight
    budget_per_guest_check = budget_after_planner / guests if guests > 0 else 0
    if budget_per_guest_check < 10 and num_assistants > 1:
        num_assistants = 1
    elif budget_after_planner < num_assistants * 100:
        num_assistants = 1

    # Step 4: Assistant fee = 5% of budget_after_planner per assistant
    assistant_fee = round(budget_after_planner * 0.05, 2)
    total_assistant_fees = round(num_assistants * assistant_fee, 2)

    # Step 5: Remaining for event
    remaining_for_event = round(budget_after_planner - total_assistant_fees, 2)

    # Step 6: Build assistants
    assistants = []
    
    if num_assistants >= 1:
        assistants.append(AssistantSuggestion(
            assistant_number=1, fee=assistant_fee,
            responsibilities=[
                f"Ask catering vendors for buffet prices for {guests} guests" if guests > 0 else "Research catering options",
                "Confirm venue availability and pricing for the event date",
            ]
        ))
    
    if num_assistants >= 2:
        assistants.append(AssistantSuggestion(
            assistant_number=2, fee=assistant_fee,
            responsibilities=[
                "Check decoration packages for flowers, lighting, and backdrop",
                "Get photography package details and pricing",
            ]
        ))
    
    if num_assistants >= 3:
        assistants.append(AssistantSuggestion(
            assistant_number=3, fee=assistant_fee,
            responsibilities=[
                "Research entertainment and music options",
                "Coordinate with other vendors for timing and logistics",
            ]
        ))

    # Step 7: Catering ($7-10 per person, scaled by budget)
    MIN_CATERING = 7.00
    MAX_CATERING = 10.00
    
    if guests > 0:
        budget_per_guest = remaining_for_event / guests
        
        if budget_per_guest >= 20:
            catering_per_person = MAX_CATERING
        elif budget_per_guest >= 10:
            catering_per_person = round(MIN_CATERING + (budget_per_guest - 10) * 0.3, 2)
        else:
            catering_per_person = MIN_CATERING
        
        catering_total = round(guests * catering_per_person, 2)
        max_catering_budget = round(remaining_for_event * 0.50, 2)
        
        if catering_total > max_catering_budget:
            catering_total = max_catering_budget
            catering_per_person = round(catering_total / guests, 2)
            catering_note = f"Based on {guests} guests at ${catering_per_person:.2f} per person (budget constrained)"
        elif catering_per_person == MAX_CATERING:
            catering_note = f"Based on {guests} guests at ${catering_per_person:.2f} per person (premium)"
        else:
            catering_note = f"Based on {guests} guests at ${catering_per_person:.2f} per person"
    else:
        catering_total = round(remaining_for_event * 0.40, 2)
        catering_note = "Guest count not provided - estimate may vary"

    # Step 8: Distribute remaining after catering
    remaining_after_catering = round(remaining_for_event - catering_total, 2)
    venue = round(remaining_after_catering * 0.35, 2)
    decoration = round(remaining_after_catering * 0.25, 2)
    photography = round(remaining_after_catering * 0.25, 2)
    entertainment = round(remaining_after_catering * 0.15, 2)

    # Step 9: Build categories
    categories = [
        BudgetCategory(
            category="Catering", estimated_cost=catering_total,
            guest_based_note=catering_note,
            suggested_orders=[f"Meals for {guests} guests" if guests > 0 else "Meals for guests", "Drinks", "Dessert table"],
            suggested_assistant_work=["Assistant 1: Ask catering vendors for prices"]
        ),
        BudgetCategory(
            category="Venue", estimated_cost=venue,
            suggested_orders=["Main hall rental", "Tables and chairs", "Parking arrangements"],
            suggested_assistant_work=["Assistant 1: Confirm venue availability and pricing"]
        ),
        BudgetCategory(
            category="Decoration", estimated_cost=decoration,
            suggested_orders=["Flowers", "Stage backdrop", "Table decoration", "Lighting setup"],
            suggested_assistant_work=[f"Assistant {min(2, num_assistants)}: Check decoration packages"]
        ),
        BudgetCategory(
            category="Photography", estimated_cost=photography,
            suggested_orders=["Full-day photographer package"],
            suggested_assistant_work=[f"Assistant {min(2, num_assistants)}: Get photography package details"]
        ),
        BudgetCategory(
            category="Entertainment", estimated_cost=max(entertainment, 0),
            suggested_orders=["DJ or live music package"],
            suggested_assistant_work=[f"Assistant {min(2, num_assistants)}: Research music options"]
        ),
    ]

    # Step 10: Dynamic Warnings
    warnings = []
    if guests > 0 and catering_per_person < MIN_CATERING:
        warnings.append(f"Catering budget constrained. Using ${catering_per_person:.2f}/person instead of ${MIN_CATERING:.2f}/person.")
    if entertainment <= 0:
        if "wedding" in event_type:
            warnings.append("No budget for entertainment. Weddings typically need music/DJ.")
        else:
            warnings.append("No budget allocated for entertainment/music")
    if guests <= 0:
        warnings.append("Guest count not provided - estimates are rough")
    if "wedding" in event_type and decoration < 100:
        warnings.append("Decoration budget is very low for a wedding. Consider increasing.")
    if "corporate" in event_type and venue <= 0:
        warnings.append("Corporate events typically require a professional venue.")
    warnings.append("These are estimates only. Real vendor prices must be entered by assistant.")

    category_total = round(sum(c.estimated_cost for c in categories), 2)

    return BudgetPlanResponse(
        total_client_budget=total_budget,
        planner_fee_percentage=15.0,
        planner_fee_amount=planner_fee,
        remaining_for_event=remaining_for_event,
        suggested_assistants=num_assistants,
        assistants=assistants,
        total_assistant_fees=total_assistant_fees,
        final_budget_for_categories=category_total,
        categories=categories,
        warnings=warnings,
        planner_questions=build_questions(request),
    )


async def generate_budget_plan(request: EventBudgetRequest) -> BudgetPlanResponse:
    """Try Gemini first, fall back to formula if no API key."""
    api_key = os.getenv("GOOGLE_API_KEY")

    if not api_key:
        print("⚠️  No API key found. Using formula-based budget calculation.")
        return calculate_budget_breakdown(request)

    try:
        import google.generativeai as genai
        genai.configure(api_key=api_key)
        
        model = genai.GenerativeModel('gemini-2.0-flash')
        prompt = SYSTEM_PROMPT + "\n\n" + build_user_prompt(request.model_dump())
        
        response = model.generate_content(prompt)
        text = response.text.strip()
        
        if text.startswith("```json"):
            text = text[7:]
        if text.endswith("```"):
            text = text[:-3]
        
        data = json.loads(text)
        return BudgetPlanResponse.model_validate(data)

    except Exception as e:
        print(f"❌ Gemini error: {e}. Using formula fallback.")
        return calculate_budget_breakdown(request)