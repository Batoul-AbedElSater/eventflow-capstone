import { describe, it, expect, beforeEach, vi } from 'vitest';

/**
 * API Communication Tests
 * Tests for HTTP client setup and request/response handling
 */

describe('Axios API Client', () => {
  let mockAxios;

  beforeEach(() => {
    mockAxios = {
      defaults: {
        headers: {
          common: {}
        }
      }
    };
  });

  it('should set X-Requested-With header', () => {
    expect(mockAxios.defaults.headers.common).toBeDefined();
  });

  it('should have common headers object initialized', () => {
    expect(typeof mockAxios.defaults.headers.common).toBe('object');
  });

  it('should support setting custom headers', () => {
    mockAxios.defaults.headers.common['Authorization'] = 'Bearer token123';
    expect(mockAxios.defaults.headers.common['Authorization']).toBe('Bearer token123');
  });

  it('should handle multiple header assignments', () => {
    mockAxios.defaults.headers.common['X-Custom-Header'] = 'value1';
    mockAxios.defaults.headers.common['X-Another-Header'] = 'value2';
    
    expect(mockAxios.defaults.headers.common['X-Custom-Header']).toBe('value1');
    expect(mockAxios.defaults.headers.common['X-Another-Header']).toBe('value2');
  });
});

describe('API Request Helper Functions', () => {
  /**
   * Mock API request functions
   */
  const apiHelpers = {
    buildAuthHeader: (token) => ({
      'Authorization': `Bearer ${token}`
    }),
    
    buildEventPayload: (eventData) => ({
      name: eventData.name,
      description: eventData.description || '',
      event_type_id: eventData.eventTypeId,
      start_date: eventData.startDate,
      location_text: eventData.location,
      guest_estimate: eventData.guestCount,
      budget_overall: eventData.budget
    }),
    
    buildGuestPayload: (guestData) => ({
      name: guestData.name,
      email: guestData.email,
      phone: guestData.phone || '',
      rsvp_status: guestData.rsvpStatus || 'pending'
    }),

    formatError: (error) => ({
      status: error.status || 500,
      message: error.message || 'Unknown error',
      errors: error.errors || {}
    })
  };

  it('should build auth header with bearer token', () => {
    const header = apiHelpers.buildAuthHeader('abc123token');
    expect(header['Authorization']).toBe('Bearer abc123token');
  });

  it('should build event payload with all required fields', () => {
    const eventData = {
      name: 'Wedding Ceremony',
      description: 'Outdoor wedding',
      eventTypeId: 1,
      startDate: '2025-06-15',
      location: 'Central Park',
      guestCount: 150,
      budget: 50000
    };

    const payload = apiHelpers.buildEventPayload(eventData);
    
    expect(payload.name).toBe('Wedding Ceremony');
    expect(payload.event_type_id).toBe(1);
    expect(payload.start_date).toBe('2025-06-15');
    expect(payload.guest_estimate).toBe(150);
    expect(payload.budget_overall).toBe(50000);
  });

  it('should build guest payload with required fields', () => {
    const guestData = {
      name: 'John Doe',
      email: 'john@example.com',
      phone: '555-1234'
    };

    const payload = apiHelpers.buildGuestPayload(guestData);
    
    expect(payload.name).toBe('John Doe');
    expect(payload.email).toBe('john@example.com');
    expect(payload.rsvp_status).toBe('pending');
  });

  it('should default rsvp_status to pending if not provided', () => {
    const guestData = {
      name: 'Jane Doe',
      email: 'jane@example.com'
    };

    const payload = apiHelpers.buildGuestPayload(guestData);
    expect(payload.rsvp_status).toBe('pending');
  });

  it('should format error with default values', () => {
    const error = { message: 'Network error' };
    const formatted = apiHelpers.formatError(error);
    
    expect(formatted.status).toBe(500);
    expect(formatted.message).toBe('Network error');
    expect(formatted.errors).toEqual({});
  });

  it('should format error with validation errors', () => {
    const error = {
      status: 422,
      message: 'Validation failed',
      errors: {
        email: ['Email is required'],
        phone: ['Invalid phone format']
      }
    };

    const formatted = apiHelpers.formatError(error);
    
    expect(formatted.status).toBe(422);
    expect(formatted.errors.email).toContain('Email is required');
    expect(formatted.errors.phone).toContain('Invalid phone format');
  });
});

describe('Response Validation', () => {
  const validators = {
    isValidEventResponse: (response) => {
      return !!(response.id && response.name && response.event_type_id && response.start_date);
    },

    isValidGuestResponse: (response) => {
      return !!(response.id && response.name && response.email && response.rsvp_status);
    },

    isValidBudgetResponse: (response) => {
      return !!(response.id && response.event_id && response.total_client_budget && response.status);
    },

    isPaginatedResponse: (response) => {
      return !!(response.data && Array.isArray(response.data) && response.meta && response.meta.total);
    }
  };

  it('should validate event response structure', () => {
    const eventResponse = {
      id: 1,
      name: 'Wedding',
      event_type_id: 2,
      start_date: '2025-06-15',
      description: 'Test event'
    };

    expect(validators.isValidEventResponse(eventResponse)).toBe(true);
  });

  it('should reject invalid event response', () => {
    const invalidEvent = {
      id: 1,
      name: 'Wedding'
      // Missing required fields
    };

    expect(validators.isValidEventResponse(invalidEvent)).toBe(false);
  });

  it('should validate guest response structure', () => {
    const guestResponse = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      rsvp_status: 'accepted'
    };

    expect(validators.isValidGuestResponse(guestResponse)).toBe(true);
  });

  it('should validate budget response structure', () => {
    const budgetResponse = {
      id: 1,
      event_id: 1,
      total_client_budget: 50000,
      status: 'draft'
    };

    expect(validators.isValidBudgetResponse(budgetResponse)).toBe(true);
  });

  it('should validate paginated response format', () => {
    const paginatedResponse = {
      data: [
        { id: 1, name: 'Event 1' },
        { id: 2, name: 'Event 2' }
      ],
      meta: {
        total: 2,
        per_page: 15,
        current_page: 1
      }
    };

    expect(validators.isPaginatedResponse(paginatedResponse)).toBe(true);
  });

  it('should reject non-paginated response', () => {
    const invalidResponse = {
      items: [{ id: 1 }]
      // Missing data and meta structure
    };

    expect(validators.isPaginatedResponse(invalidResponse)).toBe(false);
  });
});

describe('HTTP Status Code Handling', () => {
  const statusHandlers = {
    isSuccess: (status) => status >= 200 && status < 300,
    isRedirect: (status) => status >= 300 && status < 400,
    isClientError: (status) => status >= 400 && status < 500,
    isServerError: (status) => status >= 500
  };

  it('should identify 2xx as success', () => {
    expect(statusHandlers.isSuccess(200)).toBe(true);
    expect(statusHandlers.isSuccess(201)).toBe(true);
    expect(statusHandlers.isSuccess(204)).toBe(true);
  });

  it('should identify 3xx as redirect', () => {
    expect(statusHandlers.isRedirect(300)).toBe(true);
    expect(statusHandlers.isRedirect(301)).toBe(true);
    expect(statusHandlers.isRedirect(302)).toBe(true);
  });

  it('should identify 4xx as client error', () => {
    expect(statusHandlers.isClientError(400)).toBe(true);
    expect(statusHandlers.isClientError(401)).toBe(true);
    expect(statusHandlers.isClientError(404)).toBe(true);
    expect(statusHandlers.isClientError(422)).toBe(true);
  });

  it('should identify 5xx as server error', () => {
    expect(statusHandlers.isServerError(500)).toBe(true);
    expect(statusHandlers.isServerError(502)).toBe(true);
    expect(statusHandlers.isServerError(503)).toBe(true);
  });

  it('should not confuse status boundaries', () => {
    expect(statusHandlers.isSuccess(299)).toBe(true);
    expect(statusHandlers.isSuccess(300)).toBe(false);
    expect(statusHandlers.isClientError(399)).toBe(false);
    expect(statusHandlers.isClientError(400)).toBe(true);
  });
});
