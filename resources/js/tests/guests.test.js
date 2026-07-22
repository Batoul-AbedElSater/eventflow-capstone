import { describe, it, expect, beforeEach } from 'vitest';

/**
 * Guest Management Tests
 * Tests for guest-related utilities and business logic
 */

describe('Guest List Management', () => {
  const guestListUtils = {
    createGuestEntry: (name, email, phone = '') => ({
      name,
      email,
      phone,
      rsvp_status: 'pending',
      created_at: new Date().toISOString()
    }),

    updateGuestRSVP: (guest, status) => ({
      ...guest,
      rsvp_status: status,
      responded_at: new Date().toISOString()
    }),

    removeGuest: (guests, guestId) => {
      return guests.filter(g => g.id !== guestId);
    },

    searchGuests: (guests, query) => {
      const lowerQuery = query.toLowerCase();
      return guests.filter(g => 
        g.name.toLowerCase().includes(lowerQuery) ||
        g.email.toLowerCase().includes(lowerQuery)
      );
    },

    sortGuestsByName: (guests) => {
      return [...guests].sort((a, b) => a.name.localeCompare(b.name));
    }
  };

  it('should create new guest entry with default pending status', () => {
    const guest = guestListUtils.createGuestEntry('John Doe', 'john@example.com', '555-1234');
    
    expect(guest.name).toBe('John Doe');
    expect(guest.email).toBe('john@example.com');
    expect(guest.phone).toBe('555-1234');
    expect(guest.rsvp_status).toBe('pending');
  });

  it('should create guest without phone', () => {
    const guest = guestListUtils.createGuestEntry('Jane Doe', 'jane@example.com');
    
    expect(guest.name).toBe('Jane Doe');
    expect(guest.phone).toBe('');
  });

  it('should update guest RSVP status', () => {
    const guest = { id: 1, name: 'John', rsvp_status: 'pending' };
    const updated = guestListUtils.updateGuestRSVP(guest, 'accepted');
    
    expect(updated.rsvp_status).toBe('accepted');
    expect(updated.responded_at).toBeDefined();
  });

  it('should remove guest from list', () => {
    const guests = [
      { id: 1, name: 'John' },
      { id: 2, name: 'Jane' },
      { id: 3, name: 'Bob' }
    ];

    const result = guestListUtils.removeGuest(guests, 2);
    
    expect(result).toHaveLength(2);
    expect(result.map(g => g.id)).toEqual([1, 3]);
  });

  it('should search guests by name', () => {
    const guests = [
      { id: 1, name: 'John Doe', email: 'john@example.com' },
      { id: 2, name: 'Jane Doe', email: 'jane@example.com' },
      { id: 3, name: 'Bob Smith', email: 'bob@example.com' }
    ];

    const results = guestListUtils.searchGuests(guests, 'John');
    expect(results).toHaveLength(1);
    expect(results[0].name).toBe('John Doe');
  });

  it('should search guests by email', () => {
    const guests = [
      { id: 1, name: 'John Doe', email: 'john@example.com' },
      { id: 2, name: 'Jane Doe', email: 'jane@example.com' }
    ];

    const results = guestListUtils.searchGuests(guests, 'example.com');
    expect(results).toHaveLength(2);
  });

  it('should sort guests by name alphabetically', () => {
    const guests = [
      { id: 1, name: 'Zoe' },
      { id: 2, name: 'Alice' },
      { id: 3, name: 'Mike' }
    ];

    const sorted = guestListUtils.sortGuestsByName(guests);
    expect(sorted[0].name).toBe('Alice');
    expect(sorted[1].name).toBe('Mike');
    expect(sorted[2].name).toBe('Zoe');
  });
});

describe('Guest List Import/Export', () => {
  const importExport = {
    exportToCSV: (guests) => {
      const headers = 'Name,Email,Phone,RSVP Status';
      const rows = guests.map(g => 
        `"${g.name}","${g.email}","${g.phone}","${g.rsvp_status}"`
      );
      return [headers, ...rows].join('\n');
    },

    parseCSVLine: (line) => {
      // Simple CSV parser for basic cases
      const match = line.match(/"([^"]*)","([^"]*)","([^"]*)","([^"]*)"/);
      if (!match) return null;
      
      return {
        name: match[1],
        email: match[2],
        phone: match[3],
        rsvp_status: match[4]
      };
    },

    validateEmailList: (emails) => {
      return emails.every(email => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      });
    }
  };

  it('should export guests to CSV format', () => {
    const guests = [
      { name: 'John Doe', email: 'john@example.com', phone: '555-1234', rsvp_status: 'accepted' },
      { name: 'Jane Doe', email: 'jane@example.com', phone: '555-5678', rsvp_status: 'pending' }
    ];

    const csv = importExport.exportToCSV(guests);
    
    expect(csv).toContain('John Doe');
    expect(csv).toContain('jane@example.com');
    expect(csv).toContain('RSVP Status');
  });

  it('should parse CSV line into guest object', () => {
    const line = '"John Doe","john@example.com","555-1234","accepted"';
    const guest = importExport.parseCSVLine(line);
    
    expect(guest.name).toBe('John Doe');
    expect(guest.email).toBe('john@example.com');
    expect(guest.rsvp_status).toBe('accepted');
  });

  it('should validate email list', () => {
    const validEmails = ['john@example.com', 'jane@example.com'];
    const invalidEmails = ['john@example.com', 'invalid-email'];

    expect(importExport.validateEmailList(validEmails)).toBe(true);
    expect(importExport.validateEmailList(invalidEmails)).toBe(false);
  });
});

describe('Guest Notifications', () => {
  const notifications = {
    NOTIFICATION_TYPES: {
      INVITATION: 'invitation',
      RSVP_REMINDER: 'rsvp_reminder',
      EVENT_UPDATE: 'event_update',
      EVENT_CANCELLATION: 'event_cancellation'
    },

    buildInvitationMessage: (guestName, eventName, eventDate) => {
      return `Dear ${guestName}, you are invited to ${eventName} on ${eventDate}. Please RSVP.`;
    },

    buildRSVPReminderMessage: (guestName, eventName) => {
      return `${guestName}, reminder: your RSVP for ${eventName} is pending.`;
    },

    buildEventUpdateMessage: (eventName, updateDetails) => {
      return `Update for ${eventName}: ${updateDetails}`;
    },

    shouldSendReminder: (guest, daysBeforeEvent = 7) => {
      return guest.rsvp_status === 'pending' && daysBeforeEvent <= 7;
    },

    canSendNotification: (guest, type) => {
      // Check guest notification preferences
      if (!guest.notification_preferences) return true;
      
      const prefKey = `send_${type}`;
      return guest.notification_preferences[prefKey] !== false;
    }
  };

  it('should have all notification types', () => {
    expect(notifications.NOTIFICATION_TYPES.INVITATION).toBe('invitation');
    expect(notifications.NOTIFICATION_TYPES.RSVP_REMINDER).toBe('rsvp_reminder');
    expect(Object.keys(notifications.NOTIFICATION_TYPES).length).toBe(4);
  });

  it('should build invitation message', () => {
    const message = notifications.buildInvitationMessage('John', 'Wedding', 'June 15, 2025');
    
    expect(message).toContain('John');
    expect(message).toContain('Wedding');
    expect(message).toContain('June 15, 2025');
  });

  it('should build RSVP reminder message', () => {
    const message = notifications.buildRSVPReminderMessage('Jane', 'Wedding');
    
    expect(message).toContain('Jane');
    expect(message).toContain('Wedding');
    expect(message).toContain('RSVP');
  });

  it('should build event update message', () => {
    const message = notifications.buildEventUpdateMessage('Wedding', 'Venue changed to Downtown Hall');
    
    expect(message).toContain('Wedding');
    expect(message).toContain('Venue changed');
  });

  it('should determine when to send reminder', () => {
    const pendingGuest = { rsvp_status: 'pending' };
    const acceptedGuest = { rsvp_status: 'accepted' };

    expect(notifications.shouldSendReminder(pendingGuest, 5)).toBe(true);
    expect(notifications.shouldSendReminder(acceptedGuest, 5)).toBe(false);
  });

  it('should check notification preferences', () => {
    const guestWithoutPrefs = { id: 1, name: 'John' };
    const guestWithPrefs = {
      id: 2,
      name: 'Jane',
      notification_preferences: { send_invitation: true, send_rsvp_reminder: false }
    };

    expect(notifications.canSendNotification(guestWithoutPrefs, 'invitation')).toBe(true);
    expect(notifications.canSendNotification(guestWithPrefs, 'invitation')).toBe(true);
    expect(notifications.canSendNotification(guestWithPrefs, 'rsvp_reminder')).toBe(false);
  });
});

describe('Guest Validation', () => {
  const guestValidators = {
    isValidGuest: (guest) => {
      return guest.name && 
             guest.email && 
             /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(guest.email) &&
             guest.rsvp_status &&
             ['pending', 'accepted', 'declined'].includes(guest.rsvp_status);
    },

    validateGuestData: (data) => {
      const errors = [];

      if (!data.name || data.name.trim() === '') {
        errors.push('Guest name is required');
      }

      if (!data.email) {
        errors.push('Email is required');
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
        errors.push('Invalid email format');
      }

      if (data.phone && !/^\d{3}-?\d{3}-?\d{4}$/.test(data.phone)) {
        errors.push('Invalid phone format');
      }

      return {
        valid: errors.length === 0,
        errors
      };
    },

    isDuplicateEmail: (guests, email) => {
      return guests.some(g => g.email.toLowerCase() === email.toLowerCase());
    }
  };

  it('should validate complete guest entry', () => {
    const guest = {
      name: 'John Doe',
      email: 'john@example.com',
      rsvp_status: 'accepted'
    };

    expect(guestValidators.isValidGuest(guest)).toBe(true);
  });

  it('should reject guest with invalid email', () => {
    const guest = {
      name: 'John Doe',
      email: 'invalid-email',
      rsvp_status: 'accepted'
    };

    expect(guestValidators.isValidGuest(guest)).toBe(false);
  });

  it('should reject guest with invalid RSVP status', () => {
    const guest = {
      name: 'John Doe',
      email: 'john@example.com',
      rsvp_status: 'maybe'
    };

    expect(guestValidators.isValidGuest(guest)).toBe(false);
  });

  it('should validate guest data and collect errors', () => {
    const result = guestValidators.validateGuestData({
      name: '',
      email: 'invalid'
    });

    expect(result.valid).toBe(false);
    expect(result.errors.length).toBeGreaterThan(0);
    expect(result.errors.some(e => e.includes('name'))).toBe(true);
  });

  it('should detect duplicate email', () => {
    const guests = [
      { name: 'John', email: 'john@example.com' },
      { name: 'Jane', email: 'jane@example.com' }
    ];

    expect(guestValidators.isDuplicateEmail(guests, 'john@example.com')).toBe(true);
    expect(guestValidators.isDuplicateEmail(guests, 'new@example.com')).toBe(false);
  });

  it('should handle case-insensitive duplicate detection', () => {
    const guests = [
      { name: 'John', email: 'John@Example.com' }
    ];

    expect(guestValidators.isDuplicateEmail(guests, 'john@example.com')).toBe(true);
  });
});
