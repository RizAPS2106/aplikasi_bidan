<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthBidan implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        } else {
            if (session()->get('group_user') == 0) {
                return redirect()->to('/admin');
            } else if (session()->get('group_user') == 1) {
                return redirect()->to('/owner');
            } else if (session()->get('group_user') == 2) {
                return redirect()->to('/');
            } else {
                return 'Error';
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
