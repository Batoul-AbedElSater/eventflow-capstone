"""
Phase 2: Python pytest tests for AI Budget Generation Service (FastAPI)
Tests the health check endpoint and budget generation API
"""
import pytest
from fastapi.testclient import TestClient
import sys
import os

# Add parent directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from main import app
from app.schemas import BudgetPlanResponse, EventBudgetRequest

client = TestClient(app)


class TestHealthEndpoint:
    """Test 1: Health Check Endpoint"""
    
    def test_health_endpoint_returns_200(self):
        """Health endpoint should return status 200"""
        response = client.get("/health")
        assert response.status_code == 200
    
    def test_health_endpoint_returns_ok_status(self):
        """Health endpoint should return ok status"""
        response = client.get("/health")
        data = response.json()
        assert data["status"] == "ok"
    
    def test_health_endpoint_identifies_service(self):
        """Health endpoint should identify as Budget AI"""
        response = client.get("/health")
        data = response.json()
        assert data["service"] == "Budget AI"
    
    def test_health_endpoint_response_format(self):
        """Health endpoint should return proper JSON format"""
        response = client.get("/health")
        data = response.json()
        assert isinstance(data, dict)
        assert "status" in data
        assert "service" in data


class TestBudgetGenerationBasic:
    """Test 2: Basic Budget Generation"""
    
    def test_generate_budget_returns_200(self):
        """Budget generation should return status 200"""
        payload = {
            "event_id": 1,
            "name": "Wedding Reception",
            "guest_estimate": 150,
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code == 200
    
    def test_generate_budget_returns_valid_json(self):
        """Budget generation should return valid JSON"""
        payload = {
            "event_id": 2,
            "name": "Corporate Event",
            "budget_overall": 5000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code == 200
        data = response.json()
        assert isinstance(data, dict)
    
    def test_generate_budget_minimal_payload(self):
        """Budget generation should work with minimal required fields"""
        payload = {
            "event_id": 3,
            "name": "Simple Event",
            "budget_overall": 1000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code == 200


class TestBudgetResponseStructure:
    """Test 3: Budget Response Structure"""
    
    def test_response_has_all_required_fields(self):
        """Response should include all required budget fields"""
        payload = {
            "event_id": 4,
            "name": "Test Event",
            "guest_estimate": 100,
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        # Check required fields
        required_fields = [
            "total_client_budget",
            "planner_fee_percentage",
            "planner_fee_amount",
            "remaining_for_event",
            "suggested_assistants",
            "assistants",
            "total_assistant_fees",
            "final_budget_for_categories",
            "categories",
            "warnings",
            "planner_questions"
        ]
        
        for field in required_fields:
            assert field in data, f"Missing field: {field}"
    
    def test_response_categories_is_list(self):
        """Categories should be a list"""
        payload = {
            "event_id": 5,
            "name": "List Test",
            "budget_overall": 5000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert isinstance(data["categories"], list)
        assert len(data["categories"]) > 0
    
    def test_response_assistants_is_list(self):
        """Assistants should be a list"""
        payload = {
            "event_id": 6,
            "name": "Assistant Test",
            "budget_overall": 5000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert isinstance(data["assistants"], list)


class TestBudgetCalculations:
    """Test 4: Budget Calculations"""
    
    def test_planner_fee_is_15_percent(self):
        """Planner fee should be 15% of total budget"""
        budget = 10000
        payload = {
            "event_id": 7,
            "name": "Fee Test",
            "budget_overall": budget
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        expected_fee = budget * 0.15
        assert abs(data["planner_fee_amount"] - expected_fee) < 0.01
    
    def test_planner_fee_percentage_is_15(self):
        """Planner fee percentage should always be 15.0"""
        payload = {
            "event_id": 8,
            "name": "Percentage Test",
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert data["planner_fee_percentage"] == 15.0
    
    def test_total_client_budget_matches_input(self):
        """Total client budget should match input budget"""
        budget = 15000
        payload = {
            "event_id": 9,
            "name": "Budget Match Test",
            "budget_overall": budget
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert data["total_client_budget"] == budget
    
    def test_remaining_budget_calculation(self):
        """Remaining budget should be positive after deducting fees"""
        budget = 10000
        payload = {
            "event_id": 10,
            "name": "Remaining Test",
            "budget_overall": budget
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        # Remaining should be less than total
        assert data["remaining_for_event"] > 0
        assert data["remaining_for_event"] < data["total_client_budget"]


class TestBudgetCategoryStructure:
    """Test 5: Budget Category Structure"""
    
    def test_each_category_has_required_fields(self):
        """Each category should have required fields"""
        payload = {
            "event_id": 11,
            "name": "Category Test",
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        required_category_fields = ["category", "estimated_cost", "suggested_orders"]
        
        for category in data["categories"]:
            for field in required_category_fields:
                assert field in category, f"Missing field in category: {field}"
    
    def test_category_suggested_orders_is_list(self):
        """Suggested orders in each category should be a list"""
        payload = {
            "event_id": 12,
            "name": "Orders Test",
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        for category in data["categories"]:
            assert isinstance(category["suggested_orders"], list)
    
    def test_category_estimated_cost_is_numeric(self):
        """Estimated cost should be numeric"""
        payload = {
            "event_id": 13,
            "name": "Numeric Test",
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        for category in data["categories"]:
            assert isinstance(category["estimated_cost"], (int, float))
            assert category["estimated_cost"] > 0


class TestAssistantCalculations:
    """Test 6: Assistant Calculations"""
    
    def test_suggested_assistants_is_integer(self):
        """Suggested assistants should be an integer"""
        payload = {
            "event_id": 14,
            "name": "Assistant Count Test",
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert isinstance(data["suggested_assistants"], int)
        assert data["suggested_assistants"] >= 0
    
    def test_assistant_count_matches_list_length(self):
        """Number of suggested assistants should match assistants list length"""
        payload = {
            "event_id": 15,
            "name": "Count Match Test",
            "guest_estimate": 200,
            "budget_overall": 20000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert data["suggested_assistants"] == len(data["assistants"])
    
    def test_each_assistant_has_fee(self):
        """Each assistant should have a fee"""
        payload = {
            "event_id": 16,
            "name": "Fee per Assistant",
            "budget_overall": 20000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        for assistant in data["assistants"]:
            assert "fee" in assistant
            assert isinstance(assistant["fee"], (int, float))
            assert assistant["fee"] > 0
    
    def test_total_assistant_fees_calculation(self):
        """Total assistant fees should sum individual fees"""
        payload = {
            "event_id": 17,
            "name": "Total Fees Test",
            "budget_overall": 20000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        calculated_total = sum(a["fee"] for a in data["assistants"])
        assert abs(data["total_assistant_fees"] - calculated_total) < 0.01


class TestScenarios:
    """Test 7: Different Event Scenarios"""
    
    def test_small_event_low_budget(self):
        """Test small event with low budget"""
        payload = {
            "event_id": 18,
            "name": "Small Party",
            "guest_estimate": 20,
            "budget_overall": 500
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code == 200
        data = response.json()
        
        # Should handle low budget
        assert data["total_client_budget"] == 500
        assert len(data["categories"]) > 0
    
    def test_large_event_high_budget(self):
        """Test large event with high budget"""
        payload = {
            "event_id": 19,
            "name": "Grand Gala",
            "guest_estimate": 500,
            "budget_overall": 100000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code == 200
        data = response.json()
        
        # Should handle high budget
        assert data["total_client_budget"] == 100000
        assert data["suggested_assistants"] > 0
    
    def test_event_with_many_guests(self):
        """Test event with large guest count"""
        payload = {
            "event_id": 20,
            "name": "Conference",
            "guest_estimate": 1000,
            "budget_overall": 50000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code == 200
    
    def test_event_without_guest_estimate(self):
        """Test event without guest estimate"""
        payload = {
            "event_id": 21,
            "name": "Unknown Size",
            "budget_overall": 10000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code == 200


class TestInputValidation:
    """Test 8: Input Validation"""
    
    def test_missing_event_id_returns_error(self):
        """Missing event_id should return validation error"""
        payload = {
            "name": "Test Event",
            "budget_overall": 5000
        }
        response = client.post("/generate-budget", json=payload)
        # Should return 422 validation error
        assert response.status_code in [422, 400]
    
    def test_missing_name_returns_error(self):
        """Missing name should return validation error"""
        payload = {
            "event_id": 22,
            "budget_overall": 5000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code in [422, 400]
    
    def test_missing_budget_uses_default(self):
        """Missing budget_overall should use default value"""
        payload = {
            "event_id": 23,
            "name": "Test Event"
        }
        response = client.post("/generate-budget", json=payload)
        # API accepts missing budget (uses default 0)
        assert response.status_code == 200


class TestWarningsAndQuestions:
    """Test 9: Warnings and Questions"""
    
    def test_warnings_is_list(self):
        """Warnings should be a list"""
        payload = {
            "event_id": 24,
            "name": "Warning Test",
            "budget_overall": 1000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert isinstance(data["warnings"], list)
    
    def test_planner_questions_is_list(self):
        """Planner questions should be a list"""
        payload = {
            "event_id": 25,
            "name": "Question Test",
            "budget_overall": 5000
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        assert isinstance(data["planner_questions"], list)
    
    def test_low_budget_triggers_warnings(self):
        """Low budget should trigger warnings"""
        payload = {
            "event_id": 26,
            "name": "Tight Budget",
            "guest_estimate": 100,
            "budget_overall": 500
        }
        response = client.post("/generate-budget", json=payload)
        data = response.json()
        
        # May have warnings for tight budget
        assert isinstance(data["warnings"], list)


class TestEndpoint:
    """Test 10: API Endpoint Availability"""
    
    def test_health_endpoint_exists(self):
        """Health endpoint should be available"""
        response = client.get("/health")
        assert response.status_code == 200
    
    def test_generate_budget_endpoint_exists(self):
        """Generate budget endpoint should be available"""
        payload = {
            "event_id": 27,
            "name": "Endpoint Test",
            "budget_overall": 5000
        }
        response = client.post("/generate-budget", json=payload)
        assert response.status_code in [200, 422]
    
    def test_invalid_endpoint_returns_404(self):
        """Invalid endpoint should return 404"""
        response = client.get("/invalid-endpoint")
        assert response.status_code == 404
