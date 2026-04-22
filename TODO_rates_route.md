# TODO: Create Route for Parking Rates Page

**Status:** Plan ready - routes already exist.

## Information Gathered:
- GET `parking.rates` -> `ratesIndex()` exists
- POST `parking.rates` -> `ratesUpdate()` exists  
- View expects `$rates`, controller passes Rate::all()
- Form uses `parking.rates.update` (needs name fix in blade)

## Plan:
1. ✅ Update rates.blade.php form action to `route('parking.rates')`
2. ✅ Add route name to POST /rates -> name('parking.rates.update')
3. [ ] Test /parking/rates loads and saves

## Dependent Files:
- routes/web.php (add name)
- resources/views/parking/rates.blade.php (fix route name)

## Followup:
- php artisan route:clear
- Test page

Ready to implement.

