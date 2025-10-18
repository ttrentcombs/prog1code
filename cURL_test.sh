
set -e
BASE="${BASE:-http://localhost/api}"

echo "== Register =="
curl -s -X POST -H "Content-Type: application/json" -d '{"username":"steven","password":"secret"}' "$BASE/register.php" || true
echo

echo "== Login =="
TOKEN=$(curl -s -X POST -H "Content-Type: application/json" -d '{"username":"steven","password":"secret"}' "$BASE/login.php" | jq -r '.token')
echo "TOKEN=$TOKEN"

echo "== Health =="
curl -s "$BASE/health.php" | jq .

echo "== Add venue (auth) =="
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d '{"name":"City Arena","city":"Springfield"}' "$BASE/venues.php" | jq .

echo "== List venues =="
curl -s "$BASE/venues.php" | jq .

echo "== Create concert (auth) =="
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d '{"title":"Rock Night","venue_id":1,"event_date":"2025-12-31","price":59.99}' "$BASE/concerts.php" | jq .

echo "== List concerts =="
curl -s "$BASE/concerts.php" | jq .

echo "== Update concert id=1 (auth) =="
curl -s -X PUT -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d '{"price":49.99}' "$BASE/concerts.php?id=1" | jq .

echo "== Make booking (auth) =="
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d '{"concert_id":1,"qty":2}' "$BASE/bookings.php" | jq .

echo "== My bookings (auth) =="
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/bookings.php" | jq .

echo "== Users list =="
curl -s "$BASE/users.php" | jq .

echo "== Profile (auth) =="
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/profile.php" | jq .

echo "== Delete concert id=1 (auth) =="
curl -s -X DELETE -H "Authorization: Bearer $TOKEN" "$BASE/concerts.php?id=1" | jq .
