<?php

namespace App\Controllers;

use \App\Models\UserModel;

class User extends BaseController
{
    public function create()
    {
        $validation =  \Config\Services::validation();
        $validation->setRules(
            [
                'nama' => [
                    'label' => 'Nama',
                    'rules' => 'required',
                    'errors' => ['required' => 'Harap isi kolom {field}']
                ],
                'telepon' => [
                    'label' => 'Telepon',
                    'rules' => 'required|numeric|min_length[10]|is_unique[user.telepon]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'numeric' => 'Harap isi kolom {field} dengan nomor',
                        'min_length' => '{field} minimal {param} digit',
                        'is_unique' => '{field} sudah terdaftar',
                    ]
                ],
                'email' => [
                    'label' => 'Email',
                    'rules' => 'required|valid_email|is_unique[user.email]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'valid_email' => 'Harap isi email yang valid',
                        'is_unique' => '{field} sudah terdaftar'
                    ]
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'required|min_length[8]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'min_length' => 'Harap isi kolom {field} minimal 8 karakter'
                    ]
                ],
                'konfirmasi_password' => [
                    'label' => 'Konfirmasi password',
                    'rules' => 'required|min_length[8]|matches[password]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'min_length' => 'Harap isi kolom {field} minimal 8 karakter',
                        'matches' => '{field} salah',
                    ]
                ],
                'group_user' => 'required'
            ]
        );
        $isDataValid = $validation->withRequest($this->request)->run();

        if ($isDataValid) {
            $user = new UserModel();
            if ($this->request->getPost('id_cabang') !== null) {
                $user->insert([
                    "id_cabang" => $this->request->getPost('id_cabang'),
                    "nama" => $this->request->getPost('nama'),
                    "telepon" => $this->request->getPost('telepon'),
                    "email" => $this->request->getPost('email'),
                    "password" => md5($this->request->getPost('password')),
                    "group_user" => $this->request->getPost('group_user')
                ]);
            } else {
                $user->insert([
                    "nama" => $this->request->getPost('nama'),
                    "telepon" => $this->request->getPost('telepon'),
                    "email" => $this->request->getPost('email'),
                    "password" => md5($this->request->getPost('password')),
                    "group_user" => $this->request->getPost('group_user')
                ]);
            }

            echo 'Data berhasil disimpan';
        } else {
            $message = $validation->getErrors();

            foreach ($message as $msg) {
                if ($msg == end($message)) {
                    echo ucfirst($msg . '.');
                } else {
                    echo ucfirst($msg . ', ');
                }
            }
        }
    }

    public function preview_edit()
    {
        $id = $this->request->getGet('id');

        $user = new UserModel();

        $data = $user->select('user.*,cabang.nama as nama_cabang')->join('cabang', 'cabang.id = user.id_cabang', 'LEFT')->where('user.id', $id)->first();

        echo json_encode($data);
    }

    public function edit()
    {
        $id_user = $this->request->getPost('id');

        $user = new UserModel();
        $data = $user->where('id', $id_user)->first();

        $validation =  \Config\Services::validation();
        $validation->setRules(
            [
                'id' => 'required',
                'nama' => [
                    'label' => 'Nama',
                    'rules' => 'required',
                    'errors' => ['required' => 'Harap isi kolom {field}']
                ],
                'telepon' => [
                    'label' => 'Telepon',
                    'rules' => 'required|numeric|min_length[10]|is_unique[user.telepon,id,{id}]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'numeric' => 'Harap isi kolom {field} dengan nomor',
                        'min_length' => '{field} minimal {param} digit',
                        'is_unique' => '{field} sudah terdaftar',
                    ]
                ],
                'email' => [
                    'label' => 'Email',
                    'rules' => 'required|valid_email|is_unique[user.email,id,{id}]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'valid_email' => 'Harap isi email yang valid',
                        'is_unique' => '{field} sudah terdaftar'
                    ]
                ],
                'password_lama' => [
                    'label' => 'password lama',
                    'rules' => 'permit_empty',
                    'errors' => [
                        'min_length' => 'Kolom {field} Minimal 8 karakter'
                    ]
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'min_length[8]|permit_empty',
                    'errors' => [
                        'min_length' => 'Kolom {field} Minimal 8 karakter'
                    ]
                ],
                'konfirmasi_password' => [
                    'label' => 'Konfirmasi password',
                    'rules' => 'min_length[8]|matches[password]|permit_empty',
                    'errors' => [
                        'min_length' => 'Kolom {field} minimal 8 karakter',
                        'matches' => '{field} salah',
                    ]
                ]
            ]
        );
        $isDataValid = $validation->withRequest($this->request)->run();

        if ($isDataValid) {
            $first_password_data = $data['password'];
            $first_password = $this->request->getPost('password_lama');
            $password = $this->request->getPost('password');
            $password_confirm = $this->request->getPost('konfirmasi_password');

            if (empty($password) && empty($first_password) && empty($password_confirm)) {
                $user->update($id_user, [
                    "nama" => $this->request->getPost('nama'),
                    "telepon" => $this->request->getPost('telepon'),
                    "email" => $this->request->getPost('email')
                ]);

                echo 'Data berhasil diubah';
            } else {
                if (md5($first_password) == $first_password_data) {
                    if ($password != '' || $password_confirm != '') {
                        if ($password == $password_confirm) {
                            $user->update($id_user, [
                                "nama" => $this->request->getPost('nama'),
                                "telepon" => $this->request->getPost('telepon'),
                                "email" => $this->request->getPost('email'),
                                "password" => md5($this->request->getPost('password'))
                            ]);

                            $message = 'Data berhasil diubah';
                        } else {
                            $message = 'Password dan konfirmasi password tidak sesuai';
                        }
                    } else {
                        $message = 'Jika ingin mengubah password silahkan isi semua kolom password, jika tidak kosongkan saja';
                    }
                } else {
                    $message = 'password lama tidak sesuai';
                }
                echo $message;
            }
        } else {
            $message = $validation->getErrors();

            foreach ($message as $msg) {
                if ($msg == end($message)) {
                    echo $msg . '.';
                } else {
                    echo $msg . ', ';
                }
            }
        }
    }

    public function delete($id)
    {
        $user = new UserModel();
        $user->delete($id);

        echo 'Data berhasil dihapus';
    }
}
