Summary of what I added
Endpoint Method Purpose
/api/v1/mr/dashboard GET MR mobile home-screen summary (attendance, today's stats, sample stock, report status)
/api/v1/notifications GET Paginated notification history
/api/v1/notifications/feed GET Unread count + latest 10 (for a bell icon)
/api/v1/notifications/read-all POST Mark all read
/api/v1/notifications/{id}/read POST Mark one read
/api/v1/users/{user}/reset-password POST Admin resets an MR's password
/api/v1/daily-reports/{id}/review PATCH Admin marks a report Reviewed
New files: Api/NotificationController.php, Api/Mr/DashboardController.php; methods added to Api/UserController.php and Api/DailyReportController.php; routes in routes/api.php; docs in docs/API.md. The API now has full parity with the web app.

Using this API in a Flutter mobile app
Your API is already mobile-ready: token auth (Sanctum bearer), a consistent JSON envelope (success / message / data), and multipart/form-data for selfie uploads. Here's the practical wiring.

1. Dependencies (pubspec.yaml)

dependencies:
dio: ^5.4.0 # HTTP client (interceptors, multipart)
flutter_secure_storage: ^9.0.0 # store the bearer token securely
provider: ^6.1.0 # or riverpod/bloc for state
geolocator: ^11.0.0 # GPS for check-in/visits
image_picker: ^1.0.0 # selfie capture 2. A single Dio client with auth + error handling

class Api {
static final dio = Dio(BaseOptions(
baseUrl: 'https://your-domain.com/api/v1',
headers: {'Accept': 'application/json'},
// don't throw on 4xx — let us read the {success:false} envelope
validateStatus: (s) => s != null && s < 500,
));

static final \_storage = const FlutterSecureStorage();

static void init() {
dio.interceptors.add(InterceptorsWrapper(
onRequest: (options, handler) async {
final token = await \_storage.read(key: 'token');
if (token != null) options.headers['Authorization'] = 'Bearer $token';
handler.next(options);
},
onResponse: (res, handler) {
// Global 401 → force logout/redirect to login
if (res.statusCode == 401) { /_ navigate to login _/ }
handler.next(res);
},
));
}
} 3. Login — store the token

Future<String> login(String email, String password) async {
final res = await Api.dio.post('/login',
data: {'email': email, 'password': password});
final body = res.data;
if (body['success'] != true) throw Exception(body['message']);
final token = body['data']['token'];
await Api.\_storage.write(key: 'token', value: token);
return body['data']['role']; // 'Admin' or 'MR' — route the UI on this
}
Route the whole app on role: MRs go to the field-work UI (/mr/\* endpoints), Admins to the management UI.

4. Attendance check-in — multipart with selfie + GPS
   This is the one endpoint that is not JSON. Send multipart/form-data:

Future<void> checkIn(XFile selfie, Position pos) async {
final form = FormData.fromMap({
'selfie': await MultipartFile.fromFile(selfie.path, filename: 'checkin.jpg'),
'lat': pos.latitude,
'lng': pos.longitude,
'accuracy': pos.accuracy,
'address': 'resolved address',
});
final res = await Api.dio.post('/mr/attendance/check-in', data: form);
if (res.data['success'] != true) throw Exception(res.data['message']);
}
Same shape for /mr/attendance/check-out.

5. Reading lists (pagination)
   List endpoints nest Laravel pagination inside data, so the items are at data.data:

final res = await Api.dio.get('/mr/visits', queryParameters: {'per_page': 15});
final List items = res.data['data']['data'];
final int lastPage = res.data['data']['meta']['last_page']; 6. A typical MR day maps to these calls
POST /login → save token, read role
GET /mr/dashboard → home screen (the endpoint I just added)
POST /mr/attendance/check-in (multipart)
POST /mr/visits → one call creates the visit + products + samples + optional order
GET /notifications/feed → bell badge (poll every ~60s, or fetch on resume)
POST /mr/daily-report → end of day (requires check-out first)
POST /mr/attendance/check-out (multipart)
Key gotchas
Always send Accept: application/json or Laravel may return HTML redirects instead of the JSON envelope.
Check body['success'], not just HTTP status — validation errors come back as 422 with errors: { field: [msg] }; surface those under each form field.
Selfie endpoints are multipart, everything else is JSON.
Token has no built-in expiry — handle 401 globally by clearing storage and returning to login.
For local testing against php artisan serve, use http://10.0.2.2:8000 on the Android emulator (not localhost), and note HTTPS is forced in non-local envs.
