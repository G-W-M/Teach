INSERT IGNORE INTO units (unit_code, unit_name) VALUES 
('BCOM101', 'Introduction to Business'),
('BCOM102', 'Business Mathematics'),
('BCOM201', 'Financial Accounting'),
('BCOM202', 'Business Law'),
('CSC101', 'Introduction to Programming'),
('BBT102', 'Data Structures'),
('LAW101', 'Constitutional Law'),
('LAW102', 'Contract Law');

INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(1, 'What is the primary objective of most business organizations?', 'Profit maximization', 'Social welfare', 'Employee satisfaction', 'Customer acquisition', 1),
(1, 'Which business structure provides limited liability to its owners?', 'Sole Proprietorship', 'Partnership', 'Corporation', 'Non-profit organization', 3),
(1, 'What does SWOT analysis evaluate?', 'Sales, Workforce, Operations, Technology', 'Strengths, Weaknesses, Opportunities, Threats', 'Strategy, Workflow, Organization, Tactics', 'Suppliers, Wholesalers, Outlets, Transport', 2),
(1, 'In marketing, what does the "4 Ps" refer to?', 'Product, Price, Place, Promotion', 'People, Process, Physical evidence, Price', 'Planning, Production, Promotion, Profit', 'Product, Promotion, Profit, Placement', 1),
(1, 'What is the main function of management?', 'Planning, Organizing, Leading, Controlling', 'Marketing, Sales, Finance, Operations', 'Production, Distribution, Consumption, Feedback', 'Hiring, Training, Evaluating, Compensating', 1),
(1, 'Which financial statement shows company performance over time?', 'Balance Sheet', 'Income Statement', 'Cash Flow Statement', 'Statement of Equity', 2),
(1, 'What is economies of scale?', 'Cost advantages from increased production', 'Benefits from international trade', 'Advantages of being first to market', 'Cost savings from outsourcing', 1),
(1, 'Which leadership style involves minimal supervision?', 'Autocratic', 'Democratic', 'Laissez-faire', 'Bureaucratic', 3),
(1, 'What is market segmentation?', 'Dividing market into distinct groups', 'Expanding into new markets', 'Increasing market share', 'Analyzing competitor markets', 1),
(1, 'What does ROI stand for?', 'Return on Investment', 'Rate of Interest', 'Return on Income', 'Risk of Investment', 1);

INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(2, 'If a product costs $80 with a 25% markup, what is the selling price?', '$100', '$95', '$105', '$110', 1),
(2, 'What is the formula for simple interest?', 'P × r × t', 'P(1 + r)^t', 'P + r + t', 'P/(1 + rt)', 1),
(2, 'Calculate the mean: 15, 20, 25, 30, 35', '25', '26', '27', '28', 1),
(2, 'If revenue is $50,000 and costs are $35,000, what is the profit margin?', '30%', '25%', '35%', '40%', 1),
(2, 'What is 15% of 200?', '30', '25', '35', '40', 1),
(2, 'Solve for x: 2x + 5 = 17', '6', '7', '8', '9', 1),
(2, 'A store offers 20% discount on $150 item. What is sale price?', '$120', '$125', '$130', '$135', 1),
(2, 'Calculate compound interest on $1000 at 5% for 2 years.', '$1102.50', '$1100', '$1105', '$1110', 1),
(2, 'What is the median of: 10, 15, 20, 25, 30?', '20', '22', '25', '18', 1),
(2, 'If exchange rate is 1 USD = 0.85 EUR, how many EUR for $200?', '170 EUR', '180 EUR', '190 EUR', '200 EUR', 1);

INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(3, 'What is the accounting equation?', 'Assets = Liabilities + Equity', 'Assets = Revenue - Expenses', 'Liabilities = Assets - Equity', 'Equity = Assets + Liabilities', 1),
(3, 'Which financial statement shows financial position at a point in time?', 'Income Statement', 'Balance Sheet', 'Cash Flow Statement', 'Statement of Equity', 2),
(3, 'What is double-entry bookkeeping?', 'Every transaction affects two accounts', 'Recording transactions twice', 'Using two journals', 'Two people recording same transaction', 1),
(3, 'Which account increases with debit?', 'Assets', 'Liabilities', 'Equity', 'Revenue', 1),
(3, 'What is accrual accounting?', 'Recording when cash is received/paid', 'Recording when revenue/expense occurs', 'Recording only cash transactions', 'Recording annually', 2),
(3, 'What is depreciation?', 'Allocation of asset cost over time', 'Decrease in asset value', 'Loss on asset sale', 'Repair cost of assets', 1),
(3, 'Which is a current asset?', 'Accounts Receivable', 'Building', 'Equipment', 'Long-term Investments', 1),
(3, 'What is the matching principle?', 'Match expenses with related revenue', 'Match assets with liabilities', 'Match debit with credit', 'Match income with cash flow', 1),
(3, 'What does COGS stand for?', 'Cost of Goods Sold', 'Cost of General Services', 'Cost of Gross Sales', 'Calculation of Gross Sales', 1),
(3, 'Which ratio measures liquidity?', 'Current Ratio', 'Debt-to-Equity', 'Return on Equity', 'Gross Margin', 1);

INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(4, 'What is required for a valid contract?', 'Offer, Acceptance, Consideration', 'Written agreement, Signature, Witness', 'Lawyer, Notary, Registration', 'Payment, Delivery, Receipt', 1),
(4, 'What is tort law concerned with?', 'Civil wrongs and damages', 'Criminal offenses', 'Contract disputes', 'Property rights', 1),
(4, 'What does "limited liability" mean?', 'Personal assets protected', 'Limited business operations', 'Limited profit potential', 'Limited market access', 1),
(4, 'What is intellectual property?', 'Creations of the mind', 'Physical business assets', 'Financial investments', 'Real estate properties', 1),
(4, 'What is negligence?', 'Failure to exercise reasonable care', 'Intentional harm', 'Criminal behavior', 'Contract breach', 1),
(4, 'What is the statute of frauds?', 'Requires certain contracts in writing', 'Prevents fraudulent activities', 'Defines criminal fraud', 'Regulates business fraud', 1),
(4, 'What is consideration in contract law?', 'Something of value exchanged', 'Careful thought about contract', 'Legal advice received', 'Contract review process', 1),
(4, 'What is a breach of contract?', 'Failure to perform contractual duties', 'Signing invalid contract', 'Making unfair contract', 'Renegotiating contract terms', 1),
(4, 'What does "voidable contract" mean?', 'Can be canceled by one party', 'Completely invalid', 'Automatically enforceable', 'Requires court approval', 1),
(4, 'What is the UCC?', 'Uniform Commercial Code', 'United Commerce Commission', 'Universal Contract Code', 'Uniform Contract Compliance', 1);


INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(5, 'What is a variable in programming?', 'Container for storing data', 'Mathematical equation', 'Type of loop', 'Function parameter', 1),
(5, 'Which loop executes at least once?', 'for loop', 'while loop', 'do-while loop', 'if-else statement', 3),
(5, 'What is an algorithm?', 'Step-by-step procedure', 'Programming language', 'Computer hardware', 'Data structure', 1),
(5, 'Which is NOT a programming paradigm?', 'Object-Oriented', 'Procedural', 'Functional', 'Hexadecimal', 4),
(5, 'What does IDE stand for?', 'Integrated Development Environment', 'International Development Edition', 'Interactive Design Environment', 'Integrated Design Edition', 1),
(5, 'What is syntax in programming?', 'Rules for writing code', 'Meaning of code', 'Code execution speed', 'Program output', 1),
(5, 'Which data type stores true/false values?', 'boolean', 'integer', 'string', 'float', 1),
(5, 'What is a function?', 'Reusable code block', 'Variable declaration', 'Loop structure', 'Data type', 1),
(5, 'What is debugging?', 'Finding and fixing errors', 'Writing new code', 'Optimizing performance', 'Documenting code', 1),
(5, 'Which operator checks equality?', '=', '==', '===', '!=', 2);

INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(6, 'What is a linked list?', 'Linear collection of nodes', 'Two-dimensional array', 'Hierarchical structure', 'Sorted collection', 1),
(6, 'Which data structure is LIFO?', 'Queue', 'Stack', 'Array', 'Tree', 2),
(6, 'What is time complexity of binary search?', 'O(1)', 'O(log n)', 'O(n)', 'O(n²)', 2),
(6, 'Which is NOT a tree type?', 'Binary Tree', 'AVL Tree', 'Linked Tree', 'B-Tree', 3),
(6, 'What does FIFO stand for?', 'First In First Out', 'First In Last Out', 'Fast In Fast Out', 'Frequent In Frequent Out', 1),
(6, 'What is a hash table?', 'Key-value storage structure', 'Sorted data structure', 'Linear data structure', 'Graph structure', 1),
(6, 'Which sorting algorithm is fastest on average?', 'Bubble Sort', 'Quick Sort', 'Selection Sort', 'Insertion Sort', 2),
(6, 'What is a graph?', 'Collection of vertices and edges', 'Hierarchical structure', 'Linear sequence', 'Key-value pairs', 1),
(6, 'What is recursion?', 'Function calling itself', 'Looping through data', 'Multiple functions', 'Parallel processing', 1),
(6, 'What is dynamic programming?', 'Solving complex problems by breaking down', 'Programming with motion', 'Real-time programming', 'Mobile app development', 1);

INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(7, 'What is judicial review?', 'Court review of government actions', 'Review of lower court decisions', 'Judicial performance evaluation', 'Legal document review', 1),
(7, 'What are the three branches of government?', 'Executive, Legislative, Judicial', 'Federal, State, Local', 'Administrative, Judicial, Military', 'Executive, Administrative, Judicial', 1),
(7, 'What is federalism?', 'Division of power between national and state', 'Centralized government power', 'International government cooperation', 'Democratic voting system', 1),
(7, 'What does "due process" guarantee?', 'Fair legal procedures', 'Right to speedy trial', 'Freedom of speech', 'Right to bear arms', 1),
(7, 'What is the Bill of Rights?', 'First 10 amendments to Constitution', 'Congressional rights document', 'State government rights', 'International human rights', 1),
(7, 'What is separation of powers?', 'Division of government responsibilities', 'Separation of church and state', 'Division of legal jurisdictions', 'Separation of federal and state', 1),
(7, 'What is the supremacy clause?', 'Federal law over state law', 'Constitution over all laws', 'Supreme Court authority', 'Executive order power', 2),
(7, 'What are enumerated powers?', 'Powers specifically listed in Constitution', 'Powers implied by Constitution', 'Powers reserved to states', 'Powers of the judiciary', 1),
(7, 'What is the commerce clause?', 'Congress power to regulate interstate trade', 'State power over local business', 'International trade regulations', 'Consumer protection laws', 1),
(7, 'What is the equal protection clause?', '14th Amendment requirement', '5th Amendment right', '1st Amendment freedom', '4th Amendment protection', 1);

INSERT INTO test_questions (unit_id, question_text, option_1, option_2, option_3, option_4, correct_option) VALUES
(8, 'What are the elements of a valid contract?', 'Offer, Acceptance, Consideration', 'Agreement, Payment, Delivery', 'Signature, Witness, Notary', 'Writing, Registration, Stamp', 1),
(8, 'What is an express contract?', 'Terms explicitly stated', 'Contract by actions', 'Written contract only', 'Verbal agreement', 1),
(8, 'What is consideration?', 'Something of value exchanged', 'Careful thought process', 'Legal advice obtained', 'Contract review', 1),
(8, 'What makes a contract voidable?', 'Can be canceled by one party', 'Never legally binding', 'Automatically enforceable', 'Requires court approval', 1),
(8, 'What is the statute of frauds?', 'Certain contracts must be written', 'Prevention of fraud crimes', 'Definition of criminal fraud', 'Regulation of business fraud', 1),
(8, 'What is a unilateral contract?', 'Promise for an act', 'Exchange of promises', 'One-sided agreement', 'Oral contract', 1),
(8, 'What is the mirror image rule?', 'Acceptance must match offer exactly', 'Contracts must be identical copies', 'Parties must have same understanding', 'Written and verbal terms must match', 1),
(8, 'What is anticipatory breach?', 'Repudiation before performance due', 'Breach during performance', 'Minor contract violation', 'Mutual agreement to cancel', 1),
(8, 'What are liquidated damages?', 'Pre-agreed damage amount', 'Court-determined damages', 'Punitive damages', 'Compensatory damages', 1),
(8, 'What is the parol evidence rule?', 'Written contract excludes prior oral agreements', 'All evidence must be written', 'Oral contracts are invalid', 'Witness testimony required', 1);

-- USERS
INSERT INTO users (student_id, email, password_hash, user_name, phone, role)
VALUES
(NULL, 'admin@teachme.ac.ke', '$2y$10$TKh8H1.P8hX8K/6EwY4jWeQoBpXK8O6h2zV2N7P.p7h9fT6Qs8kXW', 'System Admin', '0700000000', 'admin'),
('123456', 'learner1@strathmore.edu', '$2y$10$KbQi3XOvM/8C9yF5qKxOeuI5f8lQW57iL1XhI/UZh1gVx5Y5PUEyC', 'Alice Mwangi', '0711000001', 'learner'),
('123457', 'learner2@strathmore.edu', '$2y$10$WbC8lZ4Rk3vD9xK9TqFvOuRzP6YdV5wH1jFZ8uV1fT6kP5L3qE4UO', 'Brian Otieno', '0711000002', 'learner'),
('223344', 'tutor1@strathmore.edu', '$2y$10$ZkC9lT5Wk2hF7uJ3VqLdPtM6QjYcW8yX3mA2rV0jK8gB9N1L6R4S', 'Carol Achieng', '0711222333', 'tutor'),
('223345', 'tutor2@strathmore.edu', '$2y$10$YpB7kL1Vj6fH4xN2QsRtEuP3LwMdC9yX1kF2jZ0rV6hG3J8P1T9Q', 'David Kimani', '0711222334', 'tutor');

-- ADMIN
INSERT INTO admin (admin_id, staff_number)
VALUES
(1, 'STF001');

-- TUTOR (parent table for profiles)
INSERT INTO tutor (tutor_id, bio, max_students, current_students, rating, status)
VALUES
(4, 'Expert in Business courses with 3+ years experience.', 3, 0, 4.5, 'active'),
(5, 'Passionate programmer specializing in Data Structures.', 3, 1, 4.8, 'active');

-- TUTOR PROFILES
INSERT INTO tutor_profiles (tutor_id, bio, rating, current_students, max_students)
VALUES
(4, 'Expert in Business courses with 3+ years experience.', 4.5, 0, 3),
(5, 'Passionate programmer specializing in Data Structures.', 4.8, 1, 3);

-- LEARNER PROFILES
INSERT INTO learner_profiles (profile_id, user_id, year_of_study, faculty)
VALUES
(2, 2, '2nd Year', 'Business School'),
(3, 3, '1st Year', 'Informatics & Computer Science');

-- LEARNING REQUESTS
INSERT INTO learning_requests (learner_id, unit_id, description, preferred_schedule, urgency, status, matched_tutor_id)
VALUES
(2, 1, 'Need help with introduction to business basics.', 'Mon morning', 'Medium', 'matched', 4),
(3, 6, 'Struggling with linked lists & trees.', 'Tue afternoon', 'High', 'matched', 5);

-- SESSIONS
INSERT INTO sessions (request_id, tutor_id, learner_id, session_date, start_time, end_time)
VALUES
(1, 4, 2, '2025-01-10', '09:00:00', '10:00:00'),
(2, 5, 3, '2025-01-11', '13:00:00', '14:00:00');

-- ATTENDANCE
INSERT INTO session_attendance (session_id, user_id, attended)
VALUES
(1, 2, TRUE),
(1, 4, TRUE),
(2, 3, TRUE),
(2, 5, FALSE);

-- FEEDBACK
INSERT INTO feedback (session_id, learner_id, tutor_id, rating, comments)
VALUES
(1, 2, 4, 5, 'Very helpful session!'),
(2, 3, 5, 4, 'Good explanation, but tutor came late.');

-- CERTIFICATES
INSERT INTO certificates (tutor_id, certificate_type)
VALUES
(4, 'Business Tutor Level 1'),
(5, 'ICT Tutor Level 2');

-- SYSTEM LOGS
INSERT INTO system_logs (user_id, action, category, details)
VALUES
(1, 'LOGIN', 'auth', 'Admin logged in'),
(4, 'SESSION_COMPLETED', 'tutor', 'Completed tutoring session 001'),
(2, 'REQUEST_SUBMITTED', 'learner', 'Learning request for Business 101');

-- NOTIFICATIONS
INSERT INTO notifications (user_id, message, type)
VALUES
(2, 'Your tutor has been assigned!', 'info'),
(3, 'Upcoming session scheduled for tomorrow', 'alert'),
(4, 'New learner matched!', 'info');

-- TUTOR TESTS
INSERT INTO tutor_tests (user_id, unit_id, score, total_questions, passed)
VALUES
(4, 1, 9, 10, TRUE),
(4, 3, 8, 10, TRUE),
(5, 6, 10, 10, TRUE);

-- TUTORING REQUESTS
INSERT INTO tutoring_requests (user_id, unit, assigned_tutor, status)
VALUES
(2, 'BCOM101', 'Carol Achieng', 'Closed'),
(3, 'BBT102', 'David Kimani', 'Open');