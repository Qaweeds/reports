<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Adldap\Adldap;
use Adldap\Schemas\ActiveDirectory;
use Illuminate\Support\Facades\Auth;

class ldapController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            return redirect()->route('report');
        }
        return view('login');
    }

    public function login(Request $r)
    {

        $name = $r->post('login');
        $pass = $r->post('password');
        $remember = $r->post('remember');

        try {
            $config = [
                'hosts' => config('ldap.hosts'),
                'base_dn' => config('ldap.base_dn'),
                'username' => $name . config('ldap.suffix'),
                'password' => $pass,
                'schema' => ActiveDirectory::class,
            ];

            $ad = new Adldap();
            $ad->addProvider($config);
            $provider = $ad->connect();
            $manager = $provider->search()->users()->find($name);
            if (!$manager) return redirect()->route('login.index')->withErrors(['message' => 'Пользователь не найден!']);
            if (!$manager->inGroup('ReportR', $recursive = true)) return redirect()->route('login.index')->withErrors(['message' => 'Доступ запрещен']);
            $check = User::where('name', $manager->name[0])->first();
            if (is_null($check)) {
                User::create(
                    [
                        'name' => $manager->name[0],
                        'email' => $manager->mail[0],
                        'password' => bcrypt($manager->badpasswordtime[0])
                    ]
                );
                $check = User::where('name', $manager->name[0])->first();
            }

            Auth::login($check, $remember);


        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('login.index')->withErrors(['message' => 'Не верные данные']);
        }
        return redirect()->route('report');
    }
}
