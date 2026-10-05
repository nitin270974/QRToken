PHP QR ORDER + ONE-TIME DELIVERY PROTOTYPE
==========================================

TARGET PATH
-----------
Upload the folder contents so the application is available at:
  https://mydomain/projectname/

WHAT IT DOES
------------
1. Sales user creates customer order and item lines.
2. PHP writes the order to PostgreSQL.
3. Browser produces QR containing a random 64-character token.
4. Customer can photograph, screenshot, share, or save QR.
5. Delivery user opens the same site and scans/uploads QR.
6. PHP fetches the current order from PostgreSQL.
7. Delivery user clicks Confirm Delivery / Paid.
8. PHP atomically changes ACTIVE to DELIVERED.
9. The same QR cannot be successfully redeemed again.

REQUIREMENTS
------------
- Web server with PHP 8+ recommended.
- PHP PDO PostgreSQL extension (pdo_pgsql).
- PostgreSQL database reachable by the PHP server.
- HTTPS for normal mobile-browser camera use.
- qrcode.min.js and html5-qrcode.min.js placed under assets/libs/.

FILES
-----
index.html                 Frontend
assets/app.js              Order/QR/scan UI logic
assets/style.css           Mobile responsive CSS
api/create_order.php       Creates order transactionally
api/get_order.php          Looks up order by QR token
api/deliver.php            One-time delivery update
api/health.php             DB connection test
db.php                     PDO connection helper
config.example.php         DB configuration template
sql/schema.sql             PostgreSQL schema
.htaccess                  Basic Apache protection for config files

SETUP
-----
1. Create a PostgreSQL database on your database host/provider.
2. Run sql/schema.sql against that database.
3. Copy config.example.php to config.php.
4. Fill in PostgreSQL host, port, database, user and password.
5. Upload the project to your /projectname path.
6. Add official qrcode.min.js and html5-qrcode.min.js files to assets/libs/.
7. Open /projectname/api/health.php. It should return {"ok":true}.
8. Open /projectname/ and test create -> scan -> deliver -> scan again.

DATABASE SCRIPT
---------------
Use sql/schema.sql. It creates orders and order_items, indexes, status checks, and the foreign key.

IMPORTANT RENDER / PHP NOTE
---------------------------
Render deployment capabilities and supported runtimes/plans can change. This bundle is a generic PHP + PostgreSQL project. Deploy it only in an environment that actually runs PHP and has the pdo_pgsql extension. If your current Render service is not a PHP-capable runtime, use an appropriate PHP container/runtime or another PHP-capable host.

CONFIG SECURITY
---------------
Never put DB credentials into index.html or app.js.
config.php is server-side only. On Apache, .htaccess attempts to deny direct access to it. For production, it is even better to load database credentials from environment variables or store config outside the public document root when your hosting arrangement allows this.

ONE-TIME REDEMPTION
-------------------
api/deliver.php uses an atomic conditional update equivalent to:
  UPDATE orders
  SET status='DELIVERED', delivered_at=NOW()
  WHERE qr_token=? AND status='ACTIVE'
  RETURNING ...;
Only a still-ACTIVE order can be changed to DELIVERED. A later request gets no updated row and is rejected after checking the existing status.

QR DATA
-------
The QR contains only a random token, not trusted amount/status/order details. Current details always come from PostgreSQL.

PRODUCTION HARDENING BEFORE REAL USE
------------------------------------
- Add authentication and SALES / DELIVERY / ADMIN roles.
- Record created_by and delivered_by based on the authenticated user.
- Add an audit/history table.
- Add rate limiting and logs.
- Validate business limits server-side.
- Use separate PaymentStatus and DeliveryStatus if payment and physical handover are separate events.
- Configure backups and monitoring.
- Review privacy/data-retention requirements for customer mobile numbers.
- Test simultaneous redemption on multiple phones and loss/recovery of network connectivity.

QR LIBRARIES
------------
This ZIP does not fabricate third-party minified library source. Add the official open-source browser distributions as:
  assets/libs/qrcode.min.js
  assets/libs/html5-qrcode.min.js
