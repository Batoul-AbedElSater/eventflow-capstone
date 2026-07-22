import { describe, it, expect, beforeEach } from 'vitest';

/**
 * Event Management Tests
 * Tests for event-related utilities and business logic
 */

describe('Event Status Management', () => {
  const eventStatus = {
    STATUS_TYPES: {
      PLANNING: 'planning',
      CONFIRMED: 'confirmed',
      IN_PROGRESS: 'in_progress',
      COMPLETED: 'completed',
      CANCELLED: 'cancelled'
    },

    canCancelEvent: (status) => {
      const cancellableStates = ['planning', 'confirmed'];
      return cancellableStates.includes(status);
    },

    canRescheduleEvent: (status) => {
      const reschedulableStates = ['planning', 'confirmed'];
      return reschedulableStates.includes(status);
    },

    getStatusLabel: (status) => {
      const labels = {
        planning: 'Planning',
        confirmed: 'Confirmed',
        in_progress: 'In Progress',
        completed: 'Completed',
        cancelled: 'Cancelled'
      };
      return labels[status] || 'Unknown';
    }
  };

  it('should have all event status types', () => {
    expect(eventStatus.STATUS_TYPES.PLANNING).toBe('planning');
    expect(eventStatus.STATUS_TYPES.COMPLETED).toBe('completed');
    expect(Object.keys(eventStatus.STATUS_TYPES).length).toBe(5);
  });

  it('should allow cancellation of planning events', () => {
    expect(eventStatus.canCancelEvent('planning')).toBe(true);
    expect(eventStatus.canCancelEvent('confirmed')).toBe(true);
  });

  it('should prevent cancellation of started/completed events', () => {
    expect(eventStatus.canCancelEvent('in_progress')).toBe(false);
    expect(eventStatus.canCancelEvent('completed')).toBe(false);
  });

  it('should allow rescheduling of unstarted events', () => {
    expect(eventStatus.canRescheduleEvent('planning')).toBe(true);
    expect(eventStatus.canRescheduleEvent('confirmed')).toBe(true);
  });

  it('should prevent rescheduling of ongoing events', () => {
    expect(eventStatus.canRescheduleEvent('in_progress')).toBe(false);
    expect(eventStatus.canRescheduleEvent('completed')).toBe(false);
  });

  it('should get correct status label', () => {
    expect(eventStatus.getStatusLabel('planning')).toBe('Planning');
    expect(eventStatus.getStatusLabel('completed')).toBe('Completed');
  });
});

describe('Event Date Utilities', () => {
  const eventDate = {
    isDateInPast: (dateStr) => {
      const date = new Date(dateStr);
      return date < new Date();
    },

    isEventToday: (dateStr) => {
      const date = new Date(dateStr);
      const today = new Date();
      return date.toDateString() === today.toDateString();
    },

    daysUntilEvent: (dateStr) => {
      const event = new Date(dateStr);
      const today = new Date();
      const diff = event - today;
      return Math.ceil(diff / (1000 * 60 * 60 * 24));
    },

    isEventSoon: (dateStr, daysThreshold = 7) => {
      const days = eventDate.daysUntilEvent(dateStr);
      return days > 0 && days <= daysThreshold;
    },

    formatEventDateTime: (dateStr, timeStr) => {
      const date = new Date(dateStr);
      const formatted = date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      return `${formatted} at ${timeStr}`;
    }
  };

  it('should identify past dates', () => {
    const pastDate = new Date();
    pastDate.setDate(pastDate.getDate() - 1);
    expect(eventDate.isDateInPast(pastDate.toISOString())).toBe(true);
  });

  it('should identify today', () => {
    const today = new Date().toISOString().split('T')[0];
    expect(eventDate.isEventToday(today)).toBe(true);
  });

  it('should calculate days until event', () => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const days = eventDate.daysUntilEvent(tomorrow.toISOString());
    expect(days).toBeGreaterThanOrEqual(1);
    expect(days).toBeLessThanOrEqual(2);
  });

  it('should identify events happening soon', () => {
    const soonDate = new Date();
    soonDate.setDate(soonDate.getDate() + 3);
    expect(eventDate.isEventSoon(soonDate.toISOString())).toBe(true);
  });

  it('should not flag distant events as soon', () => {
    const distantDate = new Date();
    distantDate.setDate(distantDate.getDate() + 30);
    expect(eventDate.isEventSoon(distantDate.toISOString(), 7)).toBe(false);
  });

  it('should format event date and time', () => {
    const result = eventDate.formatEventDateTime('2025-06-15', '6:00 PM');
    expect(result).toContain('6:00 PM');
    expect(result).toContain('2025');
  });
});

describe('Event Guest Management', () => {
  const guestUtils = {
    RSVP_STATUS: {
      PENDING: 'pending',
      ACCEPTED: 'accepted',
      DECLINED: 'declined'
    },

    countGuestsByStatus: (guests, status) => {
      return guests.filter(g => g.rsvp_status === status).length;
    },

    calculateAcceptanceRate: (guests) => {
      if (guests.length === 0) return 0;
      const accepted = guestUtils.countGuestsByStatus(guests, 'accepted');
      return Math.round((accepted / guests.length) * 100);
    },

    hasAllGuestsResponded: (guests) => {
      return guests.every(g => g.rsvp_status !== 'pending');
    },

    getAttendingGuestCount: (guests) => {
      return guestUtils.countGuestsByStatus(guests, 'accepted');
    },

    groupGuestsByStatus: (guests) => {
      return {
        pending: guestUtils.countGuestsByStatus(guests, 'pending'),
        accepted: guestUtils.countGuestsByStatus(guests, 'accepted'),
        declined: guestUtils.countGuestsByStatus(guests, 'declined')
      };
    }
  };

  it('should have all RSVP statuses', () => {
    expect(guestUtils.RSVP_STATUS.PENDING).toBe('pending');
    expect(guestUtils.RSVP_STATUS.ACCEPTED).toBe('accepted');
    expect(guestUtils.RSVP_STATUS.DECLINED).toBe('declined');
  });

  it('should count guests by status', () => {
    const guests = [
      { id: 1, rsvp_status: 'accepted' },
      { id: 2, rsvp_status: 'accepted' },
      { id: 3, rsvp_status: 'declined' },
      { id: 4, rsvp_status: 'pending' }
    ];

    expect(guestUtils.countGuestsByStatus(guests, 'accepted')).toBe(2);
    expect(guestUtils.countGuestsByStatus(guests, 'declined')).toBe(1);
    expect(guestUtils.countGuestsByStatus(guests, 'pending')).toBe(1);
  });

  it('should calculate acceptance rate', () => {
    const guests = [
      { id: 1, rsvp_status: 'accepted' },
      { id: 2, rsvp_status: 'accepted' },
      { id: 3, rsvp_status: 'declined' },
      { id: 4, rsvp_status: 'pending' }
    ];

    const rate = guestUtils.calculateAcceptanceRate(guests);
    expect(rate).toBe(50); // 2 accepted out of 4
  });

  it('should handle empty guest list for acceptance rate', () => {
    expect(guestUtils.calculateAcceptanceRate([])).toBe(0);
  });

  it('should check if all guests responded', () => {
    const responded = [
      { id: 1, rsvp_status: 'accepted' },
      { id: 2, rsvp_status: 'declined' }
    ];

    const pending = [
      { id: 1, rsvp_status: 'accepted' },
      { id: 2, rsvp_status: 'pending' }
    ];

    expect(guestUtils.hasAllGuestsResponded(responded)).toBe(true);
    expect(guestUtils.hasAllGuestsResponded(pending)).toBe(false);
  });

  it('should get count of attending guests', () => {
    const guests = [
      { id: 1, rsvp_status: 'accepted' },
      { id: 2, rsvp_status: 'accepted' },
      { id: 3, rsvp_status: 'declined' }
    ];

    expect(guestUtils.getAttendingGuestCount(guests)).toBe(2);
  });

  it('should group guests by status', () => {
    const guests = [
      { id: 1, rsvp_status: 'accepted' },
      { id: 2, rsvp_status: 'accepted' },
      { id: 3, rsvp_status: 'declined' },
      { id: 4, rsvp_status: 'pending' }
    ];

    const grouped = guestUtils.groupGuestsByStatus(guests);
    expect(grouped.accepted).toBe(2);
    expect(grouped.declined).toBe(1);
    expect(grouped.pending).toBe(1);
  });
});

describe('Event Validation', () => {
  const eventValidators = {
    isValidEvent: (event) => {
      return !!(event.name &&
             event.event_type_id &&
             event.start_date &&
             event.location_text &&
             event.guest_estimate > 0);
    },

    isValidEventForCreation: (event) => {
      return !!(eventValidators.isValidEvent(event) &&
             event.budget_overall > 0);
    },

    validateDateOrder: (startDate, endDate) => {
      const start = new Date(startDate);
      const end = new Date(endDate);
      
      if (end <= start) {
        return { valid: false, message: 'End date must be after start date' };
      }
      return { valid: true, message: 'Valid date range' };
    },

    validateGuestCount: (guestCount, minGuests = 1, maxGuests = 10000) => {
      if (guestCount < minGuests) {
        return { valid: false, message: `Minimum ${minGuests} guests required` };
      }
      if (guestCount > maxGuests) {
        return { valid: false, message: `Maximum ${maxGuests} guests allowed` };
      }
      return { valid: true, message: 'Valid guest count' };
    }
  };

  it('should validate complete event', () => {
    const validEvent = {
      name: 'Wedding',
      event_type_id: 1,
      start_date: '2025-06-15',
      location_text: 'Central Park',
      guest_estimate: 100,
      budget_overall: 50000
    };

    expect(eventValidators.isValidEvent(validEvent)).toBe(true);
  });

  it('should reject incomplete event', () => {
    const invalidEvent = {
      name: 'Wedding',
      // Missing required fields
    };

    expect(eventValidators.isValidEvent(invalidEvent)).toBe(false);
  });

  it('should validate event for creation with budget', () => {
    const event = {
      name: 'Wedding',
      event_type_id: 1,
      start_date: '2025-06-15',
      location_text: 'Central Park',
      guest_estimate: 100,
      budget_overall: 50000
    };

    expect(eventValidators.isValidEventForCreation(event)).toBe(true);
  });

  it('should reject event creation without budget', () => {
    const event = {
      name: 'Wedding',
      event_type_id: 1,
      start_date: '2025-06-15',
      location_text: 'Central Park',
      guest_estimate: 100,
      budget_overall: 0
    };

    expect(eventValidators.isValidEventForCreation(event)).toBe(false);
  });

  it('should validate date order', () => {
    const result1 = eventValidators.validateDateOrder('2025-06-15', '2025-06-20');
    expect(result1.valid).toBe(true);

    const result2 = eventValidators.validateDateOrder('2025-06-20', '2025-06-15');
    expect(result2.valid).toBe(false);
  });

  it('should validate guest count', () => {
    const result1 = eventValidators.validateGuestCount(100);
    expect(result1.valid).toBe(true);

    const result2 = eventValidators.validateGuestCount(0);
    expect(result2.valid).toBe(false);

    const result3 = eventValidators.validateGuestCount(15000);
    expect(result3.valid).toBe(false);
  });
});
