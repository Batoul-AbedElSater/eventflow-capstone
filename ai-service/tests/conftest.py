"""
Pytest configuration and fixtures for AI Service tests
"""
import pytest
import sys
import os

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


@pytest.fixture
def test_event_payload():
    """Fixture for basic test event payload"""
    return {
        "event_id": 1,
        "name": "Test Event",
        "budget_overall": 10000
    }


@pytest.fixture
def test_event_with_guests():
    """Fixture for event with guest estimate"""
    return {
        "event_id": 2,
        "name": "Event with Guests",
        "guest_estimate": 150,
        "budget_overall": 15000
    }
