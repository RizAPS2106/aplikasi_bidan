<?php

namespace App\Controllers;

use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        helper(['form']);

        $data = ['title' => 'Masuk'];

        echo view('login/login', $data);
    }

    public function auth()
    {
        $user = new UserModel();

        $email_field = $this->request->getVar('email');
        $password_field = md5($this->request->getVar('password'));

        $data = $user->where('email', $email_field)->first();
        if ($data) {
            $password = $data['password'];

            if ($password_field == $password) {
                $session_data = [
                    'id_user'    => $data['id'],
                    'nama_user'  => $data['nama'],
                    'email_user' => $data['email'],
                    'group_user' => $data['group_user'],
                    'logged_in'  => TRUE
                ];
                session()->set($session_data);

                $user->update($data['id'], [
                    'status_login' => 'login'
                ]);

                if ($data['group_user'] == 0) {
                    $message = "Admin";
                } else if ($data['group_user'] == 1) {
                    $message = "Owner";
                } else if ($data['group_user'] == 2) {
                    $message = "Konsumen";
                } else if ($data['group_user'] == 3) {
                    $message = "Bidan";
                }
            } else {
                $message = 'Password salah';
            }
        } else {
            $message = 'Email tidak terdaftar';
        }

        echo $message;
    }

    public function logout()
    {
        $id_user = session()->get('id_user');

        $user = new UserModel();
        $user->update($id_user, [
            'status_login' => 'logout'
        ]);

        session()->destroy();

        return redirect()->to('/');
    }
}
