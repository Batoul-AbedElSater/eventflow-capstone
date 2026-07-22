import { describe, it, expect, beforeEach } from 'vitest';

/**
 * Utility Functions Tests
 * Tests for common utility functions used across the frontend
 */

describe('String Utilities', () => {
  const stringUtils = {
    capitalize: (str) => str.charAt(0).toUpperCase() + str.slice(1),
    
    truncate: (str, length) => {
      if (str.length <= length) return str;
      return str.substring(0, length) + '...';
    },

    slugify: (str) => {
      return str
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-');
    },

    formatDateString: (dateStr) => {
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
    }
  };

  it('should capitalize first letter', () => {
    expect(stringUtils.capitalize('hello')).toBe('Hello');
    expect(stringUtils.capitalize('WORLD')).toBe('WORLD');
  });

  it('should truncate long strings', () => {
    const result = stringUtils.truncate('This is a very long string', 10);
    expect(result).toBe('This is a ...');
    expect(result.length).toBeLessThanOrEqual(13);
  });

  it('should not truncate short strings', () => {
    expect(stringUtils.truncate('Hello', 10)).toBe('Hello');
  });

  it('should convert to slug format', () => {
    expect(stringUtils.slugify('Hello World')).toBe('hello-world');
    expect(stringUtils.slugify('Event Planning 2025')).toBe('event-planning-2025');
  });

  it('should handle special characters in slug', () => {
    expect(stringUtils.slugify('Hello! World?')).toBe('hello-world');
  });

  it('should format date string', () => {
    const result = stringUtils.formatDateString('2025-06-15');
    expect(result).toContain('June');
    expect(result).toContain('15');
  });
});

describe('Number Utilities', () => {
  const numberUtils = {
    formatCurrency: (amount, currency = 'USD') => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
      }).format(amount);
    },

    roundToTwo: (num) => Math.round(num * 100) / 100,

    percentOf: (value, total) => {
      if (total === 0) return 0;
      return (value / total) * 100;
    },

    isValidNumber: (num) => {
      return !isNaN(num) && isFinite(num);
    }
  };

  it('should format currency correctly', () => {
    const result = numberUtils.formatCurrency(1234.56);
    expect(result).toContain('1,234.56');
  });

  it('should round to two decimal places', () => {
    expect(numberUtils.roundToTwo(10.556)).toBe(10.56);
    expect(numberUtils.roundToTwo(10.554)).toBe(10.55);
    expect(numberUtils.roundToTwo(10.5)).toBe(10.5);
  });

  it('should calculate percentage correctly', () => {
    expect(numberUtils.percentOf(50, 200)).toBe(25);
    expect(numberUtils.percentOf(100, 200)).toBe(50);
  });

  it('should handle zero total in percentage calculation', () => {
    expect(numberUtils.percentOf(50, 0)).toBe(0);
  });

  it('should validate numbers', () => {
    expect(numberUtils.isValidNumber(42)).toBe(true);
    expect(numberUtils.isValidNumber(42.5)).toBe(true);
    expect(numberUtils.isValidNumber(NaN)).toBe(false);
    expect(numberUtils.isValidNumber(Infinity)).toBe(false);
    expect(numberUtils.isValidNumber('not a number')).toBe(false);
  });
});

describe('Array Utilities', () => {
  const arrayUtils = {
    unique: (arr) => [...new Set(arr)],

    groupBy: (arr, key) => {
      return arr.reduce((groups, item) => {
        const groupKey = item[key];
        if (!groups[groupKey]) {
          groups[groupKey] = [];
        }
        groups[groupKey].push(item);
        return groups;
      }, {});
    },

    sortBy: (arr, key, ascending = true) => {
      return [...arr].sort((a, b) => {
        if (ascending) {
          return a[key] > b[key] ? 1 : -1;
        }
        return a[key] < b[key] ? 1 : -1;
      });
    },

    pluck: (arr, key) => arr.map(item => item[key]),

    findByProperty: (arr, key, value) => arr.find(item => item[key] === value)
  };

  it('should get unique items from array', () => {
    const result = arrayUtils.unique([1, 2, 2, 3, 3, 3]);
    expect(result).toHaveLength(3);
    expect(result).toContain(1);
    expect(result).toContain(2);
    expect(result).toContain(3);
  });

  it('should group items by property', () => {
    const guests = [
      { name: 'John', status: 'accepted' },
      { name: 'Jane', status: 'pending' },
      { name: 'Bob', status: 'accepted' }
    ];

    const grouped = arrayUtils.groupBy(guests, 'status');
    expect(grouped.accepted).toHaveLength(2);
    expect(grouped.pending).toHaveLength(1);
  });

  it('should sort by property ascending', () => {
    const items = [
      { name: 'Event C', date: '2025-06-15' },
      { name: 'Event A', date: '2025-06-10' },
      { name: 'Event B', date: '2025-06-12' }
    ];

    const sorted = arrayUtils.sortBy(items, 'date', true);
    expect(sorted[0].name).toBe('Event A');
    expect(sorted[2].name).toBe('Event C');
  });

  it('should sort by property descending', () => {
    const items = [1, 3, 2].map(n => ({ value: n }));
    const sorted = arrayUtils.sortBy(items, 'value', false);
    expect(sorted[0].value).toBe(3);
    expect(sorted[2].value).toBe(1);
  });

  it('should extract property values', () => {
    const guests = [
      { id: 1, name: 'John' },
      { id: 2, name: 'Jane' },
      { id: 3, name: 'Bob' }
    ];

    const names = arrayUtils.pluck(guests, 'name');
    expect(names).toEqual(['John', 'Jane', 'Bob']);
  });

  it('should find item by property', () => {
    const guests = [
      { id: 1, name: 'John' },
      { id: 2, name: 'Jane' },
      { id: 3, name: 'Bob' }
    ];

    const guest = arrayUtils.findByProperty(guests, 'name', 'Jane');
    expect(guest.id).toBe(2);
  });
});

describe('Object Utilities', () => {
  const objectUtils = {
    deepClone: (obj) => JSON.parse(JSON.stringify(obj)),

    merge: (target, source) => {
      return { ...target, ...source };
    },

    filterEmpty: (obj) => {
      return Object.keys(obj).reduce((filtered, key) => {
        if (obj[key] !== null && obj[key] !== undefined && obj[key] !== '') {
          filtered[key] = obj[key];
        }
        return filtered;
      }, {});
    },

    hasAllKeys: (obj, keys) => {
      return keys.every(key => key in obj);
    }
  };

  it('should deep clone object', () => {
    const original = { name: 'Event', data: { count: 100 } };
    const clone = objectUtils.deepClone(original);
    
    clone.data.count = 200;
    expect(original.data.count).toBe(100);
  });

  it('should merge objects', () => {
    const obj1 = { a: 1, b: 2 };
    const obj2 = { b: 3, c: 4 };
    
    const merged = objectUtils.merge(obj1, obj2);
    expect(merged).toEqual({ a: 1, b: 3, c: 4 });
  });

  it('should filter empty values', () => {
    const obj = { name: 'Event', desc: '', status: 'active', notes: null };
    const filtered = objectUtils.filterEmpty(obj);
    
    expect(filtered.name).toBe('Event');
    expect(filtered.status).toBe('active');
    expect(filtered.desc).toBeUndefined();
    expect(filtered.notes).toBeUndefined();
  });

  it('should check if object has all required keys', () => {
    const obj = { id: 1, name: 'Event', status: 'active' };
    
    expect(objectUtils.hasAllKeys(obj, ['id', 'name'])).toBe(true);
    expect(objectUtils.hasAllKeys(obj, ['id', 'name', 'missing'])).toBe(false);
  });
});

describe('Validation Utilities', () => {
  const validationUtils = {
    isEmail: (email) => {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
    },

    isPhoneNumber: (phone) => {
      const regex = /^\d{3}-?\d{3}-?\d{4}$/;
      return regex.test(phone);
    },

    isStrongPassword: (password) => {
      return password.length >= 8 && 
             /[A-Z]/.test(password) && 
             /[0-9]/.test(password) &&
             /[!@#$%^&*]/.test(password);
    },

    isURL: (url) => {
      try {
        new URL(url);
        return true;
      } catch {
        return false;
      }
    }
  };

  it('should validate email format', () => {
    expect(validationUtils.isEmail('valid@example.com')).toBe(true);
    expect(validationUtils.isEmail('invalid@')).toBe(false);
    expect(validationUtils.isEmail('no-at-sign.com')).toBe(false);
  });

  it('should validate phone number format', () => {
    expect(validationUtils.isPhoneNumber('555-123-4567')).toBe(true);
    expect(validationUtils.isPhoneNumber('5551234567')).toBe(true);
    expect(validationUtils.isPhoneNumber('invalid')).toBe(false);
  });

  it('should validate strong password', () => {
    expect(validationUtils.isStrongPassword('Weak')).toBe(false);
    expect(validationUtils.isStrongPassword('StrongPass123!')).toBe(true);
    expect(validationUtils.isStrongPassword('NoNumbers!')).toBe(false);
    expect(validationUtils.isStrongPassword('NoSpecial123')).toBe(false);
  });

  it('should validate URL format', () => {
    expect(validationUtils.isURL('https://example.com')).toBe(true);
    expect(validationUtils.isURL('http://example.com/path')).toBe(true);
    expect(validationUtils.isURL('not a url')).toBe(false);
  });
});
