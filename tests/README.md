Here is a **clean, copy-pastable `tests/README.md`** exactly as you requested — no fluff, no extras.

---

# **TeachMe – Test Instructions**

## **1. Setup**

1. Install dependencies:

```
composer install
```

2. Install PHPUnit:

```
composer require --dev phpunit/phpunit
```

3. Create and prepare the **test database**:

```
mysql -u root -p < database/teachme_db.sql
mysql -u root -p < database/seed_data.sql
```

4. Update database credentials in:

```
tests/config/test_db.php
```

---

## **2. Running Tests**

### Run **all** tests:

```
vendor/bin/phpunit --testdox
```

### Run **unit** tests only:

```
vendor/bin/phpunit tests/unit --testdox
```

### Run **integration** tests only:

```
vendor/bin/phpunit tests/integration --testdox
```

---

## **3. Test Files Overview**

### **Unit Tests**

- `tests/unit/test_registration.php`
  Tests signup, login, role assignment.

- `tests/unit/test_match.php`
  Tests tutor–learner matching logic.

- `tests/unit/test_feedback_system.php`
  Tests feedback submission + score updates.

- `tests/unit/test_certificate.php`
  Tests certificate qualification rules.

### **Integration Tests**

- `tests/integration/test_full_workflow.php`
  Full flow: signup → tutor test → approval → match → session → feedback → certificate.

- `tests/integration/test_admin_monitoring.php`
  Admin monitoring: approvals, logs, performance analytics.

---

## **4. Reset Test Database Before Running**

```
mysql -u root -p < database/teachme_db.sql
mysql -u root -p < database/seed_data.sql
```

---

## **5. Expected Output**

You should see:

```
OK (xx tests, xx assertions)
```

Any failure will show which module broke and why.

---

If you'd like, I can also **generate all the test files themselves** with runnable PHPUnit code.
