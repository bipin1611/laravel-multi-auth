# Multiple Authentication In Laravel (By using Custom Auth guard)

Please Follow Below Steps to implement in your Project.

So, let's create for `Admin`

### Step: 1 - Make a Model and Migration called   `Admin`
On Admin Model, add below code,
```
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $guard = 'admin';

    protected $guarded =[];

}
```
here, my [Models/Admin](https://github.com/bipin1611/laravel-multi-auth/blob/master/app/Models/Admin.php)

and on your admin migration add the necessary fields and migrate the database.

### Step: 2 - Create a `AdminController` for performing the action

on `AdminController@login`
```
public function login(Request $request)
{
    $request->validate([
        'email' => 'required',
        'password' => 'required',
    ]);

    if (Admin::where('email', $request->email)->exists()) {

        if(\Auth::guard('admin')->attempt($request->only('email','password'),$request->filled('remember'))){
            //Authentication passed...
            return redirect()
                ->intended(route('admin.dashboard'))
                ->with('status','You are Logged in as Admin!');
        }

        //Authentication failed...
        return $this->loginFailed();

    }
    return back()->with('failed', 'Please Enter Valid Email ID or Password.');
}
```
please refer this [AdminController](https://github.com/bipin1611/laravel-multi-auth/blob/master/app/Http/Controllers/Admin/AdminController.php).


### Step: 3 - Setup Custom `Auth::guard`
Go to `config/auth.php`

on `guards` array, add your custom guard 
```
'admin' => [
    'driver' => 'session',
    'provider' => 'admins',
],
```
then, on `providers` array, add your provider which you define on your custom guard
 ```
 'admins' => [
    'driver' => 'eloquent',
    'model' => App\Models\Admin::class,
],
  ```
 Also, you can mentioned resetting password on `passwords` array
```
 'admins' => [
    'provider' => 'admins',
    'table' => 'password_resets',
    'expire' => 60
],
```
here, you can check my [Config/Auth.php](https://github.com/bipin1611/laravel-multi-auth/blob/master/config/auth.php)


### Step: 4 - You can handle unauthenticated user on `App/Exceptions`
by defining `unauthenticated` method.

on `App/Exceptions/Handler.php`
```
use Illuminate\Auth\AuthenticationException;


protected function unauthenticated($request, AuthenticationException $exception)
{
    if ($request->expectsJson()) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    if ($request->is('/admin') || $request->is('admin/*')) {
        if(\Auth::guard('admin')->check() == false){
            return redirect()->route('admin.login');
        }
    }
    return redirect()->guest( url('/'));
}
```
Please check the same file on my Repo [App/Exceptions/Handler.php](https://github.com/bipin1611/laravel-multi-auth/blob/master/app/Exceptions/Handler.php)

### Step: 5 - Define Routes and Apply `auth:admin` middleware
here is my `routes` 

```
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

Route::group(['prefix' => 'admin','middleware' =>'auth:admin'], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout.post');
});
```


### Notes:-
Please note that when you define the custom auth guard, On Logout method make sure that you should logged out from particular `auth::guard` only. Here is how you should do,
```
\Auth::guard('admin')->logout();
```
