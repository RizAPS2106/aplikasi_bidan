<?php

namespace App\Controllers\Bidan;

use \App\Controllers\BaseController;
use \App\Models\UserModel;
use \App\Models\OrderModel;

class Bidan extends BaseController
{
    public function index()
    {
        $order = new OrderModel();

        $data = [
            'title' => "Bidan",
            'header' => "Dashboard",
            'order' => $order->select('order.*,detail_order.*,user.*,user.id as id_user')
                ->join('detail_order', 'detail_order.invoice = order.invoice', 'LEFT')
                ->join('user', 'user.id = detail_order.id_user', 'LEFT')
                ->findAll()
        ];

        echo view('bidan/bidan_dashboard', $data);
    }

    public function profil()
    {
        $user = new UserModel();

        $id = session()->get('id');

        $data = [
            'title' => "Profil Bidan",
            'header' => "Profil",
            'profil' => $user->where('id', $id)->first()
        ];

        echo view('bidan/bidan_profil', $data);
    }

    public function profil_preview_edit()
    {
        $user = new UserModel();

        $id = session()->get('id');

        $data = $user->where('id', $id)->first();

        echo json_encode($data);
    }

    public function profil_edit()
    {
        $id = $this->request->getPost('id');

        $user = new UserModel();
        $data['user'] = $user->where('id', $id)->first();

        $validation =  \Config\Services::validation();
        $validation->setRules(
            [
                'id' => 'required',
                'nama' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Harap isi kolom {field}']
                ],
                'telepon'  => [
                    'label' => 'nomor telepon',
                    'rules' => 'required|numeric|min_length[10]|is_unique[user.telepon,id,{id}]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'numeric' => 'Harap isi kolom {field} dengan nomor',
                        'min_length' => '{field} minimal {param} digit',
                        'is_unique' => '{field} sudah terdaftar',
                    ]
                ],
                'email'  => [
                    'rules' => 'required|valid_email|is_unique[user.email,id,{id}]',
                    'errors' => [
                        'required' => 'Harap isi kolom {field}',
                        'valid_email' => 'Harap isi email yang valid',
                        'is_unique' => '{field} sudah terdaftar'
                    ]
                ],
                'first_password'  => [
                    'label' => 'Password lama',
                    'rules' => 'min_length[8]|permit_empty',
                    'errors' => [
                        'min_length' => 'Kolom {field} Minimal 8 karakter'
                    ]
                ],
                'password'  => [
                    'rules' => 'min_length[8]|permit_empty',
                    'errors' => [
                        'min_length' => 'Kolom {field} Minimal 8 karakter'
                    ]
                ],
                'password_confirm'  => [
                    'label' => 'konfirmasi password',
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
            if (empty($this->request->getPost('password')) && empty($this->request->getPost('first_password')) && empty($this->request->getPost('password_confirm'))) {
                $user->update($id, [
                    "nama" => $this->request->getPost('nama'),
                    "telepon" => $this->request->getPost('telepon'),
                    "email" => $this->request->getPost('email')
                ]);

                $message = 'Data berhasil diubah';

                echo $message;
            } else {
                $db = \Config\Database::connect();
                $builder = $db->table('user');
                $builder->select('password');
                $builder->where('id', $id);
                $builder->limit(1);
                $query = $builder->get();
                $result = $query->getResultArray();
                foreach ($result as $rst) {
                    $first_password = $rst['password'];
                    $first_password_field = md5($this->request->getPost('first_password'));
                    $password = $this->request->getPost('password');
                    $password_confirm = $this->request->getPost('password_confirm');

                    if ($first_password == $first_password_field) {
                        if ($password != '' || $password_confirm != '') {
                            if ($password == $password_confirm) {
                                $user->update($id, [
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
                        $message = 'Password lama tidak sesuai';
                    }
                    echo $message;
                }
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
}