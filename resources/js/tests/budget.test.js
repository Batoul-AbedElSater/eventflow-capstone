import { describe, it, expect, beforeEach } from 'vitest';

/**
 * Budget Calculation Tests
 * Tests for budget-related calculations and utilities
 */

describe('Budget Calculations', () => {
  const budgetCalcs = {
    calculatePlannerFee: (clientBudget, feePercentage = 0.15) => {
      return Math.round(clientBudget * feePercentage * 100) / 100;
    },

    calculateTotalBudget: (clientBudget, plannerFee, assistantFees = 0) => {
      return clientBudget + plannerFee + assistantFees;
    },

    calculateRemainingBudget: (totalBudget, spent) => {
      return Math.max(0, Math.round((totalBudget - spent) * 100) / 100);
    },

    calculateBudgetPercentageSpent: (spent, total) => {
      if (total === 0) return 0;
      return Math.round((spent / total) * 10000) / 100;
    },

    calculateCostPerGuest: (totalCost, guestCount) => {
      if (guestCount === 0) return 0;
      return Math.round((totalCost / guestCount) * 100) / 100;
    },

    isWithinBudget: (spent, budget) => spent <= budget,

    needsBudgetReview: (percentageSpent) => percentageSpent >= 80
  };

  it('should calculate 15% planner fee', () => {
    expect(budgetCalcs.calculatePlannerFee(10000)).toBe(1500);
    expect(budgetCalcs.calculatePlannerFee(5000)).toBe(750);
  });

  it('should calculate custom planner fee percentage', () => {
    expect(budgetCalcs.calculatePlannerFee(10000, 0.10)).toBe(1000);
    expect(budgetCalcs.calculatePlannerFee(10000, 0.20)).toBe(2000);
  });

  it('should calculate total budget with fees', () => {
    const clientBudget = 50000;
    const plannerFee = 7500;
    const assistantFees = 2000;
    
    const total = budgetCalcs.calculateTotalBudget(clientBudget, plannerFee, assistantFees);
    expect(total).toBe(59500);
  });

  it('should calculate remaining budget', () => {
    expect(budgetCalcs.calculateRemainingBudget(10000, 3000)).toBe(7000);
    expect(budgetCalcs.calculateRemainingBudget(5000, 6000)).toBe(0);
  });

  it('should calculate percentage spent', () => {
    expect(budgetCalcs.calculateBudgetPercentageSpent(2500, 10000)).toBe(25);
    expect(budgetCalcs.calculateBudgetPercentageSpent(5000, 10000)).toBe(50);
    expect(budgetCalcs.calculateBudgetPercentageSpent(10000, 10000)).toBe(100);
  });

  it('should handle zero total in percentage calculation', () => {
    expect(budgetCalcs.calculateBudgetPercentageSpent(0, 0)).toBe(0);
  });

  it('should calculate cost per guest', () => {
    expect(budgetCalcs.calculateCostPerGuest(10000, 100)).toBe(100);
    expect(budgetCalcs.calculateCostPerGuest(5000, 50)).toBe(100);
    expect(budgetCalcs.calculateCostPerGuest(10000, 0)).toBe(0);
  });

  it('should check if within budget', () => {
    expect(budgetCalcs.isWithinBudget(5000, 10000)).toBe(true);
    expect(budgetCalcs.isWithinBudget(10000, 10000)).toBe(true);
    expect(budgetCalcs.isWithinBudget(11000, 10000)).toBe(false);
  });

  it('should flag budget for review when 80% spent', () => {
    expect(budgetCalcs.needsBudgetReview(79)).toBe(false);
    expect(budgetCalcs.needsBudgetReview(80)).toBe(true);
    expect(budgetCalcs.needsBudgetReview(95)).toBe(true);
  });
});

describe('Budget Categories', () => {
  const budgetUtils = {
    CATEGORY_TYPES: {
      VENUE: 'venue',
      CATERING: 'catering',
      DECORATION: 'decoration',
      TRANSPORTATION: 'transportation',
      ENTERTAINMENT: 'entertainment',
      PHOTOGRAPHY: 'photography',
      OTHER: 'other'
    },

    getDefaultCategoryAllocation: (category, totalBudget) => {
      const allocations = {
        venue: 0.30,
        catering: 0.35,
        decoration: 0.10,
        transportation: 0.05,
        entertainment: 0.10,
        photography: 0.07,
        other: 0.03
      };
      return Math.round((totalBudget * (allocations[category] || 0)) * 100) / 100;
    },

    calculateCategorySpent: (items) => {
      return items.reduce((sum, item) => sum + (item.cost || 0), 0);
    },

    isValidCategory: (category) => {
      const categories = Object.values(budgetUtils.CATEGORY_TYPES);
      return categories.includes(category);
    }
  };

  it('should have all budget category types', () => {
    expect(budgetUtils.CATEGORY_TYPES.VENUE).toBe('venue');
    expect(budgetUtils.CATEGORY_TYPES.CATERING).toBe('catering');
    expect(Object.keys(budgetUtils.CATEGORY_TYPES).length).toBe(7);
  });

  it('should calculate default venue allocation (30%)', () => {
    const allocation = budgetUtils.getDefaultCategoryAllocation('venue', 10000);
    expect(allocation).toBe(3000);
  });

  it('should calculate default catering allocation (35%)', () => {
    const allocation = budgetUtils.getDefaultCategoryAllocation('catering', 10000);
    expect(allocation).toBe(3500);
  });

  it('should calculate total spent in category', () => {
    const items = [
      { id: 1, cost: 500 },
      { id: 2, cost: 300 },
      { id: 3, cost: 200 }
    ];

    const total = budgetUtils.calculateCategorySpent(items);
    expect(total).toBe(1000);
  });

  it('should validate category type', () => {
    expect(budgetUtils.isValidCategory('venue')).toBe(true);
    expect(budgetUtils.isValidCategory('catering')).toBe(true);
    expect(budgetUtils.isValidCategory('invalid')).toBe(false);
  });
});

describe('Budget Status Management', () => {
  const budgetStatus = {
    STATUS_TYPES: {
      DRAFT: 'draft',
      SUBMITTED: 'submitted',
      APPROVED: 'approved',
      REJECTED: 'rejected'
    },

    canTransitionTo: (currentStatus, targetStatus) => {
      const transitions = {
        draft: ['submitted'],
        submitted: ['approved', 'rejected', 'draft'],
        approved: ['draft'],
        rejected: ['draft']
      };
      
      return transitions[currentStatus]?.includes(targetStatus) || false;
    },

    getStatusLabel: (status) => {
      const labels = {
        draft: 'Draft',
        submitted: 'Submitted',
        approved: 'Approved',
        rejected: 'Rejected'
      };
      return labels[status] || 'Unknown';
    },

    getStatusColor: (status) => {
      const colors = {
        draft: 'gray',
        submitted: 'blue',
        approved: 'green',
        rejected: 'red'
      };
      return colors[status] || 'gray';
    }
  };

  it('should have all status types', () => {
    expect(budgetStatus.STATUS_TYPES.DRAFT).toBe('draft');
    expect(budgetStatus.STATUS_TYPES.APPROVED).toBe('approved');
    expect(Object.keys(budgetStatus.STATUS_TYPES).length).toBe(4);
  });

  it('should allow draft to submitted transition', () => {
    expect(budgetStatus.canTransitionTo('draft', 'submitted')).toBe(true);
  });

  it('should allow submitted to approved transition', () => {
    expect(budgetStatus.canTransitionTo('submitted', 'approved')).toBe(true);
  });

  it('should allow submitted to rejected transition', () => {
    expect(budgetStatus.canTransitionTo('submitted', 'rejected')).toBe(true);
  });

  it('should not allow invalid transitions', () => {
    expect(budgetStatus.canTransitionTo('draft', 'approved')).toBe(false);
    expect(budgetStatus.canTransitionTo('approved', 'submitted')).toBe(false);
    expect(budgetStatus.canTransitionTo('rejected', 'approved')).toBe(false);
  });

  it('should allow revert to draft from most states', () => {
    expect(budgetStatus.canTransitionTo('submitted', 'draft')).toBe(true);
    expect(budgetStatus.canTransitionTo('approved', 'draft')).toBe(true);
    expect(budgetStatus.canTransitionTo('rejected', 'draft')).toBe(true);
  });

  it('should get correct status label', () => {
    expect(budgetStatus.getStatusLabel('draft')).toBe('Draft');
    expect(budgetStatus.getStatusLabel('approved')).toBe('Approved');
    expect(budgetStatus.getStatusLabel('unknown')).toBe('Unknown');
  });

  it('should get correct status color', () => {
    expect(budgetStatus.getStatusColor('draft')).toBe('gray');
    expect(budgetStatus.getStatusColor('approved')).toBe('green');
    expect(budgetStatus.getStatusColor('rejected')).toBe('red');
  });
});

describe('Budget Validation', () => {
  const budgetValidators = {
    isValidBudgetAmount: (amount) => {
      return typeof amount === 'number' && amount > 0 && isFinite(amount);
    },

    isValidBudgetItem: (item) => {
      return !!(item.category && 
             item.description && 
             item.cost && 
             item.cost > 0);
    },

    validateBudgetLimits: (spent, budget, threshold = 0.95) => {
      if (spent >= budget * threshold) {
        return { valid: false, message: 'Budget exceeded acceptable threshold' };
      }
      if (spent > budget) {
        return { valid: false, message: 'Budget exceeded' };
      }
      return { valid: true, message: 'Within budget' };
    }
  };

  it('should validate positive budget amounts', () => {
    expect(budgetValidators.isValidBudgetAmount(1000)).toBe(true);
    expect(budgetValidators.isValidBudgetAmount(0.01)).toBe(true);
  });

  it('should reject invalid budget amounts', () => {
    expect(budgetValidators.isValidBudgetAmount(0)).toBe(false);
    expect(budgetValidators.isValidBudgetAmount(-100)).toBe(false);
    expect(budgetValidators.isValidBudgetAmount(NaN)).toBe(false);
    expect(budgetValidators.isValidBudgetAmount(Infinity)).toBe(false);
  });

  it('should validate budget item structure', () => {
    const validItem = {
      category: 'catering',
      description: 'Catering service',
      cost: 5000
    };
    expect(budgetValidators.isValidBudgetItem(validItem)).toBe(true);
  });

  it('should reject incomplete budget items', () => {
    expect(budgetValidators.isValidBudgetItem({
      category: 'catering',
      cost: 5000
      // Missing description
    })).toBe(false);

    expect(budgetValidators.isValidBudgetItem({
      category: 'catering',
      description: 'Catering',
      cost: 0 // Invalid cost
    })).toBe(false);
  });

  it('should validate budget limits', () => {
    const result1 = budgetValidators.validateBudgetLimits(5000, 10000);
    expect(result1.valid).toBe(true);

    const result2 = budgetValidators.validateBudgetLimits(9500, 10000);
    expect(result2.valid).toBe(false);
    expect(result2.message).toContain('threshold');

    const result3 = budgetValidators.validateBudgetLimits(12000, 10000);
    expect(result3.valid).toBe(false);
    expect(result3.message).toContain('exceeded');
  });
});
